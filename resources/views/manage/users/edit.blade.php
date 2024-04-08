<x-layout>
    @php
        $all_departments = App\Models\Department::all();
    @endphp
    <div class="container py-5">
        <div class="container px-5">
            <nav class="navbar navbar-light bg-light">
                {{-- <form action="{{url("/users/$user->id")}}" method="post" class="container-fluid"> --}}
                <form action="{{route("users.update", $user->id)}}" method="post" class="container-fluid">
                    @csrf
                    @method('put')
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
                        <input name="user_email" type="text" class="form-control" placeholder="email" aria-label="email" aria-describedby="basic-addon4" value="{{$user->email}}" ><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon5">τηλέφωνο</span>
                        <input name="user_telephone" type="text" class="form-control" placeholder="telephone" aria-label="telephone" aria-describedby="basic-addon4" value="{{$user->telephone}}" ><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon4">Τμήμα</span>
                        <select name="user_department" class="form-select" aria-label="Default select example">
                            @foreach($all_departments as $department)
                            @php
                                $selected=null;
                                if($department->id == $user->department->id)
                                    $selected="selected";   
                            @endphp
                            <option {{$selected}} value="{{$department->id}}">{{$department->name}}</option>
                            @endforeach
                        </select>
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
                        <button type="submit" class="btn btn-outline-primary bi bi-save m-2"> Αποθήκευση αλλαγών</button>
                        {{-- <a href="{{url("/user_profile/$user->id")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a> --}}
                        <a href="{{route("users.edit", $user->id)}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                    
                </form>
            </nav>
            @isset($dberror)
                <div class="alert alert-danger" role="alert">{{$dberror}}</div>
            @endisset
        </div>
    </div>    
</x-layout>