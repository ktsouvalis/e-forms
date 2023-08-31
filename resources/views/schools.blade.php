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
        <script src="copylink.js"></script>
        <script src="datatable_init.js"></script>
    @endpush
    @push('title')
        <title>Σχολεία</title>
    @endpush
    @php
        $all_schools = App\Models\School::all();
    @endphp

    <button class="btn btn-secondary bi bi-clipboard my-2" id="copyCodeButton"> Αντιγραφή κωδικών σχολείων</button>
    <div class="table-responsive">
        <table  id="dataTable" class="align-middle table table-sm table-striped table-bordered table-hover" style="font-size: small;" >
            <thead>
                <tr>
                    {{-- <th id="search">id</th> --}}
                    <th class="align-middle">Αντιγραφή συνδέσμου</th>
                    <th class="align-middle">Αποστολή συνδέσμου</th>
                    <th id="search">Κωδικός</th>
                    <th id="search">Ονομασία</th>
                    <th id="search">email</th>
                    <th id="search">tel</th>
                    <th id="search">Δήμος</th>
                    <th id="search">Οργανικότητα</th>
                    <th id="search">Λειτουργικότητα</th>
                    <th id="search">last login</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_schools as $school)
                @php
                    $date=null;
                    if($school->logged_in_at) 
                        $date = Illuminate\Support\Carbon::parse($school->logged_in_at);
                    $text = url("/school/$school->md5");
                @endphp
                <tr>
                    {{-- <td >{{$school->id}}</td> --}}
                    <td style="text-align:center">
                        <button class="copy-button btn btn-outline-secondary bi bi-clipboard" data-clipboard-text="{{$text}}"> </button>
                    </td>
                    <td style="text-align:center;">
                        <form action="{{url("share_link/school/$school->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-warning bi bi-envelope-at"> </button>
                        </form>
                    </td>  
                    <td >{{$school->code}}</td>
                    <td >{{$school->name}}</td>
                    <td >{{$school->mail}}</td>
                    <td >{{$school->telephone}}</td>
                    <td >{{$school->municipality->name}}</td>
                    <td >{{$school->organikotita}}</td>
                    <td >{{$school->leitourgikotita}}</td>
                    @if($date)
                        <td >{{$date->day}}/{{$date->month}}/{{$date->year}}</td>
                    @else
                        <td > - </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        </table>
        </div>
    
    
    @can('upload', App\Models\School::class)
        <a href="{{url('/import_schools')}}" class="btn btn-primary bi bi-building-up my-2"> Μαζική Εισαγωγή Σχολείων</a>
    @endcan

</x-layout>
        
           