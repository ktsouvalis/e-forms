
<x-layout>
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
        <title>Όρια Σχολείων Π.Ε. Αχαΐας</title>
    @endpush
    @php
        //fetch microapp data
        $school_areas = App\Models\microapps\SchoolArea::orderBy('school_id', 'asc')->get();
    @endphp
    <h2>Γεωγραφικά Όρια εγγραφής μαθητών στις Σχολικές Μονάδες της Διεύθυνσης Π.Ε. Αχαΐας</h2>
    <h5>Μπορείτε να κάνετε αναζήτηση σε οποιοδήποτε πεδίο, αναζήτηση με το όνομα του Σχολείου, το Δήμο και τις οδούς ή περιοχές που αναφέρονται στα όρια των Σχολικών Μονάδων</h5>
    <div class="table-responsive py-2">
        <table  id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">Ονομασία Σχολείου</th>
                    <th id="search">Δήμος</th>
                    <th id="search">Γεωγραφικά Όρια / οδός /περιοχή</th>
                    <th id="">Παρατηρήσεις</th>
                    <th id="">Τελευταία ενημέρωση</th>
                    <th id="search">Κωδικός Σχολείου</th>
                </tr>
            </thead>
            <tbody>
            
                @foreach($school_areas as $school)
                {{-- $school is MicroappStakeholder object so method ->stakeholder fetches School object attributes) --}}
                    @php
                        $school_data = App\Models\School::where('id', $school->school_id)->first();
                        if($school->updated_at != ""){ // if school has a school_area record, get timestamp
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $school->updated_at);
                            $timestamp = $date->getTimestamp();
                        }else{
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', '2021-03-01 10:00:00');
                            $timestamp = $date->getTimestamp();
                        }
                    @endphp
                    <tr>
                        <td>{{$school_data->name}} </td>
                        <td>{{$school_data->municipality->name}} </td>
                        @if($school->data) {{-- if school has a school_area record, get record data --}}
                            <td>
                                @php
                                if($school->data != ""){
                                    $data = json_decode($school->data);
                                    foreach($data as $one_record){
                                        echo $one_record->street;
                                        if($one_record->comment != "")
                                            echo " (".$one_record->comment.")";
                                        echo "<br>";
                                    }
                                }
                                
                                @endphp
                            </td> {{--$school(MicroappStakeholder)->stakeholder(belongsTo: School)->school_area(hasOne: school_area)->data --}}
                            <td class="text-wrap"  style="width: 12rem;">{{$school->comments}}</td>
                            <td>{{date('d/m/Y H:i:s', $timestamp)}}</td>
                        @else
                            <td><em><small>Δεν έχουν δηλωθεί</small></em></td>
                            <td>-</td>
                            <td>-</td>
                        @endif
                        <td>{{$school_data->code}}</td> 
                    </tr> 
                @endforeach   
            </tbody>  
        </table>    
    </div> <!-- table responsive closure -->

</div> <!-- End of custom container --> 

</x-layout>