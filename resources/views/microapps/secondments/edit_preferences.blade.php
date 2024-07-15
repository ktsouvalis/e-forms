<x-layout_teacher>
    @php
        $teacher = Auth::guard('teacher')->user(); //check which teacher is logged in
        $microapp = App\Models\Microapp::where('url', '/secondments')->first();
       // $accepts = $microapp->accepts; //fetch microapp 'accepts' field    
       $organiki_school_code = $teacher->organiki->code;                                       
    @endphp
    @push('links')
        <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
            <link href="{{asset('lou-multi-select-57fb8d3/css/multi-select.css')}}" rel="stylesheet"/>
    @endpush
    @push('scripts')
        <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
        <script src="{{asset('datatable_init.js')}}"></script>
        <script src="{{asset('lou-multi-select-57fb8d3/js/jquery.multi-select.js')}}"></script>
        <script>
            var selectionOrder = [];
            var selectedOnes = [];
            $(document).ready(function() {
                // Στήσε το multi-select dropdown
                $('#schools-select').multiSelect({
                    keepOrder: true,
                    selectableHeader: "<div class='custom-header'>Διαθέσιμα Σχολεία:</div>",
                    selectionHeader: "<div class='custom-header'>Επιλεγμένα Σχολεία με σειρά προτίμησης:</div>",
                    afterSelect: function(values){
                        // When an item is selected, add it to the end of the selectionOrder array
                        selectionOrder.push(values[0]);
                    },
                    afterDeselect: function(values){
                        // When an item is deselected, remove it from the selectionOrder array
                        selectionOrder = selectionOrder.filter(function(value){
                            return value !== values[0];
                        });
                    }
                });

                // Remove the school where the teacher is currently working from the list of available schools
                $('#schools-select option[value= {!! json_encode($organiki_school_code) !!} ]').remove();
                $('#schools-select option[value= "9999999" ]').remove();
                $('#schools-select option[value= "9060439" ]').remove();
                $('#schools-select option[value= "9060552" ]').remove();
                $('#schools-select').multiSelect('refresh');

                if(selectedOnes.length >0){
                    //    selectedOnes = selectedOnes.map(function(item){
                    //         return '"' + item + '"';
                    //     });
                        // Sort the <option> elements based on the order of the values in selectedOnes
                    $('#schools-select option').sort(function(a, b) {
                        var aValue = selectedOnes.indexOf(a.value);
                        var bValue = selectedOnes.indexOf(b.value);
                        return aValue - bValue;
                    }).appendTo('#schools-select');

                    // Select the options and refresh the multi-select dropdown
                    selectedOnes.forEach(function(item) {
                        $('#schools-select').multiSelect('select', item);
                    });
                    $('#schools-select').multiSelect('refresh');
                }
                //if secondment application is submitted, disable the school choices
                var isSubmitted = {!! json_encode($secondment->submitted || $microapp->accepts == 0) !!};
                if (isSubmitted == 1) {
                    $('#schools-select option').prop('disabled', true);
                    $('#schools-select').multiSelect('refresh');
                }
                
                    
            });
            function getSelectedInOrder() {
                $('#selectionOrderInput').val(JSON.stringify(selectionOrder, null, 0));
            }
        </script>
    @endpush
    @push('title')
        <title>Αποσπάσεις</title>
    @endpush
