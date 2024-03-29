<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @stack('title')
    <link rel="stylesheet" href="{{url('/bootstrap/css/bootstrap.css')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" href="{!! asset(url('/favicon/favicon.ico')) !!}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{url('/favicon/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{url("/favicon/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{url("/favicon/favicon-16x16.png")}}">
    <link rel="manifest" href="{{url('/favicon/site.webmanifest')}}">
    <link rel="mask-icon" href="{{url('/favicon/safari-pinned-tab.svg')}}" color="#5bbad5">
    <link href="{{url('/fontawesome-free-6.4.2-web/css/fontawesome.css')}}" rel="stylesheet">
    <link href="{{url('/fontawesome-free-6.4.2-web/css/brands.css')}}" rel="stylesheet">
    <link href="{{url('/fontawesome-free-6.4.2-web/css/solid.css')}}" rel="stylesheet">
    <link href="{{url('/fontawesome-free-6.4.2-web/css/regular.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    @stack('links')
  </head> 
  @auth('consultant')
  
  @php
    $user = Auth::guard('consultant')->user();
  @endphp
  <body>
   <!--show a line containing: FrontPage, ProfileName, Logout-->
   <div class="justify-content-auto" style="background-color: #fffde3;"> 
    <div class="container">
      <div class="row justify-content-md-center">
        <div class="col">
          <div class="hstack justify-content-start gap-2">
            @if(Illuminate\Support\Facades\Request::path()!='index_teacher')
              <div class=" d-flex px-2"><a href='{{url('/index_consultant')}}' class="text-dark bi bi-house" style="text-decoration:none; " data-toggle="tooltip" title="Αρχική"> </a></div>   
            @endif
              <div class=" d-flex px-2"><a href='{{url('/index_consultant')}}' class="text-dark" style="text-decoration:none; " data-toggle="tooltip" title="Αρχική">{{$user->name}} {{$user->surname}}</a></div>
              <div class=" d-flex px-2"><a href='{{url('/clogout')}}' class="text-dark bi bi-box-arrow-right" style="text-decoration:none; " data-toggle="tooltip" title="Αποσύνδεση"> </a></div>
          </div>
        </div>
      </div>
    </div>
  </div>
    @if(Illuminate\Support\Facades\Request::path()!='index_consultant')
      <nav class="navbar navbar-light justify-content-auto py-2 p-2" style="background-color: rgb(13, 37, 54);">
        <div class="badge text-wrap py-2" style="width: 10rem; background-color:#f1948a; text-align:center;">
          <div class="text-dark fa-solid fa-file-signature"></div> 
            <a href="{{url("/consultant_app/internal_rules")}}" style=" text-decoration:none;" class="text-dark"> Εσωτερικός Κανονισμός</a>
        </div>
        <div class="badge text-wrap py-2" style="width: 10rem; background-color:#ff8f00; text-align:center;">
          <div class="text-dark fa-solid fa-map"></div> 
            <a href="{{url("/consultant_app/work_planning")}}" style=" text-decoration:none;" class="text-dark"> Προγραμματισμός Έργου</a>
        </div>
        <div class="badge text-wrap py-2" style="width: 10rem; background-color:mediumaquamarine; text-align:center;">
          <div class="text-dark fa-solid fa-school"></div> 
            <a href="{{url("/consultant_schools")}}" style=" text-decoration:none;" class="text-dark"> Σχολεία</a>
        </div>
        <div class="badge text-wrap py-2" style="width: 10rem; background-color:skyblue; text-align:center;">
          <div class="text-dark fa-solid fa-signature"></div> 
            <a href="{{url("/consultant_teachers")}}" style=" text-decoration:none;" class="text-dark"> Εκπαιδευτικοί</a>
        </div>
      </nav>
      @else
          @push('app-icon')
            <div class="d-flex justify-content-center"><img src="{{url('/favicon/android-chrome-512x512.png')}}" width="100" height="100" alt="services"></div>
            <div class="d-flex justify-content-center h4">{{$user->name}} {{$user->surname}}</div>
          @endpush
      @endif
    
    @endauth
    <div class="px-4"> {{-- Custom container --}} 
    @include('components/messages')
    {{$slot}}
    </div> {{-- End of custom container --}} 
    <!-- footer begins -->
    <footer class="border-top text-center small text-muted py-3">
      <p class="m-0">Copyright &copy; 2023 <a href="{{url("/index_consultant")}}" class="text-muted">e-forms</a>. Διεύθυνση Π.Ε. Αχαΐας - Τμήμα Πληροφορικής & Νέων Τεχνολογιών - Ηλεκτρονικές Υπηρεσίες.</p>
    </footer>
    <script src="{{url('/bootstrap/js/bootstrap.js')}}"></script>
    <script
        src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
        crossorigin="anonymous">
    </script>
    @stack('scripts')

    </div> <!-- container closing -->
   
    <div class="d-flex justify-content-center"><p class="h3" style="color:black"> {{env('APP_NAME')}}</p></div>
    @stack('copy_script')
   </body>
</html>
