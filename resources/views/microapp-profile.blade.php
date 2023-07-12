<x-layout>
    @push('links')
        <link href="../DataTables-1.13.4/css/dataTables.bootstrap5.css" rel="stylesheet"/>
        <link href="../Responsive-2.4.1/css/responsive.bootstrap5.css" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="../DataTables-1.13.4/js/jquery.dataTables.js"></script>
        <script src="../DataTables-1.13.4/js/dataTables.bootstrap5.js"></script>
        <script src="../Responsive-2.4.1/js/dataTables.responsive.js"></script>
        <script src="../Responsive-2.4.1/js/responsive.bootstrap5.js"></script>
        <script src="../datatable_init.js"></script>
    @endpush
    @push('scripts')
        <script>
            function show_edit_option(id, canedit) {
                if (document.getElementById("user"+id).checked){
                    // alert(canedit);
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
                    if(canedit === "checked"){
                        radio1.checked = true;
                    }

                    var label1 = document.createElement("label");
                    label1.htmlFor = "edit_yes" + id;
                    label1.textContent = "Can Edit";

                    // Create the second radio button and label
                    var radio2 = document.createElement("input");
                    radio2.type = "radio";
                    radio2.id = "edit_no" + id;
                    radio2.value = "no";
                    radio2.name = "edit" + id;
                    if(canedit != "checked"){
                        radio2.checked = true;
                    }

                    var label2 = document.createElement("label");
                    label2.htmlFor = "edit_no" + id;
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
    @php
    if($microapp->visible){
        $opacity_vis = "";
        $hidden_acc = "";
        $tooltip_vis = "Κλείσιμο ορατότητας";  
        if($microapp->accepts){
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
    <div class="container py-5">
        <div class="container px-5">
            <div class="hstack gap-3 py-2">
                <form action="{{url("/change_microapp_status/$microapp->id")}}" method="post">
                @csrf
                <input name="asks_to" type="hidden" value="ch_vis_status">
                <button type="submit" class="btn btn-outline-secondary bi bi-binoculars"  style="{{$opacity_vis}}" onclick="return confirm('Με την αλλαγή της ορατότητας, η φόρμα δε θα δέχεται υποβολές\n')"> {{$tooltip_vis}}</button>
                </form>
            
            
                <form action="{{url("/change_microapp_status/$microapp->id")}}" method="post">
                @csrf
                <input name="asks_to" type="hidden" value="ch_acc_status">
                <button type="submit" class="btn btn-outline-secondary bi bi-journal-arrow-down" style="{{$opacity_acc}}"  {{$hidden_acc}}> {{$tooltip_acc}}</button>
                </form>        
            </div>  
            <hr>
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/save_microapp/$microapp->id")}}" method="post" class="container-fluid">
                    @csrf
                    <input type="hidden" name="asks_to" value="insert">
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Επεξεργασία Χαρακτηριστικών Μικροεφαρμογής</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Name</span>
                        <input name="name" type="text" class="form-control" placeholder="Name" aria-label="Name" aria-describedby="basic-addon2" required value="{{$microapp->name}}"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon3">Url</span>
                        <input name="url" type="text" class="form-control" placeholder="Url" aria-label="Url" aria-describedby="basic-addon3" required value="{{$microapp->url}}"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon4">Color</span>
                        <input name="color" type="text" class="form-control" placeholder="Color" aria-label="color" aria-describedby="basic-addon4" required value="{{$microapp->color}}" ><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon5">Icon</span>
                        <input name="icon" type="text" class="form-control" placeholder="Icon" aria-label="Icon" aria-describedby="basic-addon5" required value="{{$microapp->icon}}" ><br>
                    </div>
                    <input type="hidden" name="microapp_id" value="{{$microapp->id}}">
                    @can('addUser', App\Models\Microapp::class)
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon5">Χρήστες</span>
                        @php
                            $all_users = App\Models\User::all();
                        @endphp
                        <table>
                        @foreach($all_users as $user)
                            @if(!App\Models\Superadmin::where('user_id',$user->id)->exists())
                                @php
                                
                                    $checked_checkbox="";
                                    $checked_radio_can="";
                                    $checked_radio_cant="";
                                    
                                    if($microapp->users->where('user_id', $user->id)->count()){
                                        $checked_checkbox="checked";
                                        if($microapp->users->where('user_id', $user->id)->first()->can_edit){
                                            $checked_radio_can = "checked";  
                                        }
                                        else{
                                            $checked_radio_cant = "checked";    
                                        }
                                    }  
                                @endphp
                            <tr>
                                <td>
                                <div class="hstack gap-2">
                                    <div class="form-check form-switch">
                                    <input class="form-check-input" role="switch" type="checkbox" name="user{{$user->id}}" value="{{$user->id}}" id="user{{$user->id}}" onChange="show_edit_option({{$user->id}}, '{{$checked_radio_can}}')" {{$checked_checkbox}}>
                                    @php
                                        $existed_user = $microapp->users->where('user_id', $user->id);
                                    @endphp
                                    <label for="user{{$user->id}}">@if($existed_user->count() and $existed_user->first()->can_edit) <strong> @endif  {{$user->display_name}}</strong> </label>
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
                    @endcan
                    <div class="input-group">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-outline-primary bi bi-save m-2"> Αποθήκευση αλλαγών</button>
                        <a href="{{url("/microapp_profile/$microapp->id")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                    
                </form>
                
            </nav>

            <hr>
            
            
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/import_whocan/microapp/$microapp->id")}}" method="post" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Ενδιαφερόμενοι</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Name</span>
                        <textarea name="afmscodes"  class="form-control" cols="122" rows="5" style="resize: none;" placeholder="ΑΦΜ εκπαιδευτικών ή/και κωδικοί σχολείων χωρισμένα με κόμμα (,)" required></textarea>
                    </div>
                    <div class="input-group py-1 px-1">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-outline-primary bi bi-database-add"> Εισαγωγή Σχολείων/Εκπαιδευτικών</button>
                    </div>
                </form>
            </nav>  
        </div>
        <div class="container px-5 vstack gap-2 py-3">
            @if($microapp->stakeholders->count())
            <div class="table-responsive py-3">
                <table  id="dataTable" class="align-middle display table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th id="search">Αναγνωριστικό</th>
                        <th id="search">name</th>
                        <th class="align-middle">Διαγραφή</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($microapp->stakeholders as $one_stakeholder)
                <tr>
                    @if($one_stakeholder->stakeholder_type=="App\Models\School")
                        <td>{{$one_stakeholder->stakeholder->code}}</td>
                    @else
                        <td>{{$one_stakeholder->stakeholder->afm}}</td>
                    @endif
                    <td>{{$one_stakeholder->stakeholder->surname}} {{$one_stakeholder->stakeholder->name}}</td>
                    <td>
                        <form action="{{url("/delete_one_whocan/microapp/$one_stakeholder->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger bi bi-x-circle"> </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
                </table>
            </div>   
            <div class="hstack gap-2">
                <a href="{{url("/preview_mail_all_whocans/microapp/$microapp->id")}}" class="btn btn-outline-secondary bi bi-binoculars" target="_blank"> Προεπισκόπηση email</a>
                <form action="{{url("/send_mail_all_whocans/microapp/$microapp->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> Αποστολή email σε όλους </button>
                </form>
                <form action="{{url("/delete_all_whocans/microapp/$microapp->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger bi bi-x-circle" onclick="return confirm('Επιβεβαίωση διαγραφής stakeholders;')"> Διαγραφή όλων</button>
                </form>
            </div>
            @endif       
        </div>
        <hr>
        @php
            $not_found = Session::pull('not_found', []);
        @endphp
        @if($not_found)
            <div class='container container-narrow'>
                <div class='alert alert-warning'>
                    <strong>Αναγνωριστικά που δεν βρέθηκαν</strong><br>
                    @isset($not_found)
                        @foreach($not_found as $identifier)
                            {{$identifier}}
                            <br>
                        @endforeach  
                    @endisset
                </div>
            </div>
        <hr>
        @endif
        </div>
        
</x-layout>