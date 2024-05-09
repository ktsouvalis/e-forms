
    @php
        $active_schools = App\Models\School::where('is_active',1)->with('director','schregion.consultant')->get();
        $special = App\Models\School::where('code', 9999999)->first();
    @endphp

    @push('links')
        <link href="{{ asset('DataTables-1.13.4/css/dataTables.bootstrap5.css') }}" rel="stylesheet"/>
        <link href="{{ asset('Responsive-2.4.1/css/responsive.bootstrap5.css') }}" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="{{ asset('DataTables-1.13.4/js/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('DataTables-1.13.4/js/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/dataTables.responsive.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/responsive.bootstrap5.js') }}"></script>
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
        <script src="copylink.js"></script>
        <script src="datatable_init_schools.js"></script>
        <script src="{{asset('/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script>
            var schools = @json($active_schools);
        </script>
        <script>
            $(document).ready(function() {                
                $(document).on('mousedown', 'a[data-toggle="modal"]', function (event) {
                    event.preventDefault();
                    var schoolId = $(this).data('school-id');
                    var school = schools.find(school => school.id == schoolId);
                    $('#infoModal .modal-body p:eq(0)').text('Σχολείο: ' + school.name);
                    $('#infoModal .modal-body p:eq(1)').text('Τηλέφωνο Σχολείου: ' + school.telephone);
                    $('#infoModal .modal-body p:eq(2)').text('Διεύθυνση Σχολείου: ' + school.address);
                    if(school.director){
                        $('#infoModal .modal-body p:eq(3)').text('Διευθυντής/Διευθύντρια: ' + school.director.surname + ' ' + school.director.name);
                        $('#infoModal .modal-body p:eq(4)').text('Τηλέφωνο Διευθυντή/Διευθύντριας: ' + school.director.telephone);
                    }else{
                        $('#infoModal .modal-body p:eq(3)').text('Διευθυντής/Διευθύντρια: -');
                        $('#infoModal .modal-body p:eq(4)').text('Τηλέφωνο Διευθυντή/Διευθύντριας: -');
                    }
                    if(school.logged_in_at){
                        $('#infoModal .modal-body p:eq(5)').text('Τελευταία σύνδεση: ' + school.logged_in_at);
                    }else{
                        $('#infoModal .modal-body p:eq(5)').text('Τελευταία σύνδεση: -');
                    }
                    setTimeout(function() {
                        $('#infoModal').modal('show');
                    }, 50);
                });
                
            });
        </script>
    @endpush
    @push('title')
        <title>Σχολεία</title>
    @endpush
        <div class="row">
            <div class="col">
                <p class="h4">Σχολεία Διεύθυνσης</p>
                <p class="text-muted">(Κάνετε κλικ στο όνομα του σχολείου για περισσότερες πληροφορίες)</p>
            </div>
            <div class="col">
                @if(Illuminate\Support\Facades\DB::table('last_update_schools')->find(1))
                    <div class="col-md-4 py-3" style="max-width:15rem">
                        <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
                            <div>Τελευταία ενημέρωση <br><strong> {{Illuminate\Support\Facades\DB::table('last_update_schools')->find(1)->date_updated}}</strong></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="hstack gap-3">
            <button class="btn btn-secondary bi bi-clipboard my-2" id="copyCodeButton"> Αντιγραφή κωδικών σχολείων</button>
            <button class="btn btn-secondary bi bi-clipboard my-2" id="copyMailButton"> Αντιγραφή emails σχολείων</button>
            @if(Auth::check())
                @if(Auth::user()->isAdmin())
                    @if(App\Models\School::where('sent_link_mail',0)->count())
                        <form action="{{url("share_links_to_all/school")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση μαζικής αποστολής;')"> Μαζική Αποστολή Συνδέσμων</button>
                        </form>
                    @else
                        <form action="{{url("/reset_links_to_all/schools")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-danger bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση reset μαζικής αποστολής;')" > Reset Μαζικής Αποστολής Αποστολή Συνδέσμων</button>
                        </form>
                    @endif
                @endif
            @endif
        </div>
        <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title " id="messageModalLabel">Λοιπές Πληροφορίες Σχολείου</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <p></p>   
                    <b><p></p></b>
                    <p></p>
                    <hr>
                    <p></p>
                    <b><p></p></b>
                    <br>
                    <small style="text-align: end"><p class="text-muted"></p></small>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
                </div>
            </div>
        </div>
    </div>

        <div class="table-responsive">
            <table  id="dataTable" class="align-middle table table-sm table-striped table-bordered table-hover" style="font-size: small;" >
                <thead>
                    <tr>
                        <th class="align-middle">Αντιγραφή συνδέσμου</th>
                        <th id="search">Κωδικός</th>
                        <th id="search">Ονομασία</th>
                        <th id="search">Διευθυντής/Διευθύντρια</th>
                        <th id="search">Δήμος</th>
                        <th id="search">Σύμβουλος Εκπ/σης</th>
                        <th id="search">email</th>
                        <th id="search">Οργ.</th>
                        <th id="search">Λειτ.</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($active_schools as $school)
                    @php
                        $text = url("/school/$school->md5");
                    @endphp
                    <tr>
                        <td style="text-align:center">
                            <button class="copy-button btn btn-outline-secondary bi bi-clipboard" data-clipboard-text="{{$text}}"> </button>
                        </td>
                        <td >{{$school->code}}</td> 
                        <td>
                            <a href="#" class="no-spinner" data-toggle="modal" data-target="#infoModal" data-school-id="{{$school->id}}">{{$school->name}}</a>
                        </td>
                        @if($school->director)
                            <td >{{$school->director->surname}} {{$school->director->name}}</td>
                        @else
                            <td> - </td>
                        @endif
                        <td >{{$school->municipality->name}}</td>
                        <td >{{$school->schregion->consultant->surname}} {{$school->schregion->consultant->name}}</td>
                        <td >{{$school->mail}}</td>
                        <td >{{$school->organikotita}}</td>
                        <td >{{$school->leitourgikotita}}</td>      
                    </tr>
                @endforeach
                <tr>
                    <td style="text-align:center">
                        <button class="copy-button btn btn-outline-secondary bi bi-clipboard" data-clipboard-text="{{url("/school/$special->md5")}}"> </button>
                    </td>
                    <td >{{$special->code}}</td>
                    <td >{{$special->name}}</td>
                    @if($special->director)
                        <td >{{$special->director->surname}} {{$special->director->name}}</td>
                    @else
                        <td> - </td>
                    @endif
                    <td >{{$special->municipality->name}}</td>
                    <td >{{$special->schregion->consultant->surname}} {{$special->schregion->consultant->name}}</td>
                    <td >{{$special->mail}}</td>
                    <td >{{$special->organikotita}}</td>
                    <td >{{$special->leitourgikotita}}</td>
                </tr>
            </tbody>
            </table>
            </div>
        
        
        @can('upload', App\Models\School::class)
            <a href="{{url('/import_schools')}}" class="btn btn-primary bi bi-building-up my-2"> Μαζική Εισαγωγή Σχολείων</a>
        @endcan
        
           