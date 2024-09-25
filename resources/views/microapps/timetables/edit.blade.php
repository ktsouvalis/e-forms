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
        $school = $timetable->school;
        $schoolName = $school->name;
    @endphp
        @push('title')
        <title>ΕΩΠΔ {{$schoolName}}</title>
    @endpush 
    <form action="{{url('/timetables')}}">
        <button class="btn btn-primary btn-block rounded-2 py-2 m-1 no-spin" title="Πίσω">Επιστροφή</button>
    </form>  
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                {{ $schoolName }}
            </div>
            <div class="card-body">
                <h5 class="card-title">Αρχεία</h5>
                <ul class="list-group list-group-flush">
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
                                        @if(($comments && $comments->thisCount == $thisCount) && $thisCount != $filesCount)
                                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                                            <strong>Επισημάνσεις:</strong> {{$comments->comments}}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Κλείσιμο"></button>
                                        </div>
                                        @endif
                                        <form action="{{route('timetables.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName])}}" method="get" class="container-fluid">
                                            <input type="submit" id="{{$fileId}}_{{$thisCount}}"
                                            @if($timetableFile->status == 3 && $thisCount == $filesCount) class="btn btn-success btn-block rounded-2 py-2 m-1 no-spin" @else class="btn btn-info btn-block rounded-2 py-2 m-1 no-spin" @endif  
                                            @if($thisCount != $filesCount)  style="padding: 0.25rem; margin: 0.25rem; font-size: 0.5rem;" @endif title="Λήψη αρχείου" value="{{$databaseFileName}}">
                                        </form>
                                        @if($thisCount == $filesCount)
                                        <label for="{{$timetableFile->id}}">Κατάσταση Αρχείου </label>
                                            <select name="{{$timetableFile->id}}" id="{{$thisCount}}" class="changeTimetableStatus">
                                                <option value="0" @if($timetableFile->status == 0) selected @endif disabled >Αρχική Υποβολή*</option>
                                                <option value="1" @if($timetableFile->status == 1) selected @endif>Αναμονή Διορθώσεων</option>
                                                <option value="2" @if($timetableFile->status == 2) selected @endif disabled>Υποβολή Διορθώσεων*</option>
                                                <option value="3" @if($timetableFile->status == 3) selected @endif>Έγκριση</option>
                                                <option value="0" disabled>Τα πεδία με * αφορούν ενέργειες του Σχολείου</option>
                                            </select>
                                            @php $comment = ''; @endphp
                                            @if($timetableFile->status == 1)
                                                @if($comments && $comments->thisCount == $thisCount)
                                                    @php $comment = $comments->comments @endphp
                                                @endif
                                            <div class="hideAndAppearOnTheFly{{$timetableFile->id}}">
                                                <form action="{{route('timetables.comment', ['timetableFile' => $timetableFile->id, 'thisCount' => $thisCount])}}" method="post" class="container-fluid" id="comment_form_{{$timetableFile->id}}">
                                                    @csrf
                                                    <textarea name="comments" id="comments" class="comments" placeholder="Σχόλια" style="width: 80%">{{$comment}}</textarea>
                                                    <button class="btn btn-primary btn-block btn-sm rounded-2 py-2 m-1 no-spin" id="commentButton" value="{{$timetableFile->id}}">Υποβολή Σχολίων</button>
                                                
                                                </form>
                                            </div>
                                            @endif
                                            <div class="{{$timetableFile->status}}hideAndAppearOnTheFly{{$timetableFile->id}}">
                                                <form action="/timetables/comment/${timetableFileId}/${fileCount}" method="post" class="container-fluid" id="comment_form_${timetableFileId}">
                                                    <input type="hidden" name="_token" value="${csrfToken}">
                                                    <textarea name="comments" id="comments" class="comments" placeholder="Σχόλια" style="width: 80%"></textarea>
                                                    <button class="btn btn-primary btn-block btn-sm rounded-2 py-2 m-1 no-spin" id="commentButton" value="${timetableFileId}">Υποβολή Σχολίων</button>
                                                </form>
                                            </div>
                                            <hr>
                                        @endif
                                        
                                        
                                    @endforeach
                                @endforeach
                </ul>
            </div>
        </div>
    </div>

</x-layout>