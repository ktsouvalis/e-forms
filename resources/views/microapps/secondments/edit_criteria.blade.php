<x-layout_teacher>
    @php
        $teacher = Auth::guard('teacher')->user(); //check which teacher is logged in
        $microapp = App\Models\Microapp::where('url', '/secondments')->first();
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
        <script>
        $(document).ready(function() {
            $('#uploadFilesModalForm').on('submit', function(e) {
                e.preventDefault();
                $('#uploadModal').modal('hide');
                var formData = new FormData(this);
                $(this).addClass('no-spin');
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        
                    
                        // handle success
                        $('#uploadModal').modal('hide');
                        alert('Τα αρχεία υποβλήθηκαν αλλά θα τα δείτε μόλις πατήσετε Αποθήκευση.');
                    },
                    error: function (data) {
                        // handle error
                        alert('Τα αρχεία υποβλήθηκαν αλλά θα τα δείτε μόλις πατήσετε Αποθήκευση.');
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });
        });
            var timesClickedNrOfChildren = 0;
            var displayedModal = false;
            function checkAndDisplayModal(){
                var displayModal = false;
                var maritalStatus = document.getElementById('marital_status').value;
                var nrOfCholdren = document.getElementById('nr_of_children').value;
                var applicationForReposition = {{ $secondment->application_for_reposition }};
                if(applicationForReposition === 0){// μόνο γι αυτούς που δεν έχουν κάνει αίτηση βελτίωσης
                    if (maritalStatus === '2' || maritalStatus === '4') { //αν είναι έγγαμοι ή σε χηρεία
                        if(displayedModal === false){
                            displayModal = true;
                        }
                    }
                    if(maritalStatus === '1' || maritalStatus === '3'){ //αν είναι άγαμοι ή διαζευγμένοι με παιδιά
                        if(nrOfCholdren > 0){
                            if(displayedModal === false){
                                displayModal = true;
                            }
                        }
                    }
                }

                if(displayModal){
                    displayedModal = true;
                    var uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
                    uploadModal.show();
                }
            
            }
            document.getElementById('marital_status').addEventListener('change', function() {
                checkAndDisplayModal();
            });
            document.getElementById('nr_of_children').addEventListener('change', function() {
                timesClickedNrOfChildren++;
                if(timesClickedNrOfChildren === 1){
                    setTimeout(function() {
                        checkAndDisplayModal();
                    }, 1500);
                }                
            });
        </script>
    @endpush
    @push('title')
        <title>Αποσπάσεις</title>
    @endpush
<div class="container">
    @if($microapp->accepts == 0)
    
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            Η υποβολή αιτήσεων δεν είναι ενεργή αυτή τη στιγμή.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Κλείσιμο"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Κλείσιμο"></button>
        </div>
    @endif
    <h2 class="text-center">Αίτηση Απόσπασης εντός ΠΥΣΠΕ Αχαΐας</h2>
    <h5 class="text-center"> Βήμα 1 - Δήλωση Μοριοδοτούμενων Κριτηρίων</h5>
    @include('microapps.secondments.inc_personal_data')
    {{-- Μοριοδοτούμενα Κριτήρια - Α Τμήμα Αίτησης --}}
    <div class="modal" tabindex="-1" id="uploadModal"> {{-- Modal START --}}
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Υποβολή Δικαιολογητικών</h5>
              
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Κλείσιμο"></button>
            </div>
            <div class="modal-body">
                <h6>Πατήστε "Κλείσιμο" αν έχετε υποβάλλει τα απαραίτητα δικαιολογητικά ή αν θέλετε να τα υποβάλλετε αργότερα.</h6>
                <form action="{{route('secondments.upload_files', ['secondment' => $secondment])}}" id="uploadFilesModalForm" method="post" class="container-fluid" enctype="multipart/form-data" data-export>
                    @csrf
                    <div class="text-center">
                        <input  type="file" id="files" name="files[]" multiple required @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif>
                    </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
              <input type="submit" value="Ανέβασμα" class="btn btn-info btn-block rounded-2 py-2"
                        @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
            </form>
            </div>
          </div>
        </div>
      </div>
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
            <form action="{{route('secondments.update', ['secondment' => $secondment, 'criteriaOrPreferences' => '1'])}}" method="post" id="criteriaForm">
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
                                        @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif>
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
                                @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif>
                                    <option value="0" @if($secondment->marital_status == 0) selected @endif >Δηλώστε μόνο σε περίπτωση που ζητάτε να μοριοδοτηθείτε</option>
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
                                <input type="number" min="0" max="11" name="nr_of_children" id="nr_of_children" value="{{ $secondment->nr_of_children }}"
                                @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <div class="input-group mb-2">
                            <div class="form-group">
                                <div class="input-group mb-2">
                                    <div class="px-2 input-group-text">Δήμος Οικογενειακής Μερίδας:</div>
                                    <select name="civil_status_municipality" id="civil_status_municipality" class="form-select"
                                    @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
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
                                    @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
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
                                        @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
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
                                <select name="health_issues" id="health_issues" class="form-select" @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
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
                                @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
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
                                    @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
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
                                @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
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
                                @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
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
                                        @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
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
                                        @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
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
                                @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
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
                        @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >{{$secondment->comments}}</textarea>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" name="action" value="update" class="btn btn-primary m-2 bi bi-pencil-square"
                    @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif > Αποθήκευση</button>
                    {{-- Το κουμπί Οριστική Υποβολή ενεργοποιεί javascript στο αρχείο spinner.js που ζηταει confirmation --}}
                    <button type="submit" id="criteriaFinalSubmit" class="btn btn-danger m-2 bi bi-file-earmark-lock-fill"
                    @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif data-export> Οριστική Υποβολή</button>
                    {{-- Αν ο χρήστης πατήσει ΟΚ η υποβολή γίνεται με javascript με click() στο hiddenButton --}}
                    <button type="submit" name="action" id="hiddenButton" value="submit" style="display: none;">
                    @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif data-export></button>
                </div>
            </form>
            @if($secondment->criteria_submitted == 1)
                @php
                    // $serverFileName = $secondment->teacher->afm.'_application_form.pdf';
                    // $databaseFileName = $secondment->teacher->surname.'_Δήλωση_Προτιμήσεων.pdf';
                @endphp
                <div class="text-center">
                    {{-- <form action="{{route('secondments.download_file', ['serverFileName'=>$serverFileName, 'databaseFileName'=>$databaseFileName])}}" method="get">
                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου">Λήψη Υποβληθείσας Αίτησης</button>
                    </form> --}}
                    Η αίτηση έχει πρωτοκολληθεί με αριθ. πρωτ.<strong> {{$secondment->protocol_nr}} - {{$secondment->protocol_date}} </strong>στο Πρωτόκολλο του ΠΥΣΠΕ Αχαΐας.
                </div>
                @if(!in_array($teacher->klados, ['ΠΕ60', 'ΠΕ70', 'ΠΕ60.50', 'ΠΕ70.50', 'ΠΕ71' ]))
                    <div class="text-center">
                        <form action="{{route('secondments.revoke', ['secondment'=>$secondment])}}" method="post">
                            @csrf
                                <button class="btn btn-danger bi bi-arrow-counterclockwise" title="revoke" onclick="return confirm('Θα πραγματοποιηθεί διαγραφή της αίτησης και ανάκληση από το Πρωτόκολλο του ΠΥΣΠΕ. Είστε βέβαιοι;')">Ανάκληση</button>
                        </form>
                            Πατώντας ανάκληση, η αίτηση θα ακυρωθεί.
                    </div>
                @endif
            @else
                <div class="text-center">
                    <ul><li>Με την Οριστική Υποβολή, η αίτηση Πρωτοκολλείται αυτόματα στο Πρωτόκολλο του ΠΥΣΠΕ προκειμένου να προχωρήσει η διαδικασία μοριοδότησης.</li>
                    @if($teacher->klados == "ΠΕ60" || $teacher->klados == "ΠΕ70")
                        <li>Μετά την Οριστική Υποβολή της αίτησης με τα Μοριοδοτούμενα Κριτήρια, ενεργοποιείται η δυνατότητα για δήλωση Σχολικών Μονάδων.</li>
                    @endif
                    </ul>
                </div>
            @endif
        </div>   
        </div>
    </div>
    <div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0">
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-regular fa-file-lines"></i> Υποβολή Δικαιολογητικών</h3>
                    <p class="m-0">Υποβάλλονται δικαιολογητικά σε μορφή .pdf, .jpeg .png < 10MB ανά υποβολή</p>
                </div>
            </div>
            
            <div class="card-body p-3">
                <div class="row justify-content-right">
                    <div class="text-center py-2">
                        <p class="m-0">Μπορείτε να επιλέξετε και να ανεβάσετε και περισσότερα από ένα αρχεία ταυτόχρονα.</p>
                    </div>
                    <form action="{{route('secondments.upload_files', ['secondment' => $secondment])}}" method="post" class="container-fluid" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center">
                            <input  type="file" id="files" name="files[]" multiple required @if(($secondment->criteria_submitted == 1 && $secondment->extra_files_allowed == 0) || $microapp->accepts == 0) disabled @endif>
                            <input type="submit" value="Ανέβασμα" class="btn btn-info btn-block rounded-2 py-2"
                            @if(($secondment->criteria_submitted == 1 && $secondment->extra_files_allowed == 0) || $microapp->accepts == 0) disabled @endif >
                        </div>
                    </form>
                  
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row justify-content-right">
                    <div class="text-center py-2">
                        <p class="m-0">Αρχεία που έχουν υποβληθεί:</p>
                        @if($secondment->files_json)
                            @php 
                                $count = 1;
                                $fileNames = json_decode($secondment->files_json, true);
                            @endphp
                            @foreach($fileNames as $serverFileName => $databaseFileName)
                            
                            <div class="d-flex justify-content-between">
                                <form action="{{route('secondments.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName])}}" method="get">
                                    <input type="submit" class="btn btn-info btn-block rounded-2 py-2 m-1" value="{{$databaseFileName}}" >
                                </form>
                                <form action="{{route('secondments.delete_file', [ 'secondment' => $secondment, 'serverFileName' => $serverFileName ])}}" method="get">
                                    <input type="submit" class="btn btn-danger btn-block rounded-3" value="Διαγραφή" 
                                    @if($secondment->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
                                </form>
                            </div>
                            @php $count++; @endphp
                            @endforeach
                        @else
                            <p class="m-0">Δεν έχει υποβληθεί κάποιο δικαιολογητικό</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(in_array($teacher->klados, ['ΠΕ60', 'ΠΕ70', 'ΠΕ60.50', 'ΠΕ70.50', 'ΠΕ71' ]))
    <div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0">
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-solid fa-list-ol"></i> Δήλωση Προτίμησης Σχολικών Μονάδων</h3>
                    <p class="m-0">Δήλωση Σχολείων:</p>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row justify-content-right">
                    {{-- <form action="{{ route('secondments.edit', ['secondment' => $secondment]) }}" method="get">
                        <div class="text-center">
                            <input type="hidden" name="criteriaOrPreferences" value="2"> 
                            <input type="submit" value="Βήμα 2 - Δήλωση Σχολείων" class="btn btn-info btn-block rounded-2 py-2">
                        </div>
                    </form> --}}
                    @if($secondment->criteria_submitted == 1)
                        <a href="{{ route('secondments.edit', ['secondment' => $secondment, 'criteriaOrPreferences' => '2']) }}" class="btn btn-info btn-block rounded-2 py-2">Βήμα 2 - Δήλωση Σχολείων</a>
                    @else
                        Βήμα 2 - Δήλωση Σχολείων*
                        <br><br>
                        <small>Η δήλωση προτίμησης Σχολείων ενεργοποιείται μόνο μετά την Οριστική Υποβολή των Μοριοδοτούμενων Κριτηρίων</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0">
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <p class="m-0">Η δήλωση προτίμησης Σχολείων για τους Εκπαιδευτικούς Ειδικοτήτων θα πραγματοποιηθεί με συμπλήρωση δήλωσης μετά την ανακοίνωση των Σχολείων</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div> {{-- Container --}}

</x-layout_teacher>