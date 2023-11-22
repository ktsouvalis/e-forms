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
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    @stack('links')
  </head> 
  @auth('school')
  
  @php
    $user = Auth::guard('school')->user();
  @endphp
  <div class="row">
  
    
    <div class="col-2 d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 280px;">
      @if(Illuminate\Support\Facades\Request::path()!='index_school')
          <div class="d-flex justify-content-center"><img src="{{url('/favicon/android-chrome-512x512.png')}}" width="100" height="100" alt="services"></div>
          <div class="d-flex justify-content-center h6">{{$user->name}}</div>
          <hr>
          <ul class="nav nav-pills flex-column mb-auto">
            <p>
            <li class="nav-item">
            <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:DodgerBlue; text-align:center;">
              <a href="{{url('/index_school')}}" style="text-decoration:none;" class="text-dark bi bi-house"> Αρχική</a>
            </div>
            </li>
            </p>
            <li class="nav-item">
            
            @foreach ($user->microapps as $one_microapp)
            @if($one_microapp->microapp->visible)
                <li class="nav-item">
                <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:{{$one_microapp->microapp->color}}; text-align:center;">
                  <div class="text-dark {{$one_microapp->microapp->icon}}"></div> 
                  <a href="{{url("/school_app".$one_microapp->microapp->url)}}" style=" text-decoration:none;" class="text-dark"> {{$one_microapp->microapp->name}}</a>
                </div>
                </li> 
            @endif
            @endforeach

            
            <li class="nav-item">
            <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:#00bfff; text-align:center;">
              <div class="text-dark fa-solid fa-file-pdf"></div> 
              <a href="{{url("/school_fileshare/$user->id")}}" style=" text-decoration:none;" class="text-dark"> Αρχεία Διεύθυνσης</a>
            </div>
            </li> 


            <p>
            <li class="nav-item">
            <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:Gainsboro; text-align:center;">
                <div class="text-dark fa-solid fa-arrow-right-from-bracket"></div>
                <a href="{{url('/slogout')}}" style="text-decoration:none;" class="text-dark "> Αποσύνδεση</a>
            </div>
            </li>
            </p>
          </ul>
          <hr>
    @else
          @push('app-icon')
            <div class="d-flex justify-content-center">
              <img src="{{url('/favicon/android-chrome-512x512.png')}}" width="100" height="100" alt="services">
              
            </div>
            <div class="d-flex justify-content-center h4">{{$user->name}} {{$user->surname}}</div>
          @endpush
    @endif
  </div>
  <div class="col-8">
  <div class="container ">
    <div class="row justify-content-md-center">
      <div class="col p-4">
        @stack('app-icon')
        {{-- <div class=" d-flex justify-content-center"><a href='/change_password' class="h4 text-dark" style="text-decoration:none; " data-toggle="tooltip" title="Αλλαγή κωδικού πρόσβασης"> {{$user->name}}</a></div> --}}
      </div>
    </div>
  </div>
  @endauth
  {{$slot}}

        @if (session()->has('success'))
        <div class='container container-narrow'>
          <div class='alert alert-success text-center'>
            {{session('success')}}
          </div>
        </div>
        @endif
    
        @if(session()->has('failure'))
        <div class='container container-narrow'>
        <div class='alert alert-danger text-center'>
            {{session('failure')}}
        </div>
        </div>
        @endif
        
        @if(session()->has('warning'))
        <div class='container container-narrow'>
        <div class='alert alert-warning text-center'>
            {{session('warning')}}
        </div>
        </div>
        @endif 
        

       <!-- footer begins -->
</div>

</div>
       <footer class="border-top text-center small text-muted py-3">
      <p class="m-0">Copyright &copy; 2023 <a href="{{url("/index_school")}}" class="text-muted">e-forms</a>. Διεύθυνση Π.Ε. Αχαΐας - Τμήμα Πληροφορικής & Νέων Τεχνολογιών - Ηλεκτρονικές Υπηρεσίες.</p>
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
