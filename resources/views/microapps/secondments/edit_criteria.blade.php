<x-layout_teacher>
    @php
        $teacher = Auth::guard('teacher')->user(); //check which teacher is logged in
       // $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
       // $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        $municipalities = App\Models\Municipality::all();                                            
    @endphp
    @push('links')
        <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
    @endpush
    @push('scripts')
        <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
        <script src="{{asset('datatable_init.js')}}"></script>
    @endpush
    @push('title')
        <title>Αποσπάσεις</title>
    @endpush
<div class="container">
    <h2 class="text-center">Αίτηση Απόσπασης εντός ΠΥΣΠΕ Αχαΐας</h2>
	<div class="row justify-content-center">
		<div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0">
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-solid fa-user"></i> Προσωπικά Στοιχεία</h3>
                    <p class="m-0"></p>
                </div>
            </div>
            <div class="card-body p-3">
                <!--Body-->
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fa-regular fa-user text-info"></i></div>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="name_surname" name="name_surname" placeholder="" value="{{$teacher->surname}} {{$teacher->name}}" disabled>
                            <label for="name_surname">Ονοματεπώνυμο</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="fathers_name" name="fathers_name" placeholder="" value={{$teacher->fname}} disabled>
                            <label for="fathers_name">Πατρώνυμο</label>
                        </div>
                    </div>
                </div>
                {{-- Κλάδος και Αριθμός Μητρώου --}}
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fa-solid fa-person-chalkboard text-info"></i></div>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="specialty" name="specialty" placeholder="" value={{$teacher->klados}} disabled>
                            <label for="specialty">Κλάδος</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="am" name="am" placeholder="" value={{$teacher->am}} disabled>
                            <label for="am">Αριθμός Μητρώου</label>
                        </div>
                    </div>
                </div>
                {{-- Οργανική Θέση και προϋπηρεσία --}}
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fa-solid fa-school-circle-check text-info"></i></div>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="schoool" name="school" placeholder="Οργανική Θέση" value="{{$teacher->organiki->name}}" disabled>
                            <label for="school">Οργανική Θέση</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fa-solid fa-calendar-check text-info"></i></div>
                        </div>
                        <label for="years" class="px-2" >Προϋπηρεσία</label>
                            <input type="text" class="form-control" id="years" name="years" placeholder="" value="17 Έτη" disabled>
                            
                            <input type="text" class="form-control" id="days" name="days" value="5 Μήνες" disabled>
                            <input type="text" class="form-control" id="days" name="days" value="24 Ημέρες" disabled>
                            <label for="days" class="px-2" >έως 31-8-2024</label>
                    </div>
                </div>
            </div>
        </div>
        </div>        
	</div>

    {{-- Μοριοδοτούμενα Κριτήρια - Α Τμήμα Αίτησης --}}
    <div class="row justify-content-center">
		<div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0">
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-solid fa-person-rays"></i> Μοριοδοτούμενα κριτήρια</h3>
                    <p class="m-0">Παρακαλούμε συμπληρώστε ανάλογα με τα κριτήρια που αιτείστε να μοριοδοτηθούν προκειμένου για την απόσπασή σας.</p>
                </div>
            </div>
            <div class="card-body p-3">
            <form action="{{route('secondments.update', ['secondment' => $secondment, 'criteriaOrPreferences' => '1'])}}" method="post">
                @method('PUT')
                @csrf
                <!--ειδική Κατηγορία-->
                <div class="row">
                    <div class="col-md-12">
                        <h4>Ειδική Κατηγορία Αποσπάσεων</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group mb-2">
                            <div class="form-group">
                                <div class="input-group mb-2">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="special_category" value="0">
                                        <input class="form-check-input" type="checkbox" name="special_category" value="1" id="special_category_checked" 
                                        @if($secondment->special_category==1) checked @endif
                                        @if($secondment->submitted == 1) disabled @endif>
                                        <label class="form-check-label" for="special_category">Επιθυμώ να υπαχθώ σε ειδική κατηγορία αποσπάσεων</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Οικογενειακή κατάσταση-->
                <div class="row">
                    <div class="col-md-12">
                        <h4>Οικογενειακά Κριτήρια</h4>
                    </div>
                    <div class=" col-12 col-md-12">
                        <div class="form-group">
                            <div class="input-group mb-2">
                                <div class="px-2 input-group-text">Οικογενειακή Κατάσταση:</div>
                                <select name="marital_status" id="marital_status" class="form-select" 
                                @if($secondment->submitted == 1) disabled @endif>
                                    <option value="0" @if($secondment->marital_status == 1) selected @endif >Δηλώστε μόνο σε περίπτωση που ζητάτε να μοριοδοτηθείτε</option>
                                    <option value="1" @if($secondment->marital_status == 1) selected @endif >Άγαμος</option>
                                    <option value="2" @if($secondment->marital_status == 2) selected @endif >Έγγαμος - Σύμφωνο συμβίωσης</option>
                                    <option value="3" @if($secondment->marital_status == 3) selected @endif >Διαζευγμένος - Σε διάσταση</option>
                                    <option value="4" @if($secondment->marital_status == 4) selected @endif >Σε χηρεία</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12">
                        <div class="form-group">
                        <div class="input-group mb-2">
                            <div class="px-2 form-label input-group-text">Αριθμός τέκνων ( αφορά ανήλικα ή σπουδάζοντα τέκνα):</div>
                                <input type="number" min="0" max="11" name="nr_of_children" value="{{ $secondment->nr_of_children }}"
                                @if($secondment->submitted == 1) disabled @endif >
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="input-group mb-2">
                            <div class="form-group">
                                <div class="input-group mb-2">
                                    <div class="px-2 input-group-text">Δήμος Οικογενειακής Μερίδας:</div>
                                    <select name="civil_status_municipality" id="civil_status_municipality" class="form-select"
                                    @if($secondment->submitted == 1) disabled @endif >
                                        <option value="">Μοριοδοτούνται μόνο οι δήμοι της Δνσης Π.Ε. Αχαΐας</option>
                                        @foreach($municipalities as $municipality)
                                            <option value="{{$municipality->id}}" @if($secondment->civil_status_municipality == $municipality->id) selected @endif>{{$municipality->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="input-group mb-2">
                            <div class="form-group">
                                <div class="input-group mb-2">
                                    <div class="px-2 input-group-text">Δήμος Εντοπιότητας:</div>
                                    <select name="living_municipality" id="living_municipality" class="form-select"
                                    @if($secondment->submitted == 1) disabled @endif >
                                        <option value="">Μοριοδοτούνται μόνο οι δήμοι της Δνσης Π.Ε. Αχαΐας</option>
                                        @foreach($municipalities as $municipality)
                                            <option value="{{$municipality->id}}" @if($secondment->living_municipality == $municipality->id) selected @endif>{{$municipality->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="input-group mb-2">
                            <div class="form-group">
                                <div class="input-group mb-2">
                                    <div class="px-2 input-group-text">Δήμος Συνυπηρέτησης:</div>
                                        <select name="partner_working_municipality" id="partner_working_municipality" class="form-select"
                                        @if($secondment->submitted == 1) disabled @endif >
                                            <option value="">Μοριοδοτούνται μόνο οι δήμοι της Δνσης Π.Ε. Αχαΐας</option>
                                            @foreach($municipalities as $municipality)
                                                <option value="{{$municipality->id}}" @if($secondment->partner_working_municipality == $municipality->id) selected @endif>{{$municipality->name}}</option>
                                            @endforeach
                                        </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Σοβαροί Λόγοι Υγείας-->
                <div class="row">
                    <div class="col-md-12">
                        <h4>Σοβαροί Λόγοι Υγείας</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="input-group mb-2">
                                <div class="px-2 input-group-text">Λόγοι Υγείας ιδίου, συζύγου ή τέκνων:</div>
                                <select name="health_issues" id="health_issues" class="form-select" @if($secondment->submitted == 1) disabled @endif >
                                    <option value="0" @if($secondment->health_issues == 0) selected @endif></option>
                                    <option value="1" @if($secondment->health_issues == 1) selected @endif>Αναπηρία 50% - 66%</option>
                                    <option value="2" @if($secondment->health_issues == 2) selected @endif>Αναπηρία 67% - 79%</option>
                                    <option value="3" @if($secondment->health_issues == 3) selected @endif>Αναπηρία >80%</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <div class="input-group mb-2">
                                <div class="px-2 input-group-text">Λόγοι Υγείας γονέων:</div>
                                <select name="parents_health_issues" id="parents_health_issues" class="form-select" 
                                @if($secondment->submitted == 1) disabled @endif >
                                    <option value="0" @if($secondment->parents_health_issues == 0) selected @endif></option>
                                    <option value="1" @if($secondment->parents_health_issues == 1) selected @endif>Αναπηρία 50% - 67%</option>
                                    <option value="2" @if($secondment->parents_health_issues == 2) selected @endif>Αναπηρία >67%</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <div class="input-group mb-2">
                                <div class="px-2 input-group-text">Δήμος Γονέων:</div>
                                    <select name="parents_municipality" id="parents_municipality" class="form-select" 
                                    @if($secondment->submitted == 1) disabled @endif >
                                        <option value="">Μοριοδοτούνται μόνο οι δήμοι της Δνσης Π.Ε. Αχαΐας</option>
                                        @foreach($municipalities as $municipality)
                                            <option value="{{$municipality->id}}" @if($secondment->parents_municipality == $municipality->id) selected @endif>{{$municipality->name}}</option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <div class="input-group mb-2">
                                <div class="px-2 input-group-text">Λόγοι Υγείας αδελφών <span class="text-muted">(με απόφαση επιμέλειας)</span>:</div>
                                <select name="siblings_health_issues" id="siblings_health_issues" class="form-select" 
                                @if($secondment->submitted == 1) disabled @endif >
                                    <option value="0" @if($secondment->siblings_health_issues == 0) selected @endif></option>
                                    <option value="1" @if($secondment->siblings_health_issues == 1) selected @endif>Αναπηρία >67%</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <div class="input-group mb-2">
                                <div class="px-2 input-group-text">Δήμος Αδελφών:</div>
                                <select name="siblings_municipality" id="siblings_municipality" class="form-select"
                                @if($secondment->submitted == 1) disabled @endif >
                                    <option value="">Μοριοδοτούνται μόνο οι δήμοι της Δνσης Π.Ε. Αχαΐας</option>
                                    @foreach($municipalities as $municipality)
                                        <option value="{{$municipality->id}}" @if($secondment->siblings_municipality == $municipality->id) selected @endif>{{$municipality->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12">
                        <div class="input-group mb-2">
                            <div class="form-group">
                                <div class="input-group mb-2">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="IVF" value="0">
                                        <input class="form-check-input" type="checkbox" name="IVF" value="1" id="IVF_checked" @if($secondment->IVF==1) checked @endif
                                        @if($secondment->submitted == 1) disabled @endif >
                                        <label class="form-check-label" for="IVF">Θεραπεία για εξωσωματική γονιμοποίηση</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Λόγοι Σπουδών-->
                <div class="row">
                    <div class="col-12 col-md-12">
                        <h4>Λόγοι Σπουδών</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="input-group mb-2">
                            <div class="form-group">
                                <div class="input-group mb-2">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="post_graduate_studies" value="0">
                                        <input class="form-check-input" type="checkbox" name="post_graduate_studies" value="1" id="post_graduate_studies_checked" 
                                        @if($secondment->post_graduate_studies==1) checked @endif
                                        @if($secondment->submitted == 1) disabled @endif >
                                        <label class="form-check-label" for="post_graduate_studies">Φοίτηση σε Μεταπτυχιακό Πρόγραμμα ή άλλο Τίτλο ΑΕΙ (τα προγράμματα του ΕΑΠ δεν μοριοδοτούνται)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <div class="input-group mb-2">
                                <div class="px-2 input-group-text">Δήμος Σπουδών:</div>
                                <select name="studies_municipality" id="studies_municipality" class="form-select"
                                @if($secondment->submitted == 1) disabled @endif >
                                    <option value="">Μοριοδοτούνται μόνο οι δήμοι της Δνσης Π.Ε. Αχαΐας</option>
                                    @foreach($municipalities as $municipality)
                                        <option value="{{$municipality->id}}" @if($secondment->studies_municipality == $municipality->id) selected @endif>{{$municipality->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-12">
                        <h4>Παρατηρήσεις:</h4>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="px-2 input-group-text">Σχόλιο-Επισήμανση-Παρατήρηση:</div>
                        <textarea class="form-control" name="comments" id="comments" rows="4" 
                        @if($secondment->submitted == 1) disabled @endif >{{$secondment->comments}}</textarea>
                    </div>
                </div>
                <div class="text-center">
                    <input type="submit" value="Υποβολή" class="btn btn-info btn-block rounded-2 py-2"
                    @if($secondment->submitted == 1) disabled @endif >
                </div>
            </form>
        </div>   
        </div>
    </div>
    <div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0">
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-solid fa-list-ol"></i> Δήλωση Προτίμησης Σχολικών Μονάδων</h3>
                    <p class="m-0">Για να προχωρήσετε σε δήλωση προτίμησης Σχολικών Μονάδων και οριστική υποβολή της αίτησής σας πατήστε:</p>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row justify-content-right">
                    <form action="{{ route('secondments.edit', ['secondment' => $secondment]) }}" method="get">
                        <div class="text-center">
                            <input type="hidden" name="criteriaOrPreferences" value="2"> {{-- Βήμα 2 - Δήλωση Σχολείων --}}
                            <input type="submit" value="Βήμα 2 - Δήλωση Σχολείων" class="btn btn-info btn-block rounded-2 py-2">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> {{-- Container --}}

</x-layout_teacher>