<x-layout_school>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>
                    <i class="bi bi-eye-slash"></i> Αποκρυμμένες Άδειες
                </h2>
                <a href="{{ route('leaves.create') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Επιστροφή στις Άδειες
                </a>
            </div>
            <hr>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($hiddenLeaves->isEmpty())
        {{-- No Hidden Leaves --}}
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Δεν υπάρχουν αποκρυμμένες άδειες.
                </div>
            </div>
        </div>
    @else
        {{-- Hidden Leaves List --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul"></i> 
                            Λίστα Αποκρυμμένων Αδειών ({{ $hiddenLeaves->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($hiddenLeaves as $leave)
                            <div class="row align-items-center mb-3 border-bottom pb-3">
                                {{-- Teacher Name --}}
                                <div class="col-md-2">
                                    <strong>{{ $leave->surname }} {{ $leave->name }}</strong>
                                </div>

                                {{-- Leave Type --}}
                                <div class="col-md-2">
                                    <a href="#" 
                                       class="no-spinner text-decoration-none" 
                                       data-get-teacher-leaves-url="{{ route('leaves.getTeacherLeavesApi', ['teacher_leave' => $leave->id]) }}"
                                       data-toggle="modal" 
                                       data-target="#infoModal" 
                                       data-leave-id="{{ $leave->id }}">
                                        <i class="bi bi-info-circle"></i> {{ $leave->leave_type }}
                                    </a>
                                </div>
                                
                                {{-- Leave Duration --}}
                                <div class="col-md-3">
                                    <span class="badge bg-secondary">
                                        {{ $leave->leave_days }} 
                                        {{ $leave->leave_days == 1 ? 'ημέρα στις' : 'ημέρες από' }} 
                                        {{ $leave->leave_start_date->format('d/m/Y') }}
                                    </span>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-3">
                                    @if($leave->submitted && $leave->protocol_number)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Υποβλήθηκε
                                            (Αρ. Πρωτ.: {{ $leave->protocol_number }})
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock"></i>
                                        </span>
                                    @endif
                                </div>

                                {{-- Unhide Button --}}
                                <div class="col-md-2">
                                    <form action="{{ route('leaves.unhide', ['teacher_leave' => $leave->id]) }}" 
                                          method="post" 
                                          class="w-100">
                                          <!-- onsubmit="return confirm('Είστε σίγουροι ότι θέλετε να επαναφέρετε την ορατότητα αυτής της άδειας;')" -->
                                          
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="bi bi-eye-fill"></i> Επαναφορά στην κεντρική σελίδα
                                        </button>
                                    </form>
                                </div>

                                {{-- Files List (if exists) --}}
                                @if($leave->files_json)
                                    <div class="col-12 mt-2">
                                        <small class="text-muted">
                                            <i class="bi bi-paperclip"></i> Συνημμένα αρχεία:
                                        </small>
                                        <div class="d-flex flex-wrap gap-2 mt-1">
                                            @php 
                                                $fileNames = json_decode($leave->files_json, true);
                                            @endphp
                                            
                                            @foreach($fileNames as $serverFileName => $databaseFileName)
                                                <form action="{{ route('leaves.download_file', [
                                                    'serverFileName' => $serverFileName, 
                                                    'databaseFileName' => $databaseFileName
                                                ]) }}" method="get" class="mb-0">
                                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-file-earmark-pdf"></i> {{ $databaseFileName }}
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Info Box --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-light border">
                <h6 class="alert-heading">
                    <i class="bi bi-info-circle"></i> Πληροφορίες
                </h6>
                <ul class="mb-0">
                    <li>Οι αποκρυμμένες άδειες δεν εμφανίζονται στην κύρια λίστα αδειών.</li>
                    <li>Μπορείτε να επαναφέρετε την ορατότητα μιας άδειας πατώντας το κουμπί "Εμφάνιση".</li>
                    <li>Η απόκρυψη δεν διαγράφει την άδεια - απλά την κρύβει από την κύρια λίστα.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

</x-layout_school>