<div class="container">
    @if($microapp->accepts == 0)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            Η υποβολή αιτήσεων δεν είναι ενεργή αυτή τη στιγμή.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($secondment->submitted == 1)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            Η αίτηση έχει υποβληθεί οριστικά και δε μπορεί να τροποποιηθεί.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <h2 class="text-center">Αίτηση Απόσπασης εντός ΠΥΣΠΕ Αχαΐας</h2>
    <h5 class="text-center"> Βήμα 2 - Δήλωση Σχολικών Μονάδων</h5>
	@include('microapps.secondments.inc_personal_data')
    {{-- Modal START --}}
    <div class="modal" tabindex="-1" id="submitModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Οριστική Υποβολή Αίτησης Απόσπασης</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('secondments.upload_files', ['secondment' => $secondment])}}" id="uploadFilesModalForm" method="post" class="container-fluid" enctype="multipart/form-data" data-export>
                    @csrf
                    <div class="text-center">
                        <input  type="file" id="files" name="files[]" multiple required @if($secondment->submitted == 1 || $microapp->accepts == 0) disabled @endif>
                        
                    </div>
                
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <input type="submit" value="Ανέβασμα" class="btn btn-info btn-block rounded-2 py-2"
                        @if($secondment->submitted == 1 || $microapp->accepts == 0) disabled @endif >
            </form>
            </div>
          </div>
        </div>
      </div>
    {{-- Modal END --}}
    {{-- Δήλωση Σχολείων Προτίμησης - Β Τμήμα Αίτησης --}}
    <div class="row justify-content-center">
		<div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0">
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-solid fa-list-ol"></i> Δήλωση Προτίμησης Σχολείων για απόσπαση</h3>
                    <p class="m-0">Παρακαλούμε συμπληρώστε τη σειρά προτίμησης των Σχολικών Μονάδων στα οποία επιθυμείτε να αποσπαστείτε.</p>
                </div>
            </div>
            <div class="card-body p-3">
            @php
            $controller = new App\Http\Controllers\SecondmentController();
            //dd($teacher->klados, $teacher->org_eae);
            $schoolChoices = $controller->getSchoolChoices($teacher->klados, $teacher->org_eae);
            if($secondment->preferences_json){
                $selectedOnes = json_decode($secondment->preferences_json);
            } else {
                $selectedOnes = [];
            }
            @endphp
            @push('scripts')
                <script>
                    selectedOnes = JSON.parse('{!! json_encode($selectedOnes) !!}');
                   // alert(selectedOnes);
                </script>
            @endpush
            <form action="{{route('secondments.update', ['secondment' => $secondment,'criteriaOrPreferences' => '2'])}}" method="post" id="preferencesForm">
                @method('PUT')
                @csrf
                <!--Σχολικές Μονάδες-->
                <div class="row">
                    <div class="col-12 col-md-12">
                        <h4>Σχολικές Μονάδες</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="input-group mb-2">
                            <input type="hidden" id="selectionOrderInput" name="selectionOrder">
                            <select multiple="multiple" id="schools-select" name="schools-select[]">
                                @foreach($schoolChoices as $school)
                                    <option value="{{$school->code}}" @if(in_array($school->code, old('schools-select', []))) selected @endif>{{$school->name}} 
                                        @if($teacher->org_eae==1 && $school->has_integration_section==1) (Τ.Ε.)
                                        @endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            
                        </div>
                </div>
                
                <div class="row">
                    <div class="col-12 col-md-12">
                        <h4>Παρατηρήσεις:</h4>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="px-2 input-group-text">Επισήμανση για τα Σχολεία Προτίμησης:</div>
                        <textarea class="form-control" name="preferences_comments" id="preferences_comments" rows="4"
                        @if($secondment->submitted == 1 || $microapp->accepts == 0) disabled @endif >{{$secondment->preferences_comments}}</textarea>
                    </div>
                </div>
                @if($secondment->submitted == 0 && $microapp->accepts == 1)
                    <div class="text-center">
                        <button type="submit" name="action" value="update" class="btn btn-primary m-2 bi bi-pencil-square" onclick="getSelectedInOrder();"> Αποθήκευση</button>
                        <button type="submit" name="action" value="preview" class="btn btn-primary m-2 bi bi-eye-fill" onclick="getSelectedInOrder();" target="_blank"> Προεπισκόπηση</button>
                        {{-- Το κουμπί Οριστική Υποβολή ενεργοποιεί javascript στο αρχείο spinner.js που ζηταει confirmation --}}
                        <button type="submit" id="preferencesFinalSubmit" class="btn btn-danger m-2 bi bi-file-earmark-lock-fill"
                        @if($secondment->submitted == 1 || $microapp->accepts == 0) disabled @endif data-export onclick="getSelectedInOrder();"> Οριστική Υποβολή</button>
                        {{-- Αν ο χρήστης πατήσει ΟΚ η υποβολή γίνεται με javascript με click() στο hiddenButton --}}
                        <button type="submit" name="action" id="hiddenButton" value="submit" style="display: none;"
                        @if($secondment->submitted == 1 || $microapp->accepts == 0) disabled @endif data-export onclick="getSelectedInOrder();"></button>
                    </div>
                </form>
                @else
                </form>
                    @if($secondment->submitted == 1)
                        @php
                            $serverFileName = $secondment->teacher->afm.'_application_form.pdf';
                            $databaseFileName = $secondment->teacher->surname.'_Δήλωση_Προτιμήσεων.pdf';
                        @endphp
                           
                            <div class="text-center">
                                <form action="{{route('secondments.download_file', ['serverFileName'=>$serverFileName, 'databaseFileName'=>$databaseFileName])}}" method="get">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου">Λήψη Υποβληθείσας Αίτησης</button>
                                </form>
                                <div class="py-3">Η αίτηση έχει υποβληθεί οριστικά με αριθ. πρωτ {{$secondment->protocol_nr}} - {{$secondment->protocol_date}} στο Πρωτόκολλο του ΠΥΣΠΕ Αχαΐας.</div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col col-6 text-center">
                                    <form action="{{route('secondments.revoke', ['secondment'=>$secondment])}}" method="post">
                                    @csrf
                                        <button class="btn btn-danger bi bi-arrow-counterclockwise" title="revoke" onclick="return confirm('Θα πραγματοποιηθεί διαγραφή της αίτησης και ανάκληση από το Πρωτόκολλο του ΠΥΣΠΕ. Είστε βέβαιοι;')">Ανάκληση</button>
                                    </form>
                                    Πατώντας ανάκληση, η αίτηση θα ακυρωθεί.
                                </div>
                                <div class="col col-6 text-center">
                            
                                    <form action="{{route('secondments.modify', ['secondment'=>$secondment])}}" method="post">
                                    @csrf
                                        <button class="btn btn-info bi bi-arrow-counterclockwise" title="modify" @if($microapp->accepts == 0) disabled @endif >Τροποποίηση</button>
                                    </form>
                                    {{-- <a href="{{route('secondments.revoke', ['secondment'=>$secondment])}}" class="btn btn-danger bi bi-arrow-counterclockwise" title="revoke">Ανάκληση</a> --}}
                                    Πατώντας τροποποίηση, η αίτηση θα ενεργοποιηθεί εκ νέου για Δήλωση Σχολείων.
                                </div>
                            </div>
                            
                    @else
                        <div class="text-center">
                            Η υποβολή αιτήσεων απόσπασης δεν είναι ενεργή.
                            Δεν έχετε υποβάλλει κάποια αίτηση.
                        </div>
                    @endif
                @endif
                
        </div>   
    </div>
</div>
</div>

    <div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0">
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-solid fa-user"></i> Μοριοδοτούμενα Κριτήρια </h3>
                    <p class="m-0"></p>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row justify-content-right">
                    {{-- <form action="{{ route('secondments.edit', ['secondment' => $secondment]) }}" method="get">
                        <div class="text-center">
                            <input type="hidden" value="1" name="criteriaOrPreferences">
                            <input type="submit" value="Βήμα 1 - Μοριοδοτούμενα κριτήρια" class="btn btn-info btn-block rounded-2 py-2">
                        </div>
                    </form> --}}
                    <a href="{{ route('secondments.edit', ['secondment' => $secondment, 'criteriaOrPreferences' => '1']) }}" class="btn btn-info rounded-2 py-2">Βήμα 1 - Δήλωση Κριτηρίων</a>
               
                </div>
            </div>
        </div>
    </div>
</div> {{-- Container --}}

</x-layout_teacher>