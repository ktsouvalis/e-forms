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
<div class="mx-1">
<nav class="navbar navbar-light justify-content-auto py-2 p-2" style="background-color: rgb(32, 94, 138);">
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
        <div class="badge text-wrap py-2" style="width: 10rem; background-color:{{$one_operation->color}}; text-align:center;">
            <div class="text-dark {{$one_operation->icon}}"></div>
            <a href="{{url("/admin".$one_operation->url)}}" style="color:black; text-decoration:none;" class=""> {{$one_operation->name}}</a>
        </div>
    @endforeach
</nav>
<nav class="navbar navbar-light justify-content-auto py-2 p-2" style="background-color: #1f3f57;">
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


    {{-- <div class="badge bg-light text-wrap py-2 m-1" style="width: 12rem;">
        <div class="fa-solid fa-house text-dark"></div>
        <a href="{{url('/')}}" style="color:black; text-decoration:none;" class=""> Αρχική</a>
    </div>
    <div class=" badge bg-success text-wrap py-2 m-1" style="width: 12rem; opacity: 0.9;">
        <div class="fa-solid fa-book"></div>
        <a href="{{url('/book')}}" style="color:white; text-decoration:none;" class=""> Βιβλία</a>
    </div>
    <div class=" badge bg-warning text-wrap py-2 m-1" style="width: 12rem; opacity: 0.9;">
        <div class="fa-solid fa-graduation-cap text-dark" ></div>
        <a href="{{url('/student')}}" style="color:black; text-decoration:none;" class=""> Μαθητές</a>
    </div>
    <div class=" badge bg-danger text-wrap py-2 m-1" style="width: 12rem; opacity: 0.9;">
        <div class="fa-solid fa-book-open-reader"></div>
        <a href="{{url('/loans')}}" style="color:white; text-decoration:none;" class=""> Ιστορικό</a>
    </div>
    @if (Illuminate\Support\Facades\Auth::id()==2 or Illuminate\Support\Facades\Auth::id()==1)
        <div class=" badge text-wrap py-2 m-1" style="width: 12rem; background-color:DarkKhaki">
            <div class="fa-solid fa-users text-dark"></div>
            <a href="{{url('/user')}}" style="color:black; text-decoration:none;"> Σχολεία</a>
        </div>
    @endif
    <div class=" badge bg-dark text-wrap py-2 m-1" style="width: 12rem; opacity: 0.5;">
        <div class="fa-solid fa-arrow-right-from-bracket"></div>
        <a href="{{url('/logout')}}" style="color:white; text-decoration:none;" class=""> Αποσύνδεση</a>
    </div> --}}
</nav>
</div>