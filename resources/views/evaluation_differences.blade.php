<x-layout>

    @push('links')
        <link href="DataTables-1.13.4/css/dataTables.bootstrap5.css" rel="stylesheet"/>
        <link href="Responsive-2.4.1/css/responsive.bootstrap5.css" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="DataTables-1.13.4/js/jquery.dataTables.js"></script>
        <script src="DataTables-1.13.4/js/dataTables.bootstrap5.js"></script>
        <script src="Responsive-2.4.1/js/dataTables.responsive.js"></script>
        <script src="Responsive-2.4.1/js/responsive.bootstrap5.js"></script>
        <script src="datatable_init.js"></script>
    @endpush
    @push('title')
        <title>Αξιολόγηση</title>
    @endpush

<div class="container-fluid">
    <div>
        <p class="h4">Αξιολόγηση</p>
        <p>
            <form action="evaluation">
                <button type="submit" class="btn btn-primary">Εμφάνιση ανά πεδίο αξιολόγησης</button>
            </form>
        </p>
    </div>
    <div>
        <p class="h4">Εμφάνιση ανα εκπαιδευτικό</p>
    </div>
    @php
    $teachersAfms = DB::table('evaluation_a1')->select('teacher_afm')
    ->union(DB::table('evaluation_a2')->select('teacher_afm'))
    ->union(DB::table('evaluation_b')->select('teacher_afm'))
    ->pluck('teacher_afm');
    
    @endphp
    

    <div class="table-responsive py-2" style="align-self:flex-start">
        <table  id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
        <thead>
            <tr>
                
                <th id="search">Ημνία Διορισμού</th>
                <th id="search">Κλάδος</th>
                <th id="search">Όνομα</th>
                <th id="search">Επώνυμο</th>
                <th id="search">ΑΦΜ</th>
                <th id="search">Κινητό</th>
                <th id="search">e-mail</th>
                <th id="search">Σχολείο</th>
                <th id="search">e-mail Σχολείου</th>
                <th id="search">Αξιολογητής Α1</th>
                <th id="search">Α1</th>                
                <th id="search">Α2</th>
                <th id="search">Β</th>
                
                <th id="">ΑΜ</th>
            </tr>
        </thead>
        <tbody>
        {{-- Αξιολόγηση Α1 --}}
            @php
            $evaluation_a1 = DB::table('teachers')
         ->join('evaluation_a1', 'teachers.afm', '=', 'evaluation_a1.teacher_afm')
        //  ->where('teachers.organiki_type', '=', 'App\Models\School')
         ->select('teachers.*', 'evaluation_a1.*')
         ->get();
         @endphp
         
         @foreach($teachersAfms as $afm)
                @php
                    $teacher = App\Models\Teacher::where('afm', $afm)->first();
                    if($teacher == null){
                    //  || 
                    // !in_array($teacher->klados, ['ΠΕ60', 'ΠΕ70', 'ΠΕ71']) || 
                    // !in_array(substr($teacher->appointment_date, 0, 4), ['2020', '2021']) ){
                    // || str_contains($teacher->organiki->name, 'ΔΙΕΥΘΥΝΣΗ')){
                        continue;
                    }   
                @endphp
                 <tr>
                    <td>{{$teacher->appointment_date}}</td>
                    <td> {{$teacher->klados}} </td>
                    <td>{{$teacher->name}} </td>
                     <td> {{$teacher->surname}} </td>
                     <td> {{$teacher->afm}} </td>
                     <td> {{$teacher->telephone}} </td>
                     <td> {{$teacher->mail}} </td>
                     @if($teacher->ypiretisi == null)
                        <td> Πειραματικό Σχολείο Παν/μίου Πατρών </td>
                        <td> Πειραματικό Σχολείο Παν/μίου Πατρών </td>
                     @else
                        <td> {{$teacher->ypiretisi->name}} </td>
                        <td> {{$teacher->ypiretisi->mail}} </td>
                     @endif
                     
                     @php
                        $a1 = DB::table('evaluation_a1')->where('teacher_afm', $teacher->afm)->first();
                    @endphp
                    @if($a1)
                        
                        @php
                        $evaluator_a1 = App\Models\Teacher::where('afm', $a1->evaluator_afm)->first();
                        if($a1->self_evaluation_date){
                            if($a1->date_completed_timestamp){
                                $state = "Ανάρτηση Τελικής Έκθεσης";
                            } else {
                                $state = "Ανάρτηση Αυτοαξιολόγησης";
                            }
                        } else {
                            if($a1->date_completed_timestamp){
                                $state = "Ανάρτηση Μονομερούς Πρακτικού";
                            } else {
                                $state = "Εκκρεμής";
                            }
                        }
                        @endphp
                        <td>@if($evaluator_a1) {{$evaluator_a1->surname}} {{$evaluator_a1->name}} @else {{$a1->evaluator_afm_comments}} @endif</td>
                        <td> {{$state}} </td>
                    @else
                        <td>-</td>
                        <td>-</td>
                    @endif  
                    @php
                        $a2 = DB::table('evaluation_a2')->where('teacher_afm', $teacher->afm)->first();
                        if($a2){
                            $evaluator_a2 = App\Models\Teacher::where('afm', $a2->evaluator_afm)->first();
                        }
                    @endphp
                    @if($a2)
                        @php
                        if($a2->self_evaluation_date){
                            if($a2->date_completed_timestamp){
                                $state = "Ανάρτηση Τελικής Έκθεσης";
                            } else {
                                $state = "Ανάρτηση Αυτοαξιολόγησης";
                            }
                        } else {
                            if($a2->date_completed_timestamp){
                                $state = "Ανάρτηση Μονομερούς Πρακτικού";
                            } else {
                                $state = "Εκκρεμής";
                            }
                        }
                        @endphp
                        {{-- <td> @if($evaluator_a2){{$evaluator_a2->surname}} {{$evaluator_a2->name}} @else {{$a2->evaluator_afm_comments}} @endif {{$state}} </td> --}}
                        <td> {{$state}} </td>
                    @else
                        <td>-</td>
                    @endif 
                    @php
                        $b = DB::table('evaluation_b')->where('teacher_afm', $teacher->afm)->first();
                        if($b){
                            $evaluator_b = App\Models\Teacher::where('afm', $b->evaluator_1_afm)->first();
                        }   
                    @endphp
                    @if($b)
                        @php
                        if($b->self_evaluation_date){
                            if($b->date_completed_timestamp){
                                $state = "Ανάρτηση Τελικής Έκθεσης";
                            } else {
                                $state = "Ανάρτηση Αυτοαξιολόγησης";
                            }
                        } else {
                            if($b->date_completed_timestamp){
                                $state = "Ανάρτηση Μονομερούς Πρακτικού";
                            } else {
                                $state = "Εκκρεμής";
                            }
                        }
                        @endphp
                        {{-- <td>@if($evaluator_b){{$evaluator_b->surname}} {{$evaluator_b->name}} @endif {{$state}} </td> --}}
                        <td> {{$state}} </td>
                    @else
                        <td>-</td>
                    @endif     
                    <td> {{$teacher->am}} </td>
                    </tr>
        @endforeach
        </tbody>
        </table>
    </div> <!-- table responsive closure -->
</div>
</x-layout>