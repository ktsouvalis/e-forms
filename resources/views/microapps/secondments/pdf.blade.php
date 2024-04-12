<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" href="{{ public_path('bootstrap/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" href="favicon/favicon.ico"/>
    {{-- <link href="{{ public_path('fontawesome-free-6.4.2-web/css/fontawesome.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ public_path('fontawesome-free-6.4.2-web/css/brands.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ public_path('fontawesome-free-6.4.2-web/css/solid.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ public_path('fontawesome-free-6.4.2-web/css/regular.css') }}" rel="stylesheet"> --}}
  </head>
  <body>
    @push('title')
        <title>Αποσπάσεις</title>
    @endpush
    @php
        $teacher = $secondment->teacher;
    @endphp
<div class="container">
    <h2 class="text-center">Αίτηση Απόσπασης εντός ΠΥΣΠΕ Αχαΐας</h2>
	<div class="row justify-content-center">
		<div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0">
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-solid fa-user"></i> Προσωπικά Στοιχεία</h3>
                    <p class="m-0">Παρακαλούμε ελέγξτε την ορθότητα των στοιχείων που αναγράφονται</p>
                </div>
            </div>
        </div>
        </div>  
	</div>
</div> {{-- End of Custom container --}} 
  <!-- footer begins -->
    <footer class="border-top text-center small text-muted py-3">
      <p class="m-0">Copyright &copy; 2023 <a href="{{url("/index_teacher")}}" class="text-muted">e-forms</a>. Διεύθυνση Π.Ε. Αχαΐας - Τμήμα Πληροφορικής & Νέων Τεχνολογιών - Ηλεκτρονικές Υπηρεσίες.</p>
    </footer>
    <script src="{{url('/bootstrap/js/bootstrap.js')}}"></script>
   </body>
</html>