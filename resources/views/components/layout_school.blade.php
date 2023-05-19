<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @stack('title')
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" href="{!! asset('/favicon/favicon.ico') !!}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
    <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    
    <!-- fontawesome -->
    <script src="https://kit.fontawesome.com/5083d79d45.js" crossorigin="anonymous"></script>
  
  </head> 
  
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
       <footer class="border-top text-center small text-muted py-3">
      <p class="m-0">Copyright &copy; 2023 <a href="/" class="text-muted">library</a>. Διεύθυνση Π.Ε. Αχαΐας - Τμήμα Πληροφορικής & Νέων Τεχνολογιών - Ηλεκτρονικές Υπηρεσίες.</p>
    </footer>
    <script src="/bootstrap/js/bootstrap.js"></script>
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