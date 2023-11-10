<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller
{
    /**
     * Import and read the xlsx file
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function importSchools(Request $request){

        //validate the user's input
        $rule = [
            'import_schools' => 'required|mimes:xlsx'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return redirect(url('/'))->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
        }

        //store the file
        $filename = "schools_file".Auth::id().".xlsx";
        $path = $request->file('import_schools')->storeAs('files', $filename);

        //load the file with phpspreadsheet
        $mime = Storage::mimeType($path);
        $spreadsheet = IOFactory::load("../storage/app/$path");
        $schools_array=array();
        $row=2;
        $error=0;
        $rowSumValue="1";

        //read the file line by line
        while ($rowSumValue != "" && $row<10000){
            $check=array();
            $check['name'] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(14, $row)->getValue();
            $check['code']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(13, $row)->getValue();
            $municipality_name = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(7, $row)->getValue();

            //cross check municipality with database
            if(Municipality::where('name', $municipality_name)->count()){
                $check['municipality'] = Municipality::where('name', $municipality_name)->first()->id;
            }else{
                $error=1;
                $check['municipality']="";
            }

            //check other obvious fields
            $check['primary']=0;
            if(str_contains($spreadsheet->getActiveSheet()->getCellByColumnAndRow(12, $row)->getValue(), "Δημοτικό Σχολείο"))
                $check['primary']= 1;
            $check['leitourgikotita']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(15, $row)->getValue()!=""?$spreadsheet->getActiveSheet()->getCellByColumnAndRow(15, $row)->getValue():0;
            $check['organikotita']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(16, $row)->getValue()!=""?$spreadsheet->getActiveSheet()->getCellByColumnAndRow(16, $row)->getValue():0;
            $check['telephone']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(17, $row)->getValue()!=""?$spreadsheet->getActiveSheet()->getCellByColumnAndRow(17, $row)->getValue():"-";
            $check['is_active']= ($spreadsheet->getActiveSheet()->getCellByColumnAndRow(26, $row)->getValue()=="Ναί")?0:1;
            $check['has_all_day']= ($spreadsheet->getActiveSheet()->getCellByColumnAndRow(14, $row)->getValue()=="Όχι")?1:0;
            $check['mail']= $spreadsheet->getActiveSheet()->getCellByColumnAndRow(19, $row)->getValue()!=""?$spreadsheet->getActiveSheet()->getCellByColumnAndRow(19, $row)->getValue():"-";

            $check['special_needs']=0;
            if(str_contains($spreadsheet->getActiveSheet()->getCellByColumnAndRow(12, $row)->getValue(), "Ειδικής Αγωγής"))
                $check['special_needs']= 1;

            $check['experimental']=0;
            if(str_contains($spreadsheet->getActiveSheet()->getCellByColumnAndRow(12, $row)->getValue(), "Πειραματικό"))
                $check['experimental']= 1;

            $check['international']=0;
            if(!str_contains($spreadsheet->getActiveSheet()->getCellByColumnAndRow(11, $row)->getValue(), "Ιδιωτικά Σχολεία"))
                $check['international']= 1;

            $check['md5']="";

            //prepare schools array to pass it in session
            array_push($schools_array, $check);

            //change line and check if it's empty
            $row++;
            $rowSumValue="";
            for($col=1;$col<=54;$col++){
                $rowSumValue .= $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();   
            }
        }
        session(['schools_array' => $schools_array]);

        if($error){
            return redirect(url('/import_schools'))
                ->with('asks_to','error');
        }else{
            return redirect(url('/import_schools'))
                ->with('asks_to','save');
        }
    }

    /**
     * Read the schools array from session and save the data
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function insertSchools(){
        $schools_array = session('schools_array');
        session()->forget('schools_array');
        $error=false;
        $done_at_least_once=false;
        foreach($schools_array as $school){
            // CREATE school WHO IS IN XLSX BUT NOT IN DATABASE, update existing records based on 'code' field
            try{
                School::updateOrCreate(
                    [
                        'code' => $school['code']
                    ],
                    [
                        'name' => $school['name'], 
                        'code' => $school['code'],
                        'municipality_id' => $school['municipality'],
                        'primary' => $school['primary'],
                        'leitourgikotita' => $school['leitourgikotita'],
                        'organikotita' => $school['organikotita'],
                        'telephone' => $school['telephone'],
                        'is_active' => $school['is_active'],
                        'has_all_day' => $school['has_all_day'],
                        'md5' => md5($school['code']),
                        'mail' => $school['mail'],
                        'experimental' => $school['experimental'],
                        'special_needs' => $school['special_needs'],
                        'international' => $school['international']
                    ]
                );
                $record = School::where('code', $school['code'])->first(); //η εντολή θα επιστρέψει μια εγγραφή που βρέθηκε στο excel ΚΑΙ στη βάση
                $record->updated_at=now(); //είτε δημιουργήθηκε τώρα στη βάση, είτε υπήρχε (ανεξάρτητα από το αν είναι dirty ή όχι), ανανεώνω το timestamp
                $record->save();
                $done_at_least_once=true;
            }
            catch(Throwable $e){
                Log::channel('throwable_db')->error(Auth::user()->username.' create school error '.$school['code'].' '.$e->getMessage());
                $error=true;
                continue;    
            }
        }
        if($done_at_least_once){
            DB::table('last_update_schools')->updateOrInsert(['id'=>1],['date_updated'=>now()]);
        }
        if(!$error){
            Log::channel('user_memorable_actions')->info(Auth::user()->username.' insertSchools');
            return redirect(url('/schools'))
                ->with('success', 'Η εισαγωγή ολοκληρώθηκε');
        }
        else{
            Log::channel('user_memorable_actions')->warning(Auth::user()->username.' insertSchools with errors');
            return redirect(url('/schools'))
                ->with('warning', 'Η εισαγωγή ολοκληρώθηκε με σφάλματα που καταγράφηκαν στο log throwable_db');
        }
    }

    //
    public function login($md5){ 

        $school = School::where('md5', $md5)->firstOrFail();
        Auth::guard('school')->login($school);
        $school->logged_in_at = Carbon::now();
        $school->save();
        session()->regenerate();

        return redirect(url('/index_school'))->with('success', "$school->name καλωσήρθατε");
    }

    public function logout(){
        
        auth()->guard('school')->logout();
        return redirect(url('/'))->with('success', 'Αποσυνδεθήκατε');
    }

    public function importDirectors(Request $request){
        $rule = [
            'import_directors' => 'required|mimes:xlsx'
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()){ 
            return redirect(url('/'))->with('failure', 'Μη επιτρεπτός τύπος αρχείου');
        }

        //store the file
        $filename = "directors_file".Auth::id().".xlsx";
        $path = $request->file('import_directors')->storeAs('files', $filename);

        //load the file with phpspreadsheet
        $mime = Storage::mimeType($path);
        $spreadsheet = IOFactory::load("../storage/app/$path");
        $directors_array=array();
        $row=2;
        $error=0;
        $rowSumValue="1";   

        while ($rowSumValue != "" && $row<10000){
            $check=array();
            $code = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(8, $row)->getValue();
            $afm = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(16, $row)->getValue();
            $check['school_name']='';
            $check['director_surname']='';
            $check['code'] = substr($code, 2, -1); // remove from start =" and remove from end "
            $check['afm'] = substr($afm, 2, -1); // remove from start =" and remove from end "
            if(!School::where('code', $check['code'])->count()){
                $check['code'] = 'Άγνωστος κωδικός σχολείου';
                $error = 1;
            }
            else{
                $school = School::where('code', $check['code'])->first();
                $check['school_name'] = $school->name;
                $check['school_id'] = $school->id;
            }
            if(!Teacher::where('afm', $check['afm'])->count()){
                $check['afm'] = 'Άγνωστος ΑΦΜ Εκπαιδευτικού';
                $error = 1;
            }
            else{
                $teacher = Teacher::where('afm', $check['afm'])->first();
                $check['teacher_id'] = $teacher->id;
                $check['director_surname'] = $teacher->surname;
            }

            //prepare directors array to pass it in session
            array_push($directors_array, $check);

            //change line and check if it's empty
            $row++;
            $rowSumValue="";
            for($col=1;$col<=33;$col++){
                $rowSumValue .= $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();   
            }
        }

        session(['directors_array' => $directors_array]);

        if($error){
            return redirect(url('/import_directors'))
                ->with('asks_to','error');
        }else{
            return redirect(url('/import_directors'))
                ->with('asks_to','save');
        }
    }

    public function insertDirectors(){
        $error=false;
        $done_at_least_once=false;
        $directors_array = session('directors_array');
        session()->forget('directors_array');
        // dd($directors_array);
        foreach($directors_array as $one_director){
            //  update schools records based on 'code' field
            try{
                $school = School::find($one_director['school_id']);
                $school_director = Teacher::find($one_director['teacher_id']);
                $school->director_id = $school_director->id;
                $school->save();
                $done_at_least_once=true;
            }
            catch(Throwable $e){
                Log::channel('throwable_db')->error(Auth::user()->username.' link director error '.$one_director['teacher_id'].' '.$e->getMessage());
                $error=true;
                continue;    
            }
            // if($one_director['school_id']==222 and $one_director['teacher_id']==1570)dd($one_director);
        }
        if($done_at_least_once){
            DB::table('last_update_directors')->updateOrInsert(['id'=>1],['date_updated'=>now()]);
        }
        if(!$error){
            Log::channel('user_memorable_actions')->info(Auth::user()->username.' insertDirectors');
            return redirect(url('/directors'))
                ->with('success', 'Η εισαγωγή ολοκληρώθηκε');    
        }
        else{
            Log::channel('user_memorable_actions')->warning(Auth::user()->username.' insertDirectors with errors');
            return redirect(url('/directors'))->with('warning', 'Η εισαγωγή ολοκληρώθηκε με σφάλματα που καταγράφηκαν στο log throwable_db'); 
        }
    }
}
