<x-layout_school>
  
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $school_code = $school->code;
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; 
        $app_name = $microapp->name;
        $school_year = config('enrollments.schoolYear');
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

    @push('styles')
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
            appearance: textfield;
        }
        td input.form-control {
            min-width: 80px;
        }
    </style>
    @endpush

<div class="container-xl my-4">
    <div class="alert alert-info text-center">
        Στην καρτέλα αυτή θα δηλωθούν τα στοιχεία <strong>εγγραφέντων</strong> μαθητών 
        <br>καθώς και τα στοιχεία για τον προγραμματισμό λειτουργίας του σχ. Έτους {{$school_year}}.
    </div>

    @if(config('enrollments.nextYearPlanningActive') == "1")
    <div class="alert alert-secondary text-center">
        <h5>Γενική Επισήμανση:</h5>
        Η αριστερή στήλη περιλαμβάνει τα στοιχεία που αφορούν στις <strong> εγγραφές </strong>. Η δεξιά στήλη περιλαμβάνει τα στοιχεία για τον <strong>προγραμματισμό </strong>του σχ. έτους.<br>
        Οι δύο στήλες συμπληρώνονται <strong>ανεξάρτητα</strong> μεταξύ τους.
    </div>
    <div class="alert alert-light border text-center">
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

    {{-- ΔΗΜΟΤΙΚΟ ΜΟΝΟ --}}
    @if($school->primary == 1 && config('enrollments.nextYearPlanningActive') == "1" && $school->public == 1)
    <div class="card mt-4 p-3 shadow-sm">
        <h3>Συνολικός αριθμός μαθητών</h3>
        <div class="table-responsive w-100">
            <table class="table table-bordered w-100 m-0">
                <thead>
                    <tr>
                        <th>Συνολικός αριθμός μαθητών που πρόκειται να φοιτήσουν το σχ. έτος {{$school_year}}</th>
                    </tr>
                </thead>
                <form action="{{route("enrollments.save", ['select'=>'total_students'])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <tbody>
                        <tr>
                            <td>
                                <input type="number" name="total_students_nr" class="form-control" required value="@if($old_data){{$old_data->total_students_nr}}@endif">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                @if(config('enrollments.nextYearPlanningAccepts') == "0")
                                    <div class='alert alert-warning text-center my-2'>
                                        <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                                    </div>
                                @else
                                    <button type="submit" class="btn btn-primary bi bi-plus-circle"> Υποβολή</button>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </form>
            </table>
        </div>
    </div>
    @endif

    {{-- ΣΤΟΙΧΕΙΑ ΕΓΓΡΑΦΩΝ --}}
    <div class="card mt-4 p-3 shadow-sm">
        <h3>Στοιχεία πρωινού προγράμματος @if($school->primary == 1) Δημοτικού Σχολείου @else Νηπιαγωγείου @endif</h3>
        <div class="table-responsive w-100">
            <table class="table table-bordered w-100 m-0">    
                <thead>
                    <tr>
                        <th style="width: 50%;">Στοιχεία</th>
                        <th style="width: 50%;">@if($school->primary == 1) Τάξη Α' @else Προνήπια / Νήπια @endif</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            Αρχείο για συμπλήρωση (Ενδεικτικό Υπόδειγμα)
                            <p class="fw-lighter fst-italic fs-6 mb-0"><small>(Το πρότυπο παρέχεται ενδεικτικά. Μπορείτε να ανεβάσετε οποιοδήποτε αρχείο excel.)</small></p>
                        </td>
                        <td>
                            @if($school->primary == 1)
                                <form action="{{route('enrollments.download_file', ['file' => '1_enrollments_primary_school.xlsx', 'download_file_name' => 'Εγγραφέντες.xlsx'])}}" method="get">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας </button>
                                </form>
                            @else
                                <form action="{{route('enrollments.download_file', ['file' => '1_enrollments_nursery_school.xlsx', 'download_file_name' => 'Εγγραφέντες.xlsx'])}}" method="get">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας Δ/νσης</button>
                                </form>
                            @endif  
                        </td>
                    </tr>
                    <tr>
                        <td>Αριθμός εγγεγραμμένων @if($school->primary == 1) μαθητών Α' Τάξης @else Νηπίων / Προνηπίων @endif {{$school_year}}</td>
                        <td>
                            <form action="{{route("enrollments.save", ['select'=>'enrolled'])}}" method="post" enctype="multipart/form-data" id="enrolledForm">
                                @csrf
                                <input name="nr_of_students1" id="nr_of_students1" type="number" class="form-control" required value="@if($old_data){{$old_data->nr_of_students1}}@endif" onchange="document.getElementById('hidden_students_count').value = this.value;">
                            </form>
                        </td>  
                    </tr>
                    <tr>
                        <td>
                            Αρχείο Εγγραφέντων Μαθητών
                            <p class="fw-lighter fst-italic fs-6 mb-0"><small>(Δεκτά αρχεία μορφής .xlsx)</small></p>
                        </td>
                        <td>
                            <form action="{{route("enrollments.save", ['select'=>'enrolled'])}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="nr_of_students1" id="hidden_students_count" value="@if($old_data){{$old_data->nr_of_students1}}@endif">
                                <input name="file" type="file" class="form-control" {{$required}}>
                                @if(!$accepts)
                                    <div class='alert alert-warning text-center my-2'>
                                        <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary bi bi-plus-circle"> Υποβολή</button>
                                        <a href="{{route('enrollments.create')}}" class="btn btn-outline-secondary">Ακύρωση</a>
                                    </div>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @if($old_data && $old_data->enrolled_file1)
                    <tr>
                        <td> 
                            Αρχείο που έχει υποβληθεί: 
                            <p class="fw-lighter fst-italic fs-6 mb-0"><small>(Τελευταία υποβολή: {{$old_data->updated_at}})</small></p>
                        </td>
                        @php $file1 = $old_data->enrolled_file1; @endphp
                        <td>
                            <form action="{{route('enrollments.download_file',['file'=>"enrollments1_$school_code.xlsx", 'download_file_name' => $file1])}}" method="get">
                                <button class="btn btn-success bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Λήψη αρχείου που έχει υποβληθεί"> {{$file1}} </button>
                            </form>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- ΣΤΟΙΧΕΙΑ ΠΡΟΓΡΑΜΜΑΤΙΣΜΟΥ --}}
    @if($old_data)
    @if(($school->primary ==1 && $old_data->total_students_nr && config('enrollments.nextYearPlanningActive') == 1) || ($school->primary == 0 && $old_data->nr_of_students1 && config('enrollments.nextYearPlanningActive') == 1))
    @php
        $total_st_number = ($school->primary ==1)? $old_data->total_students_nr : $old_data->nr_of_students1;
        $nextYearLeitourgikotita = App\Http\Controllers\microapps\EnrollmentController::nextYearsLeitourgikotita($school->primary, $school->leitourgikotita, $total_st_number);
        if($school->code=='9060295' || $school->code=='9060336'|| $school->code=='9060406') $nextYearLeitourgikotita = 4;
        $max_class_numbers = ($nextYearLeitourgikotita >= 6) ? 6 : $nextYearLeitourgikotita;
        $enrollments_classes = App\Models\microapps\EnrollmentsClasses::where('enrollment_id', $old_data->id)->first();
        if($enrollments_classes){
            $morning_classes_json = $enrollments_classes->morning_classes;
            $morning_classes = json_decode($morning_classes_json);
        }
    @endphp
    <div class="card mt-4 p-3 shadow-sm">
        <div class="table-responsive w-100">
            <table class="table table-bordered w-100 m-0">
                <thead>
                    <tr>
                        <th></th>
                        <th colspan="{{$max_class_numbers}}">
                            <h4 class="mb-0">Στοιχεία προγραμματισμού λειτουργίας του σχ. έτους {{$school_year}}</h4>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Τάξη</td>
                        @if($nextYearLeitourgikotita == 1)
                            <td>@if($school->primary == 1) Α'-Β'-Γ'-Δ'-Ε'-Στ' @else Τμήμα Νηπιαγωγείου @endif</td>
                        @elseif($nextYearLeitourgikotita == 2)
                            <td>Τμήμα 1</td><td>Τμήμα 2</td>
                        @elseif($nextYearLeitourgikotita == 3)
                            <td>Τμήμα 1</td><td>Τμήμα 2</td><td>Τμήμα 3</td>
                        @elseif($nextYearLeitourgikotita == 4)
                            <td>Τμήμα 1</td><td>Τμήμα 2</td><td>Τμήμα 3</td><td>Τμήμα 4</td>
                        @elseif($nextYearLeitourgikotita == 5)
                            <td>Τμήμα 1</td><td>Τμήμα 2</td><td>Τμήμα 3</td><td>Τμήμα 4</td><td>Τμήμα 5</td>
                        @else
                            <td>Α'</td><td>Β'</td><td>Γ'</td><td>Δ'</td><td>Ε'</td><td>Στ'</td>
                        @endif
                    </tr>
                    <form action="{{route("enrollments.save", ['select'=>'nextYearNumbers'])}}" method="post">
                        @csrf
                        <tr>
                            <td>Αριθμός μαθητών</td>
                            @php
                            if($nextYearLeitourgikotita >= 6){
                                $nr_of_st = isset($morning_classes[0]->nr_of_students) ? $morning_classes[0]->nr_of_students : $old_data->nr_of_students1;
                            }else{
                                if(isset($morning_classes[0]->nr_of_students)){ $nr_of_st = $morning_classes[0]->nr_of_students; }
                                else { $nr_of_st = ($nextYearLeitourgikotita == 1) ? (($school->primary == 1) ? $old_data->total_students_nr : $old_data->nr_of_students1) : 0; }
                            }
                            @endphp
                            <td>
                                <input name="nr_of_students1" id="nr_of_students1" type="text" class="form-control" required value="{{$nr_of_st}}" pattern="\d*" >
                                @if($nextYearLeitourgikotita == 1 && !$enrollments_classes)
                                <small style="display: block; font-size: 1rem; font-weight: bold; text-align: center; background-color: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 12px 16px; margin: 16px 0; color: #856404; animation: pulse 1.5s ease infinite; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                ⚠️ Παρακαλούμε <span style="color: #dc3545; font-size: 1.05rem;">πατήστε πάλι</span> Υποβολή!!! Είναι απαραίτητο για τον υπολογισμό του Τμήματος / των Τμημάτων. ⚠️
                                </small>
                                <style>
                                @keyframes pulse {
                                    0% { background-color: #fff3cd; border-color: #ffc107; }
                                    50% { background-color: #ffe69b; border-color: #ffc107; transform: scale(1.01); }
                                    100% { background-color: #fff3cd; border-color: #ffc107; }
                                }
                                </style>
                                @endif
                            </td>
                            @for($i=2; $i<=$max_class_numbers; $i++)
                                @php $nr_of_st = isset($morning_classes[$i-1]->nr_of_students) ? $morning_classes[$i-1]->nr_of_students : 0; @endphp
                                <td>
                                    <input name="nr_of_students{{$i}}" id="nr_of_students{{$i}}" type="text" class="form-control" required value="{{$nr_of_st}}" pattern="\d*" >
                                </td>
                            @endfor
                        </tr>
                        @if($enrollments_classes)
                        <tr>
                            <td>
                                Αριθμός τμημάτων <br><small class="text-muted">αυτόματος υπολογισμός (Μετά την υποβολή). <strong>Δεν επιτρέπεται η τροποποίηση.</strong></small>
                            </td>
                            @if($school->special_needs == 0)
                                @if($nextYearLeitourgikotita >= 6)
                                    @for($i=1; $i<=6; $i++)
                                        @php $nr_of_sec = isset($morning_classes[$i-1]->nr_of_sections) ? $morning_classes[$i-1]->nr_of_sections : '1'; @endphp
                                        <td><input name="nr_of_sections{{$i}}" id="nr_of_sections{{$i}}" type="text" class="form-control" value="{{$nr_of_sec}}" readonly></td>
                                    @endfor
                                @else
                                    @for($i=1; $i<=$nextYearLeitourgikotita; $i++)
                                        <td><input name="nr_of_sections{{$i}}" id="nr_of_sections{{$i}}" type="text" class="form-control" value="1" readonly></td>
                                    @endfor
                                @endif
                            @else
                                @for($i=1; $i<=$max_class_numbers; $i++)
                                    @php $nr_of_sec = isset($morning_classes[$i-1]->nr_of_sections) ? $morning_classes[$i-1]->nr_of_sections : '1'; @endphp
                                    <td><input name="nr_of_sections{{$i}}" id="nr_of_sections{{$i}}" type="text" class="form-control" value="{{$nr_of_sec}}"></td>
                                @endfor
                            @endif
                        </tr>
                        @if($school->special_needs == 0)
                        <tr>
                            <td>
                                <div style="font-weight: 500; font-size: 1rem; color: #832727;">Αριθμός Μαθητών για Τμήμα Ένταξης</div>
                                <div class="text-muted" style="font-size: 0.8rem;">(Αλλιώς αφήστε κενό ή 0)</div>
                            </td>
                            <td colspan="{{$max_class_numbers}}">
                                <input name="integration_class_students" id="integration_class_students" type="number" class="form-control" value="@if(isset($enrollments_classes->integration_class_students)){{$enrollments_classes->integration_class_students}}@endif" min="0">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 500; font-size: 1rem; color: #832727;">Αριθμός Μαθητών για Παράλληλη Στήριξη</div>
                            </td>
                            @if($nextYearLeitourgikotita >= 6)
                                @for($i=1; $i<=$max_class_numbers; $i++)
                                    @php $par = isset($morning_classes[$i-1]->parallel_support_students) ? $morning_classes[$i-1]->parallel_support_students : ''; @endphp
                                    <td><input name="parallel_support_students{{$i}}" type="text" class="form-control" value="{{$par}}" pattern="\d*"></td>
                                @endfor
                            @else
                                <td colspan="{{$max_class_numbers}}">
                                    <input name="parallel_support_students1" type="text" class="form-control" value="@if(isset($morning_classes[0]->parallel_support_students)){{$morning_classes[0]->parallel_support_students}}@endif" pattern="\d*">
                                </td>
                            @endif
                        </tr>
                        @endif
                        <tr>
                            <td>Παρατήρηση <br><small class="text-muted">(π.χ. αίτημα για επιπλέον τμήμα)</small></td>
                            @for($i=1; $i<=$max_class_numbers; $i++)
                                @php $com = isset($morning_classes[$i-1]->comment) ? $morning_classes[$i-1]->comment : ''; @endphp
                                <td><input name="comment{{$i}}" id="comment{{$i}}" type="text" class="form-control" value="{{$com}}"></td>
                            @endfor
                        </tr>
                        @if(collect(range(1, $max_class_numbers))->first(fn($i) => !empty($morning_classes[$i-1]->comment ?? '')))
                        <tr class="table-info">
                            <td>
                                <i class="bi bi-chat-left-text"></i> Καταχωρημένες Παρατηρήσεις
                            </td>
                            @for($i=1; $i<=$max_class_numbers; $i++)
                                @php $com = isset($morning_classes[$i-1]->comment) ? $morning_classes[$i-1]->comment : ''; @endphp
                                <td>
                                    @if($com)
                                        <p class="mb-0 fst-italic text-primary"><i class="bi bi-quote"></i> {{$com}}</p>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                        @endif
                        @endif
                        <tr>
                            <td colspan="{{$max_class_numbers + 1}}">
                                @if(config('enrollments.nextYearPlanningAccepts') == 0 || !$old_data)
                                    <div class='alert alert-warning text-center my-2'>
                                        <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                                    </div>
                                @else
                                    <button type="submit" class="btn btn-primary bi bi-plus-circle"> Υποβολή</button>
                                @endif
                            </td>
                        </tr>
                    </form>
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif

    {{-- ΣΤΟΙΧΕΙΑ ΕΓΓΡΑΦΗΣ ΣΤΟ ΟΛΟΗΜΕΡΟ --}}
    <div class="card mt-4 p-3 shadow-sm">
        <h3>Στοιχεία εγγραφή στο Ολοήμερο @if($school->primary == 1) πρόγραμμα. @else πρόγραμμα του Νηπιαγωγείου. @endif</h3>  
        <div class="table-responsive w-100">
            <table class="table table-bordered w-100 m-0">
                <thead>
                    <tr>
                        <th style="width: 50%;">Στοιχεία</th>
                        <th style="width: 50%;">@if($school->primary == 1) Μαθητές Ολοήμερου @else Προνήπια / Νήπια @endif</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            Αρχείο για συμπλήρωση (Ενδεικτικό Υπόδειγμα)
                            <p class="fw-lighter fst-italic fs-6 mb-0"><small>(Δεκτά αρχεία μορφής .xlsx)</small></p>
                        </td>
                        <td> 
                            @if($school->primary == 1)
                                <form action="{{route('enrollments.download_file',['file' => $school->has_extended_all_day == 1 ? '2_enrollments_primary_ext_all_day_school.xlsx' : '2_enrollments_primary_all_day_school.xlsx', 'download_file_name' => 'Ολοήμερο_'.$school_year.'.xlsx'])}}" method="get">
                                    <button class="btn btn-secondary bi bi-box-arrow-down"> Πίνακας </button>
                                </form>
                            @else
                                <form action="{{route('enrollments.download_file',['file' => $school->has_extended_all_day == 1 ? '2_enrollments_nursery_ext_all_day_school.xlsx' : '2_enrollments_nursery_all_day_school.xlsx', 'download_file_name' => 'Ολοήμερο_'.$school_year.'.xlsx'])}}" method="get">
                                    <button class="btn btn-secondary bi bi-box-arrow-down"> Πίνακας Δ/νσης</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    <form action="{{route('enrollments.save', ['select'=>'all_day'])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <tr>
                            <td>Αριθμός εγγεγραμμένων @if($school->primary == 1) μαθητών @else Νηπίων / Προνηπίων @endif στο Ολοήμερο {{$school_year}}</td>
                            <td>
                                <input name="nr_of_students1_all_day1" id="nr_of_students1_all_day1" type="number" class="form-control" required value="@if($old_data){{$old_data->nr_of_students1_all_day1}}@endif">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Υποβολή Αρχείου @if($school->primary == 1) μαθητών @else Νηπίων / Προνηπίων @endif στο Ολοήμερο
                                <p class="fw-lighter fst-italic fs-6 mb-0"><small>(Δεκτά αρχεία μορφής .xlsx)</small></p>
                            </td>
                            <td>
                                <input name="file" type="file" class="form-control" {{$required}}>
                                @if(!$accepts)
                                    <div class='alert alert-warning text-center my-2'>
                                        <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές </strong>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary bi bi-plus-circle"> Υποβολή</button>
                                        <a href="{{route('enrollments.create')}}" class="btn btn-outline-secondary">Ακύρωση</a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </form>
                    @if($old_data && $old_data->all_day_file1)
                    <tr>
                        <td> 
                            Αρχείο που έχει υποβληθεί: 
                            <p class="fw-lighter fst-italic fs-6 mb-0"><small>(Τελευταία υποβολή: {{$old_data->updated_at}})</small></p>
                        </td>
                        @php $file2 = $old_data->all_day_file1; @endphp
                        <td>
                            <form action="{{route('enrollments.download_file',['file'=>"enrollments2_$school_code.xlsx", 'download_file_name' => $file2])}}" method="get">
                                <button class="btn btn-success bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Λήψη αρχείου που έχει υποβληθεί"> {{$file2}} </button>
                            </form>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Προγραμματισμός Λειτουργίας Ολοήμερου --}}
    @if(config('enrollments.nextYearPlanningActive') == 1)
    <div class="card mt-4 p-3 shadow-sm">
        <div class="table-responsive w-100">
            <table class="table table-bordered w-100 m-0">
                @if($school->primary == 1)
                <thead>
                    <tr><th colspan="2"><h4>Προγραμματισμός Λειτουργίας Ολοήμερου</h4></th></tr>   
                </thead>
                <tbody>
                    <tr>
                        <td style="width: 50%;">
                            Αρχείο για συμπλήρωση
                            <p class="fw-lighter fst-italic fs-6 mb-0"><small>(Να συμπληρωθεί υποχρεωτικά το παρεχόμενο πρότυπο)</small></p>
                        </td>
                        <td style="width: 50%;"> 
                            Αρχείο Α1 & Α2 του ΥΠΑΙΘΑ
                            <form action="{{route('enrollments.download_file',['file'=>'5_next_year_planning_all_day_school.xlsx', 'download_file_name' => 'Ολοήμερο_Προγραμματισμός_'.$school_year.'.xlsx'])}}" method="get" class="mb-2">
                                <button class="btn btn-secondary bi bi-box-arrow-down"> Πίνακας </button>
                            </form>
                            <small class="text-muted"><em>Κατά τη συμπλήρωση του αρχείου, το πεδίο <strong> Σύνολο Μαθητών Ολοήμερου</strong> συμπληρώνεται από το άθροισμα των μαθητών που αποχωρούν στις επόμενες στήλες.</em></small>
                        </td>
                    </tr>
                    <form action="{{route("enrollments.save", ['select'=>'all_day_next_year_planning'])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <tr>
                            <td>
                                Υποβολή Αρχείου προγραμματισμού Ολοήμερου Προγράμματος
                                <p class="fw-lighter fst-italic fs-6 mb-0"><small>(Δεκτά αρχεία μορφής .xlsx)</small></p>
                            </td>
                            <td>
                                <input name="file" type="file" class="form-control" {{$required}}>
                                @if(config('enrollments.nextYearPlanningAccepts') == 0)
                                    <div class='alert alert-warning text-center my-2'>
                                        <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές </strong>
                                    </div>
                                @else
                                    <button type="submit" class="btn btn-primary mt-2 bi bi-plus-circle"> Υποβολή</button>
                                @endif
                            </td>
                        </tr>
                    </form>
                    @if($old_data && $old_data->a1_a2_file)
                    <tr>
                        <td> 
                            Αρχείο που έχει υποβληθεί: 
                            <p class="fw-lighter fst-italic fs-6 mb-0"><small>(Τελευταία υποβολή: {{$old_data->updated_at}})</small></p>
                        </td>
                        @php $file3 = $old_data->a1_a2_file; @endphp
                        <td>
                            <form action="{{route('enrollments.download_file',['file'=>"a1_a2_file_$school_code.xlsx", 'download_file_name' => $file3])}}" method="get">
                                <button class="btn btn-success bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Λήψη αρχείου που έχει υποβληθεί"> {{$file3}} </button>
                            </form>
                        </td>
                    </tr>
                    @endif
                </tbody>
                @else
                {{-- ΝΗΠΙΑΓΩΓΕΙΑ --}}
                @php
                    $morning_zone_classes = isset($enrollments_classes->morning_zone_classes) ? json_decode($enrollments_classes->morning_zone_classes) : [];
                    $all_day_school_classes = isset($enrollments_classes->all_day_school_classes) ? json_decode($enrollments_classes->all_day_school_classes) : [];
                @endphp
                <thead>
                    <tr>
                        <th>Στοιχεία προγραμματισμού λειτουργίας Ολοήμερου για το σχ. έτος {{$school_year}}</th>
                        <th>Πρόωρη Υποδοχή</th>
                        <th>Ολοήμερο Πρόγραμμα (έως 16:00)</th>
                        <th>Διευρυμένο Ολοήμερο (έως 17:30)</th>
                    </tr>
                </thead>
                <form action="{{route("enrollments.save", ['select'=>'all_day_next_year_planning'])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <tbody>
                        <tr>
                            <td>Αρ. Μαθητών</td>
                            <td><input name="nr_of_students_morning_zone" type="number" class="form-control" required value="@if(isset($morning_zone_classes[0])){{$morning_zone_classes[0]->nr_of_students}}@endif"></td>
                            <td><input name="nr_of_students_all_day" type="number" class="form-control" required value="@if(isset($all_day_school_classes[0])){{$all_day_school_classes[0]->nr_of_students}}@endif"></td>
                            <td><input name="nr_of_students_extended_all_day" type="number" class="form-control" required value="@if(isset($all_day_school_classes[1])){{$all_day_school_classes[1]->nr_of_students}}@endif"></td>
                        </tr>
                        <tr>
                            <td>Αριθμός Τμημάτων</td>
                            <td>
                                <select name="nr_of_sections_morning_zone" class="form-control" required>
                                    <option value="0" @if(count($morning_zone_classes) && $morning_zone_classes[0]->nr_of_sections == 0) selected @endif >Δε θα λειτουργήσει</option>
                                    <option value="1" @if(count($morning_zone_classes) && $morning_zone_classes[0]->nr_of_sections == 1) selected @endif >1</option>
                                    <option value="2" @if(count($morning_zone_classes) && $morning_zone_classes[0]->nr_of_sections == 2) selected @endif >2</option>
                                </select>
                            </td>
                            <td>
                                <select name="nr_of_sections_all_day" class="form-control" required>
                                    <option value="0" @if(count($all_day_school_classes) && $all_day_school_classes[0]->nr_of_sections == 0) selected @endif>Δε θα λειτουργήσει</option>
                                    <option value="1" @if(count($all_day_school_classes) && $all_day_school_classes[0]->nr_of_sections == 1) selected @endif>1</option>
                                    <option value="2" @if(count($all_day_school_classes) && $all_day_school_classes[0]->nr_of_sections == 2) selected @endif>2</option>
                                </select>
                            </td>
                            <td>
                                <select name="nr_of_sections_extended_all_day" class="form-control" required>
                                    <option value="0" @if($all_day_school_classes && $all_day_school_classes[1]->nr_of_sections == 0) selected @endif>Δε θα λειτουργήσει</option>
                                    <option value="1" @if($all_day_school_classes && $all_day_school_classes[1]->nr_of_sections == 1) selected @endif>1</option>
                                    <option value="2" @if($all_day_school_classes && $all_day_school_classes[1]->nr_of_sections == 2) selected @endif>2</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                @if(config('enrollments.nextYearPlanningAccepts') == 0)
                                <div class='alert alert-warning text-center my-2'>
                                    <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές </strong>
                                </div>
                            @else
                                <button type="submit" class="btn btn-primary bi bi-plus-circle"> Υποβολή</button>
                            @endif
                            </td>
                        </tr>
                    </tbody>
                </form>
                @endif
            </table>
        </div>
    </div>
    @endif

    {{-- ΔΗΜΙΟΥΡΓΙΑ ΕΠΙΠΛΕΟΝ ΤΜΗΜΑΤΟΣ --}}
    <div class="card mt-4 p-3 shadow-sm">
        <h3>Δημιουργία επιπλέον Τμήματος / Κατανομή μαθητών σε όμορα σχολεία</h3>
        <div class="table-responsive w-100">
            <table class="table table-bordered w-100 m-0">
                <thead>
                    <tr>
                        <td colspan="2" class="bg-light">
                            Γ) Σε περιπτώσεις που ο αριθμός προνηπίων/νηπίων για εγγραφή υπερβαίνει τους 25 μαθητές/τριες ανά τμήμα ισχύουν τα όσα προβλέπονται στην περ. δ της παρ. 4 του άρθρου 6 του Π.Δ 79/2017 (Α΄ 109), όπως τροποποιήθηκε με την παρ. 1 του άρθρου 44 του ν. 4777/2021 (Α΄ 25).<br><br>
                            Πιο συγκεκριμένα ο Διευθυντής/ντρια- Προϊστάμενος/η του Νηπιαγωγείου:<br>
                            α) υποβάλλει αίτημα στον/στην αρμόδιο/α Διευθυντή/ντρια Πρωτοβάθμιας Εκπαίδευσης για τη λειτουργία περισσότερων τμημάτων, εφόσον υπάρχει στο σχολείο διαθέσιμη αίθουσα (υπόδειγμα 3) και<br>
                            β) αποστέλλει πίνακα με τα ονόματα των νηπίων-προνηπίων που διαμένουν στα όρια της σχολικής περιφέρειας του Νηπιαγωγείου με τις σχολικές περιφέρειες όμορων Νηπιαγωγείων, καθώς και αν έχουν αδέρφια που φοιτούν στο ίδιο Νηπιαγωγείο ή σε συστεγαζόμενο Νηπιαγωγείο ή στο συστεγαζόμενο Δημοτικό Σχολείο (υπόδειγμα 4).     
                        </td>
                    </tr>
                    <tr>
                        <th style="width: 50%;">Δημιουργία Επιπλέον Τμημάτων</th>
                        <th style="width: 50%;">Μαθητές που διαμένουν στα όρια</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <form action="{{ route('enrollments.download_file', ['file' => $school->primary == 1 ? '3_enrollments_extra_section_dim.docx' : '3_enrollments_extra_section_nip.docx', 'download_file_name' => 'Αίτημα_δημιουργίας_επιπλέον_τμημ.docx']) }}" method="get">
                                <button class="btn btn-secondary bi bi-box-arrow-down"> Αίτημα Επιπλέον Τμήματος</button>
                            </form>
                            <p class="fw-lighter fst-italic fs-6 mb-0"><small>Ενδεικτικό Πρότυπο Έγγραφο</small></p>
                        </td>
                        <td>
                            <form action="{{ route('enrollments.download_file', ['file' => $school->primary == 1 ? '4_boundary_students_dim.xlsx' : '4_boundary_students_nip.xlsx', 'download_file_name' => 'Μαθητές_στα_όρια.xlsx']) }}" method="get">
                                <button class="btn btn-secondary bi bi-box-arrow-down"> Μαθητές στα όρια</button>
                            </form>
                            <p class="fw-lighter fst-italic fs-6 mb-0"><small>Ενδεικτικό Πρότυπο Έγγραφο</small></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Υποβολή αιτήματος Δημιουργίας Επιπλέον Τμήματος
                            <p class="fw-lighter fst-italic fs-6 mb-0"><small>(Δεκτά αρχεία μορφής .pdf)</small></p>
                        </td>
                        <td>Υποβολή αρχείου μαθητών που διαμένουν στα όρια
                            <p class="fw-lighter fst-italic fs-6 mb-0"><small>(Δεκτά αρχεία μορφής .xlsx)</small></p>
                        </td>
                    </tr>
                    <tr>
                        <td> 
                            <form action="{{route("enrollments.save", ["select"=>"extra_section"])}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input name="file" type="file" class="form-control" required>
                                @if(!$accepts)
                                    <div class='alert alert-warning text-center my-2'>
                                        <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary bi bi-plus-circle"> Υποβολή</button>
                                        <a href="{{route("enrollments.create")}}" class="btn btn-outline-secondary">Ακύρωση</a>
                                    </div>
                                @endif
                            </form>
                        </td>
                        <td>
                            <form action="{{route("enrollments.save", ["select"=>"boundary_students"])}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input name="file" type="file" class="form-control" required>
                                @if(!$accepts)
                                    <div class='alert alert-warning text-center my-2'>
                                        <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary bi bi-plus-circle"> Υποβολή</button>
                                        <a href="{{route("enrollments.create")}}" class="btn btn-outline-secondary">Ακύρωση</a>
                                    </div>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @if($old_data && ($old_data->extra_section_file1 || $old_data->boundaries_st_file1))
                    <tr>
                        <td>
                            @if($old_data->extra_section_file1)
                                @php $file3 = $old_data->extra_section_file1; @endphp
                                <form action="{{route('enrollments.download_file',['file'=>"enrollments3_$school_code.pdf", 'download_file_name' => $file3])}}" method="get">
                                    <button class="btn btn-success bi bi-box-arrow-down"> {{$file3}} </button>
                                </form> 
                            @endif
                        </td>
                        <td>
                            @if($old_data->boundaries_st_file1)
                                @php $file4 = $old_data->boundaries_st_file1; @endphp
                                <form action="{{route('enrollments.download_file',['file'=>"enrollments4_$school_code.xlsx", 'download_file_name' => $file4])}}" method="get">
                                    <button class="btn btn-success bi bi-box-arrow-down"> {{$file4}} </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>    

