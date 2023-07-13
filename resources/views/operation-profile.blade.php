<x-layout>
    @push('scripts')
        <script src="../canedit.js"></script>
    @endpush
    <div class="container py-5">
        <div class="container px-5">
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/save_operation/$operation->id")}}" method="post" class="container-fluid">
                    @csrf
                    <input type="hidden" name="asks_to" value="insert">
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Επεξεργασία χαρακτηριστικών Λειτουργίας</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Name</span>
                        <input name="name" type="text" class="form-control" placeholder="Name" aria-label="Name" aria-describedby="basic-addon2" required value="{{$operation->name}}"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon3">Url</span>
                        <input name="url" type="text" class="form-control" placeholder="Url" aria-label="Url" aria-describedby="basic-addon3" required value="{{$operation->url}}"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon4">Color</span>
                        <input name="color" type="text" class="form-control" placeholder="Color" aria-label="color" aria-describedby="basic-addon4" required value="{{$operation->color}}" ><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon5">Icon</span>
                        <input name="icon" type="text" class="form-control" placeholder="Icon" aria-label="Icon" aria-describedby="basic-addon5" required value="{{$operation->icon}}" ><br>
                    </div>
                    <input type="hidden" name="operation_id" value="{{$operation->id}}">
                    @can('addUser', App\Models\Operation::class)
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
                                    
                                    if($operation->users->where('user_id', $user->id)->count()){
                                        $checked_checkbox="checked";
                                        if($operation->users->where('user_id', $user->id)->first()->can_edit){
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
                                        $existed_user = $operation->users->where('user_id', $user->id);
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
                        <a href="{{url("/operation_profile/$operation->id")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                    
                </form>
            </nav>
            @isset($dberror)
                <div class="alert alert-danger" role="alert">{{$dberror}}</div>
            @endisset
        </div>
    </div>    
</x-layout>