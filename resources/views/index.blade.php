<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Ηλεκτρονικές Υπηρεσίες</title>
    <link rel="stylesheet" href="{{asset('bootstrap/css/bootstrap.css')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
    <link rel="icon" href="{!! asset('/favicon/favicon.ico') !!}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('favicon/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset("favicon/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset("favicon/favicon-16x16.png")}}">
    <link rel="manifest" href="{{asset('favicon/site.webmanifest')}}">
    <link rel="mask-icon" href="{{asset('favicon/safari-pinned-tab.svg')}}" color="#5bbad5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    
    <!-- fontawesome -->
    <script src="https://kit.fontawesome.com/5083d79d45.js" crossorigin="anonymous"></script>
  
</head> 
<body>
<div class="row">
    <!-- This blank div appears only in small screens-->
    <div class="col-2 d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary d-md-none" style="width: 280px; opacity:0.2;">
    </div>
    <!-- This div does not appear in small screens-->
    <div class="col-2 d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary d-none d-md-block " style="width: 280px; opacity:0.2;">
        <div class="d-flex justify-content-center"><img src="{{asset('favicon/android-chrome-512x512.png')}}" width="100" height="100" alt="services"></div>
          <div class="d-flex justify-content-center h6">ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ ΠΑΤΡΩΝ</div>
          <hr>
          <ul class="nav nav-pills flex-column mb-auto">
            <p>
            <li class="nav-item">
            <div class="badge text-wrap py-2 m-1" style="width: 15rem; background-color:DodgerBlue; text-align:center;">
              <a href="" style="text-decoration:none;" class="text-dark bi bi-house"> Αρχική</a>
            </div>
            </li>
            </p>
            <li class="nav-item">
            
                                        <li class="nav-item">
                <div class="badge text-wrap py-2 m-1 text-dark" style="width: 15rem; background-color:DarkKhaki; text-align:center;">
                  <div class="text-dark fa-solid fa-headset"></div> 
                    Τεχνική Στήριξη
                </div>
                </li> 
                                                    <li class="nav-item">
                <div class="badge text-wrap py-2 m-1 text-dark" style="width: 15rem; background-color:skyblue; text-align:center;">
                  <div class="text-dark fa-regular fa-sun"></div> 
                  Ολοήμερο, Πρωινή Υποδοχή
                </div>
                </li> 
                                                    <li class="nav-item">
                <div class="badge text-wrap py-2 m-1 text-dark" style="width: 15rem; background-color:Khaki; text-align:center;">
                  <div class="text-dark fa-solid fa-bus"></div> 
                        Εκδρομές
                </div>
                </li> 
                                                                            <li class="nav-item">
                <div class="badge text-wrap py-2 m-1 text-dark" style="width: 15rem; background-color:Plum; text-align:center;">
                  <div class="text-dark fa-solid fa-suitcase"></div> 
                     Πρόσφυγες Μαθητές
                </div>
                </li> 
                                                    <li class="nav-item">
                <div class="badge text-wrap py-2 m-1 text-dark" style="width: 15rem; background-color:#f1948a; text-align:center;">
                  <div class="text-dark fa-solid fa-file-signature"></div> 
                    Εσωτερικός Κανονισμός
                </div>
                </li> 
                        
            
            <li class="nav-item">
            <div class="badge text-wrap py-2 m-1 text-dark" style="width: 15rem; background-color:#00bfff; text-align:center;">
              <div class="text-dark fa-solid fa-file-pdf"></div> 
                Αρχεία Διεύθυνσης
            </div>
            </li> 


            <p>
            <li class="nav-item">
            <div class="badge text-wrap py-2 m-1 text-dark" style="width: 15rem; background-color:Gainsboro; text-align:center;">
                <div class="text-dark fa-solid fa-arrow-right-from-bracket"></div>
                    Αποσύνδεση
            </div>
            </li>
            </p>
          </ul>
          <hr>
    </div>
    <div class="col-8">
        <div class="container ">
            <div class="row justify-content-md-center">
                <div class="col py-3 h4">
                    Διεύθυνση Πρωτοβάθμιας Εκπαίδευσης Αχαΐας
                </div>
            </div>
            <div class="row flex justify-content-end">
                <div class="col px-4 pb-3 h2">Ηλεκτρονικές Φόρμες</div>
                <div class="col px-4 pb-3"> <a href="{{url('/index_user')}}" class="position-absolute bottom-0 start-0 text-secondary"><i class="fa fa-sign-in" aria-hidden="true"> Σύνδεση Υπαλλήλου Διεύθυνσης</i></a>
                </div>
        </div>
    </div>
