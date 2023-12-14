<x-layout>
    @push('links')
        <link href="../DataTables-1.13.4/css/dataTables.bootstrap5.css" rel="stylesheet"/>
        <link href="../Responsive-2.4.1/css/responsive.bootstrap5.css" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="../DataTables-1.13.4/js/jquery.dataTables.js"></script>
        <script src="../DataTables-1.13.4/js/dataTables.bootstrap5.js"></script>
        <script src="../Responsive-2.4.1/js/dataTables.responsive.js"></script>
        <script src="../Responsive-2.4.1/js/responsive.bootstrap5.js"></script>
        <script src="../datatable_init.js"></script>
    @endpush
    @push('title')
        <title>Όρια Σχολείων</title>
    @endpush
    @php
        //fetch microapp data
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $school_areas = $microapp->stakeholders; 
    @endphp
    @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}

    <div class="table-responsive py-2">
        <table  id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">Ονομασία Σχολείου</th>
                    <th id="">Γεωγραφικά Όρια</th>
                    <th id="">Σχόλια</th>
                    <th id="">Ημερομηνία Υποβολής</th>
                    <th id="search">Κωδικός Σχολείου</th>
                </tr>
            </thead>
            <tbody>
            
                @foreach($school_areas as $school)
                {{-- $school is MicroappStakeholder object so method ->stakeholder fetches School object attributes) --}}
                    @php
                        if($school->stakeholder->school_area AND $school->stakeholder->school_area->updated_at != ""){ // if school has a school_area record, get timestamp
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $school->stakeholder->school_area->updated_at);
                            $timestamp = $date->getTimestamp();
                        }else{
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', '2021-03-01 10:00:00');
                            $timestamp = $date->getTimestamp();
                        }
                    @endphp
                    <tr>
                        <td>{{$school->stakeholder->name}} </td>
                        @if($school->stakeholder->school_area) {{-- if school has a school_area record, get record data --}}
                            <td>
                                @php
                                $data = json_decode($school->stakeholder->school_area->data);
                                foreach($data as $one_record){
                                    echo $one_record->street;
                                    if($one_record->comment != "")
                                        echo " (".$one_record->comment.")";
                                     echo "<br>";
                                }
                                @endphp
                            </td> {{--$school(MicroappStakeholder)->stakeholder(belongsTo: School)->school_area(hasOne: school_area)->data --}}
                            <td class="text-wrap"  style="width: 12rem;">{{$school->stakeholder->school_area->comments}}</td>
                            <td>{{date('d/m/Y H:i:s', $timestamp)}}</td>
                        @else
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        @endif
                        <td>{{$school->stakeholder->code}}</td> 
                    </tr> 
                @endforeach   
            </tbody>  
        </table>    
    </div> <!-- table responsive closure -->
    @include('microapps.microapps_admin_after') {{-- email to those who haven't submitted an answer --}}
</x-layout>