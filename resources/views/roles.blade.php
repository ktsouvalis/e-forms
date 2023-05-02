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
        <title>Ρόλοι</title>
    @endpush
    
<body>
    <div class="container">
    {{-- @include('menu') --}}
    {{-- <div class="d-flex justify-content-end">
        <a href="/users_dl" class="btn btn-primary bi bi-download"> Λήψη αρχείου χρηστών </a>
    </div> --}}
            @php      
                $all_roles = App\Models\Role::all();
            @endphp
            <div class="table-responsive">
            <table  id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">Αναγνωριστικό</th>
                    <th id="search">Περιγραφή</th>
                    <th id="search">Αναφέρεται στον ρόλο</th>
                    <th id="search">Μέλη</th>
                    <th id="">Ενέργειες</th>
                </tr>
            </thead>
                <tbody>
                    @foreach($all_roles as $one_role)
                        <tr>  
                            <td>{{$one_role->id}}</td>
                            <td>{{$one_role->name}}</td>
                            @if($one_role->role<>NULL)
                                <td>{{$one_role->role->name}}</td>
                            @else
                                <td>-</td>
                            @endif
                            <td>
                                <table>
                                    @foreach($one_role->users as $one_user)
                                        <tr>
                                            <td>
                                                {{$one_user->user->display_name}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                            <form action="" method="post">
                            @csrf
                                <td><button class="bi bi-key-fill bg-warning" type="submit" onclick="return confirm('Επιβεβαίωση')" > </button></td>
                            </form>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @isset($dberror3)
            <div class="alert alert-danger" role="alert">{{$dberror3}}</div>
        @else
            @isset($record)
                <div class="alert alert-success" role="alert">Έγινε η καταχώρηση με τα εξής στοιχεία:</div>
                <div class="m-2 col-sm-2 btn btn-primary text-wrap">
                    <a href="/user_profile/{{$record->id}}" style="color:white; text-decoration:none;">{{$record->id}}, {{$record->name}}, {{$record->parent_id}}</a>
                </div>
            @endisset
        @endisset
        <div class="container py-5">
        <div class="container px-5">
        <nav class="navbar navbar-light bg-light">
            <form action="/insert_role" method="post" class="container-fluid">
                @csrf
                <input type="hidden" name="asks_to" value="insert">
                <div class="input-group">
                    <span class="input-group-text w-25"></span>
                    <span class="input-group-text w-75"><strong>Εισαγωγή νέου Ρόλου</strong></span>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">name</span>
                    <input name="role_name3" type="text" class="form-control" placeholder="rolename" aria-label="rolename" aria-describedby="basic-addon2" required value="@isset($dberror3){{$old_data['role_name3']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">Αναφέρεται στον: </span>
                    <select name="reports_to3">
                    @foreach($all_roles as $one_role)
                        <option value="{{$one_role->id}}">{{$one_role->name}}</option>
                    @endforeach
                    </select> 
                </div>
                <div class="input-group">
                    <span class="w-25"></span>
                    <button type="submit" class="btn btn-primary m-2">Προσθήκη</button>
                    <a href="/manage_roles" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                </div>
            </form>
        </nav>
        </div></div>
</x-layout>