@include('components/messages')

<div class="row justify-content-md-center">
    <div class="col"></div>
    <div class="col">
        <br><br><br>Μπορείτε να λάβετε στο e-mail σας το Σύνδεσμο με τη <strong>Μοναδική Καρτέλα</strong> σας, συμπληρώνοντας τα ακόλουθα στοιχεία:
        <br><br>
        <form action="{{url("/find_entity")}}" method="post">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label"><strong>ΑΜ ή ΑΦΜ</strong> για Εκπαιδευτικό <br> <strong>7ψήφιος Κωδικός Υ.ΠΑΙ.Θ.Α.</strong> για Σχολείο</label>
                <div class="py-2">
                    <input type="text" value="{{old('username')}}" name="entity_code" class="form-control">
                    @error('username')
                        {{$message}}
                    @enderror
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Αποστολή Συνδέσμου</button>
        </form>
        
    </div>
    <div class="col"></div>
</div>   
<div class="row bg-dark bg-gradient p-5 m-2  border border-danger rounded shadow text-white justify-content-md-center">
    Οι Ηλεκτρονικές Φόρμες είναι ένα Πληροφοριακό Σύστημα που έχει αναπτυχθεί στη Διεύθυνση Πρωτοβάθμιας Εκπαίδευσης Αχαΐας προκειμένου μέσα από αυτό να επιτυγχάνεται 
        γρήγορη ανταλλαγή (συλλογή και διαμοιρασμός) στοιχείων και αρχείων μεταξύ της Υπηρεσίας και των Σχολικών Μονάδων καθώς και των Εκπαιδευτικών.
</div>
<div class="row justify-content-md-center m-2">
    <div class="col p-5 my-2 me-2 bg-success border border-danger shadow rounded text-white">
        <p>Παρουσίαση λειτουργιών:</p>
        <ul>
            <li><a target="_blank" class="link-info link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" href="https://youtu.be/v6EviCZKyfI">Συλλογή Αρχείου</a></li>
            <li><a target="_blank" class="link-warning link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" href="https://youtu.be/V8TlBVjCSs4">Αποστολή Αρχείων</a> <span><em>(και <a target="_blank" class="link-warning link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" href="https://youtu.be/0i41NzpvJv8">Παραγωγή εγγράφων</a> για αυτόματη αποστολή.</em></span>)</li>
        </ul>
        
    </div>
    <div class="col p-5 my-2 mx-2 bg-primary bg-gradient border border-danger shadow rounded text-light">
        Η σύνδεση κάθε Σχολείου αλλά και κάθε Εκπαιδευτικού αρμοδιότητας της Δι.Π.Ε. Αχαΐας πραγματοποιείται μέσα από ένα <strong>μοναδικό σύνδεσμο / μοναδική καρτέλα</strong> τον οποίο μπορείτε κάθε στιγμή να ανακτήσετε
        μέσα από αυτή τη σελίδα
    </div>
    <div class="col p-5 my-2 ms-2 bg-warning border border-danger shadow rounded text-dark">
        <p>Δεν επιτρέπεται πρόσβαση στις Ηλεκτρονικές Φόρμες για μη εξουσιοδοτημένους χρήστες.</p>
        <p>Υποστήριξη: it@dipe.ach.sch.gr</p>
    </div>
    
</div>
         
<!-- footer begins -->
</div>

</div>
       <footer class="border-top text-center small text-muted py-3">
      <p class="m-0">Copyright &copy; {{Illuminate\Support\Carbon::now()->year}} <a href="" class="text-muted">e-forms</a>. Διεύθυνση Π.Ε. Αχαΐας - Τμήμα Πληροφορικής & Νέων Τεχνολογιών - Ηλεκτρονικές Υπηρεσίες.</p>
    </footer>
    <script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>

        </div> <!-- container closing -->
   
    <div class="d-flex justify-content-center"><p class="h3" style="color:black"> </p></div>
       </body>
</html>