<!-- Layout Update Notification Modal -->
<div class="modal fade" id="layoutUpdateModal" tabindex="-1" aria-labelledby="layoutUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="layoutUpdateModalLabel">
                    <i class="bi bi-info-circle-fill me-2"></i> Αλλαγή στην Εμφάνιση της Σελίδας!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3 text-primary">
                    <i class="bi bi-window-sidebar" style="font-size: 3rem;"></i>
                </div>
                <h5 class="text-center fw-bold mb-3">Ανανέωση της Εμφάνισης της Σελίδας!</h5>
                <p class="text-muted">
                    Προχωρήσαμε σε αναβάθμιση της εμφάνισης της πλατφόρμας λόγω αύξησης των στοιχείων που περιλαμβάνονται. 
                    Οι πίνακες καταχώρησης έχουν πλέον <strong>πλήρες πλάτος</strong> και δεν συμπιέζονται, ώστε να μπορείτε να βλέπετε και να συμπληρώνετε τα στοιχεία με μεγαλύτερη άνεση και σαφήνεια.
                </p>
                <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
                    <i class="bi bi-lightning-charge-fill me-2 fs-5"></i>
                    <div>
                        Όλες οι λειτουργίες και οι φόρμες υποβολής παραμένουν ακριβώς οι ίδιες!
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light justify-content-center">
                <button type="button" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm" data-bs-dismiss="modal">
                    ΟΚ
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Check if the user has already acknowledged the new layout change
        if (!localStorage.getItem('layout_updated_noticed_2026')) {
            // Initialize and show the Bootstrap modal
            var myModal = new bootstrap.Modal(document.getElementById('layoutUpdateModal'), {
                keyboard: false,
                backdrop: 'static' // Optional: forces user to click the button to dismiss
            });
            myModal.show();

            // Set the flag so it won't show up on subsequent page loads/refreshes
            localStorage.setItem('layout_updated_noticed_2026', 'true');
        }
    });
</script>
@endpush

</x-layout_school>