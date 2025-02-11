<x-layout_teacher>

    {{-- @php print json_encode($evaluation_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); @endphp --}}
<div class="table-container contained-table">
    <table>
        <tr><th colspan=3 style="text-align:center">Πεδίο Α2</th></tr>
        <tr>
            <th>Αξιολογούμενος</th><th>ΑΦΜ</th><th>Κλάδος</th>
            <th>Αξιολογητής</th><th>Κατάσταση</th><th>Ημερομηνία</th>
            <th>Αρχείο</th>
        </tr>
        @foreach($evaluation_data as $data)
        @php
            $teacher = App\Models\Teacher::where('afm', $data['a2EvaluatorAFM'])->first();
            $teacher = $teacher ?? App\Models\Consultant::firstWhere('afm', $data['a2EvaluatorAFM']);
            if (!$teacher){
                $notice = "Ενδέχεται κάποιος/κάποιοι αξιολογούμενοι να μην εμφανίζονται καθώς δε βρέθηκαν στοιχεία του αξιολογητή.
                <br> Παρακαλούμε επικοινωνήστε με το τμήμα Πληροφορικής της ΔΙΠΕ Αχαΐας";
                continue;
            }
        @endphp
            <tr><td>{{$data['lastName']}} {{$data['firstName']}}</td><td>{{$data['afm']}}</td><td>{{$data['branch']}}</td>
                <td>{{$teacher->surname}} {{substr($teacher->name, 0, 2)}}.</td><td>{{$data['a2StatusName']}}</td><td>{{$data['a2Date']}}</td>
            <td>
                <form action="">
                    <input type="file"></td>
                </form>
            </tr>

        @endforeach
    </table>
    <table>
        <tr><th colspan=3 style="text-align:center">Πεδίο Β</th></tr>
        <tr>
            <th>Αξιολογούμενος</th><th>ΑΦΜ</th><th>Κλάδος</th>
            <th>Αξιολογητής 1</th><th>Αρχείο</th><th>Κατάσταση</th><th>Ημερομηνία</th><th>Αξιολογητής 2</th>
            
        </tr>
        @foreach($evaluation_data as $data)
        @php
            $teacher1 = App\Models\Teacher::where('afm', $data['bEvaluatorAFM'])->first();
            $teacher1 = $teacher1 ?? App\Models\Consultant::firstWhere('afm', $data['bEvaluatorAFM']);
            if (!$teacher1) continue;
            $teacher2 = App\Models\Teacher::where('afm', $data['bEvaluator2AFM'])->first();
            $teacher2 = $teacher2 ?? App\Models\Consultant::firstWhere('afm', $data['bEvaluator2AFM']);
            if (!$teacher2){
                $notice = "Ενδέχεται κάποιος/κάποιοι αξιολογούμενοι να μην εμφανίζονται καθώς δε βρέθηκαν στοιχεία του αξιολογητή.
                <br> Παρακαλούμε επικοινωνήστε με το τμήμα Πληροφορικής της ΔΙΠΕ Αχαΐας";
                continue;
            } 
        @endphp
            <tr><td>{{$data['lastName']}} {{$data['firstName']}}</td><td>{{$data['afm']}}</td><td>{{$data['branch']}}</td>
                <td>{{$teacher1->surname}} {{substr($teacher1->name, 0, 2)}}.</td>
                <td><form action="">
                    <input type="file"></td>
                </form></td>
                <td>{{$data['bStatusName']}}</td><td>{{$data['bDate']}}</td>
                <td>{{$teacher2->surname}} {{substr($teacher2->name, 0, 2)}}.</td>
                
            </tr>
    
        @endforeach
    </table>
</div>

@if($notice)
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
@endif

</x-layout_teacher>