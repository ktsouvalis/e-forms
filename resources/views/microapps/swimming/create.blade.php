<x-layout_teacher>
    @php
        //check which teacher is logged in
        $teacher = Auth::guard('teacher')->user();
        $microapp = App\Models\Microapp::where('url', '/swimming')->first();
       // $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        $swimming = $teacher->swimming()->first();
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
        <title>Υπεύθυνος κολύμβησης</title>
    @endpush
<div class="container">
    @if($microapp->accepts == 0)
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        Η υποβολή αιτήσεων δεν είναι ενεργή αυτή τη στιγμή.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<h2 class="text-center">Δήλωση επιθυμίας απόσπασης στο πρόγραμμα κολύμβησης</h2>
@include('microapps.swimming.inc_personal_data')
{{-- Δηλώσεις - Α Τμήμα Αίτησης --}}
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-8 pb-5">
    <div class="card border-primary rounded-0">
        <div class="card-header p-0">
            <div class="bg-info text-white text-center py-2">
                <h3><i class="fa-regular fa-square-check"></i> Δηλώσεις</h3>
                <p class="m-0">Παρακαλούμε υποβάλλετε τις ακόλουθες απαντήσεις σύμφωνα με τα στοιχεία που κατέχετε.</p>
            </div>
        </div>
        <div class="card-body p-3">
        <form action="{{route('swimming.store')}}" method="post">
            @csrf
            <!--Body-->
            <div class="form-group">
                
                <div class="input-group mb-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="specialty" @if($swimming && $swimming->specialty == 1) checked @endif>
                        <label class="form-check-label" for="flexSwitchCheckDefault">Κατέχω κύρια/πρώτη/δεύτερη ειδικότητα στο άθλημα της κολύμβησης ή/και στα αθλήματα του υγρού στίβου.</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group mb-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="licence" @if($swimming && $swimming->licence == 1) checked @endif>
                        <label class="form-check-label" for="flexSwitchCheckDefault">Κατέχω άδεια ασκήσεως επαγγέλματος προπονητή στο άθλημα της κολύμβησης ή στα αθλήματα υγρού στίβου, εγκεκριμένη από τη Γενική Γραμματεία Αθλητισμού.</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group mb-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="studied" @if($swimming && $swimming->studied == 1) checked @endif>
                        <label class="form-check-label" for="flexSwitchCheckDefault">Έχω διδαχθεί το αντικείμενο της κολύμβησης, όπως αυτό προκύπτει από την αναλυτική βαθμολογία του πτυχίου μου.</label>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <input type="submit" value="Υποβολή" class="btn btn-info btn-block rounded-2 py-2" @if($microapp->accepts == 0) disabled @endif>
            </div>
        </form>
        </div>
    </div>
    </div>
</div> {{-- End of row --}}
{{-- ΥΠΟΒΟΛΗ ΔΙΚΑΙΟΛΟΓΗΤΙΚΩΝ --}}
@if($swimming) {{-- If swimming exists show files submit --}}
<div class="col-12 col-md-8 col-lg-8 pb-5">
    <div class="card border-primary rounded-0">
        <div class="card-header p-0">
            <div class="bg-info text-white text-center py-2">
                <h3><i class="fa-regular fa-file-lines"></i> Υποβολή Δικαιολογητικών</h3>
                <p class="m-0">Υποβάλλονται δικαιολογητικά σε μορφή .pdf, .jpeg .png < 10MB ανά υποβολή</p>
            </div>
        </div>
        
        <div class="card-body p-3">
            <div class="row justify-content-right">
                <div class="text-center py-2">
                    <p class="m-0 fw-bold">Πρέπει να υποβληθούν αρχεία σύμφωνα με τις δηλώσεις που έχετε κάνει: 
                        <ul>
                            @if($swimming->specialty == 1) <li>Κύρια/Πρώτη/Δεύτερη ειδικότητα στο άθλημα της κολύμβησης ή/και στα αθλήματα του υγρού στίβου. </li>@endif
                            @if($swimming->licence == 1) <li> Άδεια ασκήσεως επαγγέλματος προπονητή στο άθλημα της κολύμβησης ή στα αθλήματα υγρού στίβου, εγκεκριμένη από τη Γενική Γραμματεία Αθλητισμού. </li> @endif
                            @if($swimming->studied == 1) <li> Διδαχθεί το αντικείμενο της κολύμβησης, όπως αυτό προκύπτει από την αναλυτική βαθμολογία του πτυχίου μου. </li> @endif
                        </ul>
                    </p>
                    <p class="m-0">Μπορείτε να επιλέξετε και να ανεβάσετε και περισσότερα από ένα αρχεία ταυτόχρονα.</p>
                </div>
                <form action="{{route('swimming.upload_files', ['swimming' => $swimming])}}" method="post" class="container-fluid" enctype="multipart/form-data">
                    @csrf
                    <div class="text-center">
                        <input  type="file" id="files" name="files[]" multiple required @if(($swimming->submitted == 1) || ($microapp->accepts == 0)) disabled @endif>
                        <input type="submit" value="Ανέβασμα" class="btn btn-info btn-block rounded-2 py-2"
                        @if(($swimming->submitted == 1) || ($microapp->accepts == 0)) disabled @endif >
                    </div>
                </form>
              
            </div>
        </div>
        <div class="card-body p-3">
            <div class="row justify-content-right">
                <div class="text-center py-2">
                    <p class="m-0">Αρχεία που έχουν υποβληθεί:</p>
                    @if($swimming->files_json)
                        @php 
                            $count = 1;
                            $fileNames = json_decode($swimming->files_json, true);
                        @endphp
                        @foreach($fileNames as $serverFileName => $databaseFileName)
                        
                        <div class="d-flex justify-content-between">
                            <form action="{{route('swimming.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName])}}" method="get">
                                <input type="submit" class="btn btn-info btn-block rounded-2 py-2 m-1" value="{{$databaseFileName}}" >
                            </form>
                            <form action="{{route('swimming.delete_file', [ 'swimming' => $swimming, 'serverFileName' => $serverFileName ])}}" method="get">
                                <input type="submit" class="btn btn-danger btn-block rounded-3" value="Διαγραφή" 
                                @if($swimming->submitted == 1 || $microapp->accepts == 0) disabled @endif >
                            </form>
                        </div>
                        @php $count++; @endphp
                        @endforeach
                    @else
                        <p class="m-0">Δεν έχει υποβληθεί κάποιο δικαιολογητικό</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif {{-- End of files submit --}}
</div> {{-- End of container --}}

</x-layout_teacher>