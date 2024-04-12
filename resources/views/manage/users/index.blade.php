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
        <title>Χρήστες</title>
    @endpush
    
<body>
    <div class="container">
    <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade @isset($active_tab) @if($active_tab=='search') {{'show active'}}  @endif @else {{'show active'}} @endisset" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
            <!-- 1st tab's content-->
                @php      
                    $all_users = App\Models\User::all();
                    $all_departments= App\Models\Department::all();
                @endphp
                <div class="table-responsive">
                <table  id="dataTable" class="align-middle display table  table-striped table-hover" style="font-size:small">
                <thead>
                    <tr>
                        <th id="search">Αναγνωριστικό</th>
                        <th id="search">Username</th>
                        <th id="search">DisplayName</th>
                        <th id="search">email</th>
                        <th id="search">Τμήμα</th>
                        <th id="search">Superadmin</th>
                        <th id="search">CreatedAt</th>
                        <th id="search">UpdatedAt</th>
                        <th id="">Ενέργειες</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach($all_users as $user)
                        <tr>  
                            <td>{{$user->id}}</td>
                            <td>{{$user->username}}</td>
                            <td><div class=" text-wrap"><a href="{{route('users.edit', $user->id)}}" style="">{{$user->display_name}}</a></div></td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->department->name}}</td>
                            
                            @if($user->superadmin)
                                <td><strong>ΝΑΙ</strong></td>
                            @else
                                <td>ΟΧΙ</td>
                            @endIF
                            
                            <td>{{$user->created_at}}</td>
                            <td>{{$user->updated_at}}</td>
                            <td>
                            <div class="hstack gap-2">
                            <form action="{{route('users.reset_password', $user->id)}}" method="post">
                            @csrf
                                <button class="bi bi-key-fill btn btn-warning" type="submit" onclick="return confirm('Επιβεβαίωση επαναφοράς κωδικού')" > </button>
                            </form>
                            <form action="{{route('users.destroy', $user->id)}}" method="post">
                            @method('delete')
                            @csrf
                                <button class="bi bi-x-circle btn btn-danger" type="submit" onclick="return confirm('Επιβεβαίωση διαγραφής χρήστη')" > </button>
                            </form>
                            </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            
            @if(session()->has('record'))
                @php
                    $sri = session('record')->id;   
                @endphp
                <div class="m-2 col-sm-2 btn btn-primary text-wrap">
                    {{-- <a href="{{url("/user_profile/$sri")}}" style="color:white; text-decoration:none;">{{session('record')->id}}, {{session('record')->display_name}}, {{session('record')->username}}</a> --}}
                    <a href="{{route("users.edit", $sri)}}" style="color:white; text-decoration:none;">{{session('record')->id}}, {{session('record')->display_name}}, {{session('record')->username}}</a>
                </div>
            @endif
            
            <div class="container py-5">
            <div class="container px-5">
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/users")}}" method="post" class="container-fluid">
                    @csrf
                    <input type="hidden" name="asks_to" value="insert">
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Εισαγωγή νέου Χρήστη</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Username</span>
                        <input name="user_name3" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon2" required value="@if(session()->has('old_data')){{session('old_data')['user_name3']}}@endif"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon3">DisplayName</span>
                        <input name="user_display_name3" type="text" class="form-control" placeholder="DisplayName" aria-label="DisplayName" aria-describedby="basic-addon3" required value="@if(session()->has('old_data')){{session('old_data')['user_display_name3']}}@endif"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon4">email</span>
                        <input name="user_email3" type="text" class="form-control" placeholder="email" aria-label="email" aria-describedby="basic-addon4" value="@if(session()->has('old_data')){{session('old_data')['user_email3']}}@endif" ><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon4">Τμήμα</span>
                        <select name="user_department3" class="form-select" aria-label="Default select example">
                            <option selected>Επιλογή τμήματος</option>
                            @foreach($all_departments as $department)
                            <option value="{{$department->id}}">{{$department->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon6">τηλέφωνο</span>
                        <input name="user_telephone3" type="text" class="form-control" placeholder="τηλέφωνο" aria-label="τηλέφωνο" aria-describedby="basic-addon6" value="@if(session()->has('old_data')){{session('old_data')['user_telephone3']}}@endif" ><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon5">Λειτουργίες</span>
                        @php
                            $operations = App\Models\Operation::all();   
                        @endphp
                        <table>
                        @foreach($operations as $operation)
                        <tr>
                            <td><input type="checkbox" name="operation{{$operation->id}}" value="{{$operation->id}}" id="{{$operation->id}}">
                            <label for="{{$operation->id}}"> {{$operation->name}} </label></td>
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
                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Προσθήκη</button>
                        {{-- <a href="{{url("/manage_users")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a> --}}
                        <a href="{{route("users.index")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                    
                </form>
            </nav>
            </div></div>
        </div>
    </div>
    </div>

</x-layout>