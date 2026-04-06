<?php

namespace App\Http\Controllers\microapps;

use DateTime;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Teacher;
use App\Models\Microapp;
use Illuminate\Http\Request;
use App\Jobs\SubmitCorrectedLeave;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\microapps\TeacherLeaves;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
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
        /******************************************************************
         * Import αδειών από CSV αρχείο.
         * 
         * Η λογική:
         * 1. Διαβάζουμε το CSV και αγνοούμε Αναπληρωτές και Απουσίες
         * 2. Η βάση θα είναι mirror του CSV μετά το import με μεταφορά του πρωτοκόλλου και των αρχείων στη νέα άδεια αν απαιτείται (γραμμές που είναι στη βάση και δεν έχουν καμία σχέση με το αρχείο δε διαγράφονται
         * και αυτό γίνεται σκόπιμα ώστε να μπορούμε να κάνουμε hardcode περιπτώσεις)
         * 3. Ομαδοποιούμε τις γραμμές με key: afm | creator_entity_code | leave_protocol_number | leave_protocol_date
         * 4. Κάθε ομάδα επεξεργάζεται αναλόγως:
         *    - Κανονική περίπτωση → Βάση: Υποβλήθηκε | CSV: Εγκρίθηκε → Update την εγγραφή με το νέο state
         *    - Μόνο Ανάκληση → Βάση: Εγκρίθηκε με protocol | CSV: Ανακλήθηκε → Update state, κρατιέται το protocol
         *    - Ανάκληση + Διόρθωση → Βάση: Εγκρίθηκε με protocol | CSV: Ανακλήθηκε + Υποβλήθηκε → Παλιά γίνεται Ανακλήθηκε, νέα εγγραφή παίρνει το protocol
         * 4. Τα protocol_number, protocol_date και files_json ΔΕΝ αγγίζονται από το CSV
         ************************************************************************/
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
            ->with('success', "Ενημερώθηκαν {$result['updated']} άδειες");
    }

        protected function saveTeacherLeavesBatch($batch)
        { 
            DB::beginTransaction();
            try {
                // ΒΗΜΑ 1: Ομαδοποίηση ανά key
                $groupedLeaves = [];
                
                foreach ($batch as $leaveData) {
                    // Ignore Απουσία
                    if($leaveData['leave_type'] == 'Απουσία' || $leaveData['leave_state'] == '1-Δημιουργήθηκε') {
                        continue;
                    }

                    if($leaveData['employment_relation'] != 'Μόνιμος') {
                        continue;
                    }
                    
                    $key = $leaveData['afm'] . '|' . 
                        $leaveData['creator_entity_code'] . '|' . 
                        $leaveData['leave_protocol_number'] . '|' . 
                        $leaveData['leave_protocol_date'];
                    
                    if (!isset($groupedLeaves[$key])) {
                        $groupedLeaves[$key] = [];
                    }
                    
                    $groupedLeaves[$key][] = $leaveData;
                }
                
                // ΒΗΜΑ 2: Χειρισμός κάθε ομάδας
                foreach ($groupedLeaves as $key => $leaves) {
                    $this->processLeaveGroup($leaves);
                }
                
                DB::commit();
            
            } catch (Throwable $e) {
                DB::rollBack();
                Log::channel('throwable_db')->error('Batch save error: ' . $e->getMessage());
                throw $e;
            }
        }

    private function processCsvFile($fullPath)
    {
        // Pass 1: Μάθε τι υπάρχει
        $leaveGroups = $this->scanCsvForGroups($fullPath);
        
        // Pass 2: Επεξεργασία
        if (($handle = fopen($fullPath, 'r')) === false) {
            throw new \Exception('Αδυναμία ανάγνωσης αρχείου');
        }
        
        fgetcsv($handle, 0, ';'); // Skip header
        stream_filter_append($handle, 'convert.iconv.Windows-1253/UTF-8');
        
        $groupAccumulator = []; // Συλλέγει γραμμές ανά key
        $processedGroups = 0;
        $batchSize = 50;
        
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (empty(array_filter($row))) continue;
            
            $leaveData = $this->extractLeaveData($row);
            if (!$leaveData) continue;
            
            // Grouping key
            $key = "{$leaveData['afm']}|{$leaveData['creator_entity_code']}|" .
                "{$leaveData['leave_protocol_number']}|{$leaveData['leave_protocol_date']}";
            
            // Πρόσθεσε στον accumulator
            if (!isset($groupAccumulator[$key])) {
                $groupAccumulator[$key] = [];
            }
            $groupAccumulator[$key][] = $leaveData;
            
            // Τσέκαρε αν η ομάδα είναι πλήρης
            $expectedStates = $leaveGroups[$key]['states'] ?? [];
            $currentStates = array_map(fn($l) => $l['leave_state'], $groupAccumulator[$key]);
            
            if ($this->isGroupComplete($currentStates, $expectedStates)) {
                // Επεξεργασία ομάδας
                $this->processCompleteGroup($key, $groupAccumulator[$key], $leaveGroups[$key]);
                
                unset($groupAccumulator[$key]);
                $processedGroups++;
                
                // Garbage collection ανά batch
                if ($processedGroups % $batchSize == 0) {
                    gc_collect_cycles();
                }
            }
        }
        
        // Επεξεργασία τυχόν υπολειπόμενων
        foreach ($groupAccumulator as $key => $leaves) {
            $this->processCompleteGroup($key, $leaves, $leaveGroups[$key]);
        }
        
        fclose($handle);
        // Τρέχει τα jobs για τις διορθωμένες άδειες
        Log::channel('files')->info("Starting queue processing for corrected leaves...");
        Artisan::call('queue:work --stop-when-empty --tries=3 --timeout=180');
        Log::channel('files')->info("Queue processing completed");

        
        return ['updated' => $processedGroups];
    }

    private function isGroupComplete($currentStates, $expectedStates)
    {
        sort($currentStates);
        sort($expectedStates);
        return $currentStates === $expectedStates;
    }

    private function scanCsvForGroups($fullPath)

