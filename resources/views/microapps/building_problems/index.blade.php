<x-layout>

    @push('links')
        <link href="{{ asset('DataTables-1.13.4/css/dataTables.bootstrap5.css') }}" rel="stylesheet"/>
        <link href="{{ asset('Responsive-2.4.1/css/responsive.bootstrap5.css') }}" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="{{ asset('DataTables-1.13.4/js/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('DataTables-1.13.4/js/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/dataTables.responsive.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/responsive.bootstrap5.js') }}"></script>
        <script src="{{ asset('datatable_init.js') }}"></script>
    @endpush
    
    @php
        $user = Auth::user();
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        // dd($nextYearPlanningActive, $nextYearPlanningAccepts, $schoolYear, $accepts)
    @endphp
    @push('title')
        <title>Κτιριολογικά Προβλήματα</title>
    @endpush
    
    @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
    
        {{-- Data Section  --}}
        @php
            $building_problems = App\Models\BuildingProblems::all();
        @endphp
        <div class="table-responsive py-2" style="align-self:flex-start">
            <table  id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>Κωδικός</th>
                    <th id="search">Είδος</th>
                    <th id="search">Σχολείο</th>
                    <th id="search">Σοβαρότητα</th>
                    <th id="search">Σχόλια</th>
                    <th id="search">Πρωτόκολλο</th>
                    <th id="">Αρχεία</th>
                    <th>Τελευταία ενημέρωση</th>
                </tr>
            </thead>
            <tbody>
                
                @foreach($building_problems as $building_problem)
                    @php
                    dd($building_problems);
                        $school = $building_problem->stakeholder;
                    @endphp
                        <tr>
                            <td>{{$school->code}}</td>
                            <td>@if($school->primary == 1) Δημοτικό @else Νηπιαγωγείο @endif</td>
                            <td> {{$school->name}}</td>
                            <td>
                                @if($school->building_problem->severity == 0)
                                    <span class="badge bg-success">0</span>
                                @elseif($school->building_problem->severity == 1)
                                    <span class="badge bg-info">1</span>
                                @elseif($school->building_problem->severity == 2)
                                    <span class="badge bg-warning">2</span>
                                @elseif($school->building_problem->severity == 3)
                                    <span class="badge bg-warning text-dark">3</span>
                                @elseif($school->building_problem->severity == 4)
                                    <span class="badge bg-danger">4</span>
                                @else
                                    <span class="badge bg-dark text-white">5</span>
                                @endif
                            </td>
                            <td>{{$school->building_problem->comments}}</td>
                            <td>
                                @if($school->building_problem->protocol_nr)
                                    <span class="badge bg-success">{{$building_problem->protocol_nr}}</span>
                                    <br>
                                    <span class="badge bg-secondary">{{$building_problem->protocol_date}}</span>
                                @else
                                    <span class="badge bg-secondary">Δεν έχει πρωτοκολληθεί</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $files = json_decode($building_problem->files_json, false);
                                    print_r($files);
                                @endphp
                                @if($building_problem->files_json)
                                    
                                    @foreach($building_problem->files_json as $server_file_name => $filename)
                                        <a href="{{route('building_problems.download_file', ['file' => $file->filename])}}" class="btn btn-secondary btn-sm m-1" title="Λήψη αρχείου">
                                            <i class="bi bi-file-earmark-text"></i> {{$filename}}
                                        </a>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">Δεν υπάρχουν αρχεία</span>
                                @endif
                            </td>
                            <td>{{$building_problem->updated_at->format('d/m/Y H:i')}}</td>
                        
                        </tr>
            @endforeach
            </tbody>
            </table>
        </div> <!-- table responsive closure -->
        @include('microapps.microapps_admin_after') {{-- email to those who haven't submitted an answer --}}
        
        

</x-layout>