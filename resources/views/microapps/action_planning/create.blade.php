<x-layout_school>

@push('title')
    <title>{{ $microapp->name }}</title>
@endpush

<div class="container py-4">
    <div class="col-12 col-md-10 col-lg-8 mx-auto">
        <div class="card border-primary rounded-3 shadow-sm">
            <div class="card-header bg-info text-white text-center py-3">
                <h3><i class="bi bi-calendar-check"></i> Υποβολή Προγραμματισμού Σχολικής Μονάδας</h3>
                <p class="mb-0">
                    Υποβάλετε τον ετήσιο ή τριμηνιαίο προγραμματισμό σας. 
                    <br> Μπορείτε να ανεβάσετε αρχικό προγραμματισμό καθώς και τροποποιήσεις διατηρώντας πλήρες ιστορικό.
                </p>
            </div>

            <div class="card-body">
            @if($microapp->accepts == 0)
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle"></i> Η υποβολή προγραμματισμού έχει κλείσει.
                </div>
            @elseif(is_null($selectedCycle))
                {{-- ΦΟΡΜΑ ΕΠΙΛΟΓΗΣ ΤΥΠΟΥ --}}
                <form action="{{ route('action_planning.select_cycle') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Επιλέξτε τον τύπο προγραμματισμού που θα υποβάλετε για ενημέρωση του Συμβούλου Εκπαίδευσης:</label>
                        <p> ΠΡΟΣΟΧΗ: Δεν υπάρχει δυνατότητα αλλαγής από ετήσιο σε τριμηνιαίο ή αντίστροφα μετά την αποθήκευση. </p>
                        <br>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="planning_cycle" id="cycle_annual" value="Ετήσιος" required>
                            <label class="form-check-label" for="cycle_annual">
                                <strong>Ετήσιος Προγραμματισμός</strong> 
                                <small class="text-muted">(1 αρχείο για όλη τη χρονιά)</small>
                            </label>
                        </div>
                        <br>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="planning_cycle" id="cycle_quarterly" value="Τριμηνιαίος" required>
                            <label class="form-check-label" for="cycle_quarterly">
                                <strong>Τριμηνιαίος Προγραμματισμός</strong> 
                                <small class="text-muted">(3 αρχεία, ένα για κάθε τρίμηνο)</small>
                            </label>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-right"></i> Συνέχεια
                        </button>
                    </div>
                </form>
            @else
                {{-- 2. ΕΜΦΑΝΙΣΗ ΕΠΙΛΕΓΜΕΝΟΥ ΚΥΚΛΟΥ & ΦΟΡΜΑΣ ΥΠΟΒΟΛΗΣ --}}
                <div class="alert alert-secondary d-flex justify-content-between align-items-center">
                    <span>Τύπος Υποβολής: <strong>{{ $selectedCycle }}</strong></span>
                </div>

                @php
                    $filesJson = $plan->files_json ?? ['annual' => [], 'Q1' => [], 'Q2' => [], 'Q3' => []];
                @endphp

                <form action="{{ route('action_planning.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="planning_cycle" value="{{ $selectedCycle }}">

                    @if($selectedCycle === 'Ετήσιος')
                        {{-- ΕΤΗΣΙΟΣ: 1 Αρχείο --}}
                        <div class="mb-3">
                            <label for="file" class="form-label fw-bold">Αρχείο Ετήσιου Προγραμματισμού</label>
                            <input type="file" name="file" id="file" class="form-control" accept=".pdf,.xlsx,.docx,.jpeg,.png" required>
                            <div class="form-text">Επιτρεπόμενοι τύποι: pdf, xlsx, docx, jpeg, png (Max 5MB).</div>
                        </div>
                    @else
                        {{-- ΤΡΙΜΗΝΙΑΙΟΣ: Επιλογή Τριμήνου (Slot) + Αρχείο --}}
                        <div class="mb-3">
                            <label for="slot" class="form-label fw-bold">Επιλογή Τριμήνου</label>
                            <select name="slot" id="slot" class="form-select" required>
                                <option value="">-- Επιλέξτε Τρίμηνο --</option>
                                <option value="Q1">1ο Τρίμηνο</option>
                                <option value="Q2">2ο Τρίμηνο</option>
                                <option value="Q3">3ο Τρίμηνο</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label fw-bold">Αρχείο Τριμήνου</label>
                            <input type="file" name="file" id="file" class="form-control" accept=".pdf,.xlsx,.docx,.jpeg,.png" required>
                        </div>
                    @endif

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Υποβολή Αρχείου</button>
                    </div>
                </form>

                <hr class="my-4">

                {{-- 3. ΠΑΡΟΥΣΙΑΣΗ ΥΠΟΒΛΗΘΕΝΤΩΝ ΑΡΧΕΙΩΝ --}}
                <div class="mt-4">
                    <h5 class="text-center fw-bold mb-3">Αρχεία που έχουν υποβληθεί</h5>

                    {{-- Ένδειξη "έλαβε γνώση" από τον σύμβουλο (όχι έλεγχος/έγκριση) --}}
                    @if($plan && $plan->checked)
                        <div class="alert alert-success d-flex align-items-center gap-2 py-2">
                            <i class="bi bi-eye-fill"></i>
                            <span>Ο σύμβουλος έλαβε γνώση του προγραμματισμού που υποβάλατε.</span>
                        </div>
                    @else
                        <div class="alert alert-light border text-muted d-flex align-items-center gap-2 py-2">
                            <i class="bi bi-eye-slash"></i>
                            <span>Εδώ θα εμφανιστεί ένδειξη όταν ο σύμβουλος λάβει γνώση του προγραμματισμού.</span>
                        </div>
                    @endif

                    @if($selectedCycle === 'Ετήσιος')
                        {{-- Λίστα Ετήσιου --}}
                        @if(!empty($filesJson['annual']))
                            @foreach($filesJson['annual'] as $serverFileName => $databaseFileName)
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-light">
                                    <a href="{{ route('action_planning.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName]) }}" class="btn btn-sm btn-outline-info text-dark text-start text-truncate" style="max-width: 60%;">
                                        <i class="bi bi-file-earmark-text"></i> {{ $databaseFileName }}
                                    </a>
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('action_planning.delete_file', ['actionPlanning' => $plan, 'serverFileName' => $serverFileName]) }}" method="POST" onsubmit="return confirm('Είστε σίγουροι για τη διαγραφή του αρχείου;');">
                                            @csrf
                                            @method('DELETE') <!-- Αν ο controller δέχεται DELETE, αλλιώς αν είναι GET το αφήνετε όπως είχατε -->
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Διαγραφή</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-center text-muted">Δεν έχει υποβληθεί αρχείο για τον ετήσιο προγραμματισμό.</p>
                        @endif

                    @else
                        {{-- Λίστα Τριμηνιαίου (Q1, Q2, Q3) --}}
                        @foreach(['Q1' => '1ο Τρίμηνο', 'Q2' => '2ο Τρίμηνο', 'Q3' => '3ο Τρίμηνο'] as $qKey => $qLabel)
                            <div class="card mb-3">
                                <div class="card-header bg-light fw-bold">{{ $qLabel }}</div>
                                <div class="card-body py-2">
                                    @if(!empty($filesJson[$qKey]))
                                        @foreach($filesJson[$qKey] as $serverFileName => $databaseFileName)
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <a href="{{ route('action_planning.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName]) }}" class="btn btn-sm btn-outline-info text-dark text-start text-truncate" style="max-width: 60%;">
                                                    <i class="bi bi-file-earmark-text"></i> {{ $databaseFileName }}
                                                </a>
                                                <form action="{{ route('action_planning.delete_file', ['actionPlanning' => $plan, 'serverFileName' => $serverFileName]) }}" method="POST" onsubmit="return confirm('Είστε σίγουροι;');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Διαγραφή</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted small mb-0">Δεν έχει υποβληθεί αρχείο για το {{ $qLabel }}.</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif
        </div>

                

</x-layout_school>
