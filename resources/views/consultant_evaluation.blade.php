<x-layout_consultant>
@push('links')
    <link href="DataTables-1.13.4/css/dataTables.bootstrap5.css" rel="stylesheet"/>
    <link href="Responsive-2.4.1/css/responsive.bootstrap5.css" rel="stylesheet"/>
    <link href="customCss/tabs.css" rel="stylesheet"/>
@endpush
<style>
    .table-container {
      display: grid;
      grid-template-columns: repeat(3, 1fr); /* Three equal-width columns */
      gap: 20px; /* Space between tables */
    }
    table {
      border-collapse: collapse;
      width: 150px;
    }
    table, th, td {
      border: 1px solid black;
    }
    th, td {
      padding: 8px;
      text-align: center;
    }
  </style>
@push('scripts')
    <script src="DataTables-1.13.4/js/jquery.dataTables.js"></script>
    <script src="DataTables-1.13.4/js/dataTables.bootstrap5.js"></script>
    <script src="Responsive-2.4.1/js/dataTables.responsive.js"></script>
    <script src="Responsive-2.4.1/js/responsive.bootstrap5.js"></script>
    <script src="datatable_init.js"></script>
    <script src="tabs.js"></script>
@endpush
@push('title')
    <title>Αξιολόγηση</title>
@endpush

<div class="container-fluid">
<div>
    <p class="h4">Αξιολόγηση</p>
</div>
<div>
    <p class="h4">Εμφάνιση στοιχείων Αξιολόγησης στο πεδίο Β</p>
    <p class="h5">Παρουσιάζονται τα στοιχεία από το αρχείο που έχει ανέβει από τη Διεύθυνση στο πεδίο Β</p>
</div>
@php
$user = Auth::guard('consultant')->user();

$consultantAfm = $user->afm;

$evaluationData = App\Http\Controllers\ConsultantController::getEvaluationData($consultantAfm);
$evaluationDataArray = json_decode($evaluationData, true);
//dd($evaluationDataArray);

// $teachersAfms = DB::table('evaluation_b')
//     ->select('teacher_afm')
//     ->where(function ($query) use ($consultantAfm){
//         $query->Where('evaluator_1_afm', $consultantAfm)
//             ->orWhere('evaluator_2_afm', $consultantAfm);
//     })
//     ->pluck('teacher_afm');
@endphp

<!-- Tabs -->
<div class="tabs">
    <div class="tab active" data-tab="tab1">Υπό Αξιολόγηση</div>
    <div class="tab" data-tab="tab2">Ολοκληρωμένοι</div>
    <div class="tab" data-tab="tab3">Απόντες</div>
</div>

<!-- Tab Content -->
<div id="tab1" class="tab-content active">
    <div class="table-responsive py-2" style="align-self:flex-start">
        <table  id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th colspan="3">Πεδίο Α1 - Αξιολογούμενος</th>
               
                <th colspan="2">Πεδίο Α2</th>
                <th colspan="3">Πεδίο Β</th>
            </tr>
            <tr>
                <th id="search">Ονοματεπώνυμο</th>
                <th id="search">Αξιολογητής</th>  
                <th id="search">Κατάσταση Α1</th>              
        
                <th id="search">Αξιολογητής</th>
                <th id="search">Κατάσταση Α2</th>
              
                <th id="search">Αξιολογητής 1</th>
                <th id="search">Αξιολογητής 2</th>
                <th id="search">Κατάσταση B</th>
            </tr>
        </thead>
        <tbody>
    
        @foreach($evaluationDataArray['current'] as $current)
            <tr><td>{{$current['lastName']}} {{$current['firstName']}}</td>
                <td>{{$current['a1Evaluator']}}</td>
                <td> {{$current['a1Status']}} {{$current['a1Date']}} </td>
                <td>{{$current['a2Evaluator']}}</td>
                <td>{{$current['a2Status']}} {{$current['a1Date']}} </td>
                <td>{{$current['bEvaluator']}}</td>
                <td>{{$current['bEvaluator2']}}</td>
                <td>{{$current['bStatus']}} {{$current['bDate']}} </td>
            </tr>
        @endforeach
        </tbody>
        </table>
    </div>{{-- End of Table Responsive div  --}}
    
</div>{{-- End of tab 1 content div  --}}

<div id="tab2" class="tab-content">
    <div class="table-responsive py-2" style="align-self:flex-start">
        <table  id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th colspan="3">Πεδίο Α1 - Αξιολογούμενος</th>
               
                <th colspan="2">Πεδίο Α2</th>
                <th colspan="3">Πεδίο Β</th>
            </tr>
            <tr>
                <th id="search">Ονοματεπώνυμο</th>
                <th id="search">Αξιολογητής</th>  
                <th id="search">Κατάσταση Α1</th>              
        
                <th id="search">Αξιολογητής</th>
                <th id="search">Κατάσταση Α2</th>
              
                <th id="search">Αξιολογητής 1</th>
                <th id="search">Αξιολογητής 2</th>
                <th id="search">Κατάσταση B</th>
            </tr>
        </thead>
        <tbody>
    
            @foreach($evaluationDataArray['completed'] as $current)
                <tr><td>{{$current['lastName']}} {{$current['firstName']}}</td>
                    <td>{{$current['a1Evaluator']}}</td>
                    <td> {{$current['a1Status']}} {{$current['a1Date']}} </td>
                    <td>{{$current['a2Evaluator']}}</td>
                    <td>{{$current['a2Status']}} {{$current['a1Date']}} </td>
                    <td>{{$current['bEvaluator']}}</td>
                    <td>{{$current['bEvaluator2']}}</td>
                    <td>{{$current['bStatus']}} {{$current['bDate']}} </td>
                </tr>
            @endforeach
        </tbody>
        </table>
    </div>
</div>
<div id="tab3" class="tab-content">
    @if(count($evaluationDataArray['absent'])==0)
        Δεν υπάρχουν απόντες αξιολογούμενοι αυτή τη στιγμή.
    @endif
    <table>
    @foreach($evaluationDataArray['absent'] as $current)
        <tr><td>{{$current['lastName']}} {{$current['firstName']}} </td><td>{{$current['bStatus']}} {{$current['bDate']}} </td><td> {{$current['bEvaluator']}}</td></tr>
    @endforeach
    </table>
</div>



</x-layout_consultant>
        
           