<x-layout>
@push('title')
<title>Ημερήσια Αναφορά Απουσιών</title>
@endpush

<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="file-collection-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-2">Απουσίες Μαθητών λόγω Εποχικών Λοιμώξεων</h1>
                    <p class="mb-0 opacity-75">
                        
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="status-badge {{ !$deadlinePassed ? 'status-open' : 'status-closed' }}">
                        <i class="bi {{ !$deadlinePassed ? 'bi-check-circle' : 'bi-x-circle' }} me-2"></i>
                        {{ !$deadlinePassed ? 'Δέχεται υποβολές' : 'Η προθεσμία έληξε' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="row mb-4">
        <div class="col">
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        <!-- Today's Report Form -->
        <div class="col-lg-6">
            <div class="card file-card h-100">
                <div class="card-header bg-primary bg-opacity-10">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-check me-2"></i>
                        Σήμερα {{ now()->locale('el')->translatedFormat('l, d F Y') }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($deadlinePassed)
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Η προθεσμία έληξε!</strong> Η προθεσμία για τη σημερινή αναφορά ήταν στις 10:00 π.μ.
                        </div>
                        
                        @if($todayReport)
                            <div class="comment-section">
                                <h6 class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Υποβληθείσα Αναφορά
                                </h6>
                                <div class="timeline-item">
                                    <div class="fw-semibold">Αριθμός απόντων μαθητών:</div>
                                    <div class="h4 text-primary mb-0">{{ $todayReport->absent_count }}</div>
                                </div>
                                
                                @if($todayReport->comments)
                                    <div class="mt-4">
                                        <div class="fw-semibold">Σχόλια:</div>
                                        <div class="card mt-2">
                                            <div class="card-body bg-light">
                                                {{ $todayReport->comments }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <small class="text-muted d-block mt-3">
                                    <i class="bi bi-clock me-1"></i>
                                    Υποβλήθηκε στις: {{ $todayReport->submitted_at->format('H:i') }}
                                </small>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Προθεσμία:</strong> κάθε πρωί στις 10:00 π.μ.
                        </div>

                        <form action="{{ route('daily_absence_reports.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="report_date" value="{{ now()->format('Y-m-d') }}">

                            <div class="mb-4">
                                <label for="absent_count" class="form-label fw-semibold">
                                    <i class="bi bi-person-x me-2"></i>
                                    Αριθμός Απόντων Μαθητών
                                </label>
                                <input 
                                    type="number" 
                                    name="absent_count" 
                                    id="absent_count"
                                    value="{{ old('absent_count', $todayReport->absent_count ?? '') }}"
                                    min="0"
                                    required
                                    class="form-control form-control-lg @error('absent_count') is-invalid @enderror"
                                    placeholder="Εισάγετε αριθμό"
                                >
                                @error('absent_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Comments Field -->
                            <div class="mb-4">
                                <label for="comments" class="form-label fw-semibold">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    Σχόλια (Προαιρετικά)
                                </label>
                                <textarea 
                                    name="comments" 
                                    id="comments"
                                    rows="4"
                                    class="form-control @error('comments') is-invalid @enderror"
                                    placeholder="Προσθέστε σχόλια για την απουσία (π.χ. αιτιολογία, σχολικές εκδρομές, κλπ.)"
                                >{{ old('comments', $todayReport->comments ?? '') }}</textarea>
                                @error('comments')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Μπορείτε να προσθέσετε σχόλια για περισσότερες πληροφορίες σχετικά με τις απουσίες.
                                </small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>
                                    {{ $todayReport ? 'Ενημέρωση Αναφοράς' : 'Υποβολή Αναφοράς' }}
                                </button>

                                @if($todayReport)
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        Τελευταία ενημέρωση: {{ $todayReport->submitted_at->format('H:i') }}
                                    </small>
                                @endif
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-lg-6">
            <div class="card file-card border-info h-100">
                <div class="card-header bg-info bg-opacity-10 border-info">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Οδηγίες
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Υποβάλετε τον αριθμό των απόντων μαθητών λόγω εποχικών λοιμώξεων κάθε μέρα μέχρι τις <strong>10:00 π.μ.</strong>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Μπορείτε να <strong>επεξεργαστείτε</strong> την υποβολή σας όσες φορές χρειάζεται πριν την προθεσμία
                        </li>
                    
                        <li class="mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Μετά την προθεσμία, δεν είναι δυνατή η τροποποίηση ή η υποβολή νέας αναφοράς
                        </li>
                     
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Previous Reports -->
    <div class="row mt-4">
        <div class="col">
            <div class="card file-card">
                <div class="card-header bg-success bg-opacity-10">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Προηγούμενες Υποβολές
                    </h5>
                </div>
                <div class="card-body">
                    @if($previousReports->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-4 d-block mb-3"></i>
                            <p class="mb-0">Δεν υπάρχουν προηγούμενες υποβολές</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <i class="bi bi-calendar me-2"></i>
                                            Ημερομηνία
                                        </th>
                                        <th class="text-center">
                                            <i class="bi bi-person-x me-2"></i>
                                            Απόντες Μαθητές
                                        </th>
                                        <th>
                                            <i class="bi bi-chat-left-text me-2"></i>
                                            Σχόλια
                                        </th>
                                        <th class="text-center">
                                            <i class="bi bi-clock me-2"></i>
                                            Ώρα Υποβολής
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previousReports as $report)
                                        <tr>
                                            <td>
                                                <strong>{{ $report->report_date->format('d/m/Y') }}</strong>
                                                <small class="text-muted d-block">
                                                    {{ $report->report_date->locale('el')->translatedFormat('l') }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                                    {{ $report->absent_count }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($report->comments)
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                            data-bs-toggle="popover" 
                                                            data-bs-title="Σχόλια" 
                                                            data-bs-content="{{ $report->comments }}"
                                                            data-bs-container="body">
                                                        <i class="bi bi-chat-left-text me-1"></i> Προβολή
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center text-muted">
                                                {{ $report->submitted_at->format('H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    <!-- Bootstrap JS Bundle with Popper (essential for popovers) -->
    // Initialize popovers for comments
    document.addEventListener('DOMContentLoaded', function() {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    });
</script>
@endpush
</x-layout>