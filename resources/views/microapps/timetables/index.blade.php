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
    @foreach($timetables as $timetable)
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
                        <tr>
                            <td>{{$timetable->school->code}}</td>
                            <td>{{$timetable->school->name}}</td>
                            <td>
                                @foreach($timetable->files as $timetableFile)
                                    @php 
                                        $filesArray = json_decode($timetableFile->filenames_json, true);
                                        $filesCount = count($filesArray);
                                        $thisCount = 0;
                                        $fileId = $timetableFile->id;
                                    @endphp
                                    @foreach($filesArray as $serverFileName => $databaseFileName)
                                        @php $thisCount++; @endphp
                                        <form action="{{route('timetables.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName])}}" method="get" class="container-fluid">
                                            <input type="submit" id="{{$fileId}}_{{$thisCount}}"
                                            @if($timetableFile->status == 3 && $thisCount == $filesCount) class="btn btn-success btn-block rounded-2 py-2 m-1 no-spin" @else class="btn btn-info btn-block rounded-2 py-2 m-1 no-spin" @endif  
                                            @if($thisCount != $filesCount)  style="padding: 0.25rem; margin: 0.25rem; font-size: 0.5rem;" @endif title="Λήψη αρχείου" value="{{$databaseFileName}}">
                                        </form>
                                        @if($thisCount == $filesCount)
                                            <select name="{{$timetableFile->id}}" id="{{$thisCount}}" class="changeTimetableStatus">
                                                <option value="0" >Αρχική Υποβολή</option>
                                                <option value="1" @if($timetableFile->status == 1) selected @endif>Αναμονή Διορθώσεων</option>
                                                <option value="2" @if($timetableFile->status == 2) selected @endif disabled>Υποβολή Διορθώσεων</option>
                                                <option value="3" @if($timetableFile->status == 3) selected @endif>Έγκριση</option>
                                            </select>
                                            
                                            @if($timetableFile->status == 1)
                                            <div class="hideAndAppearOnTheFly{{$timetableFile->id}}">
                                                <form action="{{route('timetables.comment', ['timetableFile' => $timetableFile->id, 'thisCount' => $thisCount])}}" method="post" class="container-fluid" id="comment_form_{{$timetableFile->id}}">
                                                    @csrf
                                                    <textarea name="comments" id="comments" class="comments" placeholder="Σχόλια">{{$timetableFile->comments}}</textarea>
                                                    <button class="btn btn-primary btn-block btn-sm rounded-2 py-2 m-1 no-spin" id="commentButton" value="{{$timetableFile->id}}">Υποβολή Σχολίων</button>
                                                
                                                </form>
                                            </div>
                                            @endif
                                            <hr>
                                        @endif
                                        
                                        
                                    @endforeach
                                @endforeach
                            </td>
                            <td> {{-- Κατάσταση Αρχή--}}
                                

                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>                                      
        </div>
    @endforeach
    
</x-layout>