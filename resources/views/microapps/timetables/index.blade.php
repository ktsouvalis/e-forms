<x-layout>
    @push('links')
        <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    @endpush
    @push('scripts')
        <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
        <script src="{{asset('datatable_init.js')}}"></script>
        <script src="{{asset('check_timetable.js')}}"></script>
        <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
        <script>
            var checkTimetableStatusUrl = '{{ route("timetables.change_status", ["timetableFile" =>"mpla"]) }}';
            
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                // Function to filter table rows based on selected checkboxes
                function filterTable() {
                    var selectedSchoolKind = [];
                    $('.filter-checkbox:checked').each(function() {
                        selectedSchoolKind.push($(this).val());
                    });
    
                    $('.selected-filters').text(selectedSchoolKind.join(', '));
    
                    $('tbody tr').each(function() {
                        var rowSchoolKind = $(this).data('school-kind');
                        if (selectedSchoolKind.length === 0 || selectedSchoolKind.includes(rowSchoolKind)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
    
                // Attach change event to checkboxes
                $('.filter-checkbox').on('change', function() {
                    filterTable();
                });
    
                // Initial filter
                filterTable();
            });
        </script>
    @endpush
    <style>
        .filter-group {
            margin-bottom: 20px;
        }
        .filter-group-title {
            font-weight: bold;
        }
        .checkbox-group label {
            display: block;
        }
        .selected-filters {
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
    @php
        $microapp = App\Models\Microapp::where('url', '/timetables')->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        $timetables_schools = $microapp->stakeholders;
        $timetables = App\Models\microapps\Timetables::orderBy('status', 'asc')
                                                ->orderBy('created_at', 'desc')->get();
        // dd($timetables_schools, $timetables);
    @endphp
    @push('title')
        <title>{{$microapp->name}}</title>
    @endpush    
    @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
    <div class="container-fluid">
        <h2 class="text-center">Ωρολόγια Προγράμματα</h2>
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
        <div class="row justify-content-center">
            <div class="pb-5">
                <div class="filter-group-title">
                    <h3>Είδος Σχολείου</h3>
                </div>
                <div class="selected-filters"></div>
                    <div class="checkbox-group">
                        <label><input type="checkbox" class="filter-checkbox" value="Δημοτικά"> Δημοτικά <small>(157)</small></label>
                        <label><input type="checkbox" class="filter-checkbox" value="Νηπιαγωγεία"> Νηπιαγωγεία <small>(153)</small></label>
                    </div>
                </div>
            
                <table  id="dataTable" class="display table table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th id="search">Σχολείο</th>
                            <th id="search">Αρχεία</th>
                            <th id="search">Κατάσταση</th>
                            <th id="search">Είδος Σχολείου</th>
                            <th id="search">Κωδικός</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($timetables as $timetable)
                        <tr @if($timetable->status == 1) style="background-color: #d1ecf1;" @endif>
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
                            <td data-school-kind="@if($timetable->school->primary == 1) primary @else secondary @endif">@if($timetable->school->primary == 1) Δημοτικό @else Νηπιαγωγείο @endif</td>
                            <td>{{$timetable->school->code}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>                                      
        </div>
   
    
</x-layout>