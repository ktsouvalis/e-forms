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
        <script src="{{asset('check_timetable.js')}}"></script>
        <script>
            var checkTimetableStatusUrl = '{{ route("timetables.change_status", ["timetableFile" =>"mpla"]) }}';
            
        </script>
    @endpush
    @php
        $microapp = App\Models\Microapp::where('url', '/timetables')->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        $timetables_schools = $microapp->stakeholders;
        $timetables = App\Models\microapps\Timetables::orderBy('created_at', 'desc')->get();
        // dd($timetables_schools, $timetables);
    @endphp
    @push('title')
        <title>{{$microapp->name}}</title>
    @endpush    
    @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
    <div class="">
        <h2 class="text-center">Ωρολόγια Προγράμματα</h2>
    
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-8 pb-5">
                <table  id="dataTable" class="display table table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th id="search">Κωδικός</th>
                            <th id="search">Σχολείο</th>
                            <th id="search">Αρχεία</th>
                            <th id="search">Κατάσταση</th>
                            <th id="search"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($timetables as $timetable)
                        <tr @if($timetable->status == 1) style="background-color: #d1ecf1;" @endif>
                            <td>{{$timetable->school->code}}</td>
                            <td>
                                {{$timetable->school->name}}
                                <form action="{{route('timetables.edit', ['timetable' => $timetable->id])}}" method="get">
                                    <input type="submit" class="btn btn-info btn-block rounded-2 py-2 m-1 no-spin" title="Προβολή Ωρολογίου Προγράμματος" value="Προβολή / Επεξεργασία">
                                </form>
                            </td>
                            <td>
                                @foreach($timetable->files as $timetableFile)
                                    @php 
                                        $filesArray = json_decode($timetableFile->filenames_json, true);
                                        $filesCount = count($filesArray);
                                        $thisCount = 0;
                                        $fileId = $timetableFile->id;
                                    @endphp
                                    @foreach($filesArray as $serverFileName => $databaseFileName)

                                        @php 
                                            $thisCount++;
                                            $comments = json_decode($timetableFile->comments); 
                                        @endphp
                                        <div class="d-flex align-items-start mb-2">
                                        <form action="{{route('timetables.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName])}}" method="get" class="container-fluid">
                                            <input type="submit" id="{{$fileId}}_{{$thisCount}}"
                                            @if($timetableFile->status == 3 && $thisCount == $filesCount) class="btn btn-success btn-block rounded-2 py-2 m-1 no-spin" @else class="btn btn-info btn-block rounded-2 py-2 m-1 no-spin" @endif  
                                            @if($thisCount != $filesCount)  style="padding: 0.25rem; margin: 0.25rem; font-size: 0.5rem;" @endif title="Λήψη αρχείου" value="{{$databaseFileName}}">
                                        </form>
                                        @if($thisCount == $filesCount)
                                            @if($timetableFile->status == 0) Αρχική Υποβολή @endif    
                                            @if($timetableFile->status == 1) Αναμονή Διορθώσεων @endif
                                            @if($timetableFile->status == 2) Υποβολή Διορθώσεων @endif
                                            @if($timetableFile->status == 3) Έγκριση @endif
                                            <hr>
                                        @endif
                                        </div>
                                        
                                        
                                    @endforeach
                                @endforeach
                            </td>
                            <td> 
                                @if($timetable->status == 1)<strong class="text-success"> Οριστικοποιημένο @endif
                                @if($timetable->status == 0)<strong class="text-info"> Υπο επεξεργασία @endif
                                </strong>
                            </td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>                                      
        </div>
   
    
</x-layout>