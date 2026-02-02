<?php

namespace App\Http\Controllers\microapps;

use DateTime;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Teacher;
use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\microapps\TeacherLeaves;
use Illuminate\Database\QueryException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LeavesController extends Controller
{
    private $microapp;

    public function __construct(){
        $this->middleware('auth')->only(['index']);
        $this->middleware('isSchool')->only(['create', 'store', 'leaveUnlock', 'showHidden', 'hideLeave', 'unhideLeave']);
        $this->microapp = Microapp::where('url', '/leaves')->first();
    }

    public function index(){
        return view('microapps.leaves.index', ['appname' => 'leaves']);
    }

    public function create()
    {
        $school = Auth::guard('school')->user();
        $microapp = Microapp::where('url', '/leaves')->first();
        
        // Φέρνουμε τις άδειες του σχολείου που:
        // 1. ΔΕΝ είναι σε κατάσταση "Ανακλήθηκε" ΚΑΙ είναι ορατές
        // 2. Ή ΕΙΝΑΙ "Ανακλήθηκε" ΑΛΛΑ έχουν protocol_number (για να εμφανιστούν κλειδωμένες)
        $leavesExceptRevoked = $school->leavesIncludingRevoked()
        ->where('is_visible', 1)
        ->where(function($query) {
            $query->where('leave_state', '!=', '5-Ανακλήθηκε')
                ->orWhere(function($q) {
                    $q->where('leave_state', '5-Ανακλήθηκε')
                        ->whereNotNull('protocol_number');
                });
        })
        ->get();
        
        // Ελέγχουμε αν υπάρχουν αποκρυμμένες άδειες (is_visible = 0)
        $hasHiddenLeaves = $school->leaves()->where('is_visible', 0)->exists();
        
        return view('microapps.leaves.create', [
            'appname' => 'leaves',
            'microapp' => $microapp,
            'leaves' => $leavesExceptRevoked,
            'showHiddenLeavesLink' => $hasHiddenLeaves,
        ]);
    }

    public function import_leaves(Request $request)
    {
        // Validation
        $request->validate([
            'leaves_file' => 'required|mimes:csv,txt|max:10240'
        ]);
        
        ini_set('max_execution_time', 300);
        
        // Save file
        $filename = "teachers_file_leaves" . Auth::id() . ".csv";
        $path = $request->file('leaves_file')->storeAs('files', $filename);
        $fullPath = storage_path("app/$path");
        
        // Process CSV
        $result = $this->processCsvFile($fullPath);
        
        return redirect(url('/teachers'))
            ->with('success', "Ενημερώθηκαν {$result['updated']} ομάδες αδειών");
    }

    // ============================================
    // BATCH PROCESSING
    // ============================================

    private function processCsvFile($fullPath)
    {
        // Pass 1: Μάθε τις ομάδες με OR λογική
        $leaveGroups = $this->scanCsvForGroups($fullPath);
        
        // Pass 2: Επεξεργασία
        if (($handle = fopen($fullPath, 'r')) === false) {
            throw new \Exception('Αδυναμία ανάγνωσης αρχείου');
        }
        
        fgetcsv($handle, 0, ';');
        stream_filter_append($handle, 'convert.iconv.Windows-1253/UTF-8');
        
        $groupAccumulator = [];
        $processedGroups = 0;
        $batchSize = 50;
        $leaveIndex = 0;
        
        // ✅ Δημιουργία mapping από leave data σε group key
        $leaveToGroupMap = [];
        foreach ($leaveGroups as $groupKey => $groupData) {
            foreach ($groupData['leaves'] as $leave) {
                $leaveSignature = "{$leave['afm']}|{$leave['creator_code']}|" .
                                "{$leave['protocol_num']}|{$leave['protocol_date']}|" .
                                "{$leave['start_date']}|{$leave['days']}|{$leave['state']}";
                $leaveToGroupMap[$leaveSignature] = $groupKey;
            }
        }
        
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (empty(array_filter($row))) continue;
            
            $leaveData = $this->extractLeaveData($row);
            if (!$leaveData) continue;
            
            // ✅ Βρες σε ποια ομάδα ανήκει αυτή η άδεια
            $leaveSignature = "{$leaveData['afm']}|{$leaveData['creator_entity_code']}|" .
                            "{$leaveData['leave_protocol_number']}|{$leaveData['leave_protocol_date']}|" .
                            "{$leaveData['leave_start_date']}|{$leaveData['leave_days']}|{$leaveData['leave_state']}";
            
            $groupKey = $leaveToGroupMap[$leaveSignature] ?? null;
            
            if (!$groupKey) continue;
            
            if (!isset($groupAccumulator[$groupKey])) {
                $groupAccumulator[$groupKey] = [];
            }
            $groupAccumulator[$groupKey][] = $leaveData;
            
            // Τσέκαρε αν η ομάδα είναι πλήρης
            $expectedStates = $leaveGroups[$groupKey]['states'] ?? [];
            $currentStates = array_map(fn($l) => $l['leave_state'], $groupAccumulator[$groupKey]);
            
            if ($this->isGroupComplete($currentStates, $expectedStates)) {
                $this->processCompleteGroup($groupKey, $groupAccumulator[$groupKey], $leaveGroups[$groupKey]);
                
                unset($groupAccumulator[$groupKey]);
                $processedGroups++;
                
                if ($processedGroups % $batchSize == 0) {
                    gc_collect_cycles();
                }
            }
        }
        
        foreach ($groupAccumulator as $key => $leaves) {
            $this->processCompleteGroup($key, $leaves, $leaveGroups[$key]);
        }
        
        fclose($handle);
        
        return ['updated' => $processedGroups];
    }

    private function scanCsvForGroups($fullPath)
    {
        if (($handle = fopen($fullPath, 'r')) === false) {
            throw new \Exception('Αδυναμία ανάγνωσης αρχείου');
        }
        
        fgetcsv($handle, 0, ';');
        stream_filter_append($handle, 'convert.iconv.Windows-1253/UTF-8');
        
        $groupsByProtocol = []; // Ομαδοποίηση με protocol
        $groupsByDateDays = []; // Ομαδοποίηση με start_date + days
        $allLeaves = []; // Όλες οι γραμμές
        
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (empty(array_filter($row))) continue;
            
            $afm = $this->extractAfm($row[1] ?? '');
            $creatorCode = $this->extractCode($row[21] ?? '');
            $protocolNum = trim($row[18] ?? '');
            $protocolDate = $this->convertDate($row[19] ?? '');
            $leaveStartDate = $this->convertDate($row[16] ?? '');
            $leaveDays = trim($row[17] ?? '');
            $state = trim($row[14] ?? '');
            
            if (!$afm || !$creatorCode) continue;
            
            // ✅ Key 1: Protocol-based
            $keyProtocol = "{$afm}|{$creatorCode}|{$protocolNum}|{$protocolDate}";
            
            // ✅ Key 2: Date+Days-based
            $keyDateDays = "{$afm}|{$creatorCode}|{$leaveStartDate}|{$leaveDays}";
            
            // Αποθήκευση στοιχείων
            $leaveInfo = [
                'afm' => $afm,
                'creator_code' => $creatorCode,
                'protocol_num' => $protocolNum,
                'protocol_date' => $protocolDate,
                'start_date' => $leaveStartDate,
                'days' => $leaveDays,
                'state' => $state,
                'key_protocol' => $keyProtocol,
                'key_date_days' => $keyDateDays,
            ];
            
            $allLeaves[] = $leaveInfo;
            
            // Ομαδοποίηση Protocol
            if (!isset($groupsByProtocol[$keyProtocol])) {
                $groupsByProtocol[$keyProtocol] = [
                    'states' => [],
                    'leaves' => [],
                ];
            }
            $groupsByProtocol[$keyProtocol]['states'][] = $state;
            $groupsByProtocol[$keyProtocol]['leaves'][] = $leaveInfo;
            
            // Ομαδοποίηση Date+Days
            if (!isset($groupsByDateDays[$keyDateDays])) {
                $groupsByDateDays[$keyDateDays] = [
                    'states' => [],
                    'leaves' => [],
                ];
            }
            $groupsByDateDays[$keyDateDays]['states'][] = $state;
            $groupsByDateDays[$keyDateDays]['leaves'][] = $leaveInfo;
        }
        
        fclose($handle);
        
        // ✅ Merge των groups με κοινά στοιχεία
        return $this->mergeGroups($groupsByProtocol, $groupsByDateDays, $allLeaves);
    }

    private function mergeGroups($groupsByProtocol, $groupsByDateDays, $allLeaves)
    {
        // Union-Find structure
        $unionFind = [];
        
        foreach ($allLeaves as $index => $leave) {
            $unionFind[$index] = $index; // Κάθε άδεια είναι αρχικά στη δική της ομάδα
        }
        
        // Find function
        $find = function($x) use (&$unionFind, &$find) {
            if ($unionFind[$x] != $x) {
                $unionFind[$x] = $find($unionFind[$x]);
            }
            return $unionFind[$x];
        };
        
        // Union function
        $union = function($x, $y) use (&$unionFind, &$find) {
            $rootX = $find($x);
            $rootY = $find($y);
            if ($rootX != $rootY) {
                $unionFind[$rootX] = $rootY;
            }
        };
        
        // ✅ Ενώνουμε άδειες που έχουν ίδιο protocol
        foreach ($groupsByProtocol as $key => $group) {
            $indices = [];
            foreach ($allLeaves as $index => $leave) {
                if ($leave['key_protocol'] === $key) {
                    $indices[] = $index;
                }
            }
            for ($i = 1; $i < count($indices); $i++) {
                $union($indices[0], $indices[$i]);
            }
        }
        
        // ✅ Ενώνουμε άδειες που έχουν ίδια start_date + days
        foreach ($groupsByDateDays as $key => $group) {
            $indices = [];
            foreach ($allLeaves as $index => $leave) {
                if ($leave['key_date_days'] === $key) {
                    $indices[] = $index;
                }
            }
            for ($i = 1; $i < count($indices); $i++) {
                $union($indices[0], $indices[$i]);
            }
        }
        
        // ✅ Δημιουργία τελικών ομάδων
        $finalGroups = [];
        
        foreach ($allLeaves as $index => $leave) {
            $root = $find($index);
            
            // Δημιουργία composite key για αυτή την ομάδα
            $groupKey = "group_{$root}";
            
            if (!isset($finalGroups[$groupKey])) {
                $finalGroups[$groupKey] = [
                    'states' => [],
                    'has_revoked' => false,
                    'has_active' => false,
                    'leaves' => [],
                ];
            }
            
            $finalGroups[$groupKey]['states'][] = $leave['state'];
            $finalGroups[$groupKey]['leaves'][] = $leave;
            
            if ($leave['state'] === '5-Ανακλήθηκε') {
                $finalGroups[$groupKey]['has_revoked'] = true;
            }
            if (in_array($leave['state'], ['2-Υποβλήθηκε', '3-Εγκρίθηκε'])) {
                $finalGroups[$groupKey]['has_active'] = true;
            }
        }
        
        return $finalGroups;
    }

    private function processCompleteGroup($key, $leavesInGroup, $groupInfo)
    {
        DB::beginTransaction();
        try {
            // ✅ Ψάχνουμε με OR λογική
            $firstLeave = $leavesInGroup[0];
            
            $dbLeaveWithProtocol = TeacherLeaves::where(function($query) use ($firstLeave) {
                // Protocol-based
                $query->where(function($q) use ($firstLeave) {
                    $q->where('afm', $firstLeave['afm'])
                    ->where('creator_entity_code', $firstLeave['creator_entity_code'])
                    ->where('leave_protocol_number', $firstLeave['leave_protocol_number'])
                    ->where('leave_protocol_date', $firstLeave['leave_protocol_date']);
                })
                // OR Date+Days-based
                ->orWhere(function($q) use ($firstLeave) {
                    $q->where('afm', $firstLeave['afm'])
                    ->where('creator_entity_code', $firstLeave['creator_entity_code'])
                    ->where('leave_start_date', $firstLeave['leave_start_date'])
                    ->where('leave_days', $firstLeave['leave_days']);
                });
            })
            ->whereNotNull('protocol_number')
            ->first();
            
            $hasRevoked = $groupInfo['has_revoked'];
            $hasActive = $groupInfo['has_active'];
            
            if ($hasRevoked && $hasActive && $dbLeaveWithProtocol) {
                $this->handleRevokedAndCorrected($leavesInGroup, $dbLeaveWithProtocol);
            } 
            elseif ($hasRevoked && !$hasActive && $dbLeaveWithProtocol) {
                $this->handleOnlyRevoked($leavesInGroup, $dbLeaveWithProtocol);
            } 
            else {
                $this->handleNormalCase($leavesInGroup);
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Group $key error: " . $e->getMessage());
            throw $e;
        }
    }
    private function handleOnlyRevoked($leavesInGroup, $dbLeaveWithProtocol)
    {
        $revokedLeave = $leavesInGroup[0]; // Μόνο 1 γραμμή
        
        // Idempotent check: Αν είναι ήδη "Ανακλήθηκε" → skip
        if ($dbLeaveWithProtocol->leave_state === '5-Ανακλήθηκε') {
            return;
        }
        
        // Απλό UPDATE της υπάρχουσας εγγραφής
        $dbLeaveWithProtocol->fill([
            'leave_state' => $revokedLeave['leave_state'],
            'submitted' => 0,
            'is_visible' => 1,
            'am' => $revokedLeave['am'],
            'sex' => $revokedLeave['sex'],
            'surname' => $revokedLeave['surname'],
            'name' => $revokedLeave['name'],
            'fathers_name' => $revokedLeave['fathers_name'],
            'specialty_code' => $revokedLeave['specialty_code'],
            'specialty' => $revokedLeave['specialty'],
            'directorate' => $revokedLeave['directorate'],
            'employment_relation' => $revokedLeave['employment_relation'],
            'leave_type' => $revokedLeave['leave_type'],
            'leave_start_date' => $revokedLeave['leave_start_date'],
            'leave_days' => $revokedLeave['leave_days'],
            'leave_description' => $revokedLeave['leave_description'],
            'creator_entity_name' => $revokedLeave['creator_entity_name'],
            'creation_date' => $revokedLeave['creation_date'],
            'submission_date' => $revokedLeave['submission_date'],
            'approved_days' => $revokedLeave['approved_days'],
            'approved_months' => $revokedLeave['approved_months'],
            'approved_years' => $revokedLeave['approved_years'],
            'approved_protocol_number' => $revokedLeave['approved_protocol_number'],
            'approved_protocol_date' => $revokedLeave['approved_protocol_date'],
            'approved_description' => $revokedLeave['approved_description'],
            'revoke_description' => $revokedLeave['revoke_description'],
            'approving_authority_code' => $revokedLeave['approving_authority_code'],
            'approving_authority_name' => $revokedLeave['approving_authority_name'],
            'last_change_date' => $revokedLeave['last_change_date'],
        ]);
        
        // ΔΕΝ πειράζουμε: protocol_number, protocol_date, files_json
        
        $dbLeaveWithProtocol->save();
    }

    private function handleRevokedAndCorrected($leavesInGroup, $dbLeaveWithProtocol)
    {
        // Idempotent check: Αν υπάρχουν ήδη ΚΑΙ τα 2 states στη βάση → skip
        $keyParts = [
            'afm' => $leavesInGroup[0]['afm'],
            'creator_entity_code' => $leavesInGroup[0]['creator_entity_code'],
            'leave_protocol_number' => $leavesInGroup[0]['leave_protocol_number'],
            'leave_protocol_date' => $leavesInGroup[0]['leave_protocol_date'],
        ];
        
        $existingStates = TeacherLeaves::where($keyParts)
            ->pluck('leave_state')
            ->toArray();
        
        $hasRevokedInDb = in_array('5-Ανακλήθηκε', $existingStates);
        $hasActiveInDb = in_array('2-Υποβλήθηκε', $existingStates) || 
                         in_array('3-Εγκρίθηκε', $existingStates);
        
        if ($hasRevokedInDb && $hasActiveInDb) {
            // Ήδη επεξεργασμένη
            return;
        }
        
        // Χωρισμός των γραμμών
        $revokedData = null;
        $activeData = null;
        
        foreach ($leavesInGroup as $leave) {
            if ($leave['leave_state'] === '5-Ανακλήθηκε') {
                $revokedData = $leave;
            } else {
                $activeData = $leave;
            }
        }
        
        // Κρατά protocol για μεταφορά
        $protocolData = [
            'protocol_number' => $dbLeaveWithProtocol->protocol_number,
            'protocol_date' => $dbLeaveWithProtocol->protocol_date,
            'files_json' => $dbLeaveWithProtocol->files_json,
        ];
        
        // ΒΗΜΑ 1: UPDATE την υπάρχουσα σε "Ανακλήθηκε"
        $dbLeaveWithProtocol->fill(array_merge($this->getLeaveUpdateData($revokedData), [
            'protocol_number' => null,
            'protocol_date' => null,
            'files_json' => null,
            'submitted' => 0,
            'is_visible' => 1,
        ]));
        $dbLeaveWithProtocol->save();
        
        // ΒΗΜΑ 2: CREATE νέα με τη διορθωμένη
        $newActiveLeave = $this->createLeave($activeData);
        
        // ΒΗΜΑ 3: Μεταφορά protocol
        $newActiveLeave->protocol_number = $protocolData['protocol_number'];
        $newActiveLeave->protocol_date = $protocolData['protocol_date'];
        $newActiveLeave->submitted = 0;
        $newActiveLeave->is_visible = 1;
        
        // ΒΗΜΑ 4: Μετονομασία και μεταφορά αρχείων
        if ($protocolData['files_json']) {
            $newFilesJson = $this->renameFiles(
                $dbLeaveWithProtocol->id, 
                $newActiveLeave->id, 
                $protocolData['files_json']
            );
            $newActiveLeave->files_json = $newFilesJson;
        }
        
        $newActiveLeave->save();
    }

    private function handleNormalCase($leavesInGroup)
    {
        foreach ($leavesInGroup as $leaveData) {
            // Κάνε updateOrCreate για κάθε γραμμή
            $keys = [
                'afm' => $leaveData['afm'],
                'creator_entity_code' => $leaveData['creator_entity_code'],
                'leave_protocol_number' => $leaveData['leave_protocol_number'],
                'leave_protocol_date' => $leaveData['leave_protocol_date'],
                'leave_state' => $leaveData['leave_state'],
            ];
            
            $updateData = $this->getLeaveUpdateData($leaveData);
            
            TeacherLeaves::updateOrCreate($keys, $updateData);
        }
    }

    private function createLeave($leaveData)
    {
        return TeacherLeaves::create(array_merge([
            'afm' => $leaveData['afm'],
            'creator_entity_code' => $leaveData['creator_entity_code'],
            'leave_protocol_number' => $leaveData['leave_protocol_number'],
            'leave_protocol_date' => $leaveData['leave_protocol_date'],
            'leave_state' => $leaveData['leave_state'],
        ], $this->getLeaveUpdateData($leaveData)));
    }

    private function getLeaveUpdateData($leaveData)
    {
        return [
            'am' => $leaveData['am'],
            'sex' => $leaveData['sex'],
            'surname' => $leaveData['surname'],
            'name' => $leaveData['name'],
            'fathers_name' => $leaveData['fathers_name'],
            'specialty_code' => $leaveData['specialty_code'],
            'specialty' => $leaveData['specialty'],
            'directorate' => $leaveData['directorate'],
            'employment_relation' => $leaveData['employment_relation'],
            'leave_type' => $leaveData['leave_type'],
            'leave_start_date' => $leaveData['leave_start_date'],
            'leave_days' => $leaveData['leave_days'],
            'leave_description' => $leaveData['leave_description'],
            'creator_entity_name' => $leaveData['creator_entity_name'],
            'creation_date' => $leaveData['creation_date'],
            'submission_date' => $leaveData['submission_date'],
            'approved_days' => $leaveData['approved_days'],
            'approved_months' => $leaveData['approved_months'],
            'approved_years' => $leaveData['approved_years'],
            'approved_protocol_number' => $leaveData['approved_protocol_number'],
            'approved_protocol_date' => $leaveData['approved_protocol_date'],
            'approved_description' => $leaveData['approved_description'],
            'revoke_description' => $leaveData['revoke_description'],
            'approving_authority_code' => $leaveData['approving_authority_code'],
            'approving_authority_name' => $leaveData['approving_authority_name'],
            'last_change_date' => $leaveData['last_change_date'],
        ];
    }

    private function renameFiles($oldLeaveId, $newLeaveId, $filesJson)
    {
        $oldFiles = json_decode($filesJson, true);
        if (!$oldFiles) return null;
        
        $newFiles = [];
        
        foreach ($oldFiles as $oldServerName => $greekName) {
            // Παλιό: 34_1.pdf → Νέο: 56_1.pdf
            $parts = explode('_', $oldServerName);
            if (count($parts) < 2) continue;
            
            $fileNumber = $parts[1]; // '1.pdf'
            $newServerName = $newLeaveId . '_' . $fileNumber;
            
            // Μετονομασία φυσικού αρχείου
            $oldPath = storage_path("app/teacher_leaves/{$oldServerName}");
            $newPath = storage_path("app/teacher_leaves/{$newServerName}");
            
            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
            }
            
            $newFiles[$newServerName] = $greekName;
        }
        
        return json_encode($newFiles);
    }

    private function isGroupComplete($currentStates, $expectedStates)
    {
        sort($currentStates);
        sort($expectedStates);
        return $currentStates === $expectedStates;
    }

    private function extractAfm($raw)
    {
        $trimmed = trim($raw);
        return is_string($trimmed) && strlen($trimmed) > 4 ? substr($trimmed, 2, -1) : $trimmed;
    }

    private function extractCode($raw)
    {
        $trimmed = trim($raw);
        return is_string($trimmed) && strlen($trimmed) > 4 ? substr($trimmed, 2, -1) : $trimmed;
    }

    // ============================================
    // ΥΠΑΡΧΟΥΣΕΣ ΜΕΘΟΔΟΙ
    // ============================================

    private function extractLeaveData($row)
    {
        // Helper για clean values
        $getValue = function($index) use ($row) {
            if (!isset($row[$index])) return '';
            $value = trim($row[$index]);
            
            if (!mb_check_encoding($value, 'UTF-8')) {
                $value = mb_convert_encoding($value, 'UTF-8', 'Windows-1253');
            }
            
            return $value !== '' ? $value : '';
        };
        
        // Extract AFM (remove Excel formula notation =" ")
        $rawAfm = $getValue(1);
        $afm = $this->extractAfm($rawAfm);
        
        // Validate teacher exists
        if (!$afm || !Teacher::where('afm', $afm)->exists()) {
            return null;
        }
        
        // Extract creator entity code
        $creatorCode = $getValue(21);
        $creatorEntityCode = $this->extractCode($creatorCode);
        
        return [
            'afm' => $afm,
            'creator_entity_code' => $creatorEntityCode,
            'leave_protocol_number' => $getValue(18),
            'leave_protocol_date' => $this->convertDate($getValue(19)),
            
            // Όλα τα υπόλοιπα πεδία από το CSV
            'am' => $getValue(0),
            'sex' => $getValue(2),
            'surname' => $getValue(3),
            'name' => $getValue(4),
            'fathers_name' => $getValue(5),
            'specialty_code' => $getValue(6),
            'specialty' => $getValue(7),
            'directorate' => $getValue(11),
            'employment_relation' => $getValue(13),
            'leave_state' => $getValue(14),
            'leave_type' => $getValue(15),
            'leave_start_date' => $this->convertDate($getValue(16)),
            'leave_days' => $getValue(17),
            'leave_description' => $getValue(20),
            'creator_entity_name' => $getValue(22),
            'creation_date' => $this->convertDate($getValue(23)),
            'submission_date' => $this->convertDate($getValue(24)),
            'approved_days' => $getValue(25),
            'approved_months' => $getValue(26),
            'approved_years' => $getValue(27),
            'approved_protocol_number' => $getValue(28),
            'approved_protocol_date' => $this->convertDate($getValue(29)),
            'approved_description' => mb_substr($getValue(30), 0, 191),
            'revoke_description' => $getValue(31),
            'approving_authority_code' => $getValue(32),
            'approving_authority_name' => $getValue(33),
            'last_change_date' => $this->convertDate($getValue(34)),
        ];
    }

    public function convertDate($csvDate){
        
        if (empty($csvDate)) {
            return null;
        }
        
        $date = DateTime::createFromFormat('d/m/Y', $csvDate);
        
        if ($date) {
            return $date->format('Y-m-d');
        } else {
            return null;
        }
    }

    public function upload_files(Request $request, TeacherLeaves $teacher_leave){
        // Method for the school to upload files
        if(Auth::guard('school')->user()->code != $teacher_leave->creator_entity_code){
            return back()->with('failure', 'Δεν έχετε δικαίωμα επεξεργασίας αυτής της άδειας.');
        }
        $request->validate([
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        $files = $request->file('files');
        
        $fileNames = [];
        //Βρες πόσα αρχεία έχει ήδη ανεβάσει
        if($teacher_leave->files_json){
            $fileNames = json_decode($teacher_leave->files_json, true);
            end($fileNames);
            $lastServerFileName = key($fileNames);
            $underScorePosition = strpos($lastServerFileName, '_');
            $filesCount = substr($lastServerFileName, $underScorePosition + 1, strpos($lastServerFileName, '.') - $underScorePosition -1);
        } else {
            $filesCount = 0;
        }
        $lastFileNumber = $filesCount;
        $directory = "teacher_leaves";
        foreach($files as $file){
            $filesCount++;
            $serverFileName = $teacher_leave->id."_".$filesCount.".".$file->getClientOriginalExtension();
            $fileNames[$serverFileName] = $file->getClientOriginalName();
            $fileHandler = new FilesController();
            $uploaded = $fileHandler->upload_file($directory, $file, 'local', $serverFileName);
            
            if($uploaded->getStatusCode() == 500){
                Log::channel('files')->error($teacherAfm." Files failed to upload");
                return back()->with('failure', 'Αποτυχία στην υποβολή των αρχείων. Δοκιμάστε ξανά');
            }
        }
        $teacher_leave->files_json = json_encode($fileNames);
        try{
            $teacher_leave->save();
        } catch(\Exception $e) {
            Log::channel('files')->error($teacher_leave->id." Teacher Leave Files failed to update database field files_json");
            return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων με τα ονόματα των αρχείων. Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($teacher_leave->id." Teacher Leave Files successfully uploaded");
        return redirect(url('leaves/create'))->with('success','Τα αρχεία ανέβηκαν.');
    }

    public function submit(TeacherLeaves $leave){
        // Method that sends leave to protocol
        if(Auth::guard('school')->user()->code != $leave->creator_entity_code){
            return back()->with('failure', 'Δεν έχετε δικαίωμα υποβολής αυτής της άδειας.');
        }
        //Στείλε την αίτηση στο πρωτόκολλο
        try{
            $protocol_message = $this->sendLeaveToProtocol($leave);
            if($protocol_message['success'] == false){
                return back()->with('failure', 'Aπέτυχε η αποστολή στο πρωτόκολλο με μήνυμα: ' . $protocol_message['message'] . ' Παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
            }
        } catch(\Exception $e) {
            return back()->with('failure', 'Αποτυχία αποστολής αίτησης στο Πρωτόκολλο της Διεύθυνσης. Παρακαλούμε επικοινωνήστε με το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr.');
        }
        try{
            $protocol_message = explode(" - ", $protocol_message['message']);
            $leave->protocol_number = $protocol_message[0];
            $leave->protocol_date = Carbon::createFromFormat('d/m/Y', $protocol_message[1])->format('Y-m-d');
            $leave->save();
        } catch(\Exception $e) {
            return back()->with('failure', 'Η άδεια πρωτοκολλήθηκε με επιτυχία στο Πρωτόκολλο της Διεύθυνσης αλλά απέτυχε η αποθήκευση του αριθμού πρωτοκόλλου. Παρακαλούμε επικοινωνήστε άμεσα με το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr.');
        }
        //Οριστικοποίησε την αίτηση
        try{
            $leave->submitted = 1;
            $leave->save();
        } catch(\Exception $e) {
            return back()->with('failure', 'Η άδεια πρωτοκολλήθηκε με επιτυχία στο Πρωτόκολλο της Διεύθυνσης αλλά απέτυχε η οριστικοποίησή της. Παρακαλούμε επικοινωνήστε άμεσα με το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr.');
        }
        return redirect(url('leaves/create'))->with('success',"Η άδεια υποβλήθηκε στο Πρωτόκολλο της ΔΙΠΕ Αχαΐας με αρ. πρωτ. $protocol_message[0] - $protocol_message[1].");
    }

    public function download_file($file, $download_file_name = null){
        $username = Auth::check() ? Auth::user()->username : Auth::guard('school')->user()->code;
        $directory = "teacher_leaves";
        $fileHandler = new FilesController();
        $download = $fileHandler->download_file($directory, $file, 'local', $download_file_name);
        if($download->getStatusCode() == 500){
            Log::channel('files')->error($username." File $file failed to download");
            return back()->with('failure', 'Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($username." File $file successfully downloaded");
        return $download;
    }

    public function delete_file($serverFileName, $leaveId){
        $leave = TeacherLeaves::findOrFail($leaveId);
        if(Auth::guard('school')->user()->code != $leave->creator_entity_code){
            return back()->with('failure', 'Δεν έχετε δικαίωμα επεξεργασίας αυτής της αίτησης.');
        }
        $fileHandler = new FilesController();
        $files = json_decode($leave->files_json, true);
        try{
            $fileHandler->delete_file('teacher_leaves', $serverFileName, 'local');
            $databaseFileName = $files[$serverFileName];
            $key = array_search($databaseFileName, $files);
            if ($key !== false) {
                unset($files[$key]);
            }
            if(empty($files)){
                $leave->files_json = null;
            }
            else{
                $leave->files_json = json_encode($files);
            }
            $leave->update();
        } catch(\Exception $e) {
            Log::channel('files')->info("Teacher Leave File DatabaseFileName: $databaseFileName, ServerFileName: $serverFileName failed to delete");
            return back()->with('failure', 'Αποτυχία διαγραφής αρχείου.');
        }
        Log::channel('files')->info("Teacher Leave File DatabaseFileName: $databaseFileName, ServerFileName: $serverFileName deleted succesfully.");
        return back()->with('success', 'Επιτυχής διαγραφή αρχείου: "'.$databaseFileName.'"');
    }

    public function sendLeaveToProtocol(TeacherLeaves $leave){
        
        // Find leave type from lookup table
        $leaveType = \App\Models\LeaveType::where('description', $leave->leave_type)->first();
        $leaveProtocolDate = $leave->leave_protocol_date->format('d/m/Y');
        
        $schoolProtocol = $leave->leave_protocol_number .'-'. $leaveProtocolDate;
        
        if(!$leaveType){
            return ['success' => false, 'message' => 'No leave type found for: ' . $leave->leave_type];
        }
        
        $data = [
            ['name' => 'Afm', 'contents' => $leave->afm ],
            ['name' => 'SchoolCode', 'contents' => $leave->creator_entity_code ],
            ['name' => 'LeaveType', 'contents' => $leaveType->eProtocolId ],
            ['name' => 'StartDate', 'contents' => $leave->leave_start_date ],
            ['name' => 'Days', 'contents' => $leave->leave_days ],
            ['name' => 'SchoolProtocol', 'contents' => $schoolProtocol ],
        ];
       
        if($leave->files_json){
            $fileNames = json_decode($leave->files_json, true);
            foreach($fileNames as $serverFileName => $databaseFileName){
                $data[] = [
                    'name'     => 'Files',
                    'contents' => fopen(storage_path("app/teacher_leaves/$serverFileName"), 'r'),
                ];
            }
        }

        if($leave->protocol_number && $leave->protocol_date){
            $data[] = ['name' => 'ProtocolNum', 'contents' => $leave->protocol_number];
            $data[] = ['name' => 'ProtocolYear', 'contents' => $leave->protocol_date->format('Y')];
        }
     
        $client = new Client();
        
        try{
           $response = $client->request('POST', env('E_DIRECTORATE').'/leaves/new', [
                'headers' => [
                    'X-API-Key' => env('API_KEY'),
                ],
                'multipart' => $data,
            ]); 
        } catch (\Exception $e) {
            Log::channel('files')->error("Leave ID: ".$leave->id." - Protocol Request Exception: " . $e->getMessage());
            Log::channel('files')->info("Leave ID: ".$leave->id." - Data: " . json_encode($data));
            return ['success' => false, 'message' => 'Protocol Request Exception: ' . $e->getMessage()];
        }
        
        $status = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        Log::channel('files')->info("Leave ID: ".$leave->id." - Protocol Response Status: $status - Body: $body");
        
        if($status != 200){
            return ['success' => false, 'message' => 'Protocol Response Status: ' . $status];
        } else {
            return ['success' => true, 'message' => $body];
        }
    }

    public function getTeacherLeaves(TeacherLeaves $teacher_leave){
        $leaves = TeacherLeaves::where('afm', $teacher_leave->afm)->
                                where('leave_state', '3-Εγκρίθηκε')->
                                where('leave_type', $teacher_leave->leave_type)->get();
        $teacher = Teacher::where('afm', $teacher_leave->afm)->first();
        $isDirector = $teacher->isDirector();
        
        return response()->json([
            'leaves' => $leaves,
            'isDirector' => $isDirector,
        ]);
    }

    public function leaveUnlock(TeacherLeaves $teacher_leave) {
        if($teacher_leave->submitted == 1){
            $teacher_leave->submitted = 0;
            try {
                $teacher_leave->save();
                return redirect()->back()->with('success', 'Η άδεια ξεκλειδώθηκε επιτυχώς και μπορείτε να την επεξεργαστείτε.');
            } catch (\Exception $e) { 
                return redirect()->back()->with('error', 'Σφάλμα κατά το ξεκλείδωμα της άδειας: ' . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('warning', 'Η άδεια δεν είναι υποβεβλημένη και δεν χρειάζεται ξεκλείδωμα.');
        }
    }

    public function showHidden()
    {
        $school = Auth::guard('school')->user();
        $microapp = Microapp::where('url', '/leaves')->first();
        
        $hiddenLeaves = $school->leaves()
            ->where('is_visible', 0)
            ->orderBy('leave_start_date', 'desc')
            ->get();
        
        return view('microapps.leaves.hidden', [
            'appname' => 'leaves',
            'microapp' => $microapp,
            'hiddenLeaves' => $hiddenLeaves,
        ]);
    }

    public function hideLeave($teacher_leave)
    {
        $school = Auth::guard('school')->user();
        $leave = TeacherLeaves::findOrFail($teacher_leave);
        
        if ($leave->creator_entity_code !== $school->code) {
            abort(403, 'Unauthorized action.');
        }
        
        $canHide = false;
        
        if (!$leave->submitted && !$leave->protocol_number) {
            $canHide = true;
        } elseif ($leave->submitted && $leave->protocol_number) {
            $canHide = true;
        }
        
        if (!$canHide) {
            return redirect()->back()->with('error', 'Δεν μπορείτε να αποκρύψετε αυτή την άδεια σε αυτή την κατάσταση.');
        }
        
        if ($leave->leave_state === 'Ανακλήθηκε') {
            return redirect()->back()->with('error', 'Δεν μπορείτε να αποκρύψετε μια ανακληθείσα άδεια.');
        }
        
        $leave->is_visible = 0;
        $leave->save();
        
        return redirect()->back()->with('success', 'Η άδεια αποκρύφθηκε επιτυχώς.');
    }

    public function unhideLeave($teacher_leave)
    {
        $school = Auth::guard('school')->user();
        $leave = TeacherLeaves::findOrFail($teacher_leave);
        
        if ($leave->creator_entity_code !== $school->code) {
            abort(403, 'Unauthorized action.');
        }
        
        $leave->is_visible = 1;
        $leave->save();
        
        return redirect()->route('leaves.create')->with('success', 'Η άδεια εμφανίζεται πάλι στη λίστα.');
    }
}