<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @stack('title')
    <link rel="stylesheet" href="{{url('/bootstrap/css/bootstrap.css')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" href="{!! asset(url('/favicon/favicon.ico')) !!}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{url('/favicon/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{url("/favicon/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{url("/favicon/favicon-16x16.png")}}">
    <link rel="manifest" href="{{url('/favicon/site.webmanifest')}}">
    <link rel="mask-icon" href="{{url('/favicon/safari-pinned-tab.svg')}}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    @stack('links')
    
    <!-- fontawesome -->
    <script src="https://kit.fontawesome.com/5083d79d45.js" crossorigin="anonymous"></script>
  
  </head> 
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
  <div class="row">
  
    
    <div class="col-2 d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 280px;">
      {{-- @auth --}}
      @if(Illuminate\Support\Facades\Request::path()!='/')
      {{-- <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
        <svg class="bi pe-none me-2" width="40" height="32"><use xlink:href="#bootstrap"/></svg>
        <span class="fs-4">Λειτουργίες</span>
      </a> --}}
      <div class="d-flex justify-content-center"><img src="{{url('/favicon/index.png')}}" width="100" height="100" alt="services"></div>

      <ul class="nav nav-pills flex-column mb-auto">
        {{-- Μενού για όλους --}}
        
        <li class="nav-item">
        <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:DodgerBlue; text-align:center;">
          <a href="{{url('/')}}" style="text-decoration:none;" class="text-dark bi bi-house"> Αρχική</a>
        </div>
        </li>
        
        
          {{-- Μενού διαχείρισης χρηστών  --}}
        @if(App\Models\Superadmin::where('user_id',$user->id)->exists())
        <hr>
        <li class="nav-item">
          <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:Gainsboro; text-align:center;">
            <div class="text-dark fa-solid fa-users"></div>
            <a href="{{url('/manage_users')}}" style="text-decoration:none;" class="text-dark"> Χρήστες Διεύθυνσης</a>
          </div>
        </li>
        @endif
        
        {{-- Μενού για operations --}}
        
        <hr>
        @if(App\Models\Superadmin::where('user_id',$user->id)->exists())
        
        <li class="nav-item">
        <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:Gainsboro; text-align:center;">
          <div class="text-dark fa-solid fa-toolbox"></div>
          <a href="{{url('/manage_operations')}}" style="text-decoration:none;" class="text-dark"> Λειτουργίες</a>
        </div>
        </li>
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
            <li class="nav-item">
            <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:{{$one_operation->color}}; text-align:center;">
              <div class="text-dark {{$one_operation->icon}}"></div> 
              <a href="{{url($one_operation->url)}}" style=" text-decoration:none;" class="text-dark"> {{$one_operation->name}}</a>
            </div>
            </li> 
        @endforeach
        

        {{-- Μενού για fileshares --}}
        <hr>
        
        <li class="nav-item">
        <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:Gainsboro; text-align:center;">
          <div class="text-dark fa-solid fa-file-pdf"></div>
          <a href="{{url('/fileshares')}}" style="text-decoration:none;" class="text-dark"> Διαμοιρασμός Αρχείων</a>
        </div>
        </li>

        @foreach($user->department->fileshares as $fileshare)
        @php
          $fi = $fileshare->id;
        @endphp
          <li class="nav-item">
          <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:#00bfff; text-align:center;">
          <div class="text-dark fa-solid fa-file-pdf"></div>
          <a href="{{url("/fileshare_profile/$fi")}}" style="text-decoration:none;" class="text-dark">{{$fileshare->department->name}}: {{$fileshare->name}}</a>
          </div>
          </li>
        @endforeach
        
        

        {{-- μενού για microapps --}}
        <hr>
        
        <li class="nav-item">
        <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:Gainsboro; text-align:center;">
          <div class="text-dark fa-solid fa-microchip"></div>
          <a href="{{url('/microapps')}}" style="text-decoration:none;" class="text-dark"> Μικροεφαρμογές</a>
        </div>
        </li>
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
            <li class="nav-item">
            <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:{{$one_microapp->color}}; text-align:center;">
              <div class="text-dark {{$one_microapp->icon}}"></div> 
              <a href="{{url("/admin".$one_microapp->url)}}" style=" text-decoration:none;" class="text-dark"> {{$one_microapp->name}} @if(!$one_microapp->active) <strong style="color:red">ΑΝΕΝΕΡΓΗ</strong>@endif</a>
            </div>
            </li> 
        @endif
        @endforeach
        
        
        <hr>
        
        <li class="nav-item">
        <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:Gainsboro; text-align:center;">
            <div class="text-dark fa-solid fa-arrow-right-from-bracket"></div>
            <a href="{{url('/logout')}}" style="text-decoration:none;" class="text-dark "> Αποσύνδεση</a>
        </div>
        </li>
        
      </ul>
      <hr>
    
    
    @else
    @push('app-icon')
      <div class="d-flex justify-content-center"><img src="{{url('/favicon/index.png')}}" width="100" height="100" alt="services"></div>
    @endpush
    @endif
  {{-- @endauth  --}}
  </div>
  <div class="col-10">
  <div class="container ">
    <div class="row justify-content-md-center">
      <div class="col p-4">
        @stack('app-icon')
        <div class=" d-flex justify-content-center"><a href='{{url('/change_password')}}' class="h4 text-dark" style="text-decoration:none; " data-toggle="tooltip" title="Αλλαγή κωδικού πρόσβασης"> {{Auth::user()->display_name}}</a></div>
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
      <p class="m-0">Copyright &copy; 2023 <a href="{{url("/")}}" class="text-muted">e-forms</a>. Διεύθυνση Π.Ε. Αχαΐας - Τμήμα Πληροφορικής & Νέων Τεχνολογιών - Ηλεκτρονικές Υπηρεσίες.</p>
    </footer>
    <script src="{{url('/bootstrap/js/bootstrap.js')}}"></script>
    <script
                src="https://code.jquery.com/jquery-3.6.4.min.js"
                integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
                crossorigin="anonymous">
    </script>
    @stack('scripts')
{{-- ${title} (Συμπερίληψη) --}}
    <script>
