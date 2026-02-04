<?php

namespace App\Http\Controllers\microapps;

use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\microapps\WorkPlan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Style;

class WorkPlanController extends Controller
{
    private $microapp;

    public function __construct(){
        // $this->middleware('auth')->only(['index']);
        $this->middleware('isConsultant')->only(['create']);
        $this->microapp = Microapp::where('url', '/work_planning')->first();
    }

    public function index(){
        if(Auth::guard('consultant')->check()){
            
            $consultant = Auth::guard('consultant')->user();
            if($consultant->isSupervisor())
                return view('microapps.work_planning.index', ['appname' => 'work_planning']);
            else
                abort(403, 'Unauthorized action.');
        }
        if(Auth::check()){
            
            return view('microapps.work_planning.index', ['appname' => 'work_planning']);
        }
        
        abort(403, 'Unauthorized action.');
    }

    public function create(){
        return view('microapps.work_planning.create', ['appname' => 'work_planning']);
    }

    public function saveWorkPlan($yearWeek, Request $request){
        if($this->microapp->accepts){
            $programm = json_encode(array(
                    'mon'=> $request->all()['mon'],
                    'tue'=> $request->all()['tue'],
                    'wed'=> $request->all()['wed'],
                    'thu'=> $request->all()['thu'],
                    'fri'=> $request->all()['fri'])
            );
            // dd($programm);
            WorkPlan::updateOrCreate([
                'yearWeek' => $yearWeek,
                'consultant_id' => Auth::guard('consultant')->id()
            ],
            [
                'comments' => $request->all()['comments'],
                'programm' => $programm,
            ]);

            return back()->with('success', 'Ενημερώθηκε το πρόγραμμα');
        }
        else{
            return back()->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
        }
    }

    private function weekHasWorkdayInMonth($year, $week, $month, $targetYear)
    {
        // Η μέθοδος δημιουργήθηκε γιατί προέκυπτε πρόβλημα στο Δεκέμβριο όπου η τελευταία εβδομάδα
        // του έτους μπορεί να είναι η 01 της επόμενης χρονιάς και να μην εντοπίζεται σωστά.
        // Βρίσκουμε τη Δευτέρα της εβδομάδας
        $monday = Carbon::create()->setISODate($year, $week, 1);
        
        // Ελέγχουμε Δευτέρα έως Παρασκευή (1-5)
        for ($day = 1; $day <= 5; $day++) {
            $currentDay = Carbon::create()->setISODate($year, $week, $day);
            // Ελέγχουμε αν η μέρα είναι στον σωστό μήνα ΚΑΙ έτος
            if ($currentDay->month == $month && $currentDay->year == $targetYear) {
                return true;
            }
        }
        
        return false;
    }

