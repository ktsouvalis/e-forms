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
        <script src="datatable_init_teachers.js"></script>
    @endpush
    @push('title')
        <title>Εκπαιδευτικοί</title>
    @endpush
    @php
        $all_teachers = App\Models\Teacher::all();
    @endphp
    <body>     
    <p class="h4">Εκπαιδευτικοί Διεύθυνσης</p>
    <p class="text-muted">Δεν περιλαμβάνονται οι ΕΕΠ, ΕΒΠ, οι ιδιωτικοί εκπαιδευτικοί και όσοι συμπληρώνουν ωράριο από τη Δ/θμια</p>
    
    <div class="hstack gap-3">
    @if(Illuminate\Support\Facades\DB::table('last_update_teachers')->find(1))
    <div class="col-md-4 py-3" style="max-width:15rem">
        <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
            <div>Τελευταία ενημέρωση<br><strong> {{Illuminate\Support\Facades\DB::table('last_update_teachers')->find(1)->date_updated}}</strong></div>
        </div>
    </div>
    @endif
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
{{-- στον πίνακα πρέπει στην πρώτη στήλη (1) να είναι η αντιγραφή συνδέσμου, στην 4η στήλη (4) το e-mail και το ΑΦΜ στην 7η στήλη (7)  --}}
{{-- Στις στήλες 2, 4, 5, 6, 7 και 11 μειώνεται το πλάτος του serch field με javascript  --}}
{{-- Αν αλλαχτεί κάτι από τα παραπάνω στον πίνακα όπως σειρά στηλών κλπ θα πρέπει να αλλαχτεί και στα javascript: datatable_init_teachers.js, copycolumn.js, copycolumn2.js  --}}
{{-- Τα copycolumn αρχεία είναι κοινά για τους πίνακες teachers και schools. Αλλαγή στο ένα θα επηρεάσει και το άλλο. --}}
    <div class="table-responsive">
        <table  id="dataTable" class="align-middle table table-sm table-striped table-bordered table-hover"  style="font-size: small">
            <thead>
                <tr>
                    <th class="align-middle">Αντιγραφή συνδέσμου</th>
                    {{-- <th class="align-middle">Αποστολή συνδέσμου</th> --}}
                    <th id="search">Επώνυμο</th>
                    <th id="search">Όνομα</th>
                    <th id="search">email</th>
                    <th id="search">Τηλ.</th>
                    <th id="search">Κλάδος</th>
                    <th id="search">ΑΦΜ</th>
                    <th id="search">Οργ.</th>
                    <th id="search">Υπηρέτηση</th>
                    <th id="search">AΜ</th>
                    <th id="search">Σχ. Εργ.</th>
                    <th id="search">email ΠΣΔ</th>
                    <th id="search">last login</th> 
                    <th id="search">Μαζική Αποστολή</th>
                </tr>
            </thead>
            <tbody>
        
            @foreach($all_teachers as $teacher)
            @if($teacher->active)
                @php
                    $date=null;
                    if($teacher->logged_in_at) 
                        $date = Illuminate\Support\Carbon::parse($teacher->logged_in_at);
                    $text = url("/teacher/$teacher->md5");
                    
                @endphp
                
                <tr>  
                    <td style="text-align:center">
                        <button class="copy-button btn btn-outline-secondary bi bi-clipboard" data-clipboard-text="{{$text}}"> </button>
                    </td>
                    {{-- <td style="text-align:center" >
                        <form action="{{url("share_link/teacher/$teacher->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής συνδέσμου;')"> </button>
                        </form>
                    </td>  --}}
                    <td>{{$teacher->surname}}</td>
                    <td>{{$teacher->name}}</td>
                    <td>{{$teacher->mail}}</td>
                    <td>{{$teacher->telephone}}</td>
                    <td>{{$teacher->klados}}</td>
                    <td>{{$teacher->afm}}</td>
                    <td>{{$teacher->organiki->name}}</td>
                    @if($teacher->ypiretisi_id!=null)
                        <td>{{$teacher->ypiretisi->name}}</td>
                    @else
                        <td>-</td>
                    @endif
                    <td>{{$teacher->am}}</td>
                    <td>{{$teacher->sxesi_ergasias->name}}</td>
                    <td>{{$teacher->sch_mail}}</td>
                    @if($date)
                        <td>{{$date->day}}/{{$date->month}}/{{$date->year}}</td>
                    @else
                        <td> - </td>
                    @endif 
                    @if($teacher->sent_link_mail)
                        <td style="text-align:center"><i class="btn btn-success bi bi-check2-circle"></i></td>
                    @else
                        <td style="text-align:center"> - </td>
                    @endif
                </tr>
            @endif  
            @endforeach
        </tbody>
        </table>
    </div>
        
    @can('upload', App\Models\Teacher::class)
        <a href="{{url('/import_teachers')}}" class="btn btn-primary bi bi-person-lines-fill my-2"> Μαζική Εισαγωγή Εκπαιδευτικών</a>
    @endcan

</x-layout>