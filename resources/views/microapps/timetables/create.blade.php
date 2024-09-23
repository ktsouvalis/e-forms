<x-layout_school>
    @php
    $school = Auth::guard('school')->user(); //check which school is logged in
    $microapp = App\Models\Microapp::where('url', '/timetables')->first(); //fetch microapp
    $accepts = $microapp->accepts; //fetch microapp 'accepts' field
    $timetables = $school->timetables; //fetch school's timetables
    $timetable = $timetables->first(); //fetch first timetable
    $timetableFiles = $timetable?$timetable->files:''; //fetch timetable's files
    //dd($timetableFiles);
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
<div class="container">
    @if($microapp->accepts == 0)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            Η υποβολή αιτήσεων δεν είναι ενεργή αυτή τη στιγμή.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <h2 class="text-center">Υποβολή Ωρολογίων Προγραμμάτων</h2>
	{{-- @include('microapps.secondments.inc_personal_data') --}}
    {{-- Δηλώσεις - Α Τμήμα Αίτησης --}}
    <div class="row justify-content-center">
		<div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0"> {{-- Card Start--}}
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-regular fa-square-check"></i> Έντυπα</h3>
                    <p class="m-0">Παρακαλούμε υποβάλλετε τα έντυπα που αφορούν τα Εβδομαδιαία Ωρολόγια Προγράμματα για το Σχολείο σας</p>
                </div>
            </div>
            <div class="card-body p-3">
            
            </div>
        </div>                                      {{-- Card End--}}
        <div class="col-12 col-md-8 col-lg-8 ">
        <div class="card border-primary rounded-0"> {{-- Card Start--}}
                <div class="card-header p-0">
                    <div class="bg-info text-white text-center py-2">
                        <h3><i class="fa-regular fa-file-lines"></i> Υποβολή Ηλεκτρονικών Αρχείων</h3>
                        <p class="m-0">Υποβάλλονται δικαιολογητικά σε μορφή .xlsx, .xls, .pdf < 10MB ανά υποβολή</p>
                    </div>
                </div>
                
                <div class="card-body p-3">
                    <div class="row justify-content-right">
                        <div class="text-center py-2">
                            <p class="m-0">Μπορείτε να επιλέξετε και να ανεβάσετε και περισσότερα από ένα αρχεία ταυτόχρονα.</p>
                        </div>
                        <form action="{{route('timetables.upload_files', ['timetable' => $timetable])}}" method="post" class="container-fluid" enctype="multipart/form-data">
                            @csrf
                            <div class="text-center">
                                <input  type="file" id="files" name="files[]" multiple required >
                                <input type="submit" value="Ανέβασμα" class="btn btn-info btn-block rounded-2 py-2"
                                 >
                            </div>
                        </form>
                      
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row justify-content-right">
                        <div class="text-center py-2">
                            <p class="m-0">Αρχεία που έχουν υποβληθεί:</p>
                            @if($timetableFiles)
                            @foreach($timetableFiles as $timetableFile)
                                @php 
                                    $count = 1;
                                    $fileId = $timetableFile->id;
                                    $fileNames = json_decode($timetableFile->filenames_json, true);
                                @endphp
                                @foreach($fileNames as $serverFileName => $databaseFileName)
                                
                                <div class="d-flex justify-content-between">
                                    <form action="{{route('timetables.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName])}}" method="get">
                                        <input type="submit" class="btn btn-info btn-block rounded-2 py-2 m-1" value="{{$databaseFileName}}" >
                                    </form>
                                    <form action="{{route('timetables.delete_file', [ 'serverFileName' => $serverFileName, 'timetableFileId' => $fileId ])}}" method="get">
                                        <input type="submit" class="btn btn-danger btn-block rounded-3" value="Διαγραφή" 
                                    >
                                    </form>
                                </div>
                                @php $count++; @endphp
                                @endforeach
                            @endforeach
                            @else
                                <p class="m-0">Δεν έχει υποβληθεί κάποιο αρχείο μέχρι στιγμής.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>                                          {{-- Card End--}}
        </div>

        
	</div>
</div>

</div> {{-- Container --}}
</x-layout_school>
