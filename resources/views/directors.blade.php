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
        <script src="datatable_init.js"></script>
    @endpush
    @push('title')
        <title>Διευθυντές Σχολείων</title>
    @endpush
    @php
        $all_schools = App\Models\School::all();
    @endphp
<body>
    <p class="h4">Διευθυντές Σχολείων</p>
    <div class="table-responsive">
        <table  id="dataTable" class="align-middle table table-sm table-striped table-bordered table-hover"  style="font-size: small">
            <thead>
                <tr>
                    <th id="search">Κωδικός</th>
                    <th id="search">Επωνυμία Σχολείου</th>
                    <th id="search">email</th>
                    <th id="search">tel</th>
                    <th id="search">Διευθυντής</th>
                    {{-- <th id="search">Τηλέφωνο Διευθυντή</th> --}}
                    
                </tr>
            </thead>
            <tbody>
            @foreach($all_schools as $school)
            
                <tr>
                    <td>{{$school->code}}</td>
                    <td>{{$school->name}}</td>
                    <td>{{$school->mail}}</td>
                    <td>{{$school->telephone}}</td>
                    @if($school->director)
                    <td>{{$school->director->surname}} {{$school->director->name}}</td>
                    {{-- <td>{{$school->director->telephone}}</td> --}}
                    @else
                    <td> - </td>
                    <td> - </td>
                    @endif
                </tr>
        
            @endforeach
        </tbody>
        </table>
    </div>
    
    @can('updateDirectors', App\Models\School::class)
        <a href="{{url('/import_directors')}}" class="btn btn-primary bi bi-building-up"> Μαζική Εισαγωγή Διευθυντών Σχολείων</a>
    @endcan
</x-layout>
        
           