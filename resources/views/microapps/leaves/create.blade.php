<x-layout_school>
    @push('scripts')
    <script src="{{asset("leaves.js")}}"></script>
    @endpush
    
    @push('scripts')
        <script>
            var leaves = @json($leaves);
        </script>
    @endpush
    
    @push('title')
        <title>{{ $microapp->name }}</title>
    @endpush
    
    {{-- Info Modal --}}
    <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title " id="messageModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <p></p>   
                    <p></p>
                    <p></p>
                    <hr>
                    <p></p>
                    <p></p>
                    <br>
                    <small style="text-align: end"><p class="text-muted"></p></small>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="text-center mb-4">
            <h2 class="text-primary">
                <i class="bi bi-file-earmark-text"></i>
                Υποβολή Αδειών Εκπαιδευτικών
            </h2>
            <p class="text-muted">Διεύθυνση Πρωτοβάθμιας Εκπαίδευσης Αχαΐας</p>
        </div>

        {{-- Accordion Container --}}
        <div class="accordion mb-4" id="leavesInstructionsAccordion">
            
            {{-- Main Process Section --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingProcess">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProcess" aria-expanded="false" aria-controls="collapseProcess">
                        <i class="bi bi-diagram-3 me-2"></i>
                        <strong>Διαδικασία Υποβολής και Έγκρισης Αδειών</strong>
                    </button>
                </h2>
                <div id="collapseProcess" class="accordion-collapse collapse" aria-labelledby="headingProcess" data-bs-parent="#leavesInstructionsAccordion">
                    <div class="accordion-body">
                        <div class="timeline">
                            {{-- Step 1 --}}
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <strong>1</strong>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-primary mb-1">
                                        <i class="bi bi-pencil-square"></i> Καταχώρηση στο mySchool
                                    </h6>
                                    <p class="mb-0 text-muted">
                                        Η Σχολική Μονάδα καταχωρεί την άδεια στο mySchool.
                                    </p>
                                </div>
                            </div>

                            {{-- Step 2 --}}
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <strong>2</strong>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-primary mb-1">
                                        <i class="bi bi-arrow-repeat"></i> Αυτόματη Μετάπτωση
                                    </h6>
                                    <p class="mb-1 text-muted">
                                        Οι άδειες εμφανίζονται αυτόματα στις Ηλεκτρονικές Φόρμες καθημερινά στις:
                                    </p>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <span class="badge bg-info">09:00</span>
                                        <span class="badge bg-info">10:00</span>
                                        <span class="badge bg-info">14:00</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <strong>3</strong>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-primary mb-1">
                                        <i class="bi bi-cloud-upload"></i> Ανέβασμα Δικαιολογητικών
                                    </h6>
                                    <p class="mb-1 text-muted">
                                        Η Σχολική Μονάδα ανεβάζει τα απαραίτητα αρχεία:
                                    </p>
                                    <small class="text-muted">
                                        • Ιατρικές βεβαιώσεις<br>
                                        • Δικαιολογητικά<br>
                                        • Άλλα απαραίτητα έγγραφα
                                    </small>
                                </div>
                            </div>

                            {{-- Step 4 --}}
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <strong>4</strong>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-primary mb-1">
                                        <i class="bi bi-send"></i> Υποβολή στη Διεύθυνση
                                    </h6>
                                    <p class="mb-1 text-muted">
                                        Η Σχολική Μονάδα υποβάλλει την άδεια στη Διεύθυνση Π.Ε. Αχαΐας.
                                    </p>
                                    <div class="alert alert-light border mt-2 mb-0">
                                        <small>
                                            <i class="bi bi-info-circle text-primary"></i>
                                            Η άδεια πρωτοκολείται αυτόματα και χρεώνεται στον αρμόδιο υπάλληλο. 
                                            Ακολουθεί η διαδικασία έγκρισης ανάλογα με το είδος και τη διάρκεια της άδειας.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 5 --}}
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-check-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-success mb-1">
                                        <i class="bi bi-envelope-check"></i> Τελική Έγκριση & Ενημέρωση
                                    </h6>
                                    <p class="mb-0 text-muted">
                                        Με την τελική έγκριση της άδειας, η Σχολική Μονάδα ενημερώνεται αυτόματα μέσω email.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Management Options Section --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingManagement">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseManagement" aria-expanded="false" aria-controls="collapseManagement">
                        <i class="bi bi-sliders me-2"></i>
                        <strong>Διόρθωση, Απόκρυψη και επανεμφάνιση Αδειών</strong>
                    </button>
                </h2>
                <div id="collapseManagement" class="accordion-collapse collapse" aria-labelledby="headingManagement" data-bs-parent="#leavesInstructionsAccordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                    {{-- Hide Option --}}
                    <div class="col-md-4">
                        <div class="card h-100 border-secondary">
                            <div class="card-body text-center">
                                <i class="bi bi-eye-slash fs-1 text-secondary mb-3"></i>
                                <h5 class="card-title">Απόκρυψη Άδειας</h5>
                                <p class="card-text small text-muted">
                                    Για λόγους οργάνωσης και μόνο μπορείτε να αποκρύψετε μια άδεια από τη λίστα:
                                </p>
                                <ul class="list-unstyled text-start small">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success"></i>
                                        Είτε δεν έχει υποβληθεί ακόμα μέσα από τις Ηλεκτρονικές Φόρμες (π.χ. έχει υποβληθεί ήδη με e-mail)
                                    </li>
                                    <li>
                                        <i class="bi bi-check-circle text-success"></i>
                                        Είτε έχει υποβληθεί και εγκριθεί.
                                    </li>
                                </ul>
                                <div class="alert alert-light border mt-3 mb-0">
                                    <small>
                                        <i class="bi bi-info-circle text-primary"></i>
                                        Η άδεια δεν διαγράφεται - απλά κρύβεται από την κύρια λίστα και μπορείτε να την επαναφέρετε όποτε θελήσετε.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Edit Option --}}
                    <div class="col-md-4">
                        <div class="card h-100 border-warning">
                            <div class="card-body text-center">
                                <i class="bi bi-unlock fs-1 text-warning mb-3"></i>
                                <h5 class="card-title">Διόρθωση Άδειας</h5>
                                <p class="card-text small text-muted">
                                    Σε περίπτωση λάθους ή ανάκλησης και επανυποβολής μπορείτε να ξεκλειδώσετε μια άδεια που έχει ήδη υποβληθεί.<br>
                                    Άδειες που ανακαλούνται στο myschool και επανυποβάλλονται, εντοπίζονται αυτόματα από την εφαρμογή και ξεκλειδώνονται. Πρέπει να υποβληθούν εκ νέου και ενημερώνεται το πρωτόκολλο.                              </p>
                                <ul class="list-unstyled text-start small">
                                    <li class="mb-2">
                                        <i class="bi bi-arrow-clockwise text-warning"></i>
                                        Ξεκλείδωμα άδειας
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-pencil text-warning"></i>
                                        Προσθήκη/αφαίρεση αρχείων
                                    </li>
                                    <li>
                                        <i class="bi bi-send text-warning"></i>
                                        Επανυποβολή στη Διεύθυνση
                                    </li>
                                </ul>
                                <div class="alert alert-warning border-warning mt-3 mb-0">
                                    <small>
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Στις άδειες που ξεκλειδώνονται πρέπει να πατηθεί το "Επανυποβολή στη Διεύθυνση" ώστε να ενημερωθεί το πρωτόκολλο.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Unhide Option --}}
                    <div class="col-md-4">
                        <div class="card h-100 border-primary">
                            <div class="card-body text-center">
                                <i class="bi bi-eye fs-1 text-primary mb-3"></i>
                                <h5 class="card-title">Επανεμφάνιση Άδειας</h5>
                                <p class="card-text small text-muted">
                                    Μπορείτε να επαναφέρετε αποκρυμμένες άδειες στην κύρια λίστα.
                                </p>
                                <ul class="list-unstyled text-start small">
                                    <li class="mb-2">
                                        <i class="bi bi-list-ul text-primary"></i>
                                        Προβολή αποκρυμμένων αδειών
                                    </li>
                                    <li>
                                        <i class="bi bi-arrow-counterclockwise text-primary"></i>
                                        Επαναφορά στην κύρια λίστα
                                    </li>
                                </ul>
                                <div class="alert alert-light border mt-3 mb-0">
                                    <small>
                                        <i class="bi bi-info-circle text-primary"></i>
                                        Οι αποκρυμμένες άδειες εμφανίζονται σε ξεχωριστή σελίδα.
                                    </small>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Important Notes Section --}}
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info border-info">
                    <h6 class="alert-heading">
                        <i class="bi bi-info-circle-fill"></i> 
                        Σημείώσεις:
                    </h6>
                    <p class="mb-2 small">
                        Οι άδειες των εκπαιδευτικών που είναι <strong>αποσπασμένοι από άλλους νομούς</strong> 
                        υποβάλλονται απευθείας από το Σχολείο στην αντίστοιχη διεύθυνση οργανικής του εκπαιδευτικού 
                        και <strong>όχι</strong> στη Διεύθυνση Π.Ε. Αχαΐας.
                    </p>
                    <p class="mb-2 small">
                        Οι άδειες των αναπληρωτών εκπαιδευτικών υποβάλλονται στο <strong>invoices</strong>
                    </p>
                    <p class="mb-2 small">
                        Άδειες που έχουν αποσταλεί ήδη στη Διεύθυνση μέσα από το e-mail adeies@dipe.... δεν πρέπει να ανέβουν εδώ. Μπορείτε απλά να τις κάνετε απόκρυψη.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
    .timeline {
        position: relative;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 24px;
        top: 50px;
        bottom: 50px;
        width: 2px;
        background: linear-gradient(to bottom, #0d6efd 80%, #198754);
    }
    /* Accordion header colors */
    .accordion-button.collapsed {
        background-color: #589afc;
        color: white;
    }

    .accordion-button:not(.collapsed) {
        background-color: #0d6efd;
        color: white;
    }

    .accordion-button:not(.collapsed) i {
        color: white;
    }

    /* Different color for second accordion when open */
    #headingManagement .accordion-button.collapsed {
        background-color: #71b193;
    }

    #headingManagement .accordion-button:not(.collapsed) {
        background-color: #198754;
    }

    /* Hover effect */
    .accordion-button:hover {
        background-color: #3688fa;
    }

    #headingManagement .accordion-button:hover {
        background-color: #2c885d;
    }

    .accordion-button:not(.collapsed):hover {
        background-color: #0b5ed7;
    }

    #headingManagement .accordion-button:not(.collapsed):hover {
        background-color: #157347;
    }
    </style>

        {{-- Table Header --}}
        <div class="row">
            <div class="col-md-2"><strong>Επώνυμο</strong></div>
            <div class="col-md-2"><strong>Τύπος Άδειας</strong></div>
            <div class="col-md-2"><strong>Ημέρες Άδειας</strong></div>
            <div class="col-md-2"><strong>Δικαιολογητικό/ά</strong></div>
            <div class="col-md-2"><strong>Αποστολή</strong></div>
        </div>

        {{-- Leaves List --}}
        @forelse ($leaves as $leave)
            @include('microapps.leaves.partials.leave-row', ['leave' => $leave])
        @empty
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i>
                Δεν υπάρχουν καταχωρημένες νέες άδειες αυτή τη στιγμή. Ξεκινήστε καταχωρώντας τις άδειες που τυχόν υπάρχουν στο mySchool.
            </div>
        @endforelse

        {{-- Hidden Leaves Section (place this at the bottom of the main view, outside the loop) --}}
        @if(isset($showHiddenLeavesLink) && $showHiddenLeavesLink)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-eye-slash"></i> Αποκρυμμένες Άδειες
                            </h5>
                            <p class="card-text">
                                Έχετε αποκρυμμένες άδειες που δεν εμφανίζονται στη λίστα.
                            </p>
                            <a href="{{ route('leaves.hidden') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-eye"></i> Προβολή Αποκρυμμένων Αδειών
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layout_school>