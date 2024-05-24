<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @stack('title')
    <link rel="stylesheet" href="{{asset('bootstrap/css/bootstrap.css')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
    <link rel="icon" href="{!! asset('favicon/favicon.ico') !!}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('favicon/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset("favicon/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset("favicon/favicon-16x16.png")}}">
    <link rel="manifest" href="{{asset('favicon/site.webmanifest')}}">
    <link rel="mask-icon" href="{{asset('favicon/safari-pinned-tab.svg')}}" color="#5bbad5">
    <link href="{{asset('fontawesome-free-6.4.2-web/css/fontawesome.css')}}" rel="stylesheet">
    <link href="{{asset('fontawesome-free-6.4.2-web/css/brands.css')}}" rel="stylesheet">
    <link href="{{asset('fontawesome-free-6.4.2-web/css/solid.css')}}" rel="stylesheet">
    <link href="{{asset('fontawesome-free-6.4.2-web/css/regular.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    @stack('links')
  </head>
  <body>
    @include('components.spinner')
  @auth
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
    <!-- If this page is not index show horizontal menu -->
    @if(Illuminate\Support\Facades\Request::path()!='index_user')
      @include('components/menu_of_user') 
    @else {{--if this page is index show pc icon--}}
      @push('app-icon')
        <div class="d-flex justify-content-center"><img src="{{asset('favicon/android-chrome-512x512.png')}}" width="50" height="50" alt="services"></div>
        <div class="d-flex justify-content-center h6">{{$user->display_name}}</div>
      @endpush
    @endif
    <!--show a line containing: FrontPage, ProfileName, Logout-->
    <div class="pt-2 pb-2 ps-4">
      @stack('app-icon')
      <div class="hstack justify-content-start gap-2">
        @if(Auth::user()->notifications->where('read_at',null)->count()>0)
          <a href="{{route('notifications.index')}}" class="text-danger" style="text-decoration:none; " data-toggle="tooltip" title="Ειδοποιήσεις"><i class="fa-solid fa-bell"></i></a>
        @else
          <a href="{{route('notifications.index')}}" class="text-dark" style="text-decoration:none; " data-toggle="tooltip" title="Ειδοποιήσεις"><i class="fa-regular fa-bell"></i> </a>
        @endif
        @if(Illuminate\Support\Facades\Request::path()!='index_user')
          <div class=" d-flex "><a href='{{url('/index_user')}}' class="text-dark bi bi-house" style="text-decoration:none; " data-toggle="tooltip" title="Αρχική"> </a></div>   
        @endif
        <div class=" d-flex "><a href='{{url('/change_password')}}' class="text-dark" style="text-decoration:none; " data-toggle="tooltip" title="Αλλαγή κωδικού πρόσβασης">{{$user->username}} </a></div>
        <div class=" d-flex "><a href='{{url('/logout')}}' class="text-dark bi bi-box-arrow-right" style="text-decoration:none; " data-toggle="tooltip" title="Αποσύνδεση"> </a></div>
      </div>
    </div>
  @endauth
  
  <div class="mx-5"> <!-- Custom container --> 
  @include('components/messages')    <!-- Show Notifications --> 
  
 
  
  {{$slot}}
  </div> <!-- End of custom container --> 

  <!-- footer begins -->
  <footer class="border-top text-center small text-muted py-3">
    <p class="m-0">Copyright &copy; {{Illuminate\Support\Carbon::now()->year}} <a href="{{url("/")}}" class="text-muted">e-forms</a>. Διεύθυνση Π.Ε. Αχαΐας - Τμήμα Πληροφορικής & Νέων Τεχνολογιών - Ηλεκτρονικές Υπηρεσίες.</p>
  </footer>
  <script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
  <script
    src="https://code.jquery.com/jquery-3.6.4.min.js"
    integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
    crossorigin="anonymous">
  </script>
  <script src="{{asset('spinner.js')}}"></script>
  @stack('scripts')
  </div> <!-- container closing -->
    <div class="d-flex justify-content-center"><p class="h3" style="color:black"> {{env('APP_NAME')}}</p></div>
    @stack('copy_script')
  </body>
</html>
