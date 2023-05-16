<x-layout>
    <div class="container py-5">
        <div class="container px-5">
            <nav class="navbar navbar-light bg-light">
                <form action="/save_user/{{$user->id}}" method="post" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Επεξεργασία Χρήστη</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Username</span>
                        <input name="user_name" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon2" required value="{{$user->username}}"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon3">DisplayName</span>
                        <input name="user_display_name" type="text" class="form-control" placeholder="DisplayName" aria-label="DisplayName" aria-describedby="basic-addon3" required value="{{$user->display_name}}"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon4">email</span>
                        <input name="user_email" type="text" class="form-control" placeholder="email" aria-label="email" aria-describedby="basic-addon4" required value="{{$user->email}}" ><br>
                    </div>
                    <input type="hidden" name="user_id" value="{{$user->id}}">
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon5">Λειτουργίες</span>
                        @php
                            $all_operations = App\Models\Operation::all();
                        @endphp
                        <table>
                        @foreach($all_operations as $operation)
                            @php
                                $checked="";
                                if($user->operations->where('operation_id', $operation->id)->count()){
                                    $checked="checked";
                                }   
                            @endphp
                            <tr>
                                <td><input type="checkbox" name="operation{{$operation->id}}" value="{{$operation->id}}" id="{{$operation->id}}" {{$checked}}>
                                <label for="{{$operation->id}}"> {{$operation->name}} </label></td>
                            </tr>
                        @endforeach
                        </table>
                    </div>
                    <div class="input-group">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση</button>
                        <a href="/user_profile/{{$user->id}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                    
                </form>
            </nav>
            @isset($dberror)
                <div class="alert alert-danger" role="alert">{{$dberror}}</div>
            @endisset
        </div>
    </div>    
</x-layout>