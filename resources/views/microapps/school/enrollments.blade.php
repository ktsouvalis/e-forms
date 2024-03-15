<x-layout_school>
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $school_code = $school->code;
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; 
        $app_name = $microapp->name;
        if($school->enrollments){
            $old_data = $school->enrollments;
        }else{
            $old_data = null;
        }
    @endphp
    
    @push('title')
        <title>{{$app_name}}</title>
    @endpush
<div class="container">
    <div class="container px-5">
        <div class="alert alert-info text-center">
            Στην καρτέλα αυτή θα δηλωθούν τα στοιχεία <strong>εγγραφέντων</strong> μαθητών για το σχ. έτος 2024-25.
            <br>Σε επόμενο χρόνο, στην ίδια καρτέλα, θα δηλωθούν τα στοιχεία για τον προγραμματισμό λειτουργίας του σχ. Έτους 2024-25.
        </div>     
        
            <div class="container mt-5">
                <h3>Στοιχεία εγγραφής μαθητών @if($school->primary == 1) στην Α' Τάξη @else στο Νηπιαγωγείο @endif</h3>  {{-- Εγγραφέντες στην Α' / Εγγραφέντες στο Νηπιαγωγείο --}}
                <nav class="navbar navbar-light bg-light">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Στοιχεία</th>
                            <th>@if($school->primary == 1) Τάξη Α' @else Προνήπια / Νήπια @endif</th>
                            {{-- <th>Τάξη Β'</th>
                            <th>Τάξη Γ'</th>
                            <th>Τάξη Δ'</th>
                            <th>Τάξη Ε'</th>
                            <th>Τάξη Στ'</th>
                            <th>Σύνολο</th>  --}}
                        </tr>
                    </thead>
                    <tbody>
                        {{-- <tr>
                            <td>Μαθητές 2023-24</td><td>20</td><td>50</td><td>25</td><td>26</td><td>28</td><td>5</td><td>150</td> 
                        </tr> --}}
                        <tr>
                            <td>
                                Αρχείο για συμπλήρωση (Ενδεικτικό Υπόδειγμα)
                            <p class="fw-lighter fst-italic fs-6"><small>(Το πρότυπο παρέχεται ενδεικτικά. Μπορείτε να ανεβάσετε οποιοδήποτε αρχείο excel.)</small></p>
                            </td>
                            <td>
                                @if($school->primary == 1)
                                    <form action="{{url("/school_app/$appname/1_enrollments_primary_school.xlsx/Εγγραφέντες.xlsx")}}" method="get"class="container-fluid">
                                        <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας </button>
                                    </form>
                                @else
                                    <form action="{{url("/school_app/$appname/1_enrollments_nursery_school.xlsx/Εγγραφέντες.xlsx")}}" method="get"class="container-fluid">
                                        <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας Δ/νσης</button>
                                    </form>
                                @endif  
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <form action="{{url("/school_app/enrollments/enrolled")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                                @csrf
                            <td>Αριθμός εγγεγραμμένων μαθητών Α' Τάξης 2024-25</td>
                            <td>
                                <input name="nr_of_students1" id="nr_of_students1" type="number" class="form-control input-sm" required value="@if($old_data){{$old_data->nr_of_students1}}@endif">
                            </td>
                            {{-- </td><td>50</td><td>25</td><td>26</td><td>28</td><td>5</td><td>150</td>  --}}
                        </tr>
                        
                        <tr>
                            <td>
                                Αρχείο Εγγραφέντων Μαθητών
                                <p class="fw-lighter fst-italic fs-6"><small>(Δεκτά αρχεία μορφής .xlsx)</small></p>
                            </td>
                            <td>
                                <input name="file" type="file" class="form-control">
                                @if(!$accepts)
                                    <div class='alert alert-warning text-center my-2'>
                                        <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                                    </div>
                                @else
                                    <div class="input-group">
                                        <span class="w-25"></span>
                                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                        <a href="{{url("/school_app/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
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
                            <form action="{{url("/school_app/enrollments/enrollments1_$school_code.xlsx/$file1")}}" method="get"class="container-fluid">
                                <button class="btn btn-success bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Λήψη αρχείου που έχει υποβληθεί"> {{$file1}} </button>
                            </form>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </nav>
            
            <h3>Στοιχεία εγγραφής στο Ολοήμερο @if($school->primary == 1) (μαθητές Α' Τάξης) @else πρόγραμμα του Νηπιαγωγείου @endif</h3>  {{-- ΔΗΜΟΤΙΚΑ:  / ΝΗΠΙΑΓΩΓΕΙΑ: Εγγραφέντες στο Ολοήμερο --}}     
            <nav class="navbar navbar-light bg-light">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Στοιχεία</th>
                        <th>@if($school->primary == 1) Τάξη Α' @else Προνήπια / Νήπια @endif</th>
                        {{-- <th>Τάξη Β'</th>
                        <th>Τάξη Γ'</th>
                        <th>Τάξη Δ'</th>
                        <th>Τάξη Ε'</th>
                        <th>Τάξη Στ'</th>
                        <th>Σύνολο</th>  --}}
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
                                <form action="{{url("/school_app/$appname/2_enrollments_primary_ext_all_day_school.xlsx/Ολοήμερο_A_Τάξη.xlsx")}}" method="get"class="container-fluid">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας </button>
                                </form>
                                @else
                                <form action="{{url("/school_app/$appname/2_enrollments_primary_all_day_school.xlsx/Ολοήμερο_A_Τάξη.xlsx")}}" method="get"class="container-fluid">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας </button>
                                </form>
                                @endif
                            @else
                                @if($school->has_extended_all_day == 1)
                                <form action="{{url("/school_app/$appname/2_enrollments_nursery_ext_all_day_school.xlsx/Ολοήμερο.xlsx")}}" method="get"class="container-fluid">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας Δ/νσης</button>
                                </form>
                                @else
                                <form action="{{url("/school_app/$appname/2_enrollments_nursery_all_day_school.xlsx/Ολοήμερο.xlsx")}}" method="get"class="container-fluid">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας Δ/νσης</button>
                                </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                    <form action="{{url("/school_app/$appname/all_day")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                    <tr>
                        <td>Αριθμός εγγεγραμμένων μαθητών @if($school->primary == 1) Α' Τάξης @else - Νηπίων/Προνηπίων - @endif στο Ολοήμερο 2024-25</td>
                        <td>
                            <input name="nr_of_students1_all_day1" id="nr_of_students1_all_day1" type="number" class="form-control input-sm" required value="@if($old_data){{$old_data->nr_of_students1_all_day1}}@endif">
                        </td>
                        {{-- </td><td>50</td><td>25</td><td>26</td><td>28</td><td>5</td><td>150</td>  --}}
                    </tr>
                    
                    <tr>
                        <td>
                            Υποβολή Αρχείου μαθητών της Α' Τάξης στο Ολοήμερο
                            <p class="fw-lighter fst-italic fs-6"><small>(Δεκτά αρχεία μορφής .xlsx)</small></p>
                        </td>
                        <td>
                            <input name="file" type="file" class="form-control">
                            @if(!$accepts)
                                <div class='alert alert-warning text-center my-2'>
                                    <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές </strong>
                                </div>
                            @else
                                <div class="input-group">
                                    <span class="w-25"></span>
                                    <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                    <a href="{{url("/school_app/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
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
                        <form action="{{url("/school_app/enrollments/enrollments1_$school_code.xlsx/$file2")}}" method="get"class="container-fluid">
                            <button class="btn btn-success bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Λήψη αρχείου που έχει υποβληθεί"> {{$file2}} </button>
                        </form>
                        </td><tr>
                    @endif
                </tbody>
            </table>
        </form>
    </nav>
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
                
                {{-- <th>Τάξη Β'</th>
                <th>Τάξη Γ'</th>
                <th>Τάξη Δ'</th>
                <th>Τάξη Ε'</th>
                <th>Τάξη Στ'</th>
                <th>Σύνολο</th>  --}}
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if($school->primary == 1)
                        <form action="{{url("/school_app/$appname/3_enrollments_extra_section_dim.docx/Αίτημα_δημιουργίας_επιπλέον_τμημ.docx")}}" method="get"class="container-fluid">
                            <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Κατεβάστε το αρχείο"> Αίτημα Επιπλέον Τμήματος-Τμημάτων</button>
                        </form>
                    @else
                        <form action="{{url("/school_app/$appname/3_enrollments_extra_section_nip.docx/Αίτημα_δημιουργίας_επιπλέον_τμημ.docx")}}" method="get"class="container-fluid">
                            <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Κατεβάστε το αρχείο"> Αίτημα Επιπλέον Τμήματος-Τμημάτων</button>                                
                        </form>
                    @endif
                    <p class="fw-lighter fst-italic fs-6"><small>Ενδεικτικό Πρότυπο Έγγραφο</small></p>
                </td>
                <td>
                    @if($school->primary == 1)
                        <form action="{{url("/school_app/$appname/4_boundary_students_dim.xlsx/Μαθητές_στα_όρια.xlsx")}}" method="get"class="container-fluid">
                            <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Κατεβάστε το αρχείο"> Μαθητές στα όρια</button>
                        </form>
                    @else
                        <form action="{{url("/school_app/$appname/4_boundary_students_nip.xlsx/Μαθητές_στα_όρια.xlsx")}}" method="get"class="container-fluid">
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
                {{-- <td>20</td><td>50</td><td>25</td><td>26</td><td>28</td><td>5</td><td>150</td>  --}}
            </tr>
            <tr>
            <form action="{{url("/school_app/enrollments/extra_section")}}" method="post" enctype="multipart/form-data" class="container-fluid">
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
                            <a href="{{url("/school_app/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    @endif
                </td>
            </form>
            <form action="{{url("/school_app/$appname/boundary_students")}}" method="post" enctype="multipart/form-data" class="container-fluid">
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
                            <a href="{{url("/school_app/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
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
                    <form action="{{url("/school_app/enrollments/enrollments3_$school_code.pdf/$file3")}}" method="get"class="container-fluid">
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
                <form action="{{url("/school_app/enrollments/enrollments4_$school_code.xlsx/$file4")}}" method="get"class="container-fluid">
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