<x-layout>
    @php
        $all_teachers = App\Models\Teacher::all();
    @endphp
    @push('links')
        <link href="DataTables-1.13.4/css/dataTables.bootstrap5.css" rel="stylesheet"/>
        <link href="Responsive-2.4.1/css/responsive.bootstrap5.css" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="DataTables-1.13.4/js/jquery.dataTables.js"></script>
        <script src="DataTables-1.13.4/js/dataTables.bootstrap5.js"></script>
        <script src="Responsive-2.4.1/js/dataTables.responsive.js"></script>
        <script src="Responsive-2.4.1/js/responsive.bootstrap5.js"></script>
        <script>
            document.getElementById("copyCodeButton").addEventListener("click", function () {
                var codeColumn = document.querySelectorAll("#dataTable tbody td:nth-child(2)");
                var codeValues = Array.from(codeColumn).map(function (cell) {
                    return cell.textContent.trim();
                });
                var concatenatedValues = codeValues.join(",");
                var tempTextArea = document.createElement("textarea");
                tempTextArea.value = concatenatedValues;
                document.body.appendChild(tempTextArea);
                tempTextArea.select();
                document.execCommand("copy");
                document.body.removeChild(tempTextArea);
                alert("Αντιγράφτηκαν " + codeValues.length + " αναγνωριστικά!");
            });
        </script>
        <script>
            document.getElementById("copyMailButton").addEventListener("click", function () {
                var mailColumn = document.querySelectorAll("#dataTable tbody td:nth-child(7)");
                var mailValues = Array.from(mailColumn).map(function (cell) {
                    return cell.textContent.trim();
                });
                var concatenatedValues = mailValues.join(",");
                var tempTextArea = document.createElement("textarea");
                tempTextArea.value = concatenatedValues;
                document.body.appendChild(tempTextArea);
                tempTextArea.select();
                document.execCommand("copy");
                document.body.removeChild(tempTextArea);
                alert("Αντιγράφτηκαν " + mailValues.length + " emails");
            });
        </script>
        <script src="{{asset('copylink.js')}}"></script>
        <script src="datatable_init_teachers.js"></script>
        <script src="{{asset('/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script>
            var teachers = @json($all_teachers);
        </script>
        <script>
            $(document).ready(function() {                
                $(document).on('mousedown', 'a[data-toggle="modal"]', function (event) {
                    event.preventDefault();
                    var teacherId = $(this).data('teacher-id');
                    var teacher = teachers.find(teacher => teacher.id == teacherId);
                    $('#infoModal .modal-body p:eq(0)').text('Επώνυμο: ' + teacher.surname);
                    $('#infoModal .modal-body p:eq(1)').text('Όνομα: ' + teacher.name);
                    $('#infoModal .modal-body p:eq(2)').text('Α.Μ.: ' + teacher.am);
                    $('#infoModal .modal-body p:eq(3)').text('Τηλέφωνο: ' + teacher.telephone);
                    $('#infoModal .modal-body p:eq(4)').text('Mail ΠΣΔ: ' + teacher.sch_mail);
                    $('#infoModal .modal-body p:eq(5)').text('Τελευταία σύνδεση: ' + teacher.logged_in_at);
                    setTimeout(function() {
                        $('#infoModal').modal('show');
                    }, 50);
                });
                
            });
        </script>
    @endpush
    @push('title')
        <title>Εκπαιδευτικοί</title>
    @endpush
   
    <body> 
        <div class="row">
            <div class="col">
                <p class="h4">Εκπαιδευτικοί Διεύθυνσης</p>
                <p class="text-muted">Δεν περιλαμβάνονται οι ΕΕΠ, ΕΒΠ, οι ιδιωτικοί εκπαιδευτικοί και όσοι συμπληρώνουν ωράριο από τη Δ/θμια</p>
                <p class="text-muted">(Κάνετε κλικ στο επώνυμο του εκπαιδευτικού για περισσότερες πληροφορίες)</p>
            </div>
            <div class="col">
                @if(Illuminate\Support\Facades\DB::table('last_update_teachers')->find(1))
                    <div class="col-md-4 py-3" style="max-width:15rem">
                        <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
                            <div>Τελευταία ενημέρωση<br><strong> {{Illuminate\Support\Facades\DB::table('last_update_teachers')->find(1)->date_updated}}</strong></div>
                        </div>
                    </div>
                @endif
            </div>    
        </div>    
    
    <div class="hstack gap-3">
        <button class="btn btn-secondary bi bi-clipboard my-2" id="copyCodeButton"> Αντιγραφή ΑΦΜ εκπαιδευτικών</button>
        <button class="btn btn-secondary bi bi-clipboard my-2" id="copyMailButton"> Αντιγραφή emails εκπαιδευτικών</button>
        @if(Auth::user()->isAdmin())
            @if(App\Models\Teacher::where('sent_link_mail',0)->count())
                <form action="{{url("share_links_to_all/teacher")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση μαζικής αποστολής;')"> Μαζική Αποστολή Συνδέσμων</button>
                </form>
            @else
                <form action="{{url("/reset_links_to_all/teachers")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση reset μαζικής αποστολής;')" > Reset Μαζικής Αποστολής Αποστολή Συνδέσμων</button>
                </form>
            @endif
        @endif
    </div>
    <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Λοιπές Πληροφορίες Εκπαιδευτικού</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p></p>   
                    <p></p>
                    <p></p>
                    <p></p>
                    <p></p>
                    <br>
                    <small style="text-align: end"><p class="text-muted"></p></small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table  id="dataTable" class="align-middle table table-sm table-striped table-bordered table-hover"  style="font-size: small">
            <thead>
                <tr>
                    <th class="align-middle">Αντιγραφή συνδέσμου</th>
                    <th id="search">ΑΦΜ</th>
                    <th id="search">Επώνυμο</th>
                    <th id="search">Όνομα</th>
                    <th id="search">Κλάδος</th>
                    <th id="search">Σχ. Εργ.</th>
                    <th id="">email</th>
                    <th id="search">Υπηρέτηση</th>
                    <th id="search">Δήμος Υπηρέτησης</th>
                    <th id="search">Οργ.</th>
                </tr>
            </thead>
            <tbody>
        
            @foreach($all_teachers as $teacher)
            @if($teacher->active)
                @php
                    $text = url("/teacher/$teacher->md5");
                @endphp
                
                <tr> 
                    <td style="text-align:center">
                        <button class="copy-button btn btn-outline-secondary bi bi-clipboard" data-clipboard-text="{{$text}}"> </button>
                    </td>
                    <td>{{$teacher->afm}}</td>
                    <td>
                        <a href="#" data-toggle="modal" data-target="#infoModal" data-teacher-id="{{$teacher->id}}">
                            {{$teacher->surname}}
                        </a>
                    </td>
                    <td>{{$teacher->name}}</td>
                    <td>{{$teacher->klados}}</td>
                    <td>{{$teacher->sxesi_ergasias->name}}</td>
                    <td>{{$teacher->mail}}</td>
                    @if($teacher->ypiretisi_id!=null)
                        <td>{{$teacher->ypiretisi->name}}</td>
                        @if($teacher->ypiretisi->municipality)
                            <td>{{$teacher->ypiretisi->municipality->name}}</td>
                        @else
                            <td>-</td>
                        @endif
                    @else
                        <td>-</td>
                        <td>-</td>
                    @endif
                    <td>{{$teacher->organiki->name}}</td>
                </tr>
            @endif  
            @endforeach
        </tbody>
        </table>
    </div>
        
    @can('upload', App\Models\Teacher::class)
        @php
            $dir_info = DB::table('directorate_info')->find(1);
        @endphp
        @if($dir_info)
            <a href="{{url('/import_teachers')}}" class="btn btn-primary bi bi-person-lines-fill my-2"> Μαζική Εισαγωγή Εκπαιδευτικών</a>
        @else
        <div class='alert alert-danger text-center'>
            Ο Διαχειριστής πρέπει να ενημερώσει τις πληροφορίες της Διεύθυνσης από τη λειτουργία Εντολές Laravel <br> για να μπορείτε να ανανεώσετε στοιχεία εκππαιδευτικών
        </div>
        @endif
    @endcan

</x-layout>