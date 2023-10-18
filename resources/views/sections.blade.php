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
        <title>Τμήματα Σχολείων</title>
    @endpush
    @php
        $all_sections = App\Models\Section::all();
    @endphp
    <p class="h4">Τμήματα Σχολείων</p>
    <div class="table-responsive">
        <table  id="dataTable" class="align-middle table table-sm table-striped table-bordered table-hover" style="font-size: small;" >
            <thead>
                <tr>
                    <th id="search">ID</th>
                    <th id="search">Όνομα Σχολείου</th>
                    <th id="search">Κωδικός Σχολείου</th>
                    <th id="search">Τάξη</th>
                    <th id="search">Τμήμα</th>
                    <th id="search">Κωδικός Τμήματος</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_sections as $section)
                <tr>
                    <td >{{$section->id}}</td>
                    <td >{{$section->school->name}}</td>
                    <td >{{$section->school->code}}</td>
                    <td >{{$section->class}}</td>
                    <td >{{$section->name}}</td>
                    <td >{{$section->sec_code}}</td>

                </tr>
            @endforeach
        </tbody>
        </table>
    </div>

    <div class="vstack">
        @can('updateSections', App\Models\School::class)
            <nav class="navbar navbar-light bg-light">
                <form action="{{url('/upload_sections_template')}}" method="post" class="container-fluid" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="import_sections" >    
                    <button type="submit" class="btn bi bi-filetype-xlsx btn-primary"> Εισαγωγή Τμημάτων</button>
                </form>
            </nav>
        

            @if($all_sections->count())
            <nav class="navbar navbar-light bg-light">
                <form action="{{url('/delete_sections')}}" method="post" class="container-fluid" enctype="multipart/form-data">
                    @csrf
                    <button type="submit" class="btn bi bi-x-circle btn-danger" onclick="return confirm('Η ενέργεια θα διαγράψει τα τμήματα όλων των σχολείων. Είστε βέβαιοι;')"> Διαγραφή Τμημάτων</button>
                </form>
            </nav>
            @endif
        @endcan
    </div>
</x-layout>