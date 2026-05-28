<?php

namespace App\Http\Controllers;

use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\FilesController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ResetMicroappsController extends Controller
{
    // =========================================================
    //  VIEW
    // =========================================================

    public function reset_microapp(Request $request, Microapp $microapp)
    {
        return view('reset_microapp', ['microapp' => $microapp]);
    }

    // =========================================================
    //  ΒΗΜΑ 1 – Λήψη αρχείων (zip)
    // =========================================================

    public function download_files(Request $request, Microapp $microapp)
    {
        $directory = $this->getDirectory($microapp);
        $handler   = new FilesController;
        $response  = $handler->download_directory_as_zip($directory);

        if ($response->getStatusCode() === 500) {
            $error = json_decode($response->getContent(), true)['error'] ?? 'Άγνωστο σφάλμα';
            Log::channel('files')->error(Auth::user()->username . " failed to download $directory files: $error");
            return back()->with('failure', $error . '. Επικοινωνήστε με τον διαχειριστή.');
        }

        Log::channel('files')->info(Auth::user()->username . " downloaded $directory files");

        while (ob_get_level()) ob_end_clean();

        return $response;
    }

    // =========================================================
    //  ΒΗΜΑ 2 – Λήψη Excel από τη ΒΔ
    // =========================================================

    public function download_excel(Request $request, Microapp $microapp)
    {
        while (ob_get_level()) ob_end_clean();

        try {
            $config = $this->getMicroappConfig($microapp);

            if (!$config) {
                return response()->json(['error' => 'Δεν βρέθηκε ρύθμιση για τη μικροεφαρμογή.'], 404);
            }

            $spreadsheet = new Spreadsheet();
            $isFirstSheet = true;

            foreach ($config['models'] as $modelClass => $sheetLabel) {
                if (!class_exists($modelClass)) {
                    Log::channel('throwable_db')->warning("Model $modelClass not found, skipping.");
                    continue;
                }

                $data    = $modelClass::all();
                $columns = Schema::getColumnListing((new $modelClass)->getTable());

                if ($isFirstSheet) {
                    $sheet = $spreadsheet->getActiveSheet();
                    $isFirstSheet = false;
                } else {
                    $sheet = $spreadsheet->createSheet();
                }

                $sheet->setTitle($sheetLabel);

                // Header
                foreach ($columns as $colIndex => $column) {
                    $sheet->setCellValueByColumnAndRow($colIndex + 1, 1, $column);
                }

                // Data
                foreach ($data as $rowIndex => $row) {
                    $rowArray = $row->toArray();
                    foreach ($columns as $colIndex => $column) {
                        $sheet->setCellValueByColumnAndRow(
                            $colIndex + 1,
                            $rowIndex + 2,
                            $rowArray[$column] ?? ''
                        );
                    }
                }
            }

            $filename = 'export_' . $this->getDirectory($microapp) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            $writer   = new Xlsx($spreadsheet);

            return response()->streamDownload(
                fn() => $writer->save('php://output'),
                $filename,
                [
                    'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Cache-Control' => 'no-store, no-cache, must-revalidate',
                    'Pragma'        => 'public',
                ]
            );

        } catch (\Throwable $e) {
            Log::error('Excel export failed: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['error' => 'Αποτυχία εξαγωγής Excel.'], 500);
        }
    }

    // =========================================================
    //  ΒΗΜΑ 3 – Διαγραφή αρχείων
    // =========================================================

    public function delete_files(Request $request, Microapp $microapp)
    {
        $username  = $this->getUsername();
        $directory = $this->getDirectory($microapp);
        $handler   = new FilesController;

        $response = $handler->delete_directory($directory, 'local');

        if ($response->getStatusCode() === 500) {
            Log::channel('files')->error("$username failed to delete directory $directory");
            return redirect()->route('reset.view', $microapp)
                         ->with('failure', "Σφάλμα κατά τη διαγραφή αρχείων του $directory.");
        }

        Log::channel('files')->info("$username deleted directory $directory");
        Log::channel('user_memorable_actions')->info("$username delete_files " . $microapp->url);

        // Αναδημιουργία κενού φακέλου
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        return redirect()->route('reset.view', $microapp)
                     ->with('success', "Τα αρχεία της μικροεφαρμογής {$microapp->name} διαγράφηκαν.");
    }

    // =========================================================
    //  ΒΗΜΑ 4 – Αρχικοποίηση ΒΔ
    // =========================================================

    public function reset_db(Request $request, Microapp $microapp)
    {
        $username  = $this->getUsername();
        $directory = $this->getDirectory($microapp);
        $config    = $this->getMicroappConfig($microapp);

        if (!$config) {
            Log::channel('throwable_db')->error("$username: no config found for {$microapp->url}");
            return redirect()->route('reset.view', $microapp)
                         ->with('failure', 'Δεν βρέθηκε ρύθμιση για τη μικροεφαρμογή.');
        }

        $tables = $config['tables'];

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            try {
                DB::table($table)->truncate();
                Log::channel('throwable_db')->info("$username truncated table $table");
            } catch (\Exception $e) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                Log::channel('throwable_db')->error("$username failed to truncate $table: " . $e->getMessage());
                return redirect()->route('reset.view', $microapp)
                     ->with('failure', "Σφάλμα κατά τη διαγραφή του πίνακα $table.");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Log::channel('user_memorable_actions')->info("$username reset_db " . $microapp->url);

        return back()->with('success', "Η βάση δεδομένων της μικροεφαρμογής {$microapp->name} αρχικοποιήθηκε.");
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    /**
     * Επιστρέφει το όνομα του directory (χωρίς /) για μια μικροεφαρμογή.
     */
    private function getDirectory(Microapp $microapp): string
    {
        return ltrim($microapp->url, '/');
    }

    /**
     * Επιστρέφει το username του συνδεδεμένου χρήστη.
     */
    private function getUsername(): string
    {
        return Auth::check() ? Auth::user()->username : 'API';
    }

    /**
     * Κεντρική ρύθμιση ανά μικροεφαρμογή.
     *
     * tables  → πίνακες προς truncate (σειρά: child πρώτα λόγω FK)
     * models  → [ModelClass => 'Ετικέτα sheet'] για το Excel export
     *
     * Για κάθε νέα μικροεφαρμογή προσθέτεις ένα case εδώ.
     */
    private function getMicroappConfig(Microapp $microapp): ?array
    {
        $directory = $this->getDirectory($microapp);

        $configs = [

            'enrollments' => [
                'tables' => [
                    'enrollments_classes',  // child – πρώτα
                    'enrollments',          // parent
                ],
                'models' => [
                    \App\Models\microapps\Enrollment::class          => 'Εγγραφές',
                    \App\Models\microapps\EnrollmentsClasses::class  => 'Τμήματα',
                ],
            ],

            'outings' => [
                'tables' => ['outings_sections', 'outings'],
                'models' => [
                    \App\Models\microapps\Outing::class         => 'Εκδρομές',
                    \App\Models\microapps\OutingSection::class  => 'Τμήματα',
                ],
            ],

            'building_problems' => [
                'tables' => ['building_problems'],
                'models' => [
                    \App\Models\microapps\BuildingProblems::class => 'Κτιριολογικά Προβλήματα',
                ],
            ],

            // ➕ Πρόσθεσε εδώ τις υπόλοιπες μικροεφαρμογές...

        ];

        return $configs[$directory] ?? null;
    }
}