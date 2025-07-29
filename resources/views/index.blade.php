<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Ηλεκτρονικές Υπηρεσίες - Διεύθυνση Π.Ε. Αχαΐας</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Noto+Sans:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" href="{!! asset('/favicon/favicon.ico') !!}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('favicon/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset("favicon/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset("favicon/favicon-16x16.png")}}">
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/5083d79d45.js" crossorigin="anonymous"></script>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
        }
        
        body {
            font-family: 'Roboto', 'Noto Sans', sans-serif;
            background-color: #f5f7fa;
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--primary-color), #1a2530);
            color: white;
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            padding: 2rem;
        }
        
        .card-service {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            height: 100%;
        }
        
        .card-service:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .form-control {
            border-radius: 50px;
            padding: 10px 20px;
            border: 1px solid #ddd;
        }
        
        .info-section {
            background: linear-gradient(135deg, var(--primary-color), #1a2530);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        footer {
            background-color: var(--primary-color);
            color: white;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                width: 100%;
            }
            
            .main-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            {{-- <div class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-dark">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <img src="{{asset('favicon/android-chrome-512x512.png')}}" width="80" height="80" alt="services" class="rounded-circle border border-white">
                        <h5 class="mt-3">ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ ΠΑΤΡΩΝ</h5>
                    </div>
                    <hr class="bg-light">
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="bi bi-house-door"></i>
                                Αρχική
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-headset"></i>
                                Τεχνική Στήριξη
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-sun"></i>
                                Ολοήμερο, Πρωινή Υποδοχή
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-bus"></i>
                                Εκδρομές
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-suitcase"></i>
                                Πρόσφυγες Μαθητές
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-file-signature"></i>
                                Εσωτερικός Κανονισμός
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-file-pdf"></i>
                                Αρχεία Διεύθυνσης
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a href="#" class="nav-link text-danger">
                                <i class="fas fa-sign-out-alt"></i>
                                Αποσύνδεση
                            </a>
                        </li>
                    </ul>
                </div>
            </div> --}}
            
            <!-- Main Content -->
            <div class="col px-5 ms-sm-auto main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Διεύθυνση Πρωτοβάθμιας Εκπαίδευσης Αχαΐας - Ηλεκτρονικές Φόρμες</h1>
                    <div class="col-md-4 d-flex justify-content-end">
                        <!-- <div class="btn-toolbar mb-2 px-1 mb-md-0">
                            <a href="{{url('/sch_sso_login')}}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-sign-in-alt"></i> Σύνδεση Σχολείου / Εκπαιδευτικού
                            </a>
                        </div> -->
                        <div class="btn-toolbar mb-2 px-1 mb-md-0">
                            <a href="{{url('/index_user')}}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-sign-in-alt"></i> Σύνδεση Υπαλλήλου Διεύθυνσης
                            </a>
                        </div>
                    </div>
                    
                </div>
                
                @include('components/messages')
                
                <div class="info-section">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="fw-bold"><i class="fas fa-user-lock bg-dark text-white p-2 rounded-circle me-2"></i>Σύνδεση</h3>
                            <p class="my-4"> Η σύνδεση στις Ηλεκτρονικές Φόρμες για Σχολεία και Εκπαιδευτικούς πραγματοποιείται με χρήση των κωδικών του Πανελλήνιου Σχολικού Δικτύου.</p>
                        </div>
                        <div class="col-md-4">
                            <div class="btn-toolbar mb-2 px-1 mb-md-0">
                                <a href="{{url('/sch_sso_login')}}" class="btn btn-primary w-100">
                                    <i class="fas fa-sign-in-alt"></i> Σύνδεση Σχολείου / Εκπαιδευτικού
                                </a>
                            </div>        
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body text-center py-4">
                                <h4 class="card-title">Ηλεκτρονικές Φόρμες</h4>
                                <p class="card-text">Ένα Πληροφοριακό Σύστημα για γρήγορη ανταλλαγή (συλλογή και διαμοιρασμό) στοιχείων και αρχείων μεταξύ της Διεύθυνσης και των Σχολικών Μονάδων καθώς και των Εκπαιδευτικών.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card card-service h-100 shadow-sm">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <i class="fas fa-video fa-3x text-danger mb-3"></i>
                                    <h5>Εκπαιδευτικά Βίντεο</h5>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item border-0">
                                        <a href="https://youtu.be/4RCUqXGDDTQ" target="_blank" class="text-decoration-none text-danger">
                                            <i class="fas fa-play-circle me-2"></i>Αίτηση Απόσπασης εντός ΠΥΣΠΕ
                                        </a>
                                    </li>
                                    <li class="list-group-item border-0">
                                        <a href="https://youtu.be/v6EviCZKyfI" target="_blank" class="text-decoration-none text-info">
                                            <i class="fas fa-play-circle me-2"></i>Συλλογή Αρχείου
                                        </a>
                                    </li>
                                    <li class="list-group-item border-0">
                                        <a href="https://youtu.be/V8TlBVjCSs4" target="_blank" class="text-decoration-none text-warning">
                                            <i class="fas fa-play-circle me-2"></i>Αποστολή Αρχείων
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card card-service h-100 shadow-sm bg-primary text-white">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <i class="fas fa-key fa-3x mb-3"></i>
                                    <h5>Μοναδική Πρόσβαση</h5>
                                </div>
                                <p>Η σύνδεση Σχολείων και Εκπαιδευτικών αρμοδιότητας της Δι.Π.Ε. Αχαΐας πραγματοποιείται μέ χρήση των κωδικών του Πανελλήνιου Σχολικού Δικτύου. 
                                    Οι Σχολικές Μονάδες θα πρέπει να χρησιμοποιήσουν τους κωδικούς πρόσβασης στο MySchool.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card card-service h-100 shadow-sm bg-warning">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <i class="fas fa-shield-alt fa-3x mb-3"></i>
                                    <h5>Ασφάλεια & Υποστήριξη</h5>
                                </div>
                                <p>Δεν επιτρέπεται πρόσβαση στις Ηλεκτρονικές Φόρμες για μη εξουσιοδοτημένους χρήστες.</p>
                                <div class="alert alert-light mt-3">
                                    <i class="fas fa-envelope me-2"></i>Υποστήριξη: <a href="mailto:it@dipe.ach.sch.gr">it@dipe.ach.sch.gr</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer mt-auto py-3 bg-dark text-white">
        <div class="container text-center">
            <p class="m-0">Copyright &copy; {{Illuminate\Support\Carbon::now()->year}} <a href="#" class="text-white">e-forms</a>. Διεύθυνση Π.Ε. Αχαΐας - Τμήμα Πληροφορικής & Νέων Τεχνολογιών - Ηλεκτρονικές Υπηρεσίες.</p>
        </div>
    </footer>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>