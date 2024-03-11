<x-layout_school>
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; 
        $app_name = $microapp->name;
        //$old_data = $school->enrollments;
        $old_data = "";
    @endphp
    
    @push('title')
        <title>{{$app_name}}</title>
    @endpush
<div class="container">
    <div class="container px-5">
        <div class="alert alert-warning text-center">
            Στην καρτέλα αυτή θα δηλωθούν τα στοιχεία <strong>εγγραφέντων</strong> μαθητών για το σχ. έτος 2024-25
        </div>     
        <nav class="navbar navbar-light bg-light">
            <div class="container mt-5">
                <h3>Στοιχεία εγγραφής μαθητών @if($school->primary == 1) στην Α' Τάξη @else στο Νηπιαγωγείο @endif</h3>  {{-- Εγγραφέντες στην Α' / Εγγραφέντες στο Νηπιαγωγείο --}}
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Στοιχεία</th>
                            <th>@if($school->primary == 1) Τάξη Α' @else Προνήπια/Νήπια @endif</th>
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
                                Αρχείο για συμπλήρωση</td>
                            <td>
                                @if($school->primary == 1)
                                    <form action="{{url("/school_app/$appname/test.txt")}}" method="get"class="container-fluid">
                                @else
                                    <form action="{{url("/school_app/$appname/test.txt")}}" method="get"class="container-fluid">
                                @endif
                                    <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Μπορείτε να χρησιμοποιήσετε οποιοδήποτε πρότυπο"> Πίνακας </button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <form action="{{url("/school_app/$appname/enrolled")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                                @csrf
                            <td>Μαθητές 2024-25</td>
                            <td>
                                <input name="nr_of_studentsA" id="nr_of_studentsA" type="number" class="form-control input-sm" required value="@if($old_data){{$old_data->nr_of_studentsA}}@endif">
                            </td>
                            {{-- </td><td>50</td><td>25</td><td>26</td><td>28</td><td>5</td><td>150</td>  --}}
                        </tr>
                        
                        <tr>
                            <td>
                                Αρχείο Εγγραφέντων Μαθητών</td>
                            <td>
                                <input name="enrolled_file" type="file" class="form-control">
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
                    </tbody>
                </table>
            <h3>Στοιχεία εγγραφής στο Ολοήμερο</h3>  {{-- ΔΗΜΟΤΙΚΑ:  / ΝΗΠΙΑΓΩΓΕΙΑ: Εγγραφέντες στο Ολοήμερο --}}
            <form action="{{url("/school_app/$app_name/{1}")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                @csrf        
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Στοιχεία</th>
                        <th>Τάξη Α'</th>
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
                        <td>Μαθητές 2024-25</td>
                        <td>
                            <input name="nr_of_studentsA" id="nr_of_studentsA" type="number" class="form-control input-sm" required value="@if($old_data){{$old_data->nr_of_studentsA}}@endif">
                        </td>
                        {{-- </td><td>50</td><td>25</td><td>26</td><td>28</td><td>5</td><td>150</td>  --}}
                    </tr>
                    <tr>
                        <td>
                            Αρχείο για συμπλήρωση</td>
                        <td>
                    
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Υποβολή Αρχείου Εγγραφέντων Μαθητών</td>
                        <td>
                            <input name="template_file" type="file" class="form-control">
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
                    </tr>
                </tbody>
            </table>
        </form>
<h3>Δημιουργία επιπλέον Τμήματος / Κατανομή μαθητών σε όμορα σχολεία</h3> {{-- ΔΗΜΟΤΙΚΑ: Αίτημα για δημιουργία επιπλέον Τμήματος ή μαθητές στα όρια / ΝΗΠΙΑΓΩΓΕΙΑ: Αίτημα για δημιουργία επιπλέον Τμήματος ή μαθητές στα όρια --}}        
                    <table class="table table-bordered">
                    <thead>
                        <tr><td colspan="2">
                            Γ) Σε περιπτώσεις που ο αριθμός προνηπίων/νηπίων για εγγραφή υπερβαίνει τους 25 μαθητές/τριες
                                ανά τμήμα ισχύουν τα όσα προβλέπονται στην περ. δ της παρ. 4 του άρθρου 6 του Π.Δ 79/2017 (Α΄ 109),
                                όπως τροποποιήθηκε με την παρ. 1 του άρθρου 44 του ν. 4777/2021 (Α΄ 25).
                                Πιο συγκεκριμένα ο Διευθυντής/ντρια- Προϊστάμενος/η του Νηπιαγωγείου:
                                α) υποβάλλει αίτημα στον/στην αρμόδιο/α Διευθυντή/ντρια Πρωτοβάθμιας Εκπαίδευσης για τη λειτουργία
                                περισσοτέρων τμημάτων, εφόσον υπάρχει στο σχολείο διαθέσιμη αίθουσα (υπόδειγμα 3) και
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
                                <form action="{{url("/school_app/$app_name/{1}")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                                    @csrf
                                <td>Υποβολή αιτήματος Δημιουργίας Επιπλέον Τμήματος/Τμημάτων</td><td>Υποβολή αρχείου μαθητών που διαμένουν στα όρια</td>
                                {{-- <td>20</td><td>50</td><td>25</td><td>26</td><td>28</td><td>5</td><td>150</td>  --}}
                            </tr>
                            <tr>
                                <td> <input name="template_file" type="file" class="form-control">
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
                                <td>
                                    <input name="template_file" type="file" class="form-control">
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
                            </tr>
                        </tbody>
                    </table>
                </div>
            
        </nav>
    </div>    
</div>
</x-layout_school>