<x-layout_teacher>
    @php
        $teacher = Auth::guard('teacher')->user(); //check which teacher is logged in
       // $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
       // $accepts = $microapp->accepts; //fetch microapp 'accepts' field                                           
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
                            <input type="text" class="form-control" id="name_surname" name="name_surname" placeholder="" value={{$teacher->surname}} {{$teacher->name}} disabled>
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
                            <input type="text" class="form-control" id="schoool" name="school" placeholder="Οργανική Θέση" value={{$teacher->organiki->name}} disabled>
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
            <form action="{{route('secondments.update', ['secondment' => $secondment])}}" method="post">
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
                                    <option value="{{$school->code}}" @if(in_array($school->code, old('schools-select', []))) selected @endif>{{$school->name}}</option>
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
                        <textarea class="form-control" name="preferences_comments" id="preferences_comments" rows="4">{{$secondment->preferences_comments}}</textarea>
                    </div>
                </div>
                @if($secondment->submitted == 0)
                    <div class="text-center">
                        <button type="submit" name="action" value="update" class="btn btn-primary m-2 bi bi-pencil-square" onclick="getSelectedInOrder();"> Αποθήκευση</button>
                        <button type="submit" name="action" value="preview" class="btn btn-primary m-2 bi bi-eye-fill" onclick="getSelectedInOrder();"> Προεπισκόπηση</button>
                        <button type="submit" name="action" value="submit" class="btn btn-danger m-2 bi bi-file-earmark-lock-fill" onclick="getSelectedInOrder();"> Οριστική Υποβολή</button>
                        {{-- <input type="submit" value="Αποθήκευση" class="btn btn-info btn-block rounded-2 py-2" onclick="getSelectedInOrder();"> --}}
                    </div>
                </form>
                @else
                </form>
                    <div class="text-center">
                        <form action="{{route('secondments.download_file', ['secondment'=>$secondment->id, 'file_type'=>'application_form'])}}" method="get">
                            <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου">Λήψη Υποβληθείσας Αίτησης</button>
                        </form>
                        Η αίτηση έχει υποβληθεί οριστικά και δε μπορεί να τροποποιηθεί.
                    </div>
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
                    <form action="{{ route('secondments.edit', ['secondment' => $secondment]) }}" method="get">
                        <div class="text-center">
                            <input type="hidden" value="1" name="criteriaOrPreferences">
                            <input type="submit" value="Βήμα 1 - Μοριοδοτούμενα κριτήρια" class="btn btn-info btn-block rounded-2 py-2">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> {{-- Container --}}

</x-layout_teacher>