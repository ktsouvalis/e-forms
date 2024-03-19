<x-layout>
    @push('links')
        <link href="DataTables-1.13.4/css/dataTables.bootstrap5.css" rel="stylesheet"/>
        <link href="Responsive-2.4.1/css/responsive.bootstrap5.css" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="DataTables-1.13.4/js/jquery.dataTables.js"></script>
        <script src="DataTables-1.13.4/js/dataTables.bootstrap5.js"></script>
        <script src="Responsive-2.4.1/js/dataTables.responsive.js"></script>
        <script src="Responsive-2.4.1/js/responsive.bootstrap5.js"></script>
        <script src="copycolumn.js"></script>
        <script src="copycolumn2.js"></script>
        <script src="copylink.js"></script>
        <script src="datatable_init_schools.js"></script>
    @endpush
    @push('title')
        <title>Σχολεία</title>
    @endpush
    @php
        $active_schools = App\Models\School::where('is_active',1)->get();
        $special = App\Models\School::where('code', 9999999)->first();
    @endphp
        <p class="h4">Σχολεία Διεύθυνσης</p>
        @if(Illuminate\Support\Facades\DB::table('last_update_schools')->find(1))
        <div class="col-md-4 py-3" style="max-width:15rem">
            <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
                <div>Τελευταία ενημέρωση <br><strong> {{Illuminate\Support\Facades\DB::table('last_update_schools')->find(1)->date_updated}}</strong></div>
            </div>
        </div>
        @endif
        
        <div class="hstack gap-3">
            <button class="btn btn-secondary bi bi-clipboard my-2" id="copyCodeButton"> Αντιγραφή κωδικών σχολείων</button>
            <button class="btn btn-secondary bi bi-clipboard my-2" id="copyMailButton"> Αντιγραφή emails σχολείων</button>
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
        </div>
{{-- Στον πίνακα πρέπει στην 4η στήλη (4) να βρίσκεται το e-mail και το ΑΦΜ στην 7η στήλη (7)  --}}
{{-- Στις στήλες 2, 4, 5, 6, 7 και 11 μειώνεται το πλάτος του serch field με javascript  --}}
{{-- Αν αλλαχτεί κάτι από τα παραπάνω στον πίνακα όπως σειρά στηλών κλπ θα πρέπει να αλλαχτεί και στα javascript: datatable_init_schools.js, copycolumn.js, copycolumn2.js  --}}
{{-- Τα copycolumn αρχεία είναι κοινά για τους πίνακες teachers και schools. Αλλαγή στο ένα θα επηρεάσει και το άλλο. --}}
        <div class="table-responsive">
            <table  id="dataTable" class="align-middle table table-sm table-striped table-bordered table-hover" style="font-size: small;" >
                <thead>
                    <tr>
                        <th class="align-middle">Αντιγραφή συνδέσμου</th>
                        <th id="search">Ονομασία</th>
                        <th id="search">Τηλ.</th>
                        <th id="search">email</th>
                        <th id="search">Οργ.</th>
                        <th id="search">Λειτ.</th>
                        <th id="search">Κωδικός</th>
                        <th id="search">Δήμος</th>
                        {{-- <th id="search">Διευθυντής</th> --}}
                        {{-- <th id="search">last login</th> --}}
                        <th id="search">Σύμβουλος Εκπαίδευσης</th>
                        {{-- <th id="search">Μαζική Αποστολή</th> --}}
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
                        <td >{{$school->name}}</td>
                        <td >{{$school->telephone}}</td>
                        <td >{{$school->mail}}</td>
                        <td >{{$school->organikotita}}</td>
                        <td >{{$school->leitourgikotita}}</td>
                        <td >{{$school->code}}</td>
                        <td >{{$school->municipality->name}}</td>

                        {{-- @if($school->director)
                        <td>{{$school->director->surname}} {{$school->director->name}}</td>
                        @else
                        <td> - </td>
                        @endif --}}
                        
                        {{-- @if($school->logged_in_at)
                            <td>{{Illuminate\Support\Carbon::parse($school->logged_in_at)}}</td>
                        @else
                            <td > - </td>
                        @endif --}}
                        @if($school->schregion)
                            <td>{{$school->schregion->consultant->surname}} {{$school->schregion->consultant->name}}</td>
                        @else
                            <td>-</td>
                        @endif
                        
                        {{-- @if($school->sent_link_mail)
                            <td style="text-align:center"><i class="btn btn-success bi bi-check2-circle"></i></td>
                        @else
                            <td style="text-align:center"> - </td>
                        @endif --}}
                    </tr>
                @endforeach
                <tr>
                    <td style="text-align:center">
                        <button class="copy-button btn btn-outline-secondary bi bi-clipboard" data-clipboard-text="{{url("/school/$special->md5")}}"> </button>
                    </td>
                    <td >{{$special->name}}</td>
                    <td >{{$special->telephone}}</td>
                    <td >{{$special->mail}}</td>
                    <td >{{$special->organikotita}}</td>
                    <td >{{$special->leitourgikotita}}</td>
                    <td >{{$special->code}}</td>
                    <td >{{$special->municipality->name}}</td>
                    {{-- <td >{{$special->director->surname}} {{$special->director->name}}</td> --}}
                    {{-- <td >{{Illuminate\Support\Carbon::parse($special->logged_in_at)}}</td> --}}
                    <td >{{$special->schregion->consultant->surname}} {{$special->schregion->consultant->name}}</td>
                    {{-- <td style="text-align:center"><i class="btn btn-success bi bi-check2-circle"></i></td> --}}
                </tr>
            </tbody>
            </table>
            </div>
        
        
        @can('upload', App\Models\School::class)
            <a href="{{url('/import_schools')}}" class="btn btn-primary bi bi-building-up my-2"> Μαζική Εισαγωγή Σχολείων</a>
        @endcan
  </x-layout>
        
           