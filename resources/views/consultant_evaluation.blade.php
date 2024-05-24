<x-layout_consultant>
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
</div>
<div>
    <p class="h4">Εμφάνιση στοιχείων Αξιολόγησης στο πεδίο Β</p>
    <p class="h5">Παρουσιάζονται τα στοιχεία από το αρχείο που έχει ανέβει από τη Διεύθυνση στο πεδίο Β</p>
</div>
@php
$user = Auth::guard('consultant')->user();

$consultantAfm = $user->afm;

$teachersAfms = DB::table('evaluation_b')
    ->select('teacher_afm')
    ->where(function ($query) use ($consultantAfm){
        $query->Where('evaluator_1_afm', $consultantAfm)
            ->orWhere('evaluator_2_afm', $consultantAfm);
    })
    ->pluck('teacher_afm');
@endphp


<div class="table-responsive py-2" style="align-self:flex-start">
    <table  id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th colspan="4">Αξιολογούμενος</th>
           <th>Ημ/νία Διορισμού</th>
            {{-- <th id="search">Σχολείο</th>                
            <th id="search">mail Σχολείου</th> --}}
            <th colspan="3">Αξιολογητής 1</th>
            <th colspan="3">Αξιολογητής 2</th>
            
            <th id="">ΑΜ</th>
            <th id="">Κλάδος</th>
        </tr>
        <tr>
            <th id="search">Επώνυμο</th>
            <th id="search">Όνομα</th>
            <th id="search">ΑΦΜ</th>
            <th id="search">Σχολείο</th>                
            <th></th>
            <th id="search">Αξ. 1 - Επώνυμο</th>
            <th id="search">Αξ. 1 -Όνομα</th>
          
            <th id="search">Αξ. 1 - ΑΦΜ</th>
            {{-- <th id="search">Σχολείο</th>
            <th id="search">mail Σχολείου</th> --}}
            
            <th id="search">Αξ. 2 - Επώνυμο</th>
            <th id="search">Αξ. 2 - Όνομα</th>
            <th id="search">Αξ. 2 - ΑΦΜ</th>
            <th id="">ΑΜ</th>
            <th id="">Κλάδος</th>
        </tr>
    </thead>
    <tbody>
     @foreach($teachersAfms as $afm)
         @php
         $teacher = App\Models\Teacher::where('afm', $afm)->first();
         @endphp
         @if($teacher) 
         <tr>
             <td> {{$teacher->surname}}</td>
             <td>{{$teacher->name}}</td>
             
             <td> {{$teacher->afm}}</td>
         @if($teacher->ypiretisi_id!=null)
             <td>{{$teacher->ypiretisi->name}}</td>  
         @else
             <td>-</td>
         @endif
         @if($teacher->appointment_date != null)
            <td>{{ date('d/m/Y', strtotime($teacher->appointment_date)) }}</td>  
         @else
            <td>-</td>
        @endif
             {{-- <td>{{ $teacher->ypiretisi->name }}</td>
             <td>{{ $teacher->ypiretisi->mail }}</td> --}}
             @php
                $evaluator_1_afm = DB::table('evaluation_b')->select('evaluator_1_afm')->where('teacher_afm', $teacher->afm)->first();
                $evaluator_1 = App\Models\Teacher::where('afm', $evaluator_1_afm->evaluator_1_afm)->first();
             @endphp
             @if($evaluator_1)
                <td> {{$evaluator_1->surname}} </td>  
                <td> {{$evaluator_1->name}} </td>
                 
                 <td> {{$evaluator_1->afm}} </td1>
             @else
                 <td> - </td>
                 <td> - </td>
                 <td> - </td>
                
             @endif
                 @php
                  $evaluator_2_afm = DB::table('evaluation_b')->select('evaluator_2_afm')->where('teacher_afm', $teacher->afm)->first();
                  //dd($evaluator_2_afm->evaluator_2_afm);
                    if($evaluator_2_afm){
                        $evaluator_2 = App\Models\Teacher::where('afm', $evaluator_2_afm->evaluator_2_afm)->first();
                    }
                    else {
                        $evaluator_2 = null;
                    }
                    
                 @endphp
                 @if($evaluator_2)
                     <td> {{$evaluator_2->surname}} </td>
                     <td> {{$evaluator_2->name}} </td>
                     
                     <td> {{$evaluator_2->afm}} </td> 
                 @else
                     <td> - </td>
                     <td> - </td>
                     <td> - </td>
                     
                 @endif
                 <td> {{$teacher->am}} </td>
                 <td> {{$teacher->klados}} </td>
             </tr>
             @endif
 @endforeach
    </tbody>
    </table>
</div> <!-- table responsive closure -->
</div>
</x-layout_consultant>
        
           