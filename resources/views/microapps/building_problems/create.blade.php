<x-layout_school>
    @php
        $school = Auth::guard('school')->user();
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts;
        $name = $microapp->name;
        $building_problems = $school->buildingProblems;
        //dd($school, $building_problems);
    @endphp

    @push('title')
        <title>{{ $name }}</title>
    @endpush

    <div class="container py-4">
        <div class="col-12 col-md-10 col-lg-8 mx-auto">
            <div class="card border-primary rounded-3 shadow-sm">
                <div class="card-header bg-info text-white text-center py-3">
                    <h3><i class="bi bi-buildings"></i> Υποβολή Κτιριολογικών Προβλημάτων</h3>
                    <p class="mb-0">
                        Υποβάλλονται όλα τα σχετικά αρχεία προς τη Διεύθυνση Π.Ε. Αχαΐας, 
                        τυχόν παρατηρήσεις και μια συνολική αξιολόγηση της σοβαρότητας της συνολικής κατάστασης.
                    </p>
                </div>

                <div class="card-body">
                    @if($building_problems && $building_problems->protocol_nr)
                        <div class="alert alert-info w-100" role="alert">
                            Τα στοιχεία έχουν πρωτοκολληθεί στο Ηλεκτρονικό Πρωτόκολλο της Διεύθυνσης με αριθ. πρωτ. {{$building_problems->protocol_nr}} - {{$building_problems->protocol_date}}
                        </div>
                    @endif
                    <form action="{{ route('building_problems.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Comments --}}
                    <div class="mb-3">
                        <label for="comments" class="form-label fw-bold">Παρατηρήσεις</label>
                        <textarea name="comments" id="comments" class="form-control" rows="3" style="resize: none;">@if($building_problems){{ $building_problems->comments }}@endif</textarea>
                    </div>

                    {{-- Radio Buttons --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Συνολικός Βαθμός Αξιολόγησης:</label>
                        <div class="d-flex flex-wrap gap-3">
                            @php
                                $choices = ['0', '1', '2', '3', '4', '5'];
                                $selected = $building_problems ? $building_problems->severity : '';
                            @endphp

                            @foreach($choices as $index => $choice)
                                {{-- Radio Button --}}
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="severity" id="severity_{{ $index }}" value="{{ $choice }}" {{ $selected == $choice ? 'checked' : '' }}>
                                    <label class="form-check-label" for="severity_{{ $index }}">{{ $choice }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text text-muted mt-2">
                            <strong>Επεξήγηση:</strong><br>
                            0 - Δεν υπάρχουν προβλήματα<br>
                            1 - Ελαφριά προβλήματα<br>
                            2 - Μέτρια προβλήματα<br>
                            3 - Σημαντικά προβλήματα<br>
                            4 - Σοβαρά προβλήματα<br>
                            5 - Πολύ σοβαρά προβλήματα
                        </div>
                    </div>

                    {{-- File Upload --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Επιλογή Αρχείων:</label>
                        <p class="text-muted mb-2">Μπορείτε να ανεβάσετε πολλά αρχεία (.pdf, .jpeg, .png), μέχρι 10MB ανά υποβολή.</p>
                        <input type="file" name="files[]" multiple class="form-control" accept=".pdf,.jpeg,.jpg,.png,.docx,.xlsx">
                    </div>

                    {{-- Submit Button --}}
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Υποβολή</button>
                    </div>
                </form>

                    {{-- Display Submitted Files --}}
                    <div class="mt-4">
                        <p class="text-center fw-bold">Αρχεία που έχουν υποβληθεί:</p>

                        @if($building_problems && $building_problems->files_json)
                            @php
                                $fileNames = json_decode($building_problems->files_json, true);
                            @endphp
                            @foreach($fileNames as $serverFileName => $databaseFileName)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <form action="{{ route('building_problems.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName]) }}" method="get" class="me-2">
                                        <button type="submit" class="btn btn-info">{{ $databaseFileName }}</button>
                                    </form>
                                    <form action="{{ route('building_problems.delete_file', ['buildingProblems' => $building_problems, 'serverFileName' => $serverFileName]) }}" method="get">
                                        <button type="submit" class="btn btn-danger" @if($microapp->accepts == 0) disabled @endif>Διαγραφή</button>
                                    </form>
                                </div>
                            @endforeach
                        @else
                            <p class="text-center text-muted">Δεν έχει υποβληθεί κάποιο αρχείο</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout_school>
