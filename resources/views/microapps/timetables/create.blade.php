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
    $lockNewFileSubmission = false;
    if($timetableFiles){
        foreach($timetableFiles as $timetableFile){//Αν υπάρχει έστω και ένα πρόγραμμα που είναι σε αναμονή διορθώσεων κλείδωσε την υποβολή νέου αρχείου
            if($timetableFile->status == 1){
                $lockNewFileSubmission = true;
            }
        }
    }
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
    <div>
        <button class="btn btn-primary m-3" type="button" data-bs-toggle="collapse" data-bs-target="#instructionsList" aria-expanded="false" aria-controls="instructionsList">
            <h6>Για αναλυτική περιγραφή της διαδικασίας υποβολής και έγκρισης Ωρολογίων Προγραμμάτων πατήστε εδώ</h6>
        </button>
        <div class="collapse" id="instructionsList">    
            <ul class="list-group m-3">
                <li class="list-group-item">1) <strong>Αρχική Υποβολή Εντύπων Ωρολογίου Προγράμματος από το Σχολείο </strong>
                    <br>
                    <br>Το Σχολείο υποβάλλει όλα τα σχετικά έντυπα του Ωρολογίου Προγράμματος.
                    <br>Προτιμότερη είναι η χρήση εντύπων σε μορφή excel ή word για ευκολία στην επεξεργασία και ελαχιστοποίηση του όγκου των αρχείων.
                    <br>Τα έντυπα αναμένουν έγκριση ή αίτημα για διόρθωση από τη Διεύθυνση.
                    <br><em>Τα έτυπα σε αυτή τη φάση μπορούν να διαγραφούν και να υποβληθούν νέα από τη Σχολική Μονάδα.</em></li>
                <li class="list-group-item">2) <strong>Έλεγχος Ωρολογίου Προγράμματος από τη Διεύθυνση και υποβολή διορθώσεων από το Σχολείο.</strong>
                    <br>
                    <br>Ο αρμόδιος υπάλληλος της Διεύθυνσης ελέγχει τα έντυπα και αν εντοπιστούν λάθη επικοινωνεί με το Σχολείο για υποβολή διορθώσεων.
                    <br> Το Σχολείο υποβάλλει νέο, διορθωμένο αρχείο με τις απαραίτητες διορθώσεις σε πεδίο που ενεργοποιείται δίπλα στο προηγούμενο έντυπο.
                    <br><em>Σε περίπτωση που κάποιο ή όλα τα έντυπα του Ωρολογίου Προγράμματος εγκριθούν από τη Διεύθυνση, το βήμα αυτό παραλείπεται για το αρχείο.</em>
                </li>
                <li class="list-group-item">3) <strong>Έγκριση εντύπων Ωρολογίου Προγράμματος από τη Διεύθυνση και παραλαβή από το Σχολείο.</strong>
                    <br>
                    <br>Όταν ολοκληρωθεί ο έλεγχος των εντύπων, η Διεύθυνση εγκρίνει τα έντυπα και το πρόγραμμα κλειδώνει.
                    <br>Το Σχολείο προσκομίζει τα έντυπα σφραγισμένα και υπογεγραμμένα σε τρία αντίγραφα στο Πρωτόκολλο της Διεύθυνσης. 
                    <br>Όταν σφραγιστούν και υπογραφούν ειδοποιείται το Σχολείο να παραλάβει το εγκεκριμένο πρόγραμμα με σφραγίδα και υπογραφή σε δύο αντίγραφα (Το ένα μένει στη Διεύθυνση).
                    <br><em>Στην ενότητα "Έντυπα" μπορεί να υποβληθεί νέο πρόγραμμα από τη Σχολική Μονάδα σε περίπτωση μεταβολής του διδακτικού προσωπικού ή άλλη περίπτωση.</em>
                    <br><em>Τα εγκεκριμένα προγράμματα εμφανίζονται στην ενότητα (που δημιουργείται όταν υπάρχουν οριστικοποιημένα προγράμματα) "Οριστικοποιημένα Ωρολόγια Προγράμματα".</em>
                </li>  
            </ul>
        </div>
    </div>
        <div class="card border-primary rounded-2"> {{-- Card Start--}}
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-solid fa-laptop-file"></i> Έντυπα</h3>
                    <p class="m-0">Παρακαλούμε υποβάλλετε τα έντυπα που αφορούν το τρέχον Εβδομαδιαίο Ωρολόγιο Προγράμμα για το Σχολείο σας</p>
                </div>
            </div>
            <div class="card-body p-3">
                <p class="m-0">Υποβάλλονται έντυπα σε μορφή .xlsx, .xls, .docx, .doc, .pdf, jpg, jpeg, png < 10MB ανά υποβολή</p>
                <div class="row justify-content-right">
                    <div class="text-center py-2">
                        <p class="m-0">Μπορείτε να επιλέξετε και να ανεβάσετε και περισσότερα από ένα αρχεία ταυτόχρονα.</p>
                    </div>
                    <form action="{{route('timetables.upload_files', ['timetable' => $timetable])}}" method="post" class="container-fluid" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center">
                            <input  type="file" id="files" name="files[]" multiple required >
                            <input type="submit" value="Ανέβασμα" class="btn btn-info btn-block rounded-2 py-2"
                            @if($microapp->accepts == 0 || $lockNewFileSubmission == true) disabled @endif >
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
                            <p>(<small>Κατάσταση αρχείου: </small> 
                                @if($timetableFile->status == 0) Αρχική Υποβολή @endif    
                                @if($timetableFile->status == 1) Πρέπει να υποβληθούν Διορθώσεις @endif
                                @if($timetableFile->status == 2) Έχουν υποβληθεί Διορθώσεις @endif
                                @if($timetableFile->status == 3) Έχει Εγκριθεί @endif
                                 )</p>
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