//     [
//     '123456|9999|1234|2025-01-15' => [
//         'states' => ['2-Υποβλήθηκε', '5-Ανακλήθηκε'],
//         'has_revoked' => true,
//         'has_active' => true
//     ]
// ]
    {
        if (($handle = fopen($fullPath, 'r')) === false) {
            throw new \Exception('Αδυναμία ανάγνωσης αρχείου');
        }
        
        fgetcsv($handle, 0, ';'); // Skip header
        stream_filter_append($handle, 'convert.iconv.Windows-1253/UTF-8');
        
        $groups = [];
        
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (empty(array_filter($row))) continue;
            
            // Διάβασε τα απαραίτητα πεδία
            $afm = $this->extractAfm($row[1] ?? '');
            $creatorCode = $this->extractCode($row[21] ?? '');
            $protocolNum = trim($row[18] ?? '');
            $protocolDate = $this->convertDate($row[19] ?? '');
            $state = trim($row[14] ?? '');
            
            if (!$afm || !$creatorCode) continue;
            
            // Grouping key
            $key = "{$afm}|{$creatorCode}|{$protocolNum}|{$protocolDate}";
            
            // Αρχικοποίηση group
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'states' => [],
                    'has_revoked' => false,
                    'has_active' => false,
                ];
            }
            
            // Συλλογή states
            $groups[$key]['states'][] = $state;
            
            if ($state === '5-Ανακλήθηκε') {
                $groups[$key]['has_revoked'] = true;
            }
            if (in_array($state, ['2-Υποβλήθηκε', '3-Εγκρίθηκε'])) {
                $groups[$key]['has_active'] = true;
            }
        }
        
        fclose($handle);
        return $groups;
    }

    // Helper methods
    private function extractAfm($raw)
    {
        return is_string($raw) ? substr(trim($raw), 2, -1) : trim($raw);
    }

    private function extractCode($raw)
    {
        return is_string($raw) ? substr(trim($raw), 2, -1) : trim($raw);
    }
    private function processCompleteGroup($key, $leavesInGroup, $groupInfo)
    {
        DB::beginTransaction();
        try {
            $keyParts = explode('|', $key);
            
            // Βρες αν υπάρχει άδεια στη βάση με protocol_number
            $dbLeaveWithProtocol = TeacherLeaves::where('afm', $keyParts[0])
                ->where('creator_entity_code', $keyParts[1])
                ->where('leave_protocol_number', $keyParts[2])
                ->where('leave_protocol_date', $keyParts[3])
                ->whereNotNull('protocol_number')
                ->first();
            
            // Προσδιορισμός σεναρίου
            $hasRevoked = $groupInfo['has_revoked'];
            $hasActive = $groupInfo['has_active'];
            
            if ($hasRevoked && $hasActive && $dbLeaveWithProtocol) {
                // ΣΕΝΑΡΙΟ Β: Ανάκληση + Διόρθωση
                $this->handleRevokedAndCorrected($leavesInGroup, $dbLeaveWithProtocol);
            } 
            elseif ($hasRevoked && !$hasActive && $dbLeaveWithProtocol) {
                // ΣΕΝΑΡΙΟ Α: Μόνο Ανάκληση
                $this->handleOnlyRevoked($leavesInGroup, $dbLeaveWithProtocol);
            } 
            else {
                // Κανονική περίπτωση
                $this->handleNormalCase($leavesInGroup);
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Group $key error: " . $e->getMessage());
            throw $e;
        }
    }
    
    // Cleaned up με το helper (λογική ίδια)
    private function handleOnlyRevoked($leavesInGroup, $dbLeaveWithProtocol)
    {
        // Idempotent check
        if ($dbLeaveWithProtocol->leave_state === '5-Ανακλήθηκε') {
            return;
        }

        // Update — protocol_number, protocol_date, files_json preserved αυτόματα
        $dbLeaveWithProtocol->fill($this->extractCsvFields($leavesInGroup[0]));
        $dbLeaveWithProtocol->submitted  = 0;
        $dbLeaveWithProtocol->is_visible = 1;
        $dbLeaveWithProtocol->save();
    }

    // Cleaned up με το helper (λογική ίδια)
    private function handleRevokedAndCorrected($leavesInGroup, $dbLeaveWithProtocol)
    {
        // Idempotent check
        $keyParts = [
            'afm'                  => $leavesInGroup[0]['afm'],
            'creator_entity_code'  => $leavesInGroup[0]['creator_entity_code'],
            'leave_protocol_number'=> $leavesInGroup[0]['leave_protocol_number'],
            'leave_protocol_date'  => $leavesInGroup[0]['leave_protocol_date'],
        ];

        $existingStates = TeacherLeaves::where($keyParts)->pluck('leave_state')->toArray();

        if (in_array('5-Ανακλήθηκε', $existingStates) &&
            (in_array('2-Υποβλήθηκε', $existingStates) || in_array('3-Εγκρίθηκε', $existingStates))
        ) {
            return; // Ήδη επεξεργασμένη
        }

        // Χωρισμός γραμμών
        $revokedData = null;
        $activeData  = null;
        foreach ($leavesInGroup as $leave) {
            if ($leave['leave_state'] === '5-Ανακλήθηκε') $revokedData = $leave;
            else                                            $activeData  = $leave;
        }

        // Κράτα protocol για μεταφορά
        $protocolData = [
            'protocol_number' => $dbLeaveWithProtocol->protocol_number,
            'protocol_date'   => $dbLeaveWithProtocol->protocol_date,
            'files_json'      => $dbLeaveWithProtocol->files_json,
        ];

        // ΒΗΜΑ 1: Existing → Ανακλήθηκε, αφαιρώ protocol
        $dbLeaveWithProtocol->fill($this->extractCsvFields($revokedData));
        $dbLeaveWithProtocol->protocol_number = null;
        $dbLeaveWithProtocol->protocol_date   = null;
        $dbLeaveWithProtocol->files_json      = null;
        $dbLeaveWithProtocol->submitted       = 0;
        $dbLeaveWithProtocol->is_visible      = 1;
        $dbLeaveWithProtocol->save();

        // ΒΗΜΑ 2: Create νέα με active state
        $newActiveLeave = $this->createLeave($activeData);

        // ΒΗΜΑ 3: Μεταφορά protocol
        $newActiveLeave->protocol_number = $protocolData['protocol_number'];
        $newActiveLeave->protocol_date   = $protocolData['protocol_date'];
        $newActiveLeave->submitted       = 1;
        $newActiveLeave->is_visible      = 1;

        // ΒΗΜΑ 4: Μετονομασία και μεταφορά αρχείων
        if ($protocolData['files_json']) {
            $newActiveLeave->files_json = $this->renameFiles(
                $dbLeaveWithProtocol->id,
                $newActiveLeave->id,
                $protocolData['files_json']
            );
        }

        $newActiveLeave->save();

        // Ενεργοποίηση του job για αυτόματη υποβολή της νέας άδειας
        dispatch(new SubmitCorrectedLeave($newActiveLeave->id));
        Log::channel('files')->info("Queued auto-submit for corrected leave {$newActiveLeave->id}");

    }

    // Cleaned up με το helper (λογική ίδια)
    private function createLeave($leaveData)
    {
        return TeacherLeaves::create(array_merge(
            [
                'afm'                  => $leaveData['afm'],
                'creator_entity_code'  => $leaveData['creator_entity_code'],
                'leave_protocol_number'=> $leaveData['leave_protocol_number'],
                'leave_protocol_date'  => $leaveData['leave_protocol_date'],
            ],
            $this->extractCsvFields($leaveData)
        ));
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

    // Helper: τα πεδία που έρχονται από το CSV
    // ΝΟΤ: protocol_number, protocol_date, files_json, submitted, is_visible
    private function extractCsvFields($leaveData)
    {
        return [
            'leave_state'                => $leaveData['leave_state'],
            'am'                         => $leaveData['am'],
            'sex'                        => $leaveData['sex'],
            'surname'                    => $leaveData['surname'],
            'name'                       => $leaveData['name'],
            'fathers_name'               => $leaveData['fathers_name'],
            'specialty_code'             => $leaveData['specialty_code'],
            'specialty'                  => $leaveData['specialty'],
            'directorate'                => $leaveData['directorate'],
            'employment_relation'        => $leaveData['employment_relation'],
            'leave_type'                 => $leaveData['leave_type'],
            'leave_start_date'           => $leaveData['leave_start_date'],
            'leave_days'                 => $leaveData['leave_days'],
            'leave_description'          => $leaveData['leave_description'],
            'creator_entity_name'        => $leaveData['creator_entity_name'],
            'creation_date'              => $leaveData['creation_date'],
            'submission_date'            => $leaveData['submission_date'],
            'approved_days'              => $leaveData['approved_days'],
            'approved_months'            => $leaveData['approved_months'],
            'approved_years'             => $leaveData['approved_years'],
            'approved_protocol_number'   => $leaveData['approved_protocol_number'],
            'approved_protocol_date'     => $leaveData['approved_protocol_date'],
            'approved_description'       => $leaveData['approved_description'],
            'revoke_description'         => $leaveData['revoke_description'],
            'approving_authority_code'   => $leaveData['approving_authority_code'],
            'approving_authority_name'   => $leaveData['approving_authority_name'],
            'last_change_date'           => $leaveData['last_change_date'],
        ];
    }

    // CORRECTED: Αντικαθιστά την παλιά handleNormalCase
    private function handleNormalCase($leavesInGroup)
    {
        $baseKeys = [
            'afm'                  => $leavesInGroup[0]['afm'],
            'creator_entity_code'  => $leavesInGroup[0]['creator_entity_code'],
            'leave_protocol_number'=> $leavesInGroup[0]['leave_protocol_number'],
            'leave_protocol_date'  => $leavesInGroup[0]['leave_protocol_date'],
        ];

        // States που έρχονται από το CSV για το group
        $csvStates = array_map(fn($l) => $l['leave_state'], $leavesInGroup);

        // Όλα τα υπάρχοντα records στη βάση για το group
        $dbLeaves = TeacherLeaves::where($baseKeys)->get();

        foreach ($leavesInGroup as $leaveData) {
            $csvFields = $this->extractCsvFields($leaveData);

            // ΒΗΜΑ 1: Ψάχνω exact match (ίδιο state) → idempotent update
            $target = $dbLeaves->firstWhere('leave_state', $leaveData['leave_state']);

            if (!$target) {
                // ΒΗΜΑ 2: Ψάχνω "orphan" — DB row με state που ΔΕΝ υπάρχει πια στο CSV
                // Αυτό είναι το state transition case (π.χ. Υποβλήθηκε → Εγκρίθηκε)
                $target = $dbLeaves->first(fn($row) => !in_array($row->leave_state, $csvStates));

                if ($target) {
                    // Αφαιρώ από collection ώστε να μην re-match στην επόμενη iteration
                    $dbLeaves = $dbLeaves->reject(fn($r) => $r->id === $target->id);
                }
            }

            if ($target) {
                // Update — protocol_number, protocol_date, files_json ΝΟΤ αγγίζονται
                $target->fill($csvFields);
                $target->save();
            } else {
                // Νέα εγγραφή (πρώτη φορά import)
                TeacherLeaves::create(array_merge($baseKeys, $csvFields));
            }
        }
    }
    private function hasMultipleStates($states)
    {
        // Έχει πολλαπλά states (π.χ. Ανακλήθηκε + Υποβλήθηκε);
        return count(array_unique($states)) > 1;
    }

    
    private function simpleUpdateOrCreate($leaveData)
    {
        $keys = [
            'afm' => $leaveData['afm'],
            'creator_entity_code' => $leaveData['creator_entity_code'],
            'leave_protocol_number' => $leaveData['leave_protocol_number'],
            'leave_protocol_date' => $leaveData['leave_protocol_date'],
            'leave_state' => $leaveData['leave_state'],
        ];
        
        $updateData = $leaveData;
        unset($updateData['afm'], $updateData['creator_entity_code'], 
            $updateData['leave_protocol_number'], $updateData['leave_protocol_date'], 
            $updateData['leave_state']);
        
        return TeacherLeaves::updateOrCreate($keys, $updateData);
    }

    private function transferToNewLeave($newLeave, $protocolData)
    {
        // 1. Μεταφορά protocol
        $newLeave->protocol_number = $protocolData['protocol_number'];
        $newLeave->protocol_date = $protocolData['protocol_date'];
        $newLeave->submitted = 0; // Ξεκλείδωτη
        $newLeave->is_visible = 1; // Ορατή
        
        // 2. Μεταφορά και μετονομασία αρχείων
        if ($protocolData['files_json']) {
            $oldFiles = json_decode($protocolData['files_json'], true);
            $newFiles = [];
            
            foreach ($oldFiles as $oldServerName => $greekName) {
                // Παλιό: 34_1.pdf -> Νέο: 56_1.pdf
                $parts = explode('_', $oldServerName);
                if (count($parts) < 2) continue; // Skip invalid filenames
                
                $fileNumber = $parts[1]; // '1.pdf'
                $newServerName = $newLeave->id . '_' . $fileNumber;
                
                // Μετονομασία φυσικού αρχείου
                $oldPath = storage_path("app/teacher_leaves/{$oldServerName}");
                $newPath = storage_path("app/teacher_leaves/{$newServerName}");
                
                if (file_exists($oldPath)) {
                    rename($oldPath, $newPath);
                }
                
                $newFiles[$newServerName] = $greekName;
            }
            
            $newLeave->files_json = json_encode($newFiles);
        }
        
        $newLeave->save();
    }

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
        $afm = is_string($rawAfm) ? substr($rawAfm, 2, -1) : $rawAfm;
        
        // Ignore non-Μόνιμος
        $employmentRelation = $getValue(13);
        if ($employmentRelation !== 'Μόνιμος') {
            return null;
        }

        // Ignore Απουσία
        $leaveType = $getValue(15);
        if ($leaveType === 'Απουσία') {
            return null;
        }
        // Validate teacher exists
        if (!$afm || !Teacher::where('afm', $afm)->exists()) {
            return null;
        }
        
        // Extract creator entity code
        $creatorCode = $getValue(21);
        $creatorEntityCode = is_string($creatorCode) ? substr($creatorCode, 2, -1) : $creatorCode;
        
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

    private function updateOrCreateLeave($leaveData)
    {
        // ΠΡΩΤΑ: Ψάξε αν υπάρχει παλιά εγγραφή με protocol
        $oldLeaveWithProtocol = $this->findOldLeaveWithProtocol($leaveData);
        
        // Δημιουργία/ενημέρωση
        $keys = [
            'afm' => $leaveData['afm'],
            'creator_entity_code' => $leaveData['creator_entity_code'],
            'leave_protocol_number' => $leaveData['leave_protocol_number'],
            'leave_protocol_date' => $leaveData['leave_protocol_date'],
            'leave_state' => $leaveData['leave_state'],
        ];
        
        $updateData = $leaveData;
        unset($updateData['afm'], $updateData['creator_entity_code'], 
            $updateData['leave_protocol_number'], $updateData['leave_protocol_date'], 
            $updateData['leave_state']);
        
        $newLeave = TeacherLeaves::updateOrCreate($keys, $updateData);
        
        // ΑΝ βρήκαμε παλιά με protocol, μεταφέρουμε
        if ($oldLeaveWithProtocol) {
            $this->transferProtocolAndFiles($oldLeaveWithProtocol, $newLeave);
        }
    }

    private function findOldLeaveWithProtocol($leaveData)
    {
        // Ψάξε για άδεια με ίδια keys ΑΛΛΑ διαφορετικό leave_state
        // ΚΑΙ έχει protocol_number (δηλ. είχε υποβληθεί)
        return TeacherLeaves::where('afm', $leaveData['afm'])
            ->where('creator_entity_code', $leaveData['creator_entity_code'])
            ->where('leave_protocol_number', $leaveData['leave_protocol_number'])
            ->where('leave_protocol_date', $leaveData['leave_protocol_date'])
            ->where('leave_state', '!=', $leaveData['leave_state']) // ✨ Διαφορετικό state
            ->whereNotNull('protocol_number') // ✨ Έχει πρωτόκολλο
            ->first();
    }

    private function transferProtocolAndFiles($oldLeave, $newLeave)
    {
        // 1. Μεταφορά protocol
        $newLeave->protocol_number = $oldLeave->protocol_number;
        $newLeave->protocol_date = $oldLeave->protocol_date;
        
        // 2. Μεταφορά και μετονομασία αρχείων
        if ($oldLeave->files_json) {
            $oldFiles = json_decode($oldLeave->files_json, true);
            $newFiles = [];
            
            foreach ($oldFiles as $oldServerName => $greekName) {
                // Παλιό: 34_1.pdf -> Νέο: 56_1.pdf
                $parts = explode('_', $oldServerName); // ['34', '1.pdf']
                $fileNumber = $parts[1]; // '1.pdf'
                $newServerName = $newLeave->id . '_' . $fileNumber; // '56_1.pdf'
                
                // Μετονομασία φυσικού αρχείου
                $oldPath = storage_path("app/teacher_leaves/{$oldServerName}");
                $newPath = storage_path("app/teacher_leaves/{$newServerName}");
                
                if (file_exists($oldPath)) {
                    rename($oldPath, $newPath);
                }
                
                // Προσθήκη στο νέο JSON
                $newFiles[$newServerName] = $greekName;
            }
            
            $newLeave->files_json = json_encode($newFiles);
        }
        
        $newLeave->save();
        
        // 3. Καθαρισμός παλιάς εγγραφής (αφαίρεση protocol)
        $oldLeave->protocol_number = null;
        $oldLeave->protocol_date = null;
        $oldLeave->files_json = null;
        $oldLeave->save();
    }
    public function convertDate($csvDate){
        
        if (empty($csvDate)) {
            //dd('empty date');
            return null;
        }
        
        $date = DateTime::createFromFormat('d/m/Y', $csvDate);
        
        if ($date) {
            return $date->format('Y-m-d');
        } else {
            return null;
        }
    }

    private function trackRevokedAndCorrectedLeaves($leaveData){ 
        // Ψάξε αν υπάρχει άδεια με τα ίδια στοιχεία στη βάση
        $storedLeave = TeacherLeaves::where('afm', $leaveData['afm'])
            ->where('creator_entity_code', $leaveData['creator_entity_code'])
            ->where('leave_protocol_number', $leaveData['leave_protocol_number'])
            ->where('leave_protocol_date', $leaveData['leave_protocol_date'])
            ->first();
        if(!$storedLeave){
            return; // Αν δεν υπάρχει άδεια με αυτά τα στοιχεία, προχώρησε. Θα γίνει νέα εγγραφή
        }

        if($storedLeave->protocol_number == null){
            return; // Αν δεν έχει πάρει πρωτόκολλο μην ασχοληθείς. Θα γίνει UpdateOrCreate κανονικά
        }

        // Συνεχιζουμε με Άδειες που υπάρχουν στη βάση και έχουν πρωτόκολλο
        // Κατάσταση: Υποβλήθηκε ή Εγκρίθηκε + Πρωτόκολλο
        // Αν στο αρχείο έρχεται Ανακλήθηκε και στην βάση είναι Εγκρίθηκε ή Υποβλήθηκε
        if($leaveData['leave_state'] == '5-Ανακλήθηκε' && ($storedLeave->leave_state == '2-Υποβλήθηκε' || $storedLeave->leave_state == '3-Εγκρίθηκε')){
        /// Βάλε ένα flag ότι η άδεια αυτή είναι ανακλημένη που υπάρχει στη βάση 
            $leaveData['revoked'] = 1;
            $leaveData['protocol_number'] = $storedLeave->protocol_number;
            $leaveData['protocol_date'] = $storedLeave->protocol_date;
            $leaveData['submitted'] = $storedLeave->submitted;
            $leaveData['is_visible'] = $storedLeave->is_visible;
        }

        if(($leaveData['leave_state'] == '2-Υποβλήθηκε' || $leaveData['leave_state'] == '3-Εγκρίθηκε') && $leaveData['revoked'] == 1){
            // Αν στο αρχείο έρχεται Υποβλήθηκε ή Εγκρίθηκε και στην βάση είναι Ανακλήθηκε με πρωτόκολλο
            // ΚΑΝΕ UPDATE ΜΕ ΤΑ ΣΤΟΙΧΕΙΑ ΠΟΥ ΕΡΧΟΝΤΑΙ ΑΠΟ ΤΟ ΑΡΧΕΙΟ
        }
        
        
    }
    
    public function upload_files(Request $request, TeacherLeaves $teacher_leave){
        // Method for the school to upload files
        if(Auth::guard('school')->user()->code != $teacher_leave->creator_entity_code){
            return back()->with('failure', 'Δεν έχετε δικαίωμα επεξεργασίας αυτής της άδειας.');
        }
        $request->validate([ //Έλεγξε τον τύπο των αρχείων και το μέγεθός τους
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png',
            // 'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        $files = $request->file('files');
        
        $fileNames = [];
        //Βρες πόσα αρχεία έχει ήδη ανεβάσει
        if($teacher_leave->files_json){ // Αν έχει ανεβάσει ήδη, βρες τον αριθμό του τελευταίου αρχείου από το όνομά του
            $fileNames = json_decode($teacher_leave->files_json, true);
            end($fileNames);
            $lastServerFileName = key($fileNames);
            $underScorePosition = strpos($lastServerFileName, '_');// βρες τον αριθμό που περιλαμβάνεται στο όνομα του τελευταίου αρχείου μετά το _
            $filesCount = substr($lastServerFileName, $underScorePosition + 1, strpos($lastServerFileName, '.') - $underScorePosition -1);
        } else { //Αν δεν έχει ανεβάσει ακόμη αρχεία, βάλε τον αριθμό 0
            $filesCount = 0;
        }
        $lastFileNumber = $filesCount; // κράτα τον αριθμό του τελευταίου αρχείου για την περίπτωση που θα ανεβάσει επιπλέον αρχεία
        $directory = "teacher_leaves";
        foreach($files as $file){ // Για κάθε αρχείο που ανεβάζεις
            $filesCount++;
            $serverFileName = $teacher_leave->id."_".$filesCount.".".$file->getClientOriginalExtension();
            $fileNames[$serverFileName] = $file->getClientOriginalName();//πρόσθεσε στον πίνακα το όνομα του αρχείου που θα ανεβάσεις
            $fileHandler = new FilesController();
            $uploaded = $fileHandler->upload_file($directory, $file, 'local', $serverFileName);
            
            if($uploaded->getStatusCode() == 500){
                $teacherAfm = $teacherAfm ?? 'Didnt find Ghostscript';
                Log::channel('files')->error($teacherAfm." Files failed to upload");
                return back()->with('failure', 'Αποτυχία στην υποβολή των αρχείων. Δοκιμάστε ξανά');
            }
        }
        $teacher_leave->files_json = json_encode($fileNames);
        try{
            $teacher_leave->save();
        } catch(\Exception $e) {
            //dd($e->getMessage());
            Log::channel('files')->error($teacher_leave->id." Teacher Leave Files failed to update database field files_json");
            return back()->with('failure', 'Αποτυχία ενημέρωσης της βάσης δεδομένων με τα ονόματα των αρχείων. Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($teacher_leave->id." Teacher Leave Files successfully uploaded");
        // dd($teacher_leave, $request->all());
        return redirect(url('leaves/create'))->with('success','Τα αρχεία ανέβηκαν.');//response()->json(['message' => 'Τα αρχεία ανέβηκαν επιτυχώς (fake)!']);
    }

    public function submit(TeacherLeaves $leave){
        // Method that sends leave to protocol
        if(Auth::guard('school')->user()->code != $leave->creator_entity_code){
            return back()->with('failure', 'Δεν έχετε δικαίωμα υποβολής αυτής της άδειας.');
        }
        //Στείλε την αίτηση στο πρωτόκολλο
        try{
            $protocol_message = $this->sendLeaveToProtocol($leave);
            //dd('after sendLeaveToProtocol');
            if($protocol_message['success'] == false){
                return back()->with('failure', 'Aπέτυχε η αποστολή στο πρωτόκολλο με μήνυμα: ' . $protocol_message['message'] . ' Παρακαλούμε για την αποστολή mail στο it@dipe.ach.sch.gr.');
            }
        } catch(\Exception $e) {
            print($e->getMessage());
            print_r($e->getMessage());
            //dd('stop');
            return back()->with('failure', 'Αποτυχία αποστολής αίτησης στο Πρωτόκολλο της Διεύθυνσης. Παρακαλούμε επικοινωνήστε με το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr.');
        }
        try{
            $protocol_message = explode(" - ", $protocol_message['message']);
            $leave->protocol_number = $protocol_message[0];
            //$leave->protocol_date = $protocol_message[1];
            $leave->protocol_date = Carbon::createFromFormat('d/m/Y', $protocol_message[1])->format('Y-m-d');
            $leave->save();
        } catch(\Exception $e) {
            //dd($e->getMessage());
            return back()->with('failure', 'Η άδεια πρωτοκολλήθηκε με επιτυχία στο Πρωτόκολλο  της Διεύθυνσης αλλά απέτυχε η αποθήκευση του αριθμού πρωτοκόλλου. Παρακαλούμε επικοινωνήστε άμεσα με το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr.');
        }
        //Οριστικοποίησε την αίτηση - criteria_submitted = 1
        try{
            $leave->submitted = 1;
            $leave->save();
        } catch(\Exception $e) {
            //dd($e->getMessage());
            return back()->with('failure', 'Η άδεια πρωτοκολλήθηκε με επιτυχία στο Πρωτόκολλο  της Διεύθυνσης αλλά απέτυχε η οριστικοποίησή της. Παρακαλούμε επικοινωνήστε άμεσα με το Τμήμα Πληροφορικής στο  it@dipe.ach.sch.gr.');
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
                $leave->files_json = null; // Αν δεν υπάρχουν άλλα αρχεία, βάλε το πεδίο σε null
            }
            else{
                $leave->files_json = json_encode($files);
            }
            $leave->update();
        } catch(\Exception $e) {
            //dd($e->getMessage());
            Log::channel('files')->info("Teacher Leave File DatabaseFileName: $databaseFileName, ServerFileName: $serverFileName failed to delete");
            return back()->with('failure', 'Αποτυχία διαγραφής αρχείου.');
        }
        Log::channel('files')->info("Teacher Leave File DatabaseFileName: $databaseFileName, ServerFileName: $serverFileName deleted succesfully.");
        return back()->with('success', 'Επιτυχής διαγραφή αρχείου: "'.$databaseFileName.'"');
    }

    public function sendLeaveToProtocol(TeacherLeaves $leave){
        
        // Find leave type from lookup table
        $leaveType = \App\Models\LeaveType::where('description', $leave->leave_type)->first();
        //dd($leave->leave_protocol_date);
        $leaveProtocolDate = $leave->leave_protocol_date->format('d/m/Y');;
        
        $schoolProtocol = $leave->leave_protocol_number .'-'. $leaveProtocolDate;
        //dd('reached 1');
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
        
        //return "5184 - 2024/08/06";
        //Log::channel('files')->info("before request");
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
        
        //Log::channel('files')->info("After request");
        // Get the response body
        $status = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        Log::channel('files')->info("Leave ID: ".$leave->id." - Protocol Response Status: $status - Body: $body");
        //dd($status, $body);
        if($status != 200){
            return ['success' => false, 'message' => 'Protocol Response Status: ' . $status];
        } else {
            //dd($body);
            return ['success' => true, 'message' => $body];
        }
    }

    public function getTeacherLeaves(TeacherLeaves $teacher_leave){
        $leaves = TeacherLeaves::where('afm', $teacher_leave->afm)->
                                where('leave_state', '3-Εγκρίθηκε')->
                                where('leave_type', $teacher_leave->leave_type)->get();
        // check if teacher is director
        $teacher = Teacher::where('afm', $teacher_leave->afm)->first();
        $isDirector = $teacher->isDirector();
        // return response()->json([
        //     'isDirector' => $teacher->isDirector(),
        //     'leaves' => $leaves,
        // ]);
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

    /**
     * Εμφάνιση αποκρυμμένων αδειών για το σχολείο
     */
    public function showHidden()
    {
        $school = Auth::guard('school')->user();
        $microapp = Microapp::where('url', '/leaves')->first();
        
        // Φέρνουμε τις αποκρυμμένες άδειες του σχολείου
        // (εκτός από τις ανακληθείσες)
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

    /**
     * Απόκρυψη άδειας
     */
    public function hideLeave($teacher_leave)
    {
        $school = Auth::guard('school')->user();
        $leave = TeacherLeaves::findOrFail($teacher_leave);
        
        // Έλεγχος ότι η άδεια ανήκει στο σχολείο
        // Προσάρμοσε το πεδίο ανάλογα με το πώς συνδέεται η άδεια με το σχολείο
        if ($leave->creator_entity_code !== $school->code) {
            abort(403, 'Unauthorized action.');
        }
        
        // Έλεγχος: Μπορεί να αποκρυφθεί μόνο αν:
        // 1. submitted = 0 ΚΑΙ protocol_number = null
        // 2. submitted = 1 ΚΑΙ protocol_number υπάρχει
        $canHide = false;
        
        if (!$leave->submitted && !$leave->protocol_number) {
            $canHide = true;
        } elseif ($leave->submitted && $leave->protocol_number) {
            $canHide = true;
        }
        
        if (!$canHide) {
            return redirect()->back()->with('error', 'Δεν μπορείτε να αποκρύψετε αυτή την άδεια σε αυτή την κατάσταση.');
        }
        
        // Έλεγχος ότι δεν είναι ανακληθείσα
        if ($leave->leave_state === 'Ανακλήθηκε') {
            return redirect()->back()->with('error', 'Δεν μπορείτε να αποκρύψετε μια ανακληθείσα άδεια.');
        }
        
        // Ενημέρωση της άδειας
        $leave->is_visible = 0;
        $leave->save();
        
        return redirect()->back()->with('success', 'Η άδεια αποκρύφθηκε επιτυχώς.');
    }

    /**
     * Επαναφορά ορατότητας άδειας
     */
    public function unhideLeave($teacher_leave)
    {
        $school = Auth::guard('school')->user();
        $leave = TeacherLeaves::findOrFail($teacher_leave);
        
        // Έλεγχος ότι η άδεια ανήκει στο σχολείο
        if ($leave->creator_entity_code !== $school->code) {
            abort(403, 'Unauthorized action.');
        }
        
        // Επαναφορά της ορατότητας
        $leave->is_visible = 1;
        $leave->save();
        
        return redirect()->route('leaves.create')->with('success', 'Η άδεια εμφανίζεται πάλι στη λίστα.');
    }

    private function identifyRevokedLeaves($fullPath)
    {
        $trackedLeaves = [];
        
        if (($handle = fopen($fullPath, 'r')) === false) {
            return [];
        }
        
        fgetcsv($handle, 0, ';');
        stream_filter_append($handle, 'convert.iconv.Windows-1253/UTF-8');
        
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (empty(array_filter($row))) continue;
            
            $rawAfm = isset($row[1]) ? trim($row[1]) : '';
            $teacherAfm = is_string($rawAfm) ? substr($rawAfm, 2, -1) : $rawAfm;
            
            $creatorEntityCode = isset($row[21]) ? trim($row[21]) : '';
            $creatorEntityCode = is_string($creatorEntityCode) ? substr($creatorEntityCode, 2, -1) : $creatorEntityCode;
            
            $leaveProtocolNumber = isset($row[18]) ? trim($row[18]) : '';
            $leaveProtocolDate = $this->convertDate(isset($row[19]) ? trim($row[19]) : '');
            $leaveState = isset($row[14]) ? trim($row[14]) : '';
            
            $key = "{$teacherAfm}|{$creatorEntityCode}|{$leaveProtocolNumber}|{$leaveProtocolDate}";
            
            if (!isset($trackedLeaves[$key])) {
                $storedLeave = TeacherLeaves::where('afm', $teacherAfm)
                    ->where('creator_entity_code', $creatorEntityCode)
                    ->where('leave_protocol_number', $leaveProtocolNumber)
                    ->where('leave_protocol_date', $leaveProtocolDate)
                    ->whereNotNull('protocol_number')
                    ->first();
                
                if (!$storedLeave) continue;
                
                $trackedLeaves[$key] = [
                    'db_state' => $storedLeave->leave_state,
                    'csv_states' => [$leaveState], // ✨ Κρατάμε ΟΛΑ
                    'protocol_number' => $storedLeave->protocol_number,
                    'protocol_date' => $storedLeave->protocol_date,
                    'submitted' => $storedLeave->submitted,
                    'is_visible' => $storedLeave->is_visible,
                ];
            } else {
                // ✨ Προσθέτουμε το νέο state
                $trackedLeaves[$key]['csv_states'][] = $leaveState;
            }
        }
        
        fclose($handle);
        
        // Ανάλυση
        $revokedLeaves = [];
        
        foreach ($trackedLeaves as $key => $data) {
            $dbState = $data['db_state'];
            $csvStates = $data['csv_states'];
            
            $hasRevoked = in_array('5-Ανακλήθηκε', $csvStates);
            $hasActive = in_array('2-Υποβλήθηκε', $csvStates) || in_array('3-Εγκρίθηκε', $csvStates);
            
            // ΠΕΡΙΠΤΩΣΗ 2: Και τα δύο
            if ($hasRevoked && $hasActive && in_array($dbState, ['2-Υποβλήθηκε', '3-Εγκρίθηκε'])) {
                $revokedLeaves[$key] = [
                    'revoked_and_resubmitted' => 1,
                    'protocol_number' => $data['protocol_number'],
                    'protocol_date' => $data['protocol_date'],
                    'submitted' => $data['submitted'],
                    'is_visible' => $data['is_visible'],
                ];
            }
            // ΠΕΡΙΠΤΩΣΗ 1: Μόνο revoked
            elseif ($hasRevoked && !$hasActive && in_array($dbState, ['2-Υποβλήθηκε', '3-Εγκρίθηκε'])) {
                $revokedLeaves[$key] = [
                    'revoked' => 1,
                    'protocol_number' => $data['protocol_number'],
                    'protocol_date' => $data['protocol_date'],
                    'submitted' => $data['submitted'],
                    'is_visible' => $data['is_visible'],
                ];
            }
            // ΠΕΡΙΠΤΩΣΗ 3: Corrected
            elseif ($hasActive && $dbState == '5-Ανακλήθηκε') {
                $revokedLeaves[$key] = [
                    'corrected' => 1,
                    'protocol_number' => $data['protocol_number'],
                    'protocol_date' => $data['protocol_date'],
                ];
            }
        }
        
        return $revokedLeaves;
    }
}
