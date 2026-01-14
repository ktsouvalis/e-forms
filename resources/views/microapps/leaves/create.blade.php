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

    <div class="container">
        <h3>Υποβολή αδειών εκπαιδευτικών στη Διεύθυνση Π.Ε. Αχαΐας</h3>
        
        <div>
            <h4>ΔΟΚΙΜΑΣΤΙΚΗ ΛΕΙΤΟΥΡΓΙΑ - Ιανουάριος 2026</h4>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i>
                <p>Στα πλαίσια της ΠΙΛΟΤΙΚΗΣ ΛΕΙΤΟΥΡΓΙΑΣ έχει υποβληθεί εξαιρετικά μεγάλος αριθμός αδειών από τις Σχολικές Μονάδες. 
                <br>Σας ευχαριστούμε πάρα πολύ για τη συνεργασία!</p>
                <br><br><hr>
                <p class="text-danger">
                Ωστόσο η λειτουργία θα απενεργοποιηθεί προσωρινά έως ότου διορθωθούν μικρο-προβλήματα που εντοπίστηκαν. Παρακαλούμε μην ανεβάζετε νέες άδειες έως ότου αφαιρεθούν αυτά τα μηνύματα!
                Παρακαλούμε να αποστέλλονται οι άδειες στο mail.
                <br><br>
                Τμήμα Πληροφορικής & Νέων Τεχνολογιών - Διεύθυνση Π.Ε. Αχαΐας
                </p>
            </div>
            
            <div>
                <button class="btn btn-primary m-3" type="button" data-bs-toggle="collapse" data-bs-target="#instructionsList" aria-expanded="false" aria-controls="instructionsList">
                    <h6>Για αναλυτική περιγραφή της διαδικασίας υποβολής και έγκρισης Αδειών Εκπαιδευτικών πατήστε εδώ</h6>
                </button>
                <div class="collapse" id="instructionsList">
                    <ul class="list-group m-3">
                        <li class="list-group-item">1) Καταχώρηση άδειας στο mySchool από τη Σχολική Μονάδα.</li>
                        <li class="list-group-item">2) Εμφάνιση των καταχωρημένων αδειών στις Ηλεκτρονικές Φόρμες καθημερινά στις 9:00, στις 10:00 και στις 14:00.
                            <br><em>Η μετάπτωση πραγματοποιείται αυτόματα και είναι προγραμματισμένη στις ανωτέρω αναφερόμενες ώρες.</em>
                        </li>
                        <li class="list-group-item">3) Ανέβασμα απαραίτητων αρχείων από τη Σχολική Μονάδα στις Ηλεκτρονικές Φόρμες 
                            <br><em>(π.χ. ιατρικές βεβαιώσεις, δικαιολογητικά κλπ)</em>
                        </li>
                        <li class="list-group-item">4) Υποβολή άδειας από τη Σχολική Μονάδα στη Διεύθυνση Π.Ε. Αχαΐας
                            <br><em>(Η άδεια πρωτοκολείται αυτόματα και χρεώνεται στον αρμόδιο υπάλληλο. Στη συνέχεια, κατά περίπτωση, προχωράει η διαδικασία ενημέρωσης του φακέλου του εκπαιδευτικού, έγκριση της άδειας και διαβίβαση στην υγειονομική επιτροπή ανάλογα με το είδος και τη διάρκεια της άδειας.)</em>
                        </li>
                        <li class="list-group-item">5) Με την τελική έγκριση της άδειας (όπου απαιτείται) από τη Διεύθυνση Π.Ε. Αχαΐας, η Σχολική Μονάδα ενημερώνεται αυτόματα μέσω email.</li>
                    </ul>
                    <div class="alert alert-info" role="alert">
                        <strong><i class="bi bi-info-circle"></i> Σημείωση:</strong> Οι άδειες των εκπαιδευτικών που είναι αποσπασμένοι από άλλους νομούς υποβάλλονται απευθείας από το Σχολείο στην αντίστοιχη διεύθυνση οργανικής του εκπαιδευτικού και όχι στη Διεύθυνση Π.Ε. Αχαΐας.
                    </div>
                </div>
            </div>
        </div>

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
                Δεν υπάρχουν καταχωρημένες άδειες αυτή τη στιγμή. Ξεκινήστε καταχωρόντας τις άδειες στο mySchool.
            </div>
        @endforelse
    </div>
</x-layout_school>