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
        <script>
            function show_edit_option(id) {
                if (document.getElementById("user"+id).checked){
                    
                    // Create the main container div
                    var mainDiv = document.createElement("div");
                    mainDiv.className = "vstack";
                    mainDiv.id = "div" + id;

                    // Create the first radio button and label
                    var radio1 = document.createElement("input");
                    radio1.type = "radio";
                    radio1.id = "edit_yes" + id;
                    radio1.value = "yes";
                    radio1.name = "edit" + id;
                    radio1.checked = "checked";

                    var label1 = document.createElement("label");
                    label1.htmlFor = "edit_yes";
                    label1.textContent = "Can Edit";

                    // Create the second radio button and label
                    var radio2 = document.createElement("input");
                    radio2.type = "radio";
                    radio2.id = "edit_no" + id;
                    radio2.value = "no";
                    radio2.name = "edit" + id;

                    var label2 = document.createElement("label");
                    label2.htmlFor = "edit_no";
                    label2.textContent = "Can't Edit";

                    // Append the radio buttons and labels to the main container div
                    mainDiv.appendChild(createDivWithChildren([radio1, label1]));
                    mainDiv.appendChild(createDivWithChildren([radio2, label2]));

                    // Function to create a div with children elements
                    function createDivWithChildren(children) {
                    var div = document.createElement("div");
                    div.className = "hstack gap-2";
                    children.forEach(function(child) {
                        div.appendChild(child);
                    });
                    return div;
                    }

                    // Append the main container div to the "space" div
                    var spaceDiv = document.getElementById("space"+id);
                    spaceDiv.appendChild(mainDiv);
                } 
                else{
                    // Remove the dynamically generated elements
                    var elementToRemove = document.getElementById("div" + id);
                    if (elementToRemove) {
                        elementToRemove.parentNode.removeChild(elementToRemove);
                    }
                }
            }


        </script>
    @endpush

    @push('title')
        <title>Μικροεφαρμογές</title>
    @endpush
    
<body>
    <div class="container">
            @php      
                $all_microapps = App\Models\Microapp::all();
            @endphp
            <div class="table-responsive">
            <table  id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">ID</th>
                    <th id="search">Name</th>
                    <th id="search">URL</th>
                    <th id="search">Color</th>
                    <th id="search">Icon</th>
                    <th id="search">WhoHasAccess</th>
                    <th id="search">Ενεργή/Ανενεργή</th>
                    <th id="search">Ορατή/ΔέχεταιΑπαντήσεις </th>
                </tr>
            </thead>
                <tbody>
                    @foreach($all_microapps as $one_microapp)
                        {{-- @can('view', $one_microapp) --}}
                            <tr>  
                                <td>{{$one_microapp->id}}</td>
                                <td><div class="badge text-wrap" style="background-color:{{$one_microapp->color}};"><a href="/microapp_profile/{{$one_microapp->id}}" style="color:black; text-decoration:none;">{{$one_microapp->name}}</a></div></td>
                                <td>{{$one_microapp->url}}</td>
                                <td>{{$one_microapp->color}}</td>
                                <td>{{$one_microapp->icon}}</td>
                                <td>
                                    <table class="table table-sm table-striped table-hover">
                                        @foreach($one_microapp->users as $one_user)
                                            <tr>
                                                <td>
                                                    @if($one_user->can_edit) <strong> @endif {{$one_user->user->display_name}}</strong>  
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>tata</td>
                                <td>tata</td>
                            </tr>
                        {{-- @endcan --}}
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="container py-5">
        <div class="container px-5">
        <nav class="navbar navbar-light bg-light">
            <form action="{{url("/insert_microapp")}}" method="post" class="container-fluid">
                @csrf
                <input type="hidden" name="asks_to" value="insert">
                <div class="input-group">
                    <span class="input-group-text w-25"></span>
                    <span class="input-group-text w-75"><strong>Εισαγωγή νέας Λειτουργίας</strong></span>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">name</span>
                    <input name="microapp_name" type="text" class="form-control" placeholder="microappname" aria-label="microappname" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['microapp_name']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">url</span>
                    <input name="microapp_url" type="text" class="form-control" placeholder="π.χ. /manage_schools" aria-label="microappurl" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['microapp_url']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">color</span>
                    <input name="microapp_color" type="text" class="form-control" placeholder="π.χ. skyblue ή #f0f0f0 " aria-label="microappcolor" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['microapp_color']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">icon</span>
                    <input name="microapp_icon" type="text" class="form-control" placeholder="π.χ. bi bi-menu" aria-label="microappicon" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['operation_icon']}}@endisset"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">Έναρξη</span>
                    <input name="microapp_opens_at" type="date" class="form-control" aria-label="microopensat" aria-describedby="basic-addon2" required ><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">Λήξη</span>
                    <input name="microapp_closes_at" type="date" class="form-control"  aria-label="microappclosesat" aria-describedby="basic-addon2" required ><br>
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
                            <td>
                            <div class="hstack gap-2">
                                <input type="checkbox" name="user{{$user->id}}" value="{{$user->id}}" id="user{{$user->id}}" onChange="show_edit_option({{$user->id}})">
                                <label for="user{{$user->id}}"> {{$user->display_name}} </label>
                            
                                <div id="space{{$user->id}}">

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
                    <button type="submit" class="btn btn-primary m-2">Προσθήκη</button>
                    <a href="{{url("/microapps")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                </div>
            </form>
        </nav>
        </div></div>
</x-layout>