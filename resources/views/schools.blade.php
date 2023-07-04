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
    @endpush
    @push('title')
        <title>Σχολεία</title>
    @endpush
    @php
        $all_schools = App\Models\School::all();
    @endphp
<body>
{{-- <div class="container"> --}}
    <div class="table-responsive">
        <table  id="dataTable" class="align-middle table table-sm table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>Αποστολή συνδέσμου</th>
                    <th id="search">Κωδικός</th>
                    <th id="search">Ονομασία</th>
                    <th id="search">email</th>
                    <th id="search">tel</th>
                    <th id="search">link</th>
                    <th id="search">last login</th>
                    
                </tr>
            </thead>
            <tbody>
            @foreach($all_schools as $school)
                @php
                    $date=null;
                    if($school->logged_in_at) 
                        $date = Illuminate\Support\Carbon::parse($school->logged_in_at);
                @endphp
                <tr>
                    <td style="text-align:center">
                        <form action="{{url("share_link/school/$school->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-warning bi bi-envelope-at"> </button>
                        </form>
                    </td>  
                    <td>{{$school->code}}</td>
                    <td>{{$school->name}}</td>
                    <td>{{$school->mail}}</td>
                    <td>{{$school->telephone}}</td>
                    <td>{{$school->md5}}</td>
                    @if($date)
                        <td>{{$date->day}}/{{$date->month}}/{{$date->year}}</td>
                    @else
                        <td> - </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        </table>
    </div>
    
    @can('upload', App\Models\School::class)
        <a href="{{url('/import_schools')}}" class="btn btn-primary bi bi-building-up"> Μαζική Εισαγωγή Σχολείων</a>
    @endcan
{{-- </div> --}}
</x-layout>
        
           