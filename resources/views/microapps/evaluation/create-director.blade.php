<x-layout_teacher>
    @php
        //print_r($evaluation_data);
    @endphp
<h3 class="m-4"> Στοιχεία Αξιολόγησης </h3>
<style>
    .collapse {
        opacity: 0.5;
        transition: opacity 0.3s ease-in-out;
    }
    .collapse.show {
        opacity: 1;
    }
</style>
<div class="mb-3">
    <button class="btn btn-primary collapse_button" id="button-a2" data-bs-toggle="collapse" data-bs-target=".column-a2" button-bs-target="#button-a2">Απόκρυψη Α2</button>
    <button class="btn btn-primary collapse_button" id="button-b" data-bs-toggle="collapse" data-bs-target=".column-b" button-bs-target="#button-b">Απόκρυψη Β</button>
</div>
{{-- @php print json_encode($evaluation_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); @endphp --}}
{{-- <div class="container mt-5"> --}}
    {{-- A2 START --}}
    <h3 class="mb-4 text-secondary"></h3>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover shadow-sm">
            <thead class="table-dark">
            <tr>
                <th colspan="3" class="text-center">Στοιχεία Εκπαιδευτικού</th>
                <th colspan="4" class="column-a2 collapse show text-center">Πεδίο Α2</th>
                <th colspan="5" class="column-b collapse show text-center">Πεδίο Β</th>
                <th colspan="4">Λοιπά Στοιχεία</th>
            </tr>
            <tr>
                <th>Αξιολογούμενος</th><th>Κλάδος</th><th>Σχολείο</th> {{-- Personal data --}}
                <th class="column-a2 collapse show">A2 Αξιολογητής</th><th class="column-a2 collapse show">Κατάσταση</th><th class="column-a2 collapse show">Ημερομηνία</th><th class="column-a2 collapse show">Αρχείο</th> {{-- A2 Field --}}
                <th class="column-b collapse show">B Αξιολογητής 1</th><th class="column-b collapse show">B Αξιολογητής 2</th><th class="column-b collapse show">Κατάσταση</th><th class="column-b collapse show">Ημερομηνία</th><th class="column-b collapse show">Αρχείο</th> {{-- Β Field --}}
                <th>ΑΦΜ</th>
                <th>Τηλέφωνο</th>
                <th>email</th>
                <th>Ημ/νία Διορισμού</th>
            </tr>
            </thead>
            <tbody>
                @php
                    function getEvaluator($afm) {
                        if (!$afm) return null;
                        return App\Models\Teacher::where('afm', $afm)->first() 
                            ?? App\Models\Consultant::firstWhere('afm', $afm);
                    }
                @endphp

                @foreach($evaluation_data as $data)
                    @php
                        $teacher = getEvaluator($data['AFM']);
                        $A2_evaluator = getEvaluator($data['A2EvaluatorAFM']);
                        $B_evaluator = getEvaluator($data['BEvaluatorAFM']);
                        $B_evaluator2 = getEvaluator($data['BEvaluator2AFM']);
                    @endphp
                    <tr>
                        <td>{{ $data['LastName'] }} {{ $data['FirstName'] }}</td>
                        <td>{{ $data['Branch'] }}</td>
                        <td>{{ $teacher->ypiretisi->name }}</td>

                    {{-- A2 Field --}}
                        <td class="column-a2 collapse show">
                            {{ $A2_evaluator ? $A2_evaluator->surname . ' ' . substr($A2_evaluator->name, 0, 2) . '.' : '-' }}
                        </td>
                        @if($data['A2StatusName'] == "" || $data['A2StatusName'] == "Απεργία/Αποχή")
                            <form action="{{ route('evaluation.upload_file', ['whoIs'=>'isDirector']) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="EmployeeAfm" value="{{ $data['AFM'] }}">
                                <input type="hidden" name="Stage" value="Α2">
                                <input type="hidden" name="EvaluatorAfm" value="{{ $data['A2EvaluatorAFM'] }}">
                                <input type="hidden" name="Status" value="{{ old('A2StatusName', '') }}">
                                <td class="column-a2 collapse show">
                                    <div class="form-group">
                                        <label for="A2StatusName">Αξιολόγηση:</label>
                                        <select id="A2StatusName" name="A2StatusName" required>
                                            <option value="" disabled selected>-- Επιλέξτε --</option>
                                            <option value="Εξαιρετικός" {{ $data['A2StatusName'] ==  "Εξαιρετικός"?"selected":""}}>Εξαιρετικός</option>
                                            <option value="Πολύ καλός" {{ $data['A2StatusName'] ==  "Πολύ καλός"?"selected":""}}>Πολύ καλός</option>
                                            <option value="Ικανοποιητικός" {{ $data['A2StatusName'] ==  "Ικανοποιητικός"?"selected":""}}>Ικανοποιητικός</option>
                                            <option value="Επαρκής" {{ $data['A2StatusName'] ==  "Επαρκής"?"selected":""}}>Επαρκής</option>
                                        </select>
                                    </div>            
                                </td>
                        @else
                            <td class="column-a2 collapse show">
                                <div class="form-group">
                                    <select id="A2StatusName" name="A2StatusName" required>
                                        <option value="" disabled selected>-- Επιλέξτε --</option>
                                        <option value="Εξαιρετικός" {{ $data['A2StatusName'] ==  "Εξαιρετικός"?"selected":""}} disabled>Εξαιρετικός</option>
                                        <option value="Πολύ καλός" {{ $data['A2StatusName'] ==  "Πολύ καλός"?"selected":""}} disabled>Πολύ καλός</option>
                                        <option value="Ικανοποιητικός" {{ $data['A2StatusName'] ==  "Ικανοποιητικός"?"selected":""}} disabled>Ικανοποιητικός</option>
                                        <option value="Επαρκής" {{ $data['A2StatusName'] ==  "Επαρκής"?"selected":""}} disabled>Επαρκής</option>
                                    </select>
                                </div>            
                            </td>
                        @endif
                        <td class="column-a2 collapse show">{{ $data['A2Date'] ?? 'Η ημερομηνία ενημερώνεται αυτόματα από την πλατφόρμα.' }}</td>
                        <td class="column-a2 collapse show">
                        @if($data['A2StatusName'] == "" || $data['A2StatusName'] == "Απεργία/Αποχή")
                                <input type="file" name="file">
                                <button type="submit" class="btn btn-primary">Υποβολή</button>
                            </form>
                        @else
                            @php
                            $filename = $data['A2AttachmentFileName'];
                            @endphp
                            @if($filename)
                                <form action="{{route('evaluation.download_file', ['filename'=>$filename ]) }}" method="get">
                                    <button type="submit" class="btn btn-primary">{{ $data['A2AttachmentFileName'] }}</button>
                                </form>
                            @endif
                    
                        @endif
                        </td>

                    {{-- B Field --}}
                        <td class="column-b collapse show">
                            {{ $B_evaluator ? $B_evaluator->surname . ' ' . substr($B_evaluator->name, 0, 2) . '.' : '-' }}
                        </td>
                        <td class="column-b collapse show">
                            {{ $B_evaluator2 ? $B_evaluator2->surname . ' ' . substr($B_evaluator2->name, 0, 2) . '.' : '-' }}
                        </td>
                        <td class="column-b collapse show">{{ $B_evaluator ? $data['BStatusName'] : '-' }}</td>
                        <td class="column-b collapse show">{{ $B_evaluator ? $data['BDate'] : '-' }}</td>
                        <td class="column-b collapse show">
                            @if($data['A2StatusName'] == "" || $data['BStatusName'] == "Απεργία/Αποχή")
                                <form action="">
                                    <input type="file">
                                </form>
                            @endif
                        </td>
                        <td>{{ $data['AFM'] }}</td>
                        <td>{{ $teacher->telephone }}</td>
                        <td>{{ $teacher->mail }}</td>
                        <td>{{ $teacher->appointment_date }}</td>
                    </tr>
                @endforeach
            
            </tbody>
        </table>
    </div> {{-- Table closure --}}

{{-- @if($notice)
<div id="toast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1070">
<div class="toast show bg-success bg-opacity-75 text-white" role="alert">
    <div class="toast-header bg-success bg-opacity-75 text-white border-0">
        <strong class="me-auto">Προσοχή!</strong>
        <button type="button" class="btn-close btn-close-white" onclick="closeToast()" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        {{ $notice }}
    </div>
</div>
</div>

<script>
function closeToast() {
    document.getElementById('toast').remove();
}

// Auto hide after 3 seconds
setTimeout(() => {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.remove();
    }
}, 10000);
</script>
@endif --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".collapse_button").forEach(button => {
        let targetSelector = button.getAttribute("data-bs-target"); // Get target class selector
        let collapsibleElements = document.querySelectorAll(targetSelector); // Get associated elements

        function updateButton() {
            let allCollapsed = Array.from(collapsibleElements).every(el => !el.classList.contains("show"));

            if (allCollapsed) {
                button.classList.remove("btn-primary");
                button.classList.add("btn-danger");
                button.textContent = button.textContent.replace("Απόκρυψη", "Εμφάνιση");
            } else {
                button.classList.remove("btn-danger");
                button.classList.add("btn-primary");
                button.textContent = button.textContent.replace("Εμφάνιση", "Απόκρυψη");
            }
        }

        collapsibleElements.forEach(element => {
            element.addEventListener("shown.bs.collapse", updateButton);
            element.addEventListener("hidden.bs.collapse", updateButton);
        });
    });
});
</script>
</x-layout_teacher>