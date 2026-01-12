<x-layout>
@push('links')
    <link href="{{ asset('DataTables-1.13.4/css/dataTables.bootstrap5.css') }}" rel="stylesheet"/>
    <link href="{{ asset('Responsive-2.4.1/css/responsive.bootstrap5.css') }}" rel="stylesheet"/>
@endpush

@push('scripts')
    <script src="{{ asset('DataTables-1.13.4/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('DataTables-1.13.4/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('Responsive-2.4.1/js/dataTables.responsive.js') }}"></script>
    <script src="{{ asset('Responsive-2.4.1/js/responsive.bootstrap5.js') }}"></script>
    <script src="{{ asset('datatable_init.js') }}"></script>
@endpush
@push('title')
<title>Αναφορές Απουσιών Σχολείων</title>
@endpush
@php
    $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
    $accepts = $microapp->accepts; //fetch microapp 'accepts' field
@endphp
@include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="file-collection-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-2">Ημερήσιες Αναφορές Απουσιών</h1>
                    <p class="mb-0 opacity-75">
                        <i class="bi bi-building me-2"></i>
                        Επισκόπηση όλων των σχολείων
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <form action="{{ route('daily_absence_reports.index') }}" method="GET" class="d-inline-flex align-items-center">
                        <label for="date" class="me-2 mb-0 text-white">
                            <i class="bi bi-calendar me-1"></i>
                            Ημερομηνία:
                        </label>
                        <input 
                            type="date" 
                            name="date" 
                            id="date"
                            value="{{ $viewDate->format('Y-m-d') }}"
                            max="{{ now()->format('Y-m-d') }}"
                            class="form-control form-control-sm"
                            onchange="this.form.submit()"
                            style="max-width: 200px;"
                        >
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">Σύνολο Σχολείων</div>
                        <div class="h2 mb-0 fw-bold">{{ $totalSchools }}</div>
                    </div>
                    <div class="stats-icon icon-blue">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">Υποβολές</div>
                        <div class="h2 mb-0 fw-bold text-success">{{ $submittedCount }}</div>
                    </div>
                    <div class="stats-icon icon-green">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">Εκκρεμείς</div>
                        <div class="h2 mb-0 fw-bold text-danger">{{ $notSubmittedCount }}</div>
                    </div>
                    <div class="stats-icon icon-red">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">Σύνολο Απουσιών</div>
                        <div class="h2 mb-0 fw-bold text-warning">{{ $totalAbsent }}</div>
                    </div>
                    <div class="stats-icon icon-orange">
                        <i class="bi bi-person-x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    @php
        $percentage = $totalSchools > 0 ? round(($submittedCount / $totalSchools) * 100) : 0;
    @endphp
    <div class="row mb-4">
        <div class="col">
            <div class="card file-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold">Πρόοδος Υποβολών</span>
                        <span class="badge bg-primary">{{ $percentage }}%</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $percentage }}%;" 
                             aria-valuenow="{{ $percentage }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $submittedCount }} από {{ $totalSchools }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schools Table -->
    <div class="row">
        <div class="col">
            <div class="card file-card">
                <div class="card-header bg-primary bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-check me-2"></i>
                            Λίστα Σχολείων - {{ $viewDate->format('d/m/Y') }}
                        </h5>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>
                            Εκτύπωση
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Όνομα Σχολείου</th>
                                    <th class="text-center" style="width: 150px;">Κατάσταση</th>
                                    <th class="text-center" style="width: 150px;">Απουσίες</th>
                                    <th class="text-center" style="width: 150px;">Παρατηρήσεις</th>
                                    <th class="text-center" style="width: 150px;">Ώρα Υποβολής</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schoolsData as $index => $school)
                                    <tr class="{{ !$school['has_submitted'] ? 'table-danger' : '' }}">
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $school['name'] }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if($school['has_submitted'])
                                                <span class="status-badge status-open">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Υποβλήθηκε
                                                </span>
                                            @else
                                                <span class="status-badge status-closed">
                                                    <i class="bi bi-x-circle me-1"></i>
                                                    Εκκρεμεί
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($school['has_submitted'])
                                                <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">
                                                    {{ $school['absent_count'] }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($school['has_submitted'])
                                                <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">
                                                    {{ $school['comments'] }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($school['has_submitted'])
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($school['submitted_at'])->format('H:i') }}
                                                </small>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                            <p class="mb-0">Δεν βρέθηκαν σχολεία</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    @if($submittedCount > 0)
    <div class="row mt-4">
        <div class="col">
            <div class="card file-card border-info">
                <div class="card-header bg-info bg-opacity-10 border-info">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>
                        Σύνοψη
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border-end">
                                <div class="text-muted small mb-1">Ποσοστό Ανταπόκρισης</div>
                                <div class="h3 mb-0 text-primary">{{ $percentage }}%</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-end">
                                <div class="text-muted small mb-1">Μέσος Όρος Απουσιών</div>
                                <div class="h3 mb-0 text-warning">
                                    {{ $submittedCount > 0 ? round($totalAbsent / $submittedCount, 1) : 0 }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small mb-1">Συνολικές Απουσίες</div>
                            <div class="h3 mb-0 text-danger">{{ $totalAbsent }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Auto-refresh every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000);
</script>
@endpush

</x-layout>