$(document).ready(function () {
  // Setup - add a text input for inclusion and exclusion to each header cell
  $('#dataTable thead tr #search').each(function () {
    var title = $(this).text();
    $(this).html(`
      <div class="vstack gap-1">
        <input type="text" class="include-search" style="width:5rem; font-size:small;" placeholder="${title} +" />
        <input type="text" class="exclude-search" style="width:5rem; font-size:small;" placeholder="${title} - " />
      </div>
    `);
  });

  // DataTable
  var table = $('#dataTable').DataTable({
    initComplete: function () {
      // Apply the search
      this.api()
        .columns()
        .every(function () {
          var that = this;
          var includeColumn = $('input.include-search', this.header());
          var excludeColumn = $('input.exclude-search', this.header());

          includeColumn.on('keyup change clear', function () {
            if (that.search() !== this.value) {
              that.search(this.value).draw();
            }
          }).on('click', function (e) {
            e.stopPropagation();
            column.search($(this).val()).draw();
          });

          excludeColumn.on('keyup change clear', function () {
            var excludeValue = this.value;
            var includeValue = includeColumn.val();
            var regex;

            if (excludeValue) {
              regex = `^(?!.*${excludeValue}).*${includeValue}`;
            } else {
              regex = includeValue;
            }

            that.search(regex, true, false).draw();
          }).on('click', function (e) {
            e.stopPropagation();
            column.search($(this).val()).draw();
          });
        });
    },
  });
});
</script>





    </div> <!-- container closing -->
   
    <div class="d-flex justify-content-center"><p class="h3" style="color:black"> {{env('APP_NAME')}}</p></div>
    @stack('copy_script')
   </body>
</html>
