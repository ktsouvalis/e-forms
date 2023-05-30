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
<div class="container">
    <!--tabs-->
    

      
            <!-- 1st tab's content-->
            <div class="table-responsive">
                <table  id="dataTable" class=" table table-sm table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th id="search">Κωδικός</th>
                            <th id="search">Ονομασία</th>
                            <th id="search">email</th>
                            <th id="search">link</th>
                            <th id="search">Δημ/Νηπ</th>
                            <th id="search">Ειδικό</th>
                            <th id="search">Δημόσιο</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($all_schools as $school)
                        <tr>  
                            <td>{{$school->code}}</td>
                            <td>{{$school->name}}</td>
                            <td>{{$school->mail}}</td>
                            <td>{{$school->md5}}</td>
                            <td>{{$school->primary}}</td>
                            <td>{{$school->special_needs}}</td>
                            <td>{{$school->international}}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
           
            {{-- @can('create', App\Models\Teacher::class) --}}
            <a href="{{url('/import_schools')}}" class="btn btn-primary bi bi-person-lines-fill"> Μαζική Εισαγωγή Σχολείων</a>
        {{--@endcan --}}
</div>
</x-layout>
        
           