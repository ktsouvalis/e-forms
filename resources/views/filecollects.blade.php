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
        <script src="canedit.js"></script>
        <script src="datatable_init.js"></script>
    @endpush

    @push('title')
        <title>Συλλογή Αρχείων</title>
    @endpush
    
<body>
    <div class="container">
            @php      
                $all_filecollects = App\Models\Filecollect::all();
            @endphp
            <div class="table-responsive">
            <table  id="dataTable" class="align-middle table table-striped table-hover">
            <thead>
                <tr >
                    <th id="search">ID</th>
                    <th id="search">Name</th>
                    <th id="search">WhoHasAccess</th>
                    <th id="search">Ορατή</th>
                    <th id="search">Δέχεται</th>
                </tr>
            </thead>
                <tbody>
                    @foreach($all_filecollects as $one_filecollect)              
                        @can('view', $one_filecollect)
                            <tr >  
                                <td>{{$one_filecollect->id}}</td>
                                @can('update', $one_filecollect)
                                <td><div class="badge text-wrap" style="background-color:{{$one_filecollect->color}};"><a href="{{url("/filecollect_profile/$one_filecollect->id")}}" style="color:black; text-decoration:none;">{{$one_filecollect->name}}</a></div></td>
                                @else
                                <td>{{$one_filecollect->name}}</td>
                                @endcan
                                <td>
                                    <table class="table table-sm table-striped table-hover">
                                        @foreach($one_filecollect->users as $one_user)
                                            <tr>
                                                <td>
                                                    @if($one_user->can_edit) <strong> @endif {{$one_user->user->display_name}}</strong>  
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                @php
                                    
                                if($one_filecollect->visible){
                                    $opacity_vis = "";
                                    $hidden_acc = "";
                                    $tooltip_vis = "Κλείσιμο ορατότητας";  
                                    if($one_filecollect->accepts){
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
                            @can('update', $one_filecollect)
                            <td >
                                <form action="{{url("/change_filecollect_status/$one_filecollect->id")}}" method="post">
                                @csrf
                                <input name="asks_to" type="hidden" value="ch_vis_status">
                                <button type="submit" class="btn btn-secondary bi bi-binoculars" data-toggle="tooltip" title="{{$tooltip_vis}}" style="{{$opacity_vis}}" onclick="return confirm('Με την αλλαγή της ορατότητας, η φόρμα δε θα δέχεται υποβολές\n')"> </button>
                                </form>
                            </td>
                            <td >
                                <form action="{{url("/change_filecollect_status/$one_filecollect->id")}}" method="post">
                                @csrf
                                <input name="asks_to" type="hidden" value="ch_acc_status">
                                <button type="submit" class="btn btn-secondary bi bi-journal-arrow-down" style="{{$opacity_acc}}" data-toggle="tooltip" title="{{$tooltip_acc}}" {{$hidden_acc}}></button>
                                </form>
                            </td>
                            @else
                            <td> - </td>
                            <td> - </td>
                            
                            @endcan
                        @endcan
                        
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @can('create', App\Models\Filecollect::class)
        <div class="container py-5">
        <div class="container px-5">
        <nav class="navbar navbar-light bg-light">
            <form action="{{url("/insert_filecollect")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                @csrf
                <input type="hidden" name="asks_to" value="insert">
                <div class="input-group">
                    <span class="input-group-text w-25"></span>
                    <span class="input-group-text w-75"><strong>Εισαγωγή νέας Συλογής Αρχείου</strong></span>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">Ονομασία</span>
                    <input name="filecollect_name" type="text" class="form-control" placeholder="π.χ. Τμήμα Ένταξης, Εργαστήριο Πληροφορικής, κ.α." aria-label="filecollectname" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['filecollect_name']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">Πρότυπο Αρχείο</span>
                    <input name="filecollect_original_file" type="file" class="form-control"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">Τύπος Δεκτών Αρχείων</span>
                    <select name="filecollect_mime" class="form-control" required>
                        <option value="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">Excel (.xlsx)</option>
                        <option value="application/pdf">Pdf (.pdf)</option>
                        <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document">Word (.docx)</option>
                    </select>
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
                    <a href="{{url("/filecollects")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                </div>
            </form>
        </nav>
        </div></div>
        @endcan
    </div>
</x-layout>