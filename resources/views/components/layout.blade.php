<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @stack('title')
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" href="{!! asset(url('/favicon/favicon.ico')) !!}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
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
      <hr>
      <ul class="nav nav-pills flex-column mb-auto">
        <p>
        <li class="nav-item">
        <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:DodgerBlue; text-align:center;">
          <a href="{{url('/')}}" style="text-decoration:none;" class="text-dark bi bi-house"> Αρχική</a>
        </div>
        </li>
        </p>
        
        @if(Auth::id()==1 or Auth::id()==2)
        <p>
        <li class="nav-item">
        <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:Gainsboro; text-align:center;">
          <div class="text-dark fa-solid fa-toolbox"></div>
          <a href="{{url('/manage_operations')}}" style="text-decoration:none;" class="text-dark"> Λειτουργίες</a>
        </div>
        </li>
        <li class="nav-item">
        <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:Gainsboro; text-align:center;">
          <div class="text-dark fa-solid fa-microchip"></div>
          <a href="{{url('/microapps')}}" style="text-decoration:none;" class="text-dark"> Μικροεφαρμογές</a>
        </div>
        </li>
        <li class="nav-item">
          <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:Gainsboro; text-align:center;">
            <div class="text-dark fa-solid fa-users"></div>
            <a href="{{url('/manage_users')}}" style="text-decoration:none;" class="text-dark"> Χρήστες Διεύθυνσης</a>
          </div>
        </li>
        </p>
        @endif

        @foreach ($user->operations as $one_operation)
            <li class="nav-item">
            <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:{{$one_operation->operation->color}}; text-align:center;">
              <div class="text-dark {{$one_operation->operation->icon}}"></div> 
              <a href="{{url($one_operation->operation->url)}}" style=" text-decoration:none;" class="text-dark"> {{$one_operation->operation->name}}</a>
            </div>
            </li> 
        @endforeach
        
        @foreach ($user->microapps as $one_microapp)
            <li class="nav-item">
            <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:{{$one_microapp->microapp->color}}; text-align:center;">
              <div class="text-dark {{$one_microapp->microapp->icon}}"></div> 
              <a href="{{url("/admin".$one_microapp->microapp->url)}}" style=" text-decoration:none;" class="text-dark"> {{$one_microapp->microapp->name}}</a>
            </div>
            </li> 
        @endforeach

        <p>
        <li class="nav-item">
        <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:Gainsboro; text-align:center;">
            <div class="text-dark fa-solid fa-arrow-right-from-bracket"></div>
            <a href="{{url('/logout')}}" style="text-decoration:none;" class="text-dark "> Αποσύνδεση</a>
        </div>
        </li>
        </p>
      </ul>
      <hr>
    
    
    @else
    @push('app-icon')
      <div class="d-flex justify-content-center"><img src="{{url('/favicon/index.png')}}" width="100" height="100" alt="services"></div>
    @endpush
    @endif
  {{-- @endauth  --}}
  </div>
  <div class="col-8">
  <div class="container ">
    <div class="row justify-content-md-center">
      <div class="col p-4">
        @stack('app-icon')
        <div class=" d-flex justify-content-center"><a href='/change_password' class="h4 text-dark" style="text-decoration:none; " data-toggle="tooltip" title="Αλλαγή κωδικού πρόσβασης"> {{Auth::user()->display_name}}</a></div>
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
      <p class="m-0">Copyright &copy; 2023 <a href="/" class="text-muted">library</a>. Διεύθυνση Π.Ε. Αχαΐας - Τμήμα Πληροφορικής & Νέων Τεχνολογιών - Ηλεκτρονικές Υπηρεσίες.</p>
    </footer>
    <script src="{{url('/bootstrap/js/bootstrap.js')}}"></script>
    <script
                src="https://code.jquery.com/jquery-3.6.4.min.js"
                integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
                crossorigin="anonymous">
    </script>
    @stack('scripts')

    <script>
$(document).ready(function () {
  // Setup - add a text input to each header cell
  $('#dataTable thead tr #search').each(function () {
    var title = $(this).text();
    $(this).html('<input type="text" style="width:7rem;" placeholder="' + title + '" />');
  });

  // DataTable
  var table = $('#dataTable').DataTable({
    initComplete: function () {

      // Apply the search
      this.api()
        .columns()
        .every(function () {
          var that = this;
          var column = this;

          $('input', this.header()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
              that.search(this.value).draw();
            }
          }).on('click', function(e) {
            e.stopPropagation(); // Stop the click event from propagating to the DataTables header cell
            // table.ordering([[], []]); // Toggle sorting off
            column.search($(this).val()).draw(); // Apply the search filter
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
