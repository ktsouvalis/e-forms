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
        <title>Διαμοιρασμός Αρχείων</title>
    @endpush
    
<body>
    <div class="container">
        @php      
            $all_fileshares = App\Models\Fileshare::all();
        @endphp
        <div class="table-responsive">
        <table  id="dataTable" class="display table table-sm table-striped table-hover">
        <thead>
            <tr>
                <th id="search">ID</th>
                <th id="search">Όνομα</th>
                <th id="search">WhoHasAccess</th>
                <th>Διαγραφή</th>
            </tr>
        </thead>
            <tbody>
                @foreach($all_fileshares as $one_fileshare)
                    @can('view', $one_fileshare)
                        <tr>  
                            <td>{{$one_fileshare->id}}</td>
                            <td><div class="badge text-wrap" ><a href="{{url("/fileshare_profile/$one_fileshare->id")}}" style="color:black; text-decoration:none;">{{$one_fileshare->name}}</a></div></td>
                            <td>
                                {{$one_fileshare->department->name}}
                            </td>
                            <td>
                                <form action="{{url("/delete_fileshare/$one_fileshare->id")}}" method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-danger bi bi-x-circle"> </button>
                                </form>
                            </td>
                        </tr>
                    @endcan
                @endforeach
            </tbody>
        </table>
        </div>

        <div class="container py-5">
            <div class="container px-5">
                <nav class="navbar navbar-light bg-light">
                    <form action="{{url("/insert_fileshare")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <input type="hidden" name="asks_to" value="insert">
                        <div class="input-group">
                            <span class="input-group-text w-25"></span>
                            <span class="input-group-text w-75"><strong>Εισαγωγή νέου Fileshare</strong></span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25" id="basic-addon2">Τίτλος</span>
                            <input name="fileshare_name" type="text" class="form-control" placeholder="filesharename" aria-label="filesharename" aria-describedby="basic-addon2" required><br>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25" id="basic-addon2">Κοινά αρχεία</span>
                            <input name="fileshare_common_files[]" type="file" class="form-control" multiple ><br>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25" id="basic-addon2">Προσωπικά αρχεία</span>
                            <input name="fileshare_personal_files[]" type="file" class="form-control" multiple><br>
                        </div>
                        <div class="input-group">
                            <span class="w-25"></span>
                            <button type="submit" class="btn btn-primary m-2">Προσθήκη</button>
                            <a href="{{url("/fileshares")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    </form>
                </nav>
            </div>
        </div>  
</x-layout>