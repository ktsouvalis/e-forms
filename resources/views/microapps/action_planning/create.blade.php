<x-layout_school>
@php
$school = Auth::guard('school')->user();
$microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
$accepts =$microapp->accepts;
$name =$microapp->name;

$plan = $school->actionPlanning();

@endphp

@push('title')
    <title>{{ $name }}</title>
@endpush

<div class="container py-4">
    <div class="col-12 col-md-10 col-lg-8 mx-auto">
        <div class="card border-primary rounded-3 shadow-sm">
            <div class="card-header bg-info text-white text-center py-3">
                <h3><i class="bi bi-calendar-check"></i> Υποβολή Προγραμματισμού</h3>
                <p class="mb-0">
                    Υποβάλετε τον ετήσιο ή τριμηνιαίο προγραμματισμό σας[cite: 1]. 
                    <br> Μπορείτε να ανεβάσετε ή να τροποποιήσετε τα αρχεία σας διατηρώντας πλήρες ιστορικό.
                </p>
            </div>

            <div class="card-body">
                <form action="{{ route('action_planning.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Τύπος Προγραμματισμού (Ετήσιο / Τριμηνιαίο) --}}
                    <div class="mb-3">
                        <label for="planning_cycle" class="form-label fw-bold">Τύπος Προγραμματισμού</label>
                        <select name="planning_cycle" id="planning_cycle" class="form-select" required>
                            <option value="Ετήσιος" @if(optional($plan)->planning_cycle == 'Ετήσιος') selected @endif>Ετήσιος</option>
                            <option value="Τριμηνιαίος" @if(optional($plan)->planning_cycle == 'Τριμηνιαίος') selected @endif>Τριμηνιαίος</option>
                        </select>
                    </div>

                    {{-- Slot Identifier (Εμφανίζεται αν είναι Τριμηνιαίο) --}}
                    <div class="mb-3" id="slot_container">
                        <label for="slot" class="form-label fw-bold">Τρίμηνο / Περίοδος</label>
                        <select name="slot" id="slot" class="form-select">
                            <option value="">-- Επιλέξτε Περίοδο (για Τριμηνιαίο) --</option>
                            <option value="Q1">1ο Τρίμηνο</option>
                            <option value="Q2">2ο Τρίμηνο</option>
                            <option value="Q3">3ο Τρίμηνο</option>
                        </select>
                    </div>

                    {{-- File Upload --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Επιλογή Αρχείου:</label>
                        <p class="text-muted mb-2">Μπορείτε να ανεβάσετε νέο αρχείο (το παλιό αρχείο παραμένει στο ιστορικό). (.pdf, .xlsx, .docx)</p>
                        <input type="file" name="file" class="form-control" accept=".pdf,.xlsx,.docx,.jpeg,.png" required>
                    </div>

                    {{-- Submit Button --}}
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" @if($microapp->accepts == 0) disabled @endif>Υποβολή Αρχείου</button>
                    </div>
                </form>

                

</x-layout_school>