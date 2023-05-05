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
        <title>Χρήστες</title>
    @endpush
    
<body>
    <div class="container">
    {{-- @include('menu') --}}
    {{-- <div class="d-flex justify-content-end">
        <a href="/users_dl" class="btn btn-primary bi bi-download"> Λήψη αρχείου χρηστών </a>
    </div> --}}
<!--tabs-->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link @isset($active_tab) @if($active_tab=='search') {{'active'}} @endif @else {{'active'}} @endisset" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="true">Αναζήτηση Χρήστη</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link @isset($active_tab) @if($active_tab=='import') {{'active'}} @endif @endisset" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="tab2" aria-selected="false">Μαζική Εισαγωγή Χρηστών</button>
        </li>
    </ul>
<!--tab content-->
    <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade @isset($active_tab) @if($active_tab=='search') {{'show active'}}  @endif @else {{'show active'}} @endisset" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
            <!-- 1st tab's content-->
                @php      
                    $all_users = App\Models\User::all();
                @endphp
                <div class="table-responsive">
                <table  id="dataTable" class="display table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th id="search">Αναγνωριστικό</th>
                        <th id="search">Username</th>
                        <th id="search">DisplayName</th>
                        <th id="search">email</th>
                        <th id="search">Έχει πρόσβαση</th>
                        <th id="search">CreatedAt</th>
                        <th id="search">UpdatedAt</th>
                        <th id="">Password Reset</th>
                    </tr>
                </thead>
                    <tbody>
                        @foreach($all_users as $user)
                            <tr>  
                                <td>{{$user->id}}</td>
                                <td>{{$user->username}}</td>
                                <td><div class="badge bg-primary text-wrap"><a href="/user_profile/{{$user->id}}" style="color:white; text-decoration:none;">{{$user->display_name}}</a></div></td>
                                <td>{{$user->email}}</td>
                                <td>
                                    <table class="table table-sm table-striped table-hover">
                                        @foreach ($user->menus as $one_menu)
                                            <tr>
                                            <td>{{$one_menu->menu->name}}</td>  
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>{{$user->created_at}}</td>
                                <td>{{$user->updated_at}}</td>
                                <form action="/reset_password/{{$user->id}}" method="post">
                                @csrf
                                    <td><button class="bi bi-key-fill bg-warning" type="submit" onclick="return confirm('Επιβεβαίωση επαναφοράς κωδικού')" > </button></td>
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
                        <a href="/user_profile/{{$record->id}}" style="color:white; text-decoration:none;">{{$record->id}}, {{$record->display_name}}, {{$record->name}}</a>
                    </div>
                @endisset
            @endisset
            <div class="container py-5">
            <div class="container px-5">
            <nav class="navbar navbar-light bg-light">
                <form action="/insert_user" method="post" class="container-fluid">
                    @csrf
                    <input type="hidden" name="asks_to" value="insert">
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Εισαγωγή νέου Χρήστη</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Username</span>
                        <input name="user_name3" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon2" required value="@isset($dberror3){{$old_data['user_name3']}}@endisset"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon3">DisplayName</span>
                        <input name="user_display_name3" type="text" class="form-control" placeholder="DisplayName" aria-label="DisplayName" aria-describedby="basic-addon3" required value="@isset($dberror3){{$old_data['user_display_name3']}}@endisset"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon4">email</span>
                        <input name="user_email3" type="text" class="form-control" placeholder="email" aria-label="email" aria-describedby="basic-addon4" required value="@isset($dberror3){{$old_data['user_email3']}}@endisset" ><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon5">Λειτουργίες</span>
                        @php
                            $menus = App\Models\Menu::all();   
                        @endphp
                        <table>
                        @foreach($menus as $menu)
                        <tr>
                            <td><input type="checkbox" name="menu{{$menu->id}}" value="{{$menu->id}}" id="{{$menu->id}}">
                            <label for="{{$menu->id}}"> {{$menu->name}} </label></td>
                        </tr>
                        @endforeach
                        </table>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon5">Password</span>
                        <input name="user_password3" type="text" class="form-control" placeholder="Password" aria-label="Password" aria-describedby="basic-addon5" required><br>
                    </div>
                    <div class="input-group">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-primary m-2">Προσθήκη</button>
                        <a href="/manage_users" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                    
                </form>
            </nav>
            </div></div>
        </div>

        <div class="tab-pane fade @isset($active_tab) @if($active_tab=='import') {{'show active'}} @endif @endisset" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
            @if(empty($asks_to))
            <nav class="navbar navbar-light bg-light">
                <a href="/users_template.xlsx" class="link-info">Πρότυπο αρχείο για συμπλήρωση</a>
                <form action="{{route('upload_user_template')}}" method="post" class="container-fluid" enctype="multipart/form-data">
                    @csrf
                    
                    <input type="file" name="import_users" > 
                    <button type="submit" class="btn btn-primary bi bi-filetype-xlsx"> Αποστολή αρχείου</button>
                </form>
            </nav>
            @else
            <div style="p-3 mb-2 bg-info text-dark">
                Διαβάστηκαν οι ακόλουθοι χρήστες από το αρχείο:
            </div>
            
            <table class="table table-striped table-hover table-light">
                <tr>
                    <th>Username</th>
                    <th>DisplayName</th>
                    <th>email</th>
                    <th>password</th>
                </tr>
                @foreach($users_array as $user)
                    <tr>  
                        <td @if ($user['username']=="Κενό πεδίο" or $user['username']=="Υπάρχει ήδη το username") style='color:red;' @endif>{{$user['username']}}</td>
                        <td @if ($user['display_name']=='Κενό πεδίο') style='color:red;' @endif>{{$user['display_name']}}</td>
                        <td @if ($user['email']=="Κενό πεδίο" or $user['email']=="Υπάρχει ήδη το email") style='color:red;' @endif>{{$user['email']}}</td>
                        <td @if ($user['password']=='Κενό πεδίο') style='color:red;' @endif>{{$user['password']}}</td>
                    </tr>
                @endforeach
            </table>
                @if($asks_to=='save')
                Να προχωρήσει η εισαγωγή αυτών των στοιχείων;
                <div class="row">
                    <form action="/insert_users" method="post" class="col container-fluid" enctype="multipart/form-data">
                    @csrf
                        <button type="submit" class="btn btn-primary bi bi-file-arrow-up"> Εισαγωγή</button>
                    </form>
                    <a href="/manage_users" class="col">Ακύρωση</a>
                </div>
                @else
                <div class="row">
                    <div>
                        Διορθώστε τα σημειωμένα σφάλματα και υποβάλετε εκ νέου το αρχείο.
                    </div>
                    <a href="/manage_users" class="col">Ακύρωση</a>
                </div>
                @endif
            @endif
            @isset($dberror2)
                <div class="alert alert-danger" role="alert">{{$dberror2}}</div>
            @endisset
        </div>
    </div>
    </div>

</x-layout>