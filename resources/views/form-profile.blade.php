<x-layout>
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
    <div class="container py-5">
        <div class="container px-5">
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/save_microapp/$microapp->id")}}" method="post" class="container-fluid">
                    @csrf
                    <input type="hidden" name="asks_to" value="insert">
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Επεξεργασία Μικροεφαρμογής</strong></span>
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
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon5">Χρήστες</span>
                        @php
                            $all_users = App\Models\User::all();
                        @endphp
                        <table>
                        @foreach($all_users as $user)
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
                                    <input type="checkbox" name="user{{$user->id}}" value="{{$user->id}}" id="user{{$user->id}}" onChange="show_edit_option({{$user->id}}, '{{$checked_radio_can}}')" {{$checked_checkbox}}>
                                    @php
                                        $existed_user = $microapp->users->where('user_id', $user->id);
                                    @endphp
                                    <label for="user{{$user->id}}">@if($existed_user->count() and $existed_user->first()->can_edit) <strong> @endif  {{$user->display_name}}</strong> </label>
                                    <div id="space{{$user->id}}">
                                        
                                    </div>
                                </div>
                                </td>
                            </tr>
                        @endforeach
                        </table>
                    </div>
                    <div class="input-group">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση</button>
                        <a href="{{url("/microapp_profile/$form->id")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                    
                </form>
                
            </nav>
            <div>
                <a href="{{url("/import_whocan/$form->id")}}" class="btn btn-primary bi bi-person-lines-fill"> Εισαγωγή Stakeholders</a>
            </div>
        </div>
    </div>    
</x-layout>