    public function extractWorkPlan($yearWeek) {
        // Πρόσθεσε αυτό στην αρχή της extractWorkPlan
  
        $user = Auth::guard('consultant')->user(); //check which user is logged in 
        //found and fix date of month to extract
        $year = substr($yearWeek, 0, 4);
        $week = substr($yearWeek, 4, 2);
  
        $selected_day = Carbon::create()->setISODate($year, $week);
        $first_day_of_month = new Carbon('first day of '.$selected_day->format('M')." ".$selected_day->format('Y'));
        $last_day_of_month = new Carbon('last day of '.$selected_day->format('M')." ".$selected_day->format('Y'));
        $month = $selected_day->month;
        $targetYear = $selected_day->year;

        // Βρίσκουμε τη Δευτέρα της πρώτης εβδομάδας
        $currentMonday = $first_day_of_month->copy()->startOfWeek();

        // Συλλέγουμε όλες τις εβδομάδες που έχουν εργάσιμες μέρες στον μήνα
        $weeks = [];
        // Προσθέτουμε +7 μέρες στο τέλος για να πιάσουμε και την τελευταία εβδομάδα
        $endDate = $last_day_of_month->copy()->addWeek();

        while ($currentMonday <= $endDate) {
            $weekNumber = $currentMonday->weekOfYear;
            $weekYear = $currentMonday->year;
            
            if ($this->weekHasWorkdayInMonth($weekYear, $weekNumber, $month, $targetYear)) {
                $weeks[] = ['year' => $weekYear, 'week' => $weekNumber];
            }
            
            $currentMonday->addWeek();
        }
        //$end_week cannot be 01 so if it is 01 means that the last day of the month is in the previous year
        // if($end_week == 01){
        //     $end_week = 52;
        // }
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        // Setting font to Calibri
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
        // Setting font size to 12
        $spreadsheet->getDefaultStyle()->getFont()->setSize(12);
        //Setting description, creator and title
        $spreadsheet ->getProperties()->setTitle("Πρόγραμμα Συμβούλου Εκπαίδευσης");
        $spreadsheet ->getProperties()->setCreator("ΔΠΕ Αχαΐας");
        $spreadsheet ->getProperties()->setDescription("Αρχείο αυτόματα δημιουργημένο από Φόρμα Υποβολής Στοιχείων");
        
        $activeWorksheet = $spreadsheet->getActiveSheet();
        // Setting title of the sheet
        $activeWorksheet->setTitle($user->surname." ".$user->name);
        $activeWorksheet->mergeCells('A1:G1');
        // Creating spreadsheet header
        $text = "Διεύθυνση Π.Ε. Αχαΐας";
        $activeWorksheet->getCell('A1')->setValue($text);
        $activeWorksheet->mergeCells('A2:G2');
        ///to implement NOT WORKING
        // $text = $user->schRegion()->name." - Προγραμματισμός και υλοποίηση έργου";
        // $activeWorksheet->getCell('A2')->setValue($text);
        $text = "Σύμβουλος Εκπαίδευσης: ".$user->surname." ".$user->name.".";
        $activeWorksheet->mergeCells('A3:G3');
        $activeWorksheet->getCell('A3')->setValue($text);
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => [
                    'rgb' => 'FFCC99',
                ],
            ],
        ];
        $activeWorksheet->getStyle('A3')->applyFromArray($styleArray);
        $styleArray = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => [
                    'rgb' => 'FFF8DC',
                ],
            ],
        ];
        $activeWorksheet->getStyle('A1:A3')->applyFromArray($styleArray);
        $text = "Χρονική Περίοδος Αποτύπωσης: Από ".$first_day_of_month->format('d-m-Y')." έως και ".$last_day_of_month->format('d-m-Y');
        $activeWorksheet->getCell('A4')->setValue($text);
        $activeWorksheet->mergeCells('A4:G4');
        $styleArray = [
            'row_dimensions' => [
                'height' => 40,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => [
                    'rgb' => 'ffdead',
                ],
            ],
        ];
        $activeWorksheet->getStyle('A4')->applyFromArray($styleArray);
        // Making headers text bold and larger
        $activeWorksheet->getStyle('A1:A4')->getFont()->setBold(true);
        $text = "Αυτόματη εξαγωγή από τη Φόρμα Υποβολής Στοιχείων της Διεύθυνσης Π.Ε. Αχαΐας";
        $activeWorksheet->getCell('A5')->setValue($text);
        $activeWorksheet->getStyle('A5')->getFont()->setItalic(true);
        $activeWorksheet->getStyle('B7:G7')->getFont()->setBold(true);
        $activeWorksheet->getCell('B7')->setValue('Εβδομάδα');
        $activeWorksheet->getCell('B7')->setValue('ΔΕΥΤΕΡΑ');
        $activeWorksheet->getCell('C7')->setValue('ΤΡΙΤΗ');
        $activeWorksheet->getCell('D7')->setValue('ΤΕΤΑΡΤΗ');
        $activeWorksheet->getCell('E7')->setValue('ΠΕΜΠΤΗ');
        $activeWorksheet->getCell('F7')->setValue('ΠΑΡΑΣΚΕΥΗ');
        $activeWorksheet->getCell('G7')->setValue('Παρατηρήσεις');
            
        // Insert data      
        $row=8;
        foreach($weeks as $weekData){
            $weekYear = $weekData['year'];
            $weekNumber = $weekData['week'];
            
            $yw = $weekYear . $weekNumber; // ΧΩΡΙΣ str_pad
            
            $week_format = str_pad($weekNumber, 2, '0', STR_PAD_LEFT);
            $monday = date( "d/m/Y", strtotime($weekYear."W".$week_format."1") );
            $friday = date( "d/m/Y", strtotime($weekYear."W".$week_format."5") );
            
            $activeWorksheet->getCell('A'.$row)->setValue($monday." έως ".$friday);
            
            $advisorsProgramm = $user->workplans()->where('yearWeek', $yw)->first();
            
            if($advisorsProgramm){
                $programm = json_decode($advisorsProgramm->programm);
                
                $activeWorksheet->getCell('B'.$row)->setValue($programm->mon);
                $activeWorksheet->getCell('C'.$row)->setValue($programm->tue);
                $activeWorksheet->getCell('D'.$row)->setValue($programm->wed);
                $activeWorksheet->getCell('E'.$row)->setValue($programm->thu);
                $activeWorksheet->getCell('F'.$row)->setValue($programm->fri);
                $activeWorksheet->getCell('G'.$row)->setValue($advisorsProgramm->comments);
            }
            $row++;
        }
        $today = Carbon::now();
        $today->format("Y-m-d");  
        $activeWorksheet->getCell('F14')->setValue('Πάτρα, '.$today->format("d-m-Y"));
        $activeWorksheet->getCell('F15')->setValue('Ο/Η Σύμβουλος Εκπαίδευσης,');
        $activeWorksheet->getCell('F18')->setValue($user->surname." ".$user->name);
        // size the rows and columns
        $activeWorksheet->getRowDimension($row)->setRowHeight(-1);
        $activeWorksheet->getColumnDimension('A')->setWidth(28);
        $activeWorksheet->getColumnDimension('B')->setWidth(25);
       
        $activeWorksheet->getColumnDimension('C')->setWidth(25);
        $activeWorksheet->getColumnDimension('D')->setWidth(25);
        $activeWorksheet->getColumnDimension('E')->setWidth(25);
        $activeWorksheet->getColumnDimension('F')->setWidth(25);
        $activeWorksheet->getColumnDimension('G')->setWidth(25);
        $activeWorksheet->getStyle('A8:G12')->getAlignment()->setVertical('center');
        $activeWorksheet->getStyle('B8:G13')->getAlignment()->setWrapText(true);
        $activeWorksheet->getRowDimension(8)->setRowHeight(-1);
        $activeWorksheet->getRowDimension(9)->setRowHeight(-1);
        $activeWorksheet->getRowDimension(10)->setRowHeight(-1);
        $activeWorksheet->getRowDimension(11)->setRowHeight(-1);
        $activeWorksheet->getRowDimension(12)->setRowHeight(-1);
        $activeWorksheet->getRowDimension(13)->setRowHeight(-1);         

        //set printing preferences
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set the printing area from A1 to G18
        $activeWorksheet->getPageSetup()->setPrintArea('A1:G18');
        $activeWorksheet->getPageSetup()->setFitToWidth('1');
        $activeWorksheet->getPageSetup()->setFitToHeight('0');
        // Create the spreadsheet writer object
        $writer = new Xlsx($spreadsheet);
        // When creating the writer object, the first sheet is also created
        // We will get the already created sheet
        $directory = storage_path('app/consultant_programms');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $fileName = $directory.'/'.$user->id.'consultant_programm.xlsx';
        $writer->save($fileName);
        
        if (file_exists($fileName)) {
            ob_end_clean();
            return response()->download($fileName, 'Προγραμματισμός_Έργου_'.$selected_day->format('Y').'_'.$selected_day->format('m').'.xlsx');
        }
        return;
    }
}
