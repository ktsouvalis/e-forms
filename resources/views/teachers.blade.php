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
        <title>Εκπαιδευτικοί</title>
    @endpush
    @php
        $all_teachers = App\Models\Teacher::all();
    @endphp
<body>
<div class="container">
            
    <div class="table-responsive">
        <table  id="dataTable" class=" table table-sm table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th id="search">AΜ</th>
                    <th id="search">ΑΦΜ</th>
                    <th id="search">Επώνυμο</th>
                    <th id="search">Όνομα</th>
                    <th id="search">Πατρώνυμο</th>
                    <th id="search">Μητρώνυμο</th>
                    <th id="search">Κλάδος</th>
                    <th id="search">email</th>
                    <th id="search">email ΠΣΔ</th>
                    <th id="search">Τηλέφωνο</th>
                    <th id="search">Σχέση Εργασίας</th>
                    <th id="search">Οργανική</th>
                    {{-- <th id="search">Υπηρέτηση</th> --}}
                    <th id="search">Οργανική στην Ειδική Αγωγή</th>
                    <th id="search">link</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_teachers as $teacher)
                <tr>  
                    <td>{{$teacher->am}}</td>
                    <td>{{$teacher->afm}}</td>
                    <td>{{$teacher->surname}}</td>
                    <td>{{$teacher->name}}</td>
                    <td>{{$teacher->fname}}</td>
                    <td>{{$teacher->mname}}</td>
                    <td>{{$teacher->klados}}</td>
                    <td>{{$teacher->mail}}</td>
                    <td>{{$teacher->sch_mail}}</td>
                    <td>{{$teacher->telephone}}</td>
                    <td>{{$teacher->sxesi_ergasias->name}}</td>
                    <td>{{$teacher->organiki->name}}</td>
                    {{-- <td>{{$teacher->ypiretisi->name}}</td> --}}
                    @if($teacher->org_eae)
                        <td>ΝΑΙ</td>
                    @else
                        <td> - </td>
                    @endif
                    <td>{{$teacher->md5}}</td>
                </tr>
            @endforeach
        </tbody>
        </table>
    </div>
        
    @can('create', App\Models\Teacher::class)
        <a href="{{url('/import_teachers')}}" class="btn btn-primary bi bi-person-lines-fill"> Μαζική Εισαγωγή Εκπαιδευτικών</a>
    @endcan
</div>
</x-layout>