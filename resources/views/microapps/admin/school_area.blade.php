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
        $microapp = App\Models\Microapp::where('url', '/school_area')->first();
        $school_areas = $microapp->stakeholders; 
    @endphp
    @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
    <div class="container">
        <div class="alert alert-warning text-muted text-center">
            Σύνδεσμος για δημοσίευση ορίων Σχολικών Μονάδων:
            <strong><a href="{{url("/school_areas")}}" class="text-dark" target="_blank">Περιοχή εγγραφής μαθητών στις Σχολικές Μονάδες της Δ/νσης Π.Ε. Αχαΐας</a>    
            </strong><br><br> 
        </div>
    </div>
    <div class="table-responsive py-2">
        <table  id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">Ονομασία Σχολείου</th>
                    <th id="search">Δήμος</th>
                    <th id="">Γεωγραφικά Όρια</th>
                    <th id="">Σχόλια</th>
                    {{-- <th id="">Ημερομηνία Υποβολής</th> --}}
                    <th id="">Επιβεβαίωση</th>
                    <th id="search">Κωδικός Σχολείου</th>
                </tr>
            </thead>
            <tbody>
            
                @foreach($school_areas as $school)
                {{-- $school is MicroappStakeholder object so method ->stakeholder fetches School object attributes) --}}
                    {{-- @php
                        if($school->stakeholder->school_area AND $school->stakeholder->school_area->updated_at != ""){ // if school has a school_area record, get timestamp
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $school->stakeholder->school_area->updated_at);
                            $timestamp = $date->getTimestamp();
                        }else{
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', '2021-03-01 10:00:00');
                            $timestamp = $date->getTimestamp();
                        }
                    @endphp --}}
                    <tr>
                        @php
                            $school_id = $school->stakeholder->id;
                        @endphp
                        <td>
                        @if(App\Models\MicroappUser::where('user_id',Auth::user()->id)->where('microapp_id', $microapp->id)->where('can_edit', 1)->exists() or Auth::user()->isAdmin())
                            <form action="{{url("/school_area_profile/$school_id")}}" method="get" target="_blank">
                                <button type="submit">{{$school->stakeholder->name}} </button>
                            </form>
                        @else
                            {{$school->stakeholder->name}}
                        @endif
                        </td>

                        @php
                            
                        @endphp
                        <td >{{$school->stakeholder->municipality->name}} </td>
                        @if($school->stakeholder->school_area) {{-- if school has a school_area record, get record data --}}
                            @php
                                $data=array();
                                if($school->stakeholder->school_area->data != "")
                                    $data = json_decode($school->stakeholder->school_area->data);
                            @endphp
                            <td>
                                @foreach($data as $one_record)
                                    {{$one_record->street}}
                                    @if($one_record->comment != "")
                                        ({{$one_record->comment}})
                                    @endif
                                    <br>
                                @endforeach
                            </td> {{--$school(MicroappStakeholder)->stakeholder(belongsTo: School)->school_area(hasOne: school_area)->data --}}
                            <td class="text-wrap"  style="width: 12rem;">{{$school->stakeholder->school_area->comments}}</td>
                            {{-- <td>{{date('d/m/Y H:i:s', $timestamp)}}</td> --}}
                            @if($school->stakeholder->school_area->confirmed)
                                <td class=""><strong><i class="bi bi-check-circle text-success"></i></strong></td>
                            @else
                                <td class=""><i class="bi bi-hourglass-split text-danger"></i></td>
                            @endif
                        @else
                            <td><em><small>Δεν έχουν δηλωθεί</small></em></td>
                            <td>-</td>
                            {{-- <td>-</td> --}}
                            <td class="">-</td>
                        @endif
                        <td>{{$school->stakeholder->code}}</td> 
                    </tr>
                @endforeach   
            </tbody>  
        </table>    
    </div> <!-- table responsive closure -->
    @include('microapps.microapps_admin_after') {{-- email to those who haven't submitted an answer --}}
</x-layout>