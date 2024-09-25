<x-layout_school>
    @php
    $school = Auth::guard('school')->user(); //check which school is logged in
    $microapp = App\Models\Microapp::where('url', '/timetables')->first(); //fetch microapp
    $accepts = $microapp->accepts; //fetch microapp 'accepts' field
    $timetables = $school->timetables; //fetch school's timetables
    $timetable = null;
    $oldTimetables = null;
    if($timetables->count() > 0){
        $timetable = $timetables->filter(function ($item) {//Φέρε το πρόγραμμα που είναι υπο επεξεργασία
                        return $item->status == 0;
                    })->first();
        $oldTimetables = $timetables->filter(function ($item) {//Φέρε τα προηγούμενα οριστικοποιημένα προγράμματα
                        return $item->status == 1;
                    });    
    }
    
    //$timetable = $timetables->first(); //fetch first timetable
    $timetableFiles = $timetable?$timetable->files:''; //fetch timetable's files
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
@endpush
@push('title')
    <title>Ωρολόγια Προγράμματα</title>
@endpush
<div class="container">
@if($microapp->accepts == 0)
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        Η υποβολή Ωρολογίων Προγραμμάτων δεν είναι ενεργή αυτή τη στιγμή.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Κλείσιμο"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Κλείσιμο"></button>
    </div>
@endif
</div>
<div class="container">
    <h2 class="text-center">Υποβολή Ωρολογίων Προγραμμάτων</h2>
    	
        <div class="card border-primary rounded-2"> {{-- Card Start--}}
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-solid fa-laptop-file"></i> Έντυπα</h3>
                    <p class="m-0">Παρακαλούμε υποβάλλετε τα έντυπα που αφορούν το τρέχον Εβδομαδιαίο Ωρολόγιο Προγράμμα για το Σχολείο σας</p>
                </div>
            </div>
            <div class="card-body p-3">
            
                        <p class="m-0">Υποβάλλονται έντυπα σε μορφή .xlsx, .xls, .pdf < 10MB ανά υποβολή</p>
                    <div class="row justify-content-right">
                        <div class="text-center py-2">
                            <p class="m-0">Μπορείτε να επιλέξετε και να ανεβάσετε και περισσότερα από ένα αρχεία ταυτόχρονα.</p>
                        </div>
                        <form action="{{route('timetables.upload_files', ['timetable' => $timetable])}}" method="post" class="container-fluid" enctype="multipart/form-data">
                            @csrf
                            <div class="text-center">
                                <input  type="file" id="files" name="files[]" multiple required >
                                <input type="submit" value="Ανέβασμα" class="btn btn-info btn-block rounded-2 py-2"
                                @if($microapp->accepts == 0) disabled @endif >
                            </div>
                        </form>
                      
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row justify-content-right">
                        <div class="text-center py-2">
                            <p class="m-0">Αρχεία που έχουν υποβληθεί:</p>
                            @if($timetableFiles)                        <!-- Αν υπάρχουν καταχωρίσεις για αρχεία -->
                            @foreach($timetableFiles as $timetableFile) <!--  Για κάθε καταχώριση αρχείου -->
                                @php 
                                    $fileId = $timetableFile->id;
                                    $fileNames = json_decode($timetableFile->filenames_json, true);
                                    $filesCount = count($fileNames);      //Πόσα αρχεία (διορθώσεις) έχουν υποβληθεί γι αυτό το αρχείο
                                    $thisCount = 0;                         //Κράτα σε ένα δείκτη την τρέχουσα διόρθωση
                                @endphp
                                @foreach($fileNames as $serverFileName => $databaseFileName)
                                @php 
                                    $thisCount++;
                                    $comments = json_decode($timetableFile->comments);
                                @endphp
                                <div class="d-flex justify-content-start">
                                    <form action="{{route('timetables.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName])}}" method="get">
                                        <input type="submit"  @if($timetableFile->status == 3 && $thisCount == $filesCount) class="btn btn-success btn-block rounded-2 py-2 m-1" @else class="btn btn-info btn-block rounded-2 py-2 m-1" @endif  @if($thisCount != $filesCount)  style="padding: 0.25rem; margin: 0.25rem; font-size: 0.5rem;" @endif value="{{$databaseFileName}}" >
                                    </form>
                                    @if($timetableFile->status == 3 && $thisCount == $filesCount) <!-- Αν το αρχείο έχει εγκριθεί και είναι το τελευταίο, εμφάνισε την έγκριση και σε μήνυμα-->
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Έγκριση:</strong> Το αρχείο αυτό έχει ελεγχθεί και εγκριθεί. 
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Κλείσιμο"></button>
                                    </div>
                                    @endif
                                    @if(($comments && $comments->thisCount == $thisCount) && $thisCount == $filesCount) <!-- Αν υπάρχουν παρατηρήσεις και αν υπάρχουν παρατηρήσεις γι αυτη τη διόρθωση δείξε τις παρατηρήσεις μόνο για το τελευταίο αρχείο που πρέπει να διορθωθεί-->
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        <p><small>Παρακαλούμε να υποβάλλετε νέο, διορθωμένο αρχείο σύμφωνα με τις ακόλουθες επισημάνσεις:</small></p>
                                        <strong>Επισημάνσεις:</strong> {{$comments->comments}}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Κλείσιμο"></button>
                                    </div>
                                    @endif
                                    @if($timetableFile->status == 0 || ($timetableFile->status == 2 && $thisCount == $filesCount))
                                    <form action="{{route('timetables.delete_file', [ 'serverFileName' => $serverFileName, 'timetableFileId' => $fileId ])}}" method="get">
                                        <input type="submit" class="btn btn-danger btn-block btn-sm rounded-3" value="x" 
                                    >
                                    </form>
                                    @endif
                                    @if(($timetableFile->status == 1 || $timetableFile->status == 2) && $thisCount == $filesCount)
                                    <form action="{{route('timetables.upload_file', ['timetableFileId' => $fileId])}}" method="post" class="container-fluid" enctype="multipart/form-data">
                                        @csrf
                                        <div class="text-center">
                                        <input  type="file" id="file" name="file"  required >
                                        <input type="submit" value="Υποβολή" class="btn btn-info btn-block btn-sm rounded-2 py-2"
                                        >
                                        </div>
                                    </form>
                                    @endif
                                </div>
                                @endforeach
                                <hr>
                            @endforeach
                            @else
                                <p class="m-0">Δεν έχει υποβληθεί κάποιο αρχείο μέχρι στιγμής.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>                                          {{-- Card End--}}

        @if($oldTimetables && $oldTimetables->count() > 0)
        <div class="card border-primary rounded-2 my-2"> {{-- Card Start--}}
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-regular fa-square-check"></i> Οριστικοποιημένα Ωρολόγια Προγράμματα</h3>
                    <p class="m-0">Δείτε τα προηγούμενα οριστικοποιημένα Ωρολόγια Προγράμματα</p>
                </div>
            </div>
            @foreach($oldTimetables as $oldTimetable)
            <div class="card-body p-3">
            @foreach($oldTimetable->files as $timetableFile)
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
            </div>
            <hr>
            @endforeach
        </div>                                              {{-- Card End--}}
        @endif

        
	</div>
</div>

</div> {{-- Container --}}
</x-layout_school>
