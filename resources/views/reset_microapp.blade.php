<x-layout>

@push('title') 
    <title>Reset-{{ $microapp->name }}</title>
@endpush

<div class="container py-4" style="max-width: 720px;">

    {{-- Τίτλος --}}
    <h4 class="mb-1 fw-semibold">{{ $microapp->name }}</h4>
    <p class="text-muted mb-4">Εδώ μπορείτε να αρχικοποιήσετε τη μικροεφαρμογή.</p>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('failure'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('failure') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Steps --}}
    <div class="d-flex flex-column gap-3">

        {{-- ΒΗΜΑ 1 --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px;font-weight:600;">1</div>
                <div class="flex-grow-1">
                    <div class="fw-semibold">Λήψη Backup Αρχείων</div>
                    <div class="text-muted small">Κατεβάστε όλα τα υποβληθέντα αρχεία της μικροεφαρμογής ως .zip</div>
                </div>
                <form method="POST" action="{{ route('reset.download_files', $microapp) }}" data-export>
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm text-nowrap">
                        <i class="bi bi-download me-1"></i> Λήψη .zip
                    </button>
                </form>
            </div>
        </div>

        {{-- ΒΗΜΑ 2 --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px;font-weight:600;">2</div>
                <div class="flex-grow-1">
                    <div class="fw-semibold">Λήψη Excel Βάσης Δεδομένων</div>
                    <div class="text-muted small">Εξαγωγή όλων των εγγραφών της ΒΔ σε αρχείο .xlsx (ένα sheet ανά πίνακα)</div>
                </div>
                <form method="POST" action="{{ route('reset.download_excel', $microapp) }}" data-export>
                    @csrf
                    <button type="submit" class="btn btn-outline-success btn-sm text-nowrap">
                        <i class="bi bi-file-earmark-excel me-1"></i> Λήψη .xlsx
                    </button>
                </form>
            </div>
        </div>

        {{-- ΔΙΑΧΩΡΙΣΤΗΣ --}}
        <div class="d-flex align-items-center gap-2 text-danger-emphasis small px-1">
            <i class="bi bi-exclamation-triangle-fill text-danger"></i>
            <span>Οι παρακάτω ενέργειες είναι <strong>μη αναστρέψιμες</strong>. Βεβαιωθείτε ότι έχετε κατεβάσει backup πριν συνεχίσετε.</span>
        </div>

        {{-- ΒΗΜΑ 3 --}}
        <div class="card border-0 shadow-sm border border-danger-subtle">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px;font-weight:600;">3</div>
                <div class="flex-grow-1">
                    <div class="fw-semibold">Διαγραφή Αρχείων</div>
                    <div class="text-muted small">Διαγράφει όλα τα αρχεία από τον server και δημιουργεί κενό φάκελο</div>
                </div>
                <form method="POST" action="{{ route('reset.delete_files', $microapp) }}"
                      onsubmit="return confirm('Είστε σίγουροι; Τα αρχεία θα διαγραφούν οριστικά.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm text-nowrap">
                        <i class="bi bi-trash3 me-1"></i> Διαγραφή Αρχείων
                    </button>
                </form>
            </div>
        </div>

        {{-- ΒΗΜΑ 4 --}}
        <div class="card border-0 shadow-sm border border-danger-subtle">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px;font-weight:600;">4</div>
                <div class="flex-grow-1">
                    <div class="fw-semibold">Αρχικοποίηση Βάσης Δεδομένων</div>
                    <div class="text-muted small">Διαγράφει <strong>όλες</strong> τις εγγραφές των πινάκων της μικροεφαρμογής</div>
                </div>
                <form method="POST" action="{{ route('reset.reset_db', $microapp) }}"
                      onsubmit="return confirm('ΠΡΟΣΟΧΗ: Θα διαγραφούν όλα τα δεδομένα της ΒΔ. Είστε απολύτως σίγουροι;')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm text-nowrap">
                        <i class="bi bi-database-x me-1"></i> Αρχικοποίηση ΒΔ
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>


</x-layout>