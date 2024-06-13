<x-layout_school>
  
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $school_code = $school->code;
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; 
        $app_name = $microapp->name;
        if($school->enrollments){
            $old_data = $school->enrollments;
            $required ='';
        }else{
            $old_data = null;
            $required = 'required';
        }
    @endphp
    
    @push('title')
        <title>{{$app_name}}</title>
    @endpush
<div class="container">
    <div class="container px-5">
        <div class="alert alert-info text-center">
            Στην καρτέλα αυτή θα δηλωθούν τα στοιχεία <strong>εγγραφέντων</strong> μαθητών 
            <br>καθώς και τα στοιχεία για τον προγραμματισμό λειτουργίας του σχ. Έτους {{config('enrollments.schoolYear')}}.
        </div>
        @if(config('enrollments.nextYearPlanningActive') == "1")
        <div class="alert text-center">
            <h5>Γενική Επισήμανση:</h5>
            Η αριστερή στήλη περιλαμβάνει τα στοιχεία που αφορούν στις <strong> εγγραφές </strong>. Η δεξιά στήλη περιλαμβάνει τα στοιχεία για τον <strong>προγραμματισμό </strong>του σχ. έτους.<br>
            Οι δύο στήλες συμπληρώνονται <strong>ανεξάρτητα</strong> μεταξύ τους.
        </div>
        <div class="alert text-center">
            <h5>Οδηγίες για τα στοιχεία προγραμματισμού του σχ. έτους:</h5>
            @if($school->primary == 1) 
                1) Υποβάλλετε το <strong>συνολικό αριθμό μαθητών</strong> που πρόκειται να φοιτήσουν το επόμενο σχ. έτος. <br>
                Μετά την υποβολή του συνολικού αριθμού,<br>
                2) Υποβάλλετε τον <strong>αριθμό μαθητών κατά τμήμα</strong>.<br>
                Μετά την υποβολή των μαθητών κατά Τμήμα μπορείτε να υποβάλλετε τυχόν παρατηρήσεις για κάθε τμήμα (π.χ. διχοτόμηση κάθε χρόνο, εκκρεμείς μετεγγραφές, κλπ). <br>
                3) Υποβάλλετε το <strong>αρχείο</strong> για τη λειτουργία του Ολοήμερου συμπληρώνοντας <strong> και τα δύο Φύλλα του αρχείου excel.</strong>
            @else
                1) Υποβάλλετε τον <strong>αριθμό μαθητών κατά τμήμα </strong> στη δεξιά στήλη.
                Η ονομασία των τμημάτων (Τμήμα 1, κλπ είναι ενδεικτική) <br>
                Μετά την υποβολή των μαθητών κατά Τμήμα μπορείτε να υποβάλλετε τυχόν παρατηρήσεις για κάθε τμήμα (π.χ. εκκρεμείς μετεγγραφές, κλπ). <br>
                2) Υποβάλλετε τα στοιχεία για τη λειτουργία του <strong> Ολοήμερου Προγράμματος και Πρωινής Υποδοχής </strong> συμπληρώνοντας <strong>Αριθμό Μαθητών</strong> και <strong>Αριθμό Τμημάτων</strong> που θα λειτουργήσουν.<br>
            @endif
        </div>
        @endif
        {{-- ΔΗΜΟΤΙΚΟ ΜΟΝΟ: Μόνο αν είναι Δημοτικό ζήτησε πρώτα το συνολικό αριθμό μαθητών  --}}
        @if($school->primary == 1 && config('enrollments.nextYearPlanningActive') == "1" && $school->public == 1)
        <div class="container mt-5">
            <h3>Συνολικός αριθμός μαθητών</h3>
            <nav class="navbar navbar-light bg-light">
                {{-- <div style="display: flex; justify-content: space-between;"> --}}
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Συνολικός αριθμός μαθητών που πρόκειται να φοιτήσουν το σχ. έτος {{config('enrollments.schoolYear')}}</th>
                    </tr>
                </thead>
                <form action="{{route("enrollments.save", ['select'=>'total_students'])}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                <tbody>
                    <tr><td>
                        <input type="number" name="total_students_nr" class="form-control" required value="@if($old_data){{$old_data->total_students_nr}}@endif">
                    </td></tr>
                    <tr><td>
                        @if(config('enrollments.nextYearPlanningAccepts') == "0")
                            <div class='alert alert-warning text-center my-2'>
                                <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                            </div>
                        @else
                            <button type="submit" class="btn btn-primary bi bi-plus-circle"> Υποβολή</button>
                        @endif
                    </td></tr>
                </tbody>
                </form>
                </table>
                </div>
            </nav>
        </div>
        @endif
        {{-- ΔΗΜΟΤΙΚΟ ΜΟΝΟ ΤΕΛΟΣ --}}
        {{-- ΣΤΟΙΧΕΙΑ ΕΓΓΡΑΦΩΝ - Όλα τα Σχολεία Δημοτικό: Εγγραφές στην Α' Δημοτικού, Νηπιαγωγείο: όλες οι εγγραφές --}}
        <div class="container mt-5">
            <h3>Στοιχεία πρωινού προγράμματος @if($school->primary == 1) Δημοτικού Σχολείου @else Νηπιαγωγείου @endif</h3>  {{-- Εγγραφέντες στην Α' / Εγγραφέντες στο Νηπιαγωγείο --}}
            <nav class="navbar navbar-light bg-light">
            <div style="display: flex; justify-content: space-between;">
            <table class="table table-bordered">    
                <thead>
                    <tr>
                        <th>Στοιχεία</th>
                        <th>@if($school->primary == 1) Τάξη Α' @else Προνήπια / Νήπια @endif</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            Αρχείο για συμπλήρωση (Ενδεικτικό Υπόδειγμα)
                        <p class="fw-lighter fst-italic fs-6"><small>(Το πρότυπο παρέχεται ενδεικτικά. Μπορείτε να ανεβάσετε οποιοδήποτε αρχείο excel.)</small></p>
                        </td>
                        <td>
                            @if($school->primary == 1)
                                <form action="{{route('enrollments.download_file', ['file' => "1_enrollments_primary_school.xlsx", 'download_file_name' => "Εγγραφέντες.xlsx"])}}" method="get"class="container-fluid">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας </button>
                                </form>
                                {{-- {{url("/$appname/1_enrollments_primary_school.xlsx/Εγγραφέντες.xlsx")}} --}}
                            @else
                                <form action="{{route('enrollments.download_file', ['file' => "1_enrollments_nursery_school.xlsx", 'download_file_name' => "Εγγραφέντες.xlsx"])}}" method="get"class="container-fluid">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας Δ/νσης</button>
                                </form>
                            @endif  
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <form action="{{route("enrollments.save", ['select'=>'enrolled'])}}" method="post" enctype="multipart/form-data" class="container-fluid">
                            @csrf
                        <td>Αριθμός εγγεγραμμένων @if($school->primary == 1)  μαθητών Α' Τάξης @else Νηπίων / Προνηπίων @endif  2024-25</td>
                        </form>
                        <form action="{{route("enrollments.save", ['select'=>'enrolled'])}}" method="post" enctype="multipart/form-data" class="container-fluid">
                            @csrf
                        {{-- {{url("/enrollments/enrolled")}} --}}
                        <td>
                            <input name="nr_of_students1" id="nr_of_students1" type="number" class="form-control input-sm" required value="@if($old_data){{$old_data->nr_of_students1}}@endif">
                        </td>  
                    </tr>
                    <tr>
                        <td>
                            Αρχείο Εγγραφέντων Μαθητών
                            <p class="fw-lighter fst-italic fs-6"><small>(Δεκτά αρχεία μορφής .xlsx)</small></p>
                        </td>
                        <td>
                            <input name="file" type="file" class="form-control" {{$required}}>
                            @if(!$accepts)
                                <div class='alert alert-warning text-center my-2'>
                                    <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                                </div>
                            @else
                                <div class="input-group">
                                    <span class="w-25"></span>
                                    <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                    <a href="{{route('enrollments.create')}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                                    {{-- {{url("/$appname/create")}} --}}
                                    {{-- {{route('microapps.'.$appname.".create")}} --}}
                                </div>
                            @endif
                        </td>
                    </form>
                </tr>
            @if($old_data && $old_data->enrolled_file1)
                <tr><td> Αρχείο που έχει υποβληθεί: 
                <p class="fw-lighter fst-italic fs-6"><small>(Τελευταία υποβολή: {{$old_data->updated_at}})</small></p>
                </td>
                @php
                    $file1 = $old_data->enrolled_file1;
                @endphp<td>
                <form action="{{route('enrollments.download_file',['file'=>"enrollments1_$school_code.xlsx", 'download_file_name' => $file1])}}" method="get"class="container-fluid">
                    <button class="btn btn-success bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Λήψη αρχείου που έχει υποβληθεί"> {{$file1}} </button>
                </form>
                {{-- {{url("/enrollments/enrollments1_$school_code.xlsx/$file1")}} --}}
                </td>
            </tr>
            @endif
                </tbody>
            </table>
            {{-- ΣΤΟΙΧΕΙΑ ΕΓΓΡΑΦΩΝ ΤΕΛΟΣ --}}
            {{-- ΣΤΟΙΧΕΙΑ ΠΡΟΓΡΑΜΜΑΤΙΣΜΟΥ επόμενου σχ. έτους --}}
            @if($old_data)
            @if(($school->primary ==1 && $old_data->total_students_nr && config('enrollments.nextYearPlanningActive') == 1) || ($school->primary == 0 && $old_data->nr_of_students1 && config('enrollments.nextYearPlanningActive') == 1))
            @php
                $total_st_number = ($school->primary ==1)? $old_data->total_students_nr : $old_data->nr_of_students1;
                $nextYearLeitourgikotita = App\Http\Controllers\microapps\EnrollmentController::nextYearsLeitourgikotita($school->primary, $school->leitourgikotita, $total_st_number);
                $max_class_numbers = ($nextYearLeitourgikotita >= 6) ? 6 : $nextYearLeitourgikotita;
                $enrollments_classes = App\Models\microapps\EnrollmentsClasses::where('enrollment_id', $old_data->id)->first();
                if($enrollments_classes){
                    $morning_classes_json = $enrollments_classes->morning_classes;
                    $morning_classes = json_decode($morning_classes_json);
                }
                //dd($morning_classes[1]->comment);
            @endphp
                
                <table class="table table-bordered">
                    <thead><tr><th></th><th colspan="{{$nextYearLeitourgikotita}}"><h4>Στοιχεία προγραμματισμού λειτουργίας του σχ. έτους {{config('enrollments.schoolYear')}}</h4><th></tr>
                        </tr></thead>
                    <tbody>
                        <tr>
                            <td>Ταξη</td>
                            @if($nextYearLeitourgikotita == 1)
                                @if($school->primary == 1)
                                    <td>Α'-Β'-Γ'-Δ'-Ε'-Στ'</td>
                                @else
                                    <td>Τμήμα Νηπιαγωγείου</td>
                                @endif
                            @endif
                            @if($nextYearLeitourgikotita == 2)
                                <td>Τμήμα 1</td><td>Τμήμα 2</td>
                            @endif
                            @if($nextYearLeitourgikotita == 3)
                                <td>Τμήμα 1</td><td>Τμήμα 2</td><td>Τμήμα 3</td>
                            @endif
                            @if($nextYearLeitourgikotita == 4)
                                <td >Τμήμα 1</td><td >Τμήμα 2</td><td >Τμήμα 3</td><td >Τμήμα 4</td>
                            @endif
                            @if($nextYearLeitourgikotita == 5)
                            <td >Τμήμα 1</td><td >Τμήμα 2</td><td >Τμήμα 3</td><td >Τμήμα 4</td><td >Τμήμα 5</td>
                            @endif
                            @if($nextYearLeitourgikotita >= 6)
                                <td>Α'</td><td>Β'</td><td>Γ'</td><td>Δ'</td><td>Ε'</td><td>Στ'</td>
                            @endif
                            
                        </tr>
                        <tr>
                        <form action="{{route("enrollments.save", ['select'=>'nextYearNumbers'])}}" method="post" class="container-fluid">
                            @csrf
                        <td>
                            Αριθμός μαθητών
                        </td>
                        @php //
                        if($nextYearLeitourgikotita >= 6){ // Αν τα τμήματα είναι περισσότερα από 6 και δεν έχει καταχωρηθεί τίποτα δείξε τον αριθμό μαθητών της Α
                            if(isset($morning_classes[0]->nr_of_students)){ $nr_of_st = $morning_classes[0]->nr_of_students; }
                            else { $nr_of_st = $old_data->nr_of_students1; }
                        }else{ // Αν τα τμήματα είναι λιγότερα από 6
                           
                            if(isset($morning_classes[0]->nr_of_students)){ $nr_of_st = $morning_classes[0]->nr_of_students; }
                            else { 
                                if($nextYearLeitourgikotita == 1){
                                    if($school->primary == 1){ 
                                        $nr_of_st = $old_data->total_students_nr; 
                                    } else { 
                                        $nr_of_st = $old_data->nr_of_students1; 
                                    }
                                }else{ 
                                    $nr_of_st = 0; 
                                }
                            }
                        }
                        @endphp

                        <td>
                            <input name="nr_of_students1" id="nr_of_students1" type="text" class="form-control input-sm" required value="{{$nr_of_st}}" pattern="\d*" >
                            @if($nextYearLeitourgikotita == 1 && !$enrollments_classes)
                                
                            <small>Παρακαλούμε πατήστε πάλι Υποβολή για επιβεβαίωση και για να υπολογιστεί το Τμήμα από το Σύστημα.</small>
                            @endif
                        </td>
                        @for($i=2; $i<=$max_class_numbers; $i++)
                            @php 
                                if(isset($morning_classes[$i-1]->nr_of_students)){ $nr_of_st = $morning_classes[$i-1]->nr_of_students; }
                                else { $nr_of_st = 0; }
                            @endphp

                            <td>
                                <input name="nr_of_students{{$i}}" id="nr_of_students{{$i}}" type="text" class="form-control input-sm" required value="{{$nr_of_st}}" pattern="\d*" >
                            </td>
                        @endfor
                        </tr>
                        {{-- αν έχουν καταχωρηθεί τμήματα --}}
                        @if($enrollments_classes)
                        <tr>
                            <td>
                                Αριθμός τμημάτων  <br><small>αυτόματος υπολογισμός από το σύστημα με βάση τον αριθμό μαθητών (μετά την υποβολή). <br><strong>Δεν επιτρέπεται η τροποποίηση.</strong><br>
                               Αν προγραμματίζετε τη λειτουργία επιπλέον τμήματος σημειώστε το στις παρατηρήσεις.</small>                            </td>
                            @if($nextYearLeitourgikotita >= 6) {{-- Αν τα τμήματα είναι περισσότερα από 6 --}}
                                @for($i=1; $i<=6; $i++)
                                    @php 
                                        if(isset($morning_classes[$i-1]->nr_of_sections)){ $nr_of_sec = $morning_classes[$i-1]->nr_of_sections; }
                                        else { $nr_of_sec = '1'; }
                                    @endphp
                                    <td>
                                        <input name="nr_of_sections{{$i}}" id="nr_of_sections{{$i}}" type="text" class="form-control input-sm" value="{{$nr_of_sec}}" readonly 
                                         >
                                    </td>
                                @endfor
                            @else {{-- Αν τα τμήματα είναι λιγότερα από 6 --}}
                                @for($i=1; $i<=$nextYearLeitourgikotita; $i++)
                                    <td>
                                        <input name="nr_of_sections{{$i}}" id="nr_of_sections{{$i}}" type="text" class="form-control input-sm" value="1" readonly>
                                    </td>
                                @endfor
                            @endif
                        </tr>
                        <tr>
                            <td>
                                Παρατήρηση <br><small>(π.χ. αίτημα για επιπλέον τμήμα)</small>

                            </td>
                            @for($i=1; $i<=$max_class_numbers; $i++)
                                @php 
                                    if(isset($morning_classes[$i-1]->comment)){ $com = $morning_classes[$i-1]->comment; }
                                    else { $com = ''; }
                                @endphp
                                <td>
                                    <input name="comment{{$i}}" id="comment{{$i}}" type="text" class="form-control input-sm" value="{{$com}}" >
                                </td>
                            @endfor
                        </tr>
                        @endif
                        <tr><td colspan="5">
                            @if(config('enrollments.nextYearPlanningAccepts') == 0 || !$old_data)
                                <div class='alert alert-warning text-center my-2'>
                                    <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                                </div>
                            @else
                                <div class="input-group">
                                    <span class="w-25"></span>
                                    <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                </div>
                            @endif
                        </td></tr>
                            </form>
                    </tbody>
                </table>
            @endif
            @endif
        </div>
        </nav>
        {{-- ΣΤΟΙΧΕΙΑ ΠΡΟΓΡΑΜΜΑΤΙΣΜΟΥ ΤΕΛΟΣ --}}
        {{-- ΣΤΟΙΧΕΙΑ ΕΓΓΡΑΦΗΣ ΣΤΟ ΟΛΟΗΜΕΡΟ - Όλα τα Σχολεία  --}}
            <h3>Στοιχεία εγγραφής στο Ολοήμερο @if($school->primary == 1) πρόγραμμα. @else πρόγραμμα του Νηπιαγωγείου. @endif</h3>  {{-- ΔΗΜΟΤΙΚΑ:  / ΝΗΠΙΑΓΩΓΕΙΑ: Εγγραφέντες στο Ολοήμερο --}}     
            <nav class="navbar navbar-light bg-light">
            <div style="display: flex; justify-content: space-between;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Στοιχεία</th>
                        <th>@if($school->primary == 1) Μαθητές Ολοήμερου @else Προνήπια / Νήπια @endif</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            Αρχείο για συμπλήρωση (Ενδεικτικό Υποδειγμα)
                            <p class="fw-lighter fst-italic fs-6"><small>(Μπορεί να χρησιμοποιηθεί οποιοδήποτε αρχείο ως πρότυπο αρκεί να είναι της μορφής .xlsx.)</small></p>
                        </td>
                        <td> 
                            @if($school->primary == 1)
                                @if($school->has_extended_all_day == 1)
                                <form action="{{route('enrollments.download_file',['file'=>"2_enrollments_primary_ext_all_day_school.xlsx", 'download_file_name' => "Ολοήμερο_2024_25.xlsx"])}}" method="get"class="container-fluid">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας </button>
                                </form>
                                @else
                                <form action="{{route('enrollments.download_file',['file'=>"2_enrollments_primary_all_day_school.xlsx", 'download_file_name' => "Ολοήμερο_2024_25.xlsx"])}}" method="get"class="container-fluid">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας </button>
                                </form>
                                @endif
                            @else
                                @if($school->has_extended_all_day == 1)
                                <form action="{{route('enrollments.download_file',['file'=>"2_enrollments_nursery_ext_all_day_school.xlsx", 'download_file_name' => "Ολοήμερο.xlsx"])}}" method="get"class="container-fluid">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας Δ/νσης</button>
                                </form>
                                @else
                                <form action="{{route('enrollments.download_file',['file'=>"2_enrollments_nursery_all_day_school.xlsx", 'download_file_name' => "Ολοήμερο.xlsx"])}}" method="get"class="container-fluid">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας Δ/νσης</button>
                                </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                    <form action="{{route("enrollments.save", ['select'=>'all_day'])}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                    <tr>
                        <td>Αριθμός εγγεγραμμένων  @if($school->primary == 1) μαθητών @else Νηπίων / Προνηπίων @endif στο Ολοήμερο 2024-25</td>
                        <td>
                            <input name="nr_of_students1_all_day1" id="nr_of_students1_all_day1" type="number" class="form-control input-sm" required value="@if($old_data){{$old_data->nr_of_students1_all_day1}}@endif">
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            Υποβολή Αρχείου @if($school->primary == 1) μαθητών @else Νηπίων / Προνηπίων @endif στο Ολοήμερο
                            <p class="fw-lighter fst-italic fs-6"><small>(Δεκτά αρχεία μορφής .xlsx)</small></p>
                        </td>
                        <td>
                            <input name="file" type="file" class="form-control" {{$required}}>
                            @if(!$accepts)
                                <div class='alert alert-warning text-center my-2'>
                                    <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές </strong>
                                </div>
                            @else
                                <div class="input-group">
                                    <span class="w-25"></span>
                                    <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                    <a href="{{route('enrollments.create')}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    </form>
                    @if($old_data && $old_data->all_day_file1)
                        <tr><td> Αρχείο που έχει υποβληθεί: 
                        <p class="fw-lighter fst-italic fs-6"><small>(Τελευταία υποβολή: {{$old_data->updated_at}})</small></p>
                        </td>
                        @php
                            $file2 = $old_data->all_day_file1;
                        @endphp<td>
                        <form action="{{route('enrollments.download_file',['file'=>"enrollments2_$school_code.xlsx", 'download_file_name' => $file2])}}" method="get"class="container-fluid">
                            <button class="btn btn-success bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Λήψη αρχείου που έχει υποβληθεί"> {{$file2}} </button>
                        </form>
                        </td></tr>
                    @endif
                </tbody>
            </form>
            </table>
            {{-- Στοιχεία προγραμματισμού Ολοήμερου Προγράμματος --}}
            @if(config('enrollments.nextYearPlanningActive') == 1)
            <table class="table table-bordered">
                    @if($school->primary == 1)
                <tr><th colspan="2">Προγραμματισμός Λειτουργίας Ολοήμερου</th></tr>   
                <tbody>
                    <tr>
                        <td>
                            Αρχείο για συμπλήρωση
                            <p class="fw-lighter fst-italic fs-6"><small>(Να συμπληρωθεί υποχρεωτικά το παρεχόμενο πρότυπο)</small></p>
                        </td>
                        <td> 
                            Αρχέιο Α1 & Α2 του ΥΠΑΙΘΑ
                            <form action="{{route('enrollments.download_file',['file'=>"5_next_year_planning_all_day_school.xlsx", 'download_file_name' => "Ολοήμερο_Προγραμματισμός_2024_25.xlsx"])}}" method="get"class="container-fluid">
                                <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title=""> Πίνακας </button>
                            </form>
                            Σημείωση: <small><em>Κατά τη συμπλήρωση του αρχείου, το πεδίο <strong> Σύνολο Μαθητών Ολοήμερου</strong> συμπληρώνεται από το άθροισμα των μαθητών που αποχωρούν, στις επόμενες στήλες.</em></small>
                        </td>
                    </tr>
                    <form action="{{route("enrollments.save", ['select'=>'all_day_next_year_planning'])}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                    <tr>
                        <td>
                            Υποβολή Αρχείου προγραμματισμού Ολοήμερου Προγράμματος
                            <p class="fw-lighter fst-italic fs-6"><small>(Δεκτά αρχεία μορφής .xlsx)</small></p>
                        </td>
                        <td>
                            <input name="file" type="file" class="form-control" $required>
                            @if(config('enrollments.nextYearPlanningAccepts') == 0)
                                <div class='alert alert-warning text-center my-2'>
                                    <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές </strong>
                                </div>
                            @else
                                <div class="input-group">
                                    <span class="w-25"></span>
                                    <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                </div>
                            @endif
                        </td>
                    </tr>
                    </form>
                    @if($old_data && $old_data->a1_a2_file)
                        <tr><td> Αρχείο που έχει υποβληθεί: 
                        <p class="fw-lighter fst-italic fs-6"><small>(Τελευταία υποβολή: {{$old_data->updated_at}})</small></p>
                        </td>
                        @php
                            $file3 = $old_data->a1_a2_file;
                        @endphp<td>
                        <form action="{{route('enrollments.download_file',['file'=>"a1_a2_file_$school_code.xlsx", 'download_file_name' => $file3])}}" method="get"class="container-fluid">
                            <button class="btn btn-success bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Λήψη αρχείου που έχει υποβληθεί"> {{$file3}} </button>
                        </form>
                        </td></tr>
                    @endif
                </tbody>
                    @else {{-- ΝΗΠΙΑΓΩΓΕΙΑ --}}
                    @php
                    if(isset($enrollments_classes->morning_zone_classes)){
                        $morning_zone_classes = json_decode($enrollments_classes->morning_zone_classes);
                    } 
                    else {
                        $morning_zone_classes = [];
                    }
                    if(isset($enrollments_classes->all_day_school_classes)){
                        $all_day_school_classes = json_decode($enrollments_classes->all_day_school_classes);
                    }
                    else {$all_day_school_classes = [];
                    }
                    
                    @endphp
                    <thead>
                    <tr>
                        <th>Στοιχεία προγραμματισμού λειτουργίας Ολοήμερου Προγράμματος για το σχ. έτους {{config('enrollments.schoolYear')}}</td>
                        <th>Πρόωρη Υποδοχή</td>
                        <th>Ολοήμερο Πρόγραμμα (έως 16:00)</td>
                        <th>Διευρυμένο Ολοήμερο (έως 17:30)</td>
                    </tr>
                    </thead>
                    <form action="{{route("enrollments.save", ['select'=>'all_day_next_year_planning'])}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                    <tr>
                        <td>Αρ. Μαθητών</td>
                        <td>
                            <input name="nr_of_students_morning_zone" id="nr_of_students_morning_zone" type="number" class="form-control input-sm" required value="@if(isset($morning_zone_classes[0])){{$morning_zone_classes[0]->nr_of_students}}@endif">
                        </td>
                        <td>
                            <input name="nr_of_students_all_day" id="nr_of_students_all_day" type="number" class="form-control input-sm" required value="@if(isset($all_day_school_classes[0])){{$all_day_school_classes[0]->nr_of_students}}@endif">
                        </td>
                        <td>
                            <input name="nr_of_students_extended_all_day" id="nr_of_students_extended_all_day" type="number" class="form-control input-sm" required value="@if(isset($all_day_school_classes[1])){{$all_day_school_classes[1]->nr_of_students}}@endif">
                        </td>
                    </tr>
                    <tr>
                        <td>Αριθμός Τμημάτων</td>
                        <td>
                            <select name="nr_of_sections_morning_zone" id="nr_of_sections_morning_zone" class="form-control input-sm" required>
                                <option value="0" @if(count($morning_zone_classes) && $morning_zone_classes[0]->nr_of_sections == 0) selected @endif >Δε θα λειτουργήσει</option>
                                <option value="1" @if(count($morning_zone_classes) && $morning_zone_classes[0]->nr_of_sections == 1) selected @endif >1</option>
                                <option value="2" @if(count($morning_zone_classes) && $morning_zone_classes[0]->nr_of_sections == 2) selected @endif >2</option>
                            </select>
                        </td>
                        <td>
                            <select name="nr_of_sections_all_day" id="nr_of_sections_all_day" class="form-control input-sm" required>
                                <option value="0" @if(count($all_day_school_classes) && $all_day_school_classes[0]->nr_of_sections == 0) selected @endif>Δε θα λειτουργήσει</option>
                                <option value="1" @if(count($all_day_school_classes) && $all_day_school_classes[0]->nr_of_sections == 1) selected @endif>1</option>
                                <option value="2" @if(count($all_day_school_classes) && $all_day_school_classes[0]->nr_of_sections == 2) selected @endif>2</option>
                            </select>
                        </td>
                        <td>
                            <select name="nr_of_sections_extended_all_day" id="nr_of_sections_extended_all_day" class="form-control input-sm" required>
                                <option value="0" @if($all_day_school_classes && $all_day_school_classes[1]->nr_of_sections == 0) selected @endif>Δε θα λειτουργήσει</option>
                                <option value="1" @if($all_day_school_classes && $all_day_school_classes[1]->nr_of_sections == 1) selected @endif>1</option>
                                <option value="2" @if($all_day_school_classes && $all_day_school_classes[1]->nr_of_sections == 2) selected @endif>2</option>
                            </select>
                        </td>
                        {{-- <td>{{$old_data->nr_of_students1_all_day1}}</td> --}}
                    </tr>
                    <tr><td></td>
                        <td colspan="3">
                            @if(config('enrollments.nextYearPlanningAccepts') == 0)
                            <div class='alert alert-warning text-center my-2'>
                                <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές </strong>
                            </div>
                        @else
                            <div class="input-group">
                                <span class="w-25"></span>
                                <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            </div>
                        @endif
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
            @endif
    </nav>
     {{-- ΣΤΟΙΧΕΙΑ ΕΓΓΡΑΦΗΣ ΣΤΟ ΟΛΟΗΜΕΡΟ ΤΕΛΟΣ  --}}
     {{-- ΔΗΜΙΟΥΡΓΙΑ ΕΠΙΠΛΕΟΝ ΤΜΗΜΑΤΟΣ / ΚΑΤΑΝΟΜΗ ΣΕ ΟΜΟΡΑ ΣΧΟΛΕΙΑ --}}
<h3>Δημιουργία επιπλέον Τμήματος / Κατανομή μαθητών σε όμορα σχολεία</h3> {{-- ΔΗΜΟΤΙΚΑ: Αίτημα για δημιουργία επιπλέον Τμήματος ή μαθητές στα όρια / ΝΗΠΙΑΓΩΓΕΙΑ: Αίτημα για δημιουργία επιπλέον Τμήματος ή μαθητές στα όρια --}}        
    <nav class="navbar navbar-light bg-light">
    <table class="table table-bordered">
        <thead>
            <tr><td colspan="2">
                Γ) Σε περιπτώσεις που ο αριθμός προνηπίων/νηπίων για εγγραφή υπερβαίνει τους 25 μαθητές/τριες
                    ανά τμήμα ισχύουν τα όσα προβλέπονται στην περ. δ της παρ. 4 του άρθρου 6 του Π.Δ 79/2017 (Α΄ 109),
                    όπως τροποποιήθηκε με την παρ. 1 του άρθρου 44 του ν. 4777/2021 (Α΄ 25).<br><br>
                    Πιο συγκεκριμένα ο Διευθυντής/ντρια- Προϊστάμενος/η του Νηπιαγωγείου:<br>
                    α) υποβάλλει αίτημα στον/στην αρμόδιο/α Διευθυντή/ντρια Πρωτοβάθμιας Εκπαίδευσης για τη λειτουργία
                    περισσοτέρων τμημάτων, εφόσον υπάρχει στο σχολείο διαθέσιμη αίθουσα (υπόδειγμα 3) και<br>
                    β) αποστέλλει πίνακα με τα ονόματα των νηπίων-προνηπίων που διαμένουν στα όρια της σχολικής
                    περιφέρειας του Νηπιαγωγείου με τις σχολικές περιφέρειες όμορων Νηπιαγωγείων, καθώς και αν έχουν
                    αδέρφια που φοιτούν στο ίδιο Νηπιαγωγείο ή σε συστεγαζόμενο Νηπιαγωγείο ή στο συστεγαζόμενο
                    Δημοτικό Σχολείο (υπόδειγμα 4).     
            </td></tr>
            <tr>
                <th>Δημιουργία Επιπλέον Τμημάτων</th><th>Μαθητές που διαμένουν στα όρια</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if($school->primary == 1)
                        <form action=" {{route('enrollments.download_file',['file'=>"3_enrollments_extra_section_dim.docx", 'download_file_name' => "Αίτημα_δημιουργίας_επιπλέον_τμημ.docx"])}}" method="get"class="container-fluid">
                            <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Κατεβάστε το αρχείο"> Αίτημα Επιπλέον Τμήματος-Τμημάτων</button>
                        </form>
                    @else
                        <form action="{{route('enrollments.download_file',['file'=>"3_enrollments_extra_section_nip.docx", 'download_file_name' => "Αίτημα_δημιουργίας_επιπλέον_τμημ.docx"])}}" method="get"class="container-fluid">
                            <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Κατεβάστε το αρχείο"> Αίτημα Επιπλέον Τμήματος-Τμημάτων</button>                                
                        </form>
                    @endif
                    <p class="fw-lighter fst-italic fs-6"><small>Ενδεικτικό Πρότυπο Έγγραφο</small></p>
                </td>
                <td>
                    @if($school->primary == 1)
                        <form action=" {{route('enrollments.download_file',['file'=>"4_boundary_students_dim.xlsx", 'download_file_name' => "Μαθητές_στα_όρια.xlsx"])}}" method="get"class="container-fluid">
                            <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Κατεβάστε το αρχείο"> Μαθητές στα όρια</button>
                        </form>
                    @else
                        <form action=" {{route('enrollments.download_file',['file'=>"4_boundary_students_nip.xlsx", 'download_file_name' => "Μαθητές_στα_όρια.xlsx"])}}" method="get"class="container-fluid">
                            <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Κατεβάστε το αρχείο"> Μαθητές στα όρια</button>
                        </form>
                    @endif
                    <p class="fw-lighter fst-italic fs-6"><small>Ενδεικτικό Πρότυπο Έγγραφο</small></p>
                </td>
            </tr>
            <tr>
                <td>Υποβολή αιτήματος Δημιουργίας Επιπλέον Τμήματος/Τμημάτων
                    <p class="fw-lighter fst-italic fs-6"><small>(Δεκτά αρχεία μορφής .pdf)</small></p>
                </td>
                <td>Υποβολή αρχείου μαθητών που διαμένουν στα όρια
                    <p class="fw-lighter fst-italic fs-6"><small>(Δεκτά αρχεία μορφής .xlsx)</small></p>
                </td>
            </tr>
            <tr>
            <form action="{{route("enrollments.save", ["select"=>"extra_section"])}}" method="post" enctype="multipart/form-data" class="container-fluid">
                @csrf
                <td> <input name="file" type="file" class="form-control" required>
                    @if(!$accepts)
                        <div class='alert alert-warning text-center my-2'>
                            <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                        </div>
                    @else
                        <div class="input-group">
                            <span class="w-25"></span>
                            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            <a href="{{route("enrollments.create")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    @endif
                </td>
            </form>
            <form action="{{route("enrollments.save", ["select"=>"boundary_students"])}}" method="post" enctype="multipart/form-data" class="container-fluid">
                @csrf
                <td>
                    <input name="file" type="file" class="form-control" required>
                    @if(!$accepts)
                        <div class='alert alert-warning text-center my-2'>
                            <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                        </div>
                    @else
                        <div class="input-group">
                            <span class="w-25"></span>
                            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            <a href="{{route("enrollments.create")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    @endif
                </td>
            </form>
            </tr>
            @if($old_data && ($old_data->extra_section_file1 || $old_data->boundaries_st_file1))
            <tr>
            @if($old_data->extra_section_file1)
                <td>
                    @php
                    $file3 = $old_data->extra_section_file1;
                    @endphp
                    <form action="{{route('enrollments.download_file',['file'=>"enrollments3_$school_code.pdf", 'download_file_name' => $file3])}}" method="get"class="container-fluid">
                        <button class="btn btn-success bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Λήψη αρχείου που έχει υποβληθεί"> {{$file3}} </button>
                    </form> 
                </td>
            @else 
                <td></td>
            @endif
            @if($old_data->boundaries_st_file1)
                @php
                $file4 = $old_data->boundaries_st_file1;
                @endphp
                <td>
                <form action="{{route('enrollments.download_file',['file'=>"enrollments4_$school_code.xlsx", 'download_file_name' => $file4])}}" method="get"class="container-fluid">
                    <button class="btn btn-success bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Λήψη αρχείου που έχει υποβληθεί"> {{$file4}} </button>
                </form>
                </td>
            @else
                <td></td>
            @endif
            <tr>
        @endif
        </tbody>
        </table>
    </div>
            
</nav>
</div>    
</div>
</x-layout_school>