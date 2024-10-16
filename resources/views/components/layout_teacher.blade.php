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
  <body>
    @include('components.spinner')
  @auth('teacher')
  
  @php
    $user = Auth::guard('teacher')->user();
  @endphp
  <!--show a line containing: FrontPage, ProfileName, Logout-->
  <div class="justify-content-auto" style="background-color: #fffde3;"> 
    <div class="container">
      <div class="row justify-content-md-center">
        <div class="col">
          <div class="hstack justify-content-start gap-2">
            @if(Illuminate\Support\Facades\Request::path()!='index_teacher')
              <div class=" d-flex px-2"><a href='{{url('/index_teacher')}}' class="text-dark bi bi-house" style="text-decoration:none; " data-toggle="tooltip" title="Αρχική"> </a></div>   
            @endif
              <div class=" d-flex px-2"><a href='{{url('/index_teacher')}}' class="text-dark" style="text-decoration:none; " data-toggle="tooltip" title="Αρχική">{{$user->name}} {{$user->surname}}</a></div>
              <div class=" d-flex px-2"><a href='{{url('/tlogout')}}' class="text-dark bi bi-box-arrow-right" style="text-decoration:none; " data-toggle="tooltip" title="Αποσύνδεση"> </a></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @php
    $active_microapp=false;
    if($user->microapps->count()){
      foreach($user->microapps as $microapp){
        if($microapp->microapp->visible){
          $active_microapp = true;
          break;
        }
      } 
    }

    $satisfiesCriteria = false;
    foreach(App\Models\Microapp::all() as $one_microapp){
        if($one_microapp->accessCriteria){
            $criteria = json_decode($one_microapp->accessCriteria->criteria, true);
            $satisfiesCriteria = true;
            foreach ($criteria as $key => $value) {
                if (!in_array($user->$key, $value)) {
                    $satisfiesCriteria = false;
                    break;
                }  
            }
        }
        if($satisfiesCriteria){
            $active_microapp = true;
        }
    }
    
    $active_filecollect=false;
    if($user->filecollects->count()){
      foreach($user->filecollects as $filecollect){
        if($filecollect->filecollect->visible){
          $active_filecollect=true;
          break;
        }
      } 
    }
  @endphp
  
  @if(!$active_microapp AND count($user->fileshares)==0 AND !$active_filecollect)
    <div class='container container-narrow pt-4'>
      <div class='alert alert-info text-center'>
      Δεν υπάρχει αυτή τη στιγμή κάποια ενεργή ηλεκτρονική υπηρεσία για σας. Ευχαριστούμε για την επίσκεψη!
      </div>
    </div>
  @endif
  @if(Illuminate\Support\Facades\Request::path()!='index_teacher')
    <nav class="navbar navbar-light justify-content-auto p-2 mb-2" style="background-color: rgb(13, 37, 54);">
      @foreach ($user->microapps as $one_microapp)
        @if($one_microapp->microapp->visible)
          <div class="badge text-wrap py-2" style="width: 10rem; background-color:{{$one_microapp->microapp->color}}; text-align:center;">
            <div class="text-dark {{$one_microapp->microapp->icon}}"></div> 
            @php $resource = substr($one_microapp->microapp->url, 1); @endphp
            {{-- <a href="{{url($one_microapp->microapp->url."/create")}}" style=" text-decoration:none;" class="text-dark"> {{$one_microapp->microapp->name}}</a> --}}
            <a href="{{route("$resource.create")}}" style=" text-decoration:none;" class="text-dark"> {{$one_microapp->microapp->name}}</a>
          </div>
        @endif 
      @endforeach
      @foreach(App\Models\Microapp::all() as $one_microapp)
          @if($one_microapp->accessCriteria)
            @php
                $criteria = json_decode($one_microapp->accessCriteria->criteria, true);
                $satisfiesCriteria = true;

                foreach ($criteria as $key => $value) {
                  if (!in_array($user->$key, $value)) {
                      $satisfiesCriteria = false;
                      break;
                  }
                }
            @endphp
              
            @if ($satisfiesCriteria)
              @if(!$user->microapps->where('microapp_id', $one_microapp->id)->count())
                  <div class="badge text-wrap py-2" style="width: 10rem; background-color:{{$one_microapp->color}}; text-align:center;">
                    <div class="text-dark {{$one_microapp->icon}}"></div> 
                    @php $resource = substr($one_microapp->url, 1); @endphp
                    {{-- <a href="{{url($one_microapp->url."/create")}}" style=" text-decoration:none;" class="text-dark"> {{$one_microapp->name}}</a> --}}
                    <a href="{{route("$resource.create")}}" style=" text-decoration:none;" class="text-dark"> {{$one_microapp->name}}</a>
                  </div>
              @endif
            @endif
          @endif
      @endforeach
      
      @foreach($user->fileshares as $fileshare)
        @php
          $ffi = $fileshare->fileshare->id
        @endphp
        <div class="badge text-wrap py-2" style="width: 10rem; background-color:#00bfff; text-align:center;">
          <div class="text-dark fa-solid fa-file-pdf"></div> 
          <a href="{{url("/fileshares/$ffi")}}" style=" text-decoration:none;" class="text-dark"> {{$fileshare->fileshare->name}}</a>
        </div>
      @endforeach
      @foreach($user->filecollects as $filecollect)
        @php
            $ffi = $filecollect->filecollect->id
        @endphp
        @if($filecollect->filecollect->visible)
        <div class="badge text-wrap py-2" style="width: 10rem; background-color:#4bac97; text-align:center;">
          <div class="text-dark fa-solid fa-file-pdf"></div> 
          <a href="{{url("/filecollects/$ffi")}}" style=" text-decoration:none;" class="text-dark"> {{$filecollect->filecollect->name}}</a>
        </div>
        @endif
      @endforeach
    </nav>
    @endif
  @endauth
  @include('components/messages')
  <div class="container-xl px-2"> {{-- Custom container --}} 
  {{$slot}}
  </div> {{-- End of Custom container --}} 
  <!-- footer begins -->
    <footer class="border-top text-center small text-muted py-3">
      <p class="m-0">Copyright &copy; 2023 <a href="{{url("/index_teacher")}}" class="text-muted">e-forms</a>. Διεύθυνση Π.Ε. Αχαΐας - Τμήμα Πληροφορικής & Νέων Τεχνολογιών - Ηλεκτρονικές Υπηρεσίες.</p>
    </footer>
    <script src="{{url('/bootstrap/js/bootstrap.js')}}"></script>
    <script
        src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
        crossorigin="anonymous">
    </script>
    <script src="{{asset('spinner.js')}}"></script>
    @stack('scripts')   
    <div class="d-flex justify-content-center"><p class="h3" style="color:black"> {{env('APP_NAME')}}</p></div>
    @stack('copy_script')
   </body>
</html>
