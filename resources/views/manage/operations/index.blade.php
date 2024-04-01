<x-layout>
    

    @push('links')
        <link href="{{ asset('DataTables-1.13.4/css/dataTables.bootstrap5.css') }}" rel="stylesheet"/>
        <link href="{{ asset('Responsive-2.4.1/css/responsive.bootstrap5.css') }}" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="{{ asset('DataTables-1.13.4/js/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('DataTables-1.13.4/js/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/dataTables.responsive.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/responsive.bootstrap5.js') }}"></script>
        <script src="{{ asset('canedit.js') }}"></script>
    @endpush

    @push('title')
        <title>Διαχειριστικές Λειτουργίες</title>
    @endpush
    
<body>
    <div class="container">
            @php      
                $all_operations = App\Models\Operation::all();
            @endphp
            <div class="table-responsive">
            <table  id="dataTable" class="align-middle display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">ID</th>
                    <th id="search">Name</th>
                    <th id="search">URL</th>
                    <th id="search">Color</th>
                    <th id="search">Icon</th>
                    <th id="search">WhoHasAccess</th>
                </tr>
            </thead>
                <tbody>
                    @foreach($all_operations as $one_operation)
                        @can('view', $one_operation)
                            <tr>  
                                <td>{{$one_operation->id}}</td>
                                <td><div class="badge text-wrap" style="background-color:{{$one_operation->color}};"><a href="{{url("/manage/operations/$one_operation->id/edit")}}" style="color:black; text-decoration:none;">{{$one_operation->name}}</a></div></td>
                                <td>{{$one_operation->url}}</td>
                                <td>{{$one_operation->color}}</td>
                                <td>{{$one_operation->icon}}</td>
                                <td>
                                    <table class="table table-sm table-striped table-hover">
                                        @foreach($one_operation->users as $one_user)
                                            <tr>
                                                <td>
                                                    @if($one_user->can_edit) <strong> @endif  {{$one_user->user->display_name}}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                            </tr>
                        @endcan
                    @endforeach
                </tbody>
            </table>
        </div>
        @isset($dberror)
            <div class="alert alert-danger" role="alert">{{$dberror}}</div>
        @else
            @isset($record)
                <div class="alert alert-success" role="alert">Έγινε η καταχώρηση με τα εξής στοιχεία:</div>
                <div class="m-2 col-sm-2 btn btn-primary text-wrap">
                    <a href="{{url("/operation_profile/$record->id")}}" style="color:white; text-decoration:none;">{{$record->id}}, {{$record->name}}, {{$record->parent_id}}</a>
                </div>
            @endisset
        @endisset
        <hr>

        <form action="{{url("/manage/operations/set_menu_priority")}}" method="post">
            @csrf
            <div class="input-group">
            <span class="input-group-text w-25"></span>
            <span class="input-group-text w-75"><strong>Αλλαγή Προτεραιότητας Μενού</strong></span>
            </div>
            @foreach($all_operations as $operation)
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">{{$operation->name}}</span>
                    <input name="operation{{$operation->id}}" type="number" class="form-control" required value="{{$operation->menu_priority}}">
                </div>
            @endforeach
            <div class="input-group">
                <span class="w-25"></span>
                <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Αλλαγή</button>
                <a href="{{url("/manage/operations")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
            </div>
        </form>
        <hr>
        
        @can('create', App\Models\Operation::class)
        <div class="container py-5">
        <div class="container px-5">
        <nav class="navbar navbar-light bg-light">
            <form action="{{url("/manage/operations")}}" method="post" class="container-fluid">
                @csrf
                <div class="input-group">
                    <span class="input-group-text w-25"></span>
                    <span class="input-group-text w-75"><strong>Εισαγωγή νέας Λειτουργίας</strong></span>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">name</span>
                    <input name="operation_name" type="text" class="form-control" placeholder="operationname" aria-label="operationname" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['operation_name']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">url</span>
                    <input name="operation_url" type="text" class="form-control" placeholder="π.χ. /manage_schools" aria-label="operationurl" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['operation_url']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">color</span>
                    <input name="operation_color" type="text" class="form-control" placeholder="π.χ. skyblue ή #f0f0f0 " aria-label="operationcolor" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['operation_color']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">icon</span>
                    <input name="operation_icon" type="text" class="form-control" placeholder="π.χ. bi bi-menu" aria-label="operationicon" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['operation_icon']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon5">Χρήστες</span>
                    @php
                        $users = App\Models\User::all();   
                    @endphp
                    <table>
                    @foreach($users as $user)
                    @if(!App\Models\Superadmin::where('user_id',$user->id)->exists())
                        <tr>
                            <td>
                            <div class="hstack gap-2">
                                <div class="form-check form-switch">
  

                                <input class="form-check-input" role="switch" type="checkbox" name="user{{$user->id}}" value="{{$user->id}}" id="user{{$user->id}}" onChange="show_edit_option({{$user->id}})">
                                <label for="user{{$user->id}}"> {{$user->display_name}} </label>
                            
                                <div id="space{{$user->id}}">

                                </div>
                                </div>
                            </div>
                            </td>
                        </tr>
                    @endif
                    @endforeach
                    </table>
                </div>
                <div class="input-group">
                    <span class="w-25"></span>
                    <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Προσθήκη</button>
                    <a href="{{url("/manage/operations")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                </div>
            </form>
        </nav>
        </div></div>
        @endcan
</x-layout>