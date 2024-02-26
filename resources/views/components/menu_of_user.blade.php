@php
    $user = Auth::user();
    if(App\Models\Superadmin::where('user_id',$user->id)->exists()){
        $operations=App\Models\Operation::all(); //$operations is Operation model
        $microapps=App\Models\Microapp::all(); //$microapps is Microapp model
        $super_admin=true;
    }
    else {
        $operations=$user->operations; //$operations is UsersOperations model
        $microapps=$user->microapps; // $microapps is MicroappUser model
        $super_admin=false;
    }
@endphp

<nav class="navbar navbar-light justify-content-auto py-2 p-2" style="background-color: rgb(13, 37, 54);">
<!-- Navbar content -->
    @if($user->isAdmin())
    <div class="badge text-wrap py-2" style="width: 10rem; background-color:Gainsboro; text-align:center;">
        <div class="text-dark fa-solid fa-users"></div>
        <a href="{{url("/manage_users")}}" style="color:black; text-decoration:none;" class=""> Χρήστες</a>
    </div>
    @endif
    @foreach ($operations as $operation)
        @php
            if(!$super_admin){
                $one_operation = $operation->operation;
            }
            else{
                $one_operation = $operation;
            }
        @endphp
        @if($one_operation->url!='/month' and $one_operation->url!='/commands')
        
        <div class="badge text-wrap py-2" style="width: 10rem; background-color:{{$one_operation->color}}; text-align:center;">
            <div class="text-dark {{$one_operation->icon}}"></div>
            <a href="{{url($one_operation->url)}}" style="color:black; text-decoration:none;" class=""> {{$one_operation->name}}</a>
        </div>
        @endif
    @endforeach
    <div class="badge text-wrap py-2" style="width: 10rem; background-color:#00bfff; text-align:center;">
        <div class="text-dark fa-solid fa-file-pdf"></div>
        <a href="{{url('/fileshares')}}" style="color:black; text-decoration:none;" class=""> Αποστολή Αρχείων </a>
    </div>
    <div class="badge text-wrap py-2" style="width: 10rem; background-color:#4bac97; text-align:center;">
        <div class="text-dark fa-solid fa-file"></div>
        <a href="{{url('/filecollects')}}" style="color:black; text-decoration:none;" class=""> Συλλογή Αρχείων </a>
    </div>
</nav>
<nav class="navbar navbar-light justify-content-auto py-2 p-2" style="background-color: rgb(71, 151, 209);">
    @foreach ($microapps as $microapp)
        @php
          if(!$super_admin){
              $one_microapp = $microapp->microapp;
          }
          else{
              $one_microapp = $microapp;
          }
        @endphp
        @if($one_microapp->active)
            <div class="badge text-wrap py-2" style="width: 10rem; background-color:{{$one_microapp->color}}; text-align:center;">
                <div class="text-dark {{$one_microapp->icon}}"></div>
                <a href="{{url("/admin".$one_microapp->url)}}" style="color:black; text-decoration:none;" class=""> {{$one_microapp->name}} @if(!$one_microapp->active) <strong style="color:red">ΑΝΕΝΕΡΓΗ</strong>@endif</a>
            </div>
        @endif
    @endforeach
</nav>