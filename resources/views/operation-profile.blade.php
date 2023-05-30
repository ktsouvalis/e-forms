<x-layout>
    <div class="container py-5">
        <div class="container px-5">
            <nav class="navbar navbar-light bg-light">
                <form action="/save_operation/{{$operation->id}}" method="post" class="container-fluid">
                    @csrf
                    <input type="hidden" name="asks_to" value="insert">
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Επεξεργασία Λειτουργίας</strong></span>
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
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon5">Χρήστες</span>
                        @php
                            $all_users = App\Models\User::all();
                        @endphp
                        <table>
                        @foreach($all_users as $user)
                            @php
                                $checked="";
                                if($operation->users->where('user_id', $user->id)->count()){
                                    $checked="checked";
                                }   
                            @endphp
                            <tr>
                                <td><input type="checkbox" name="user{{$user->id}}" value="{{$user->id}}" id="{{$user->id}}" {{$checked}}>
                                <label for="{{$user->id}}"> {{$user->username}} </label></td>
                            </tr>
                        @endforeach
                        </table>
                    </div>
                    <div class="input-group">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση</button>
                        <a href="/operation_profile/{{$operation->id}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                    
                </form>
            </nav>
            @isset($dberror)
                <div class="alert alert-danger" role="alert">{{$dberror}}</div>
            @endisset
        </div>
    </div>    
</x-layout>