<x-layout_school>
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        $outings = $school->outings;
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
        <script>// Script to handle the modal for delete request
            $(document).ready(function() {
                $(document).on('mousedown', 'a[data-toggle="modal"]', function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var baseUrl = "{{ route('outings.send_delete_request', ['outing' => 'OUTING_ID_PLACEHOLDER']) }}";
                    var outingId = $(this).data('outing-id');
                    
                    $('#outingId').val(outingId);

                    var actionUrl = baseUrl.replace('OUTING_ID_PLACEHOLDER', outingId);
                    $('#deleteRequestForm').attr('action', actionUrl); 

                    setTimeout(function() {
                        $('#deleteRequestModal').modal('show');
                    }, 50);

                    $('#deleteRequestModal').on('shown.bs.modal', function() {
                        $('#delete_request').focus();
                    });
                });
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                function handleCheckboxChange(event) {
                    if (event.target.checked) {
                        const checkboxId = event.target.id; // Get the checkbox ID
                        //alert('Checkbox with ID ' + checkboxId + ' is checked!'); // Display the ID in an alert
                        fetch('{{ route('actions.index_school') }}?checkboxId=' + checkboxId)
                            .then(response => response.text())
                            .then(html => {
                                const newWindow = window.open('', '_blank', 'width=750,height=550');
                                newWindow.document.write(html);
                                newWindow.document.close();
                            })
                            .catch(error => console.error('Error:', error));
                    }
                }

                // Single Checkbox Outside the Table
                const singleCheckbox = document.getElementById('openActionCheckbox');
                if (singleCheckbox) {
                    singleCheckbox.addEventListener('change', handleCheckboxChange);
                }

                // Multiple Checkboxes in the Table
                document.querySelectorAll('.openActionCheckbox').forEach(checkbox => {
                    checkbox.addEventListener('change', handleCheckboxChange);
                });
            });
                // Listen for messages from the action window
                window.addEventListener('message', function(event) {
        if (event.data.type === 'ACTION_SELECTED') {
            const actionData = event.data.data;
            console.log("Received action data:", actionData);

            // For new outing creation
            if (actionData.oldOuting === "False") {
                const targetDiv = document.getElementById('action-title'); 
                const targetInput = document.getElementById('action-title-input');
                
                if (targetDiv && actionData.actions.length > 0) {
                    // Join all selected action titles with comma
                    targetDiv.textContent = actionData.actions.map(a => a.title).join(', ');
                }
                if (targetInput && actionData.actions.length > 0) {
                    // Store action IDs as comma-separated string
                    targetInput.value = actionData.actions.map(a => a.actionId).join(',');
                }
            } 
            // For existing outing
            else {
                actionData.actions.forEach(action => {
                    const targetElement = document.getElementById('outing-action-title-' + actionData.outingId);
                    if (targetElement) {
                        // Update with all action titles
                        targetElement.textContent = actionData.actions.map(a => a.title).join(', ');
                        
                        // If you have a hidden input for actions, update it too
                        const actionInput = document.getElementById('outing-action-input-' + actionData.outingId);
                        if (actionInput) {
                            actionInput.value = actionData.actions.map(a => a.actionId).join(',');
                        }
                    }
                });
            }
        }
    });
        </script>
    @endpush
    @push('title')
        <title>Εκδρομές</title>
    @endpush
        <div class="py-3">
            <div class="container">
                <div class="instruction-container p-4 border rounded shadow-sm bg-light">
                    <h5 class="text-primary mb-3 fw-semibold">Διαχείριση Εγκεκριμένων Δράσεων</h5>
                    
                    <p class="mb-3 text-dark lh-base">
                        1) Προσθέστε εγκεκριμένες Δράσεις που υλοποιούνται στη σχολική σας μονάδα και εντάσσονται στο πλαίσιο επίσημων προγραμμάτων από αναγνωρισμένους Φορείς 
                        <span class="fst-italic">(Υπουργείο Παιδείας, ΙΕΠ, Διεύθυνση Πρωτοβάθμιας Εκπαίδευσης Αχαΐας, κ.ά.)</span> μέσα από την επιλογή <span class="btn text-success"> "Δημιουργία - Διαχείριση Δράσεων"</span>.
                    </p>
                    
                    <p class="text-dark lh-base">
                        2) Μετά την καταχώρηση των Δράσεων, μπορείτε να δηλώνετε και να κατηγοριοποιείτε κάθε ενδοσχολική δραστηριότητα ή εκπαιδευτική επίσκεψη που πραγματοποιείται στη σχολική σας μονάδα, συσχετίζοντάς την με την αντίστοιχη εγκεκριμένη Δράση.
                    </p>
                </div>
                <div class="hstack gap-2">
                    <h4 class="my-5">Διαχείριση Δράσεων Σχολείου: </h4>
                    @if($accepts)
                        <a href="{{route('actions.index_school')}}" class="btn btn-success no-spinner" target="_blank" onclick="window.open(this.href, '_blank', 'width=600,height=500'); return false;">Δημιουργία - Διαχείριση Δράσεων</a>
                    @else
                        {{-- <a href="{{route('outings.index')}}" class="btn btn-success">Επιστροφή στην Καταχώρηση Εκδρομών (Δε δέχεται υποβολές)</a> --}}
                    @endif
                </div>
                <div class="alert alert-info text-center my-2">
                    <strong> <i class="bi bi-info-circle"> </i> Σημείωση: Μπορείτε να εντάξετε σε Δράσεις τόσο τις νέες όσο και τις παλαιότερες εκδρομές ή ενδοσχολικές δραστηριότητες. </strong>
                </div>
            </div>
            <nav class="navbar navbar-light bg-light">
                    {{-- <form action="{{url("/outings")}}" method="post" enctype="multipart/form-data" class="container-fluid"> --}}
                    <form action="{{route('outings.store')}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <div class="input-group">
                            <span class="input-group-text w-25"></span>
                            <span class="input-group-text w-75"><strong>Καταχώρηση στοιχείων εκδρομής</strong></span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25" id="basic-addon4">Τύπος Εκδρομής</span>
                            <select name="type" class="form-select" aria-label="Default select example">
                                @foreach(App\Models\microapps\OutingType::all() as $type)
                                @php
                                    $selected=null;
                                    if($type->id == 1)
                                        $selected="selected";   
                                @endphp
                                <option {{$selected}} value="{{$type->id}}">{{$type->description}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25">Εντάσσεται σε Δράση: </span>
                            <input class="form-check-input" role="switch" type="checkbox" id="openActionCheckbox">
                            <input type="hidden" id="action-title-input" name="action_id" value="">
                            <div id='action-title'></div> (Δεν έχει ενεργοποιηθεί ακόμη αυτή η λειτουργία)
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25" id="basic-addon2">Ημερομηνία</span>
                            <input name="outing_date" type="date" class="form-control"  aria-label="outing_date" aria-describedby="basic-addon1" required ><br>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Δραστηριότητα-Προορισμός: </span>
                            <input name="destination" id="destination" type="text" class="form-control" placeholder="π.χ. Πάρκο Κυκλοφοριακής Αγωγής" aria-label="Δράση" aria-describedby="basic-addon2" required><br>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Αριθμός πρακτικού: </span>
                            <input name="record" id="record" type="text" class="form-control" placeholder="π.χ. 15-11/9/2023" aria-label="αριθμός πρακτικού" aria-describedby="basic-addon3" required><br>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Τμήματα που συμμετέχουν</span>
                            @php
                                $sections = $school->sections;
                            @endphp
                            <div class="v-stack gap-2">
                            @if($sections->count()==0)
                                <div class='alert alert-warning text-center my-2'>
                                    <strong> <i class="bi bi-exclamation-triangle"> </i> Τη στιγμή που ενημερώθηκαν τα τμήματα στην εφαρμογή από το myschool, δεν είχαν καταχωρηθεί τμήματα για το Σχολείο σας
                                        Παρακαλούμε ενημερώστε τα τμήματα του Σχολείου στο myschool και ειδοποιήστε το Τμήμα Πληροφορικής στο it@dipe.ach.sch.gr
                                    </strong>
                                </div>
                            @else
                                @foreach($sections as $section)
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" role="switch" type="checkbox" name="section{{$section->id}}" value="{{$section->id}}" id="section{{$section->id}}">
                                        <label for="section{{$section->id}}"> {{$section->name}} </label>
                                    </div>     
                                @endforeach
                            @endif
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25" id="basic-addon4">Απόσπασμα Πρακτικού</span>
                            <input name="record_file" type="file" class="form-control" required><br>
                        </div>
                        @if(!$accepts || $sections->count()==0)
                            <div class='alert alert-warning text-center my-2'>
                               <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                            </div>
                        @else
                            <div class="input-group">
                                <span class="input-group-text w-25"><em>Μορφή αρχείου: .pdf < 10MB</em></span>
                                <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή Εκδρομής </button>
                                {{-- <a href="{{url("/$appname/create")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a> --}}
                                <a href="{{route("$appname.create")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                            </div>
                        @endif
                    </form>
                </nav>
            </div>
            <div class="py-3">
                <div class="modal fade" id="deleteRequestModal" tabindex="-1" role="dialog" >
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-light">
                            <h5 class="modal-title" id="messageModalLabel">Αιτιολογία Αιτήματος Διαγραφής</h5>
                        </div>
                        <form id="deleteRequestForm" method="post">
                            @csrf
                            <div class="modal-body bg-light">
                                <input type="hidden" id="outingId" name="outing_id">
                                <div class="form-group">
                                    <textarea class="form-control" id="delete_request" name="delete_request" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
                                <button type="submit" class="btn btn-outline-primary bi bi-send"> Αποστολή</button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
                <div class="table-responsive py-2">
                <table  id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th id="search">Τύπος</th>
                        <th id="">Ημερομηνία <p class="text-muted">(Ε/Μ/Η)</p></th>
                        <th id="">Προορισμός - Δραστηριότητα</th>
                        <th id="">Πρακτικό</th>
                        <th id="">Αρχείο</th>
                        <th id="">Τμήματα</th>
                        <th>Επεξεργασία μελλοντικής εκδρομής</th>
                        <th>Διαγραφή μελλοντικής εκδρομής</th>
                        <th id="search">Εκπ/κή Δράση</th>
                    </tr>
                </thead>
                <tbody>
                
                    @foreach($outings as $outing)
                        @php
                            $my_date = Illuminate\Support\Carbon::parse($outing->outing_date); 
                            $today = Illuminate\Support\Carbon::now();
                            if($outing->action_id != 0)
                                $action = $outing->action;
                            else
                                $action = null;
                        @endphp
                        <tr>
                            <td>{{$outing->type->description}}</td> 
                            <td>{{$my_date->year}}/{{$my_date->month}}/{{$my_date->day}} </td>
                            <td>{{$outing->destination}}</td>
                            <td>{{$outing->record}}</td>
                            <td>
                                <div class="hstack gap-2">
                                    {{-- <form action="{{url("/outings/download_file/$outing->id")}}" method="get"> --}}
                                    <form action="{{route('outings.download_file', ['outing'=>$outing->id])}}" method="get">
                                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> </button>
                                    </form>
                                    {{$outing->file}}
                                </div>
                            </td>
                            <td>
                                @foreach($outing->sections as $out_section)
                                    {{$out_section->section->name}}<br>
                                @endforeach
                            </td>
                            
                            @if(Illuminate\Support\Carbon::parse($outing->outing_date)->startOfDay()>=Illuminate\Support\Carbon::now()->startOfDay() and $accepts)
                            {{-- <td><a href="{{url("/outings/$outing->id/edit")}}" class="btn btn-primary bi bi-pencil-square "> </a></td> --}}
                            <td><a href="{{route('outings.edit', ['outing'=>$outing->id])}}" class="btn btn-primary bi bi-pencil-square "> </a></td>
                            <td>
                                {{-- <form action="{{url("/outings/$outing->id")}}" method="post"> --}}
                                <form action="{{route('outings.destroy', ['outing'=>$outing->id])}}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <button class="bi bi-x-circle btn btn-danger" type="submit" style="color:white" onclick="return confirm('Επιβεβαίωση διαγραφής εκδρομής;')"> </button>
                                </form>
                            </td>
                            @else
                            <td> - </td>
                            <td>
                                <a href="#" class="no-spinner" data-toggle="modal" data-target="#deleteRequestModal" data-outing-id="{{$outing->id}}">
                                    Υποβολή Αιτήματος Διαγραφής
                                </a>
                            </td>
                            @endif
                            <td>
                                <input class="form-check-input openActionCheckbox" role="switch" type="checkbox" id="{{ $outing->id }}">
                                <div id="outing-action-title-{{ $outing->id }}">
                                    @foreach($outing->actions as $action)
                                        {{ $action->title }}@if(!$loop->last), @endif
                                    @endforeach
                                </div>
                            </td>
                        </tr> 
                    @endforeach   
                </tbody>  
                </table>    
            </div>
    </div>
    <div class="  py-3">
        <table class="display table table-sm table-striped table-bordered table-hover">
            <tr>
                <th>Τμήμα</th>
                <th>Πλήθος ολιγόωρων</th>
                <th>Πλήθος πολύωρων</th>
                <th>Πλήθος ολοήμερων</th>
                <th>Σύνολο</th>
            </tr>
            
            @foreach($school->sections as $one_section)
                @php
                    $ligo = 0;
                    $poly = 0;
                    $day = 0;
                @endphp
                @foreach($one_section->outings as $section_outing)
                    @php
                        if($section_outing->outing->outingtype_id==1)$ligo++;
                        if($section_outing->outing->outingtype_id==2)$poly++;
                        if($section_outing->outing->outingtype_id==3)$day++;
                    @endphp
                @endforeach  
            <tr>
                <td>{{$one_section->name}}</td>
                <td>{{ $ligo }}</td>
                <td>{{ $poly }}</td>
                <td>{{ $day }}</td>
                <td><strong>{{$ligo+$poly+$day}}</strong></td>
            </tr>
            @endforeach
        </table>
    </div>
</x-layout_school>