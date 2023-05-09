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
            @php      
                $all_menus = App\Models\Menu::all();
            @endphp
            <div class="table-responsive">
            <table  id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">ID</th>
                    <th id="search">Name</th>
                    <th id="search">URL</th>
                    <th id="search">Color</th>
                    <th id="search">Opacity</th>
                    <th id="search">Icon</th>
                    <th id="search">WhoHasAccess</th>
                    <th id="">Ορατότητα</th>
                    <th id="">Δυνατότητα Υποβολής</th>
                </tr>
            </thead>
                <tbody>
                    @foreach($all_menus as $one_menu)
                        <tr>  
                            <td>{{$one_menu->id}}</td>
                            <td>{{$one_menu->name}}</td>
                            <td>{{$one_menu->url}}</td>
                            <td>{{$one_menu->color}}</td>
                            <td>{{$one_menu->opacity}}</td>
                            <td>{{$one_menu->icon}}</td>
                            <td>
                                <table class="table table-sm table-striped table-hover">
                                    @foreach($one_menu->users as $one_user)
                                        <tr>
                                            <td>
                                                {{$one_user->user->display_name}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                    
                            @php
                                if($one_menu->visible){
                                    $opacity_vis = "";
                                    $hidden_acc = "";
                                    $tooltip_vis = "Κλείσιμο ορατότητας";  
                                    if($one_menu->accepts){
                                        $opacity_acc="";
                                        $tooltip_acc="Κλείσιμο υποβολών";
                                    }
                                    else{
                                        $opacity_acc = "opacity: 0.4";
                                        $tooltip_acc="Άνοιγμα Υποβολών";
                                    }
                                }
                                else{
                                    $opacity_vis = "opacity: 0.4";
                                    $hidden_acc="hidden";
                                    $opacity_acc="";
                                    $tooltip_acc="";
                                    $tooltip_vis = "Άνοιγμα ορατότητας";
                                }
                            @endphp

                            <td style="text-align: center">
                                <form action="/change_menu_status" method="post">
                                @csrf
                                <input name="asks_to" type="hidden" value="ch_vis_status">
                                <input name="menu_id" type="hidden" value="{{$one_menu->id}}">
                                <button type="submit" class="btn btn-success bi bi-binoculars" data-toggle="tooltip" title="{{$tooltip_vis}}" style="{{$opacity_vis}}" onclick="return confirm('Με την αλλαγή της ορατότητας, η φόρμα θα εμφανίζεται αλλά δε θα δέχεται υποβολές\n')"> </button>
                                </form>
                            </td>
                            <td style="text-align: center">
                                <form action="/change_menu_status" method="post">
                                @csrf
                                <input name="asks_to" type="hidden" value="ch_acc_status">
                                <input name="menu_id" type="hidden" value="{{$one_menu->id}}">
                                <button type="submit" class="btn btn-success bi bi-journal-arrow-down" style="{{$opacity_acc}}" data-toggle="tooltip" title="{{$tooltip_acc}}" {{$hidden_acc}}></button>
                                </form>
                            </td>
                               
                        </tr>
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
                    <a href="/user_profile/{{$record->id}}" style="color:white; text-decoration:none;">{{$record->id}}, {{$record->name}}, {{$record->parent_id}}</a>
                </div>
            @endisset
        @endisset
        <div class="container py-5">
        <div class="container px-5">
        <nav class="navbar navbar-light bg-light">
            <form action="/insert_menu" method="post" class="container-fluid">
                @csrf
                <input type="hidden" name="asks_to" value="insert">
                <div class="input-group">
                    <span class="input-group-text w-25"></span>
                    <span class="input-group-text w-75"><strong>Εισαγωγή νέου Menu</strong></span>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">name</span>
                    <input name="menu_name" type="text" class="form-control" placeholder="menuname" aria-label="menuname" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['menu_name']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">url</span>
                    <input name="menu_url" type="text" class="form-control" placeholder="π.χ. /manage_schools" aria-label="menuurl" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['menu_url']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">color</span>
                    <input name="menu_color" type="text" class="form-control" placeholder="π.χ. skyblue ή #f0f0f0 " aria-label="menucolor" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['menu_color']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">icon</span>
                    <input name="menu_icon" type="text" class="form-control" placeholder="π.χ. bi bi-menu" aria-label="menuicon" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['menu_icon']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon5">Χρήστες</span>
                    @php
                        $users = App\Models\User::all();   
                    @endphp
                    <table>
                    @foreach($users as $user)
                    @if($user->id<>1 and $user->id <> 2)
                        <tr>
                            <td><input type="checkbox" name="user{{$user->id}}" value="{{$user->id}}" id="{{$user->id}}">
                            <label for="{{$user->id}}"> {{$user->display_name}} </label></td>
                        </tr>
                    @endif
                    @endforeach
                    </table>
                </div>
                <div class="input-group">
                    <span class="w-25"></span>
                    <button type="submit" class="btn btn-primary m-2">Προσθήκη</button>
                    <a href="/manage_menus" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                </div>
            </form>
        </nav>
        </div></div>
</x-layout>