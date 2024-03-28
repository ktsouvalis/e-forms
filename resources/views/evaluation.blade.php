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
    <div class="table-responsive py-2" style="align-self:flex-start">
        <table  id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th id="search">Α1, Α2, Β</th>
                <th id="search">Όνομα</th>
                <th id="search">Επώνυμο</th>
                <th id="search">ΑΦΜ</th>
                {{-- <th id="search">Σχολείο</th>                
                <th id="search">mail Σχολείου</th> --}}
                <th id="search">Αξ. 1 -Όνομα</th>
                <th id="search">Αξ. 1 - Επώνυμο</th>
                <th id="search">Αξ. 1 - ΑΦΜ</th>
                {{-- <th id="search">Σχολείο</th>
                <th id="search">mail Σχολείου</th> --}}
                <th id="search">Αξ. 2 - Όνομα</th>
                <th id="search">Αξ. 2 - Επώνυμο</th>
                <th id="search">Αξ. 2 - ΑΦΜ</th>
                <th id="search">Κατηγορία</th>
                <th id="">Ημερομηνία ολοκλήρωσης</th>
                <th id="">ΑΜ</th>
                <th id="">Κλάδος</th>
            </tr>
        </thead>
        <tbody>
        {{-- Αξιολόγηση Α1 --}}
            @php
            $evaluation_a1 = DB::table('teachers')
         ->join('evaluation_a1', 'teachers.afm', '=', 'evaluation_a1.teacher_afm')
         ->select('teachers.*', 'evaluation_a1.*')
         ->get();
         @endphp
         
         @foreach($evaluation_a1 as $teacher)
                 <tr>
                     <td> Α1 </td>
                     <td>{{$teacher->name}}</td>
                     <td> {{$teacher->surname}}</td>
                     <td> {{$teacher->afm}}</td>
                     @php
                         $evaluator_1 = App\Models\Teacher::where('afm', $teacher->evaluator_afm)->first();
                     @endphp
                     @if($evaluator_1)
                         <td> {{$evaluator_1->name}} </td>
                         <td> {{$evaluator_1->surname}} </td>
                         <td> {{$evaluator_1->afm}} </td>
                     @else
                         <td> - </td>
                         <td> - </td>
                         <td> - </td>
                     @endif
                         <td> - </td>
                         <td> - </td>
                         <td> - </td>
                     <td> {{$teacher->category}} </td> 
                     @if($teacher->date_completed == null)
                         <td> Εκκρεμότητα </td>
                     @else
                         <td> {{$teacher->date_completed}} </td>
                     @endif
                     <td> {{$teacher->am}} </td>
                     <td> {{$teacher->klados}} </td>
                 </tr>
     @endforeach
     {{-- Αξιολόγηση Α2 --}}
            @php
            $evaluation_a2 = DB::table('teachers')
         ->join('evaluation_a2', 'teachers.afm', '=', 'evaluation_a2.teacher_afm')
         ->select('teachers.*', 'evaluation_a2.*')
         ->get();
         @endphp
         
         @foreach($evaluation_a2 as $teacher)
                 <tr>
                     <td> Α2 </td> 
                     <td>{{$teacher->name}}</td>
                     <td> {{$teacher->surname}}</td>
                     <td> {{$teacher->afm}}</td>
                     @php
                         $evaluator_1 = App\Models\Teacher::where('afm', $teacher->evaluator_afm)->first();
                     @endphp
                     @if($evaluator_1)
                         <td> {{$evaluator_1->name}} </td>
                         <td> {{$evaluator_1->surname}} </td>
                         <td> {{$evaluator_1->afm}} </td>
                     @else
                         <td> - </td>
                         <td> - </td>
                         <td> - </td>
                     @endif
                    <td> - </td>
                    <td> - </td>
                    <td> - </td>
                    <td> {{$teacher->category}} </td> 
                     @if($teacher->date_completed == null)
                         <td> Εκκρεμότητα </td>
                     @else
                         <td> {{$teacher->date_completed}} </td>
                     @endif
                     <td> {{$teacher->am}} </td>
                     <td> {{$teacher->klados}} </td>
                 </tr>
     @endforeach
     {{-- Αξιολόγηση Β --}}
            @php
               $evaluation_b = DB::table('teachers')
            ->join('evaluation_b', 'teachers.afm', '=', 'evaluation_b.teacher_afm')
            ->select('teachers.*', 'evaluation_b.*', 'teachers.id')
            ->get();
        
            @endphp
            @foreach($evaluation_b as $teacher_db)
                @php
                $teacher = App\Models\Teacher::find($teacher_db->id);
                @endphp 
                <tr>
                    <td> Β </td> 
                    <td>{{$teacher_db->name}}</td>
                    <td> {{$teacher_db->surname}}</td>
                    <td> {{$teacher_db->afm}}</td>
                    {{-- <td>{{ $teacher->ypiretisi->name }}</td>
                    <td>{{ $teacher->ypiretisi->mail }}</td> --}}
                    @php
                        $evaluator_1 = App\Models\Teacher::where('afm', $teacher_db->evaluator_1_afm)->first();
                    @endphp
                    @if($evaluator_1)
                        <td> {{$evaluator_1->name}} </td>
                        <td> {{$evaluator_1->surname}} </td>
                        <td> {{$evaluator_1->afm}} </td>
                        @php
                            $evaluator_1_teacher = App\Models\Teacher::find($evaluator_1->id);
                        @endphp
                        {{-- <td> {{$evaluator_1_teacher->ypiretisi->name}} </td>
                        <td> {{$evaluator_1_teacher->ypiretisi->mail}} </td> --}}
                    @else
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        {{-- <td> - </td>
                        <td> - </td> --}}
                    @endif
                        @php
                            if($teacher_db->evaluator_2_afm){
                                $evaluator_2 = App\Models\Teacher::where('afm', $teacher_db->evaluator_2_afm)->first();
                            }
                            else {
                                $evaluator_2 = null;
                            }
                        @endphp
                        @if($evaluator_2)
                            <td> {{$evaluator_2->name}} </td>
                            <td> {{$evaluator_2->surname}} </td>
                            <td> {{$evaluator_2->afm}} </td> 
                        @else
                            <td> - </td>
                            <td> - </td>
                            <td> - </td>
                            
                        @endif
                        <td> {{$teacher->category}} </td> 
                        @if($teacher->date_completed == null)
                            <td> Εκκρεμότητα </td>
                        @else
                            <td> {{$teacher->date_completed}} </td>
                        @endif
                        <td> {{$teacher->am}} </td>
                        <td> {{$teacher->klados}} </td>
                    </tr>
        @endforeach
        </tbody>
        </table>
    </div> <!-- table responsive closure -->
</div>

<div>
    <nav class="navbar navbar-light bg-light">
        <form action="{{url("/admin_create_ticket")}}" method="post" enctype="multipart/form-data" class="container-fluid">
            @csrf
            <div class="input-group">
                <span class="input-group-text w-25"></span>
                <span class="input-group-text w-75"><strong>Καταχώρηση Εκπαιδευτικού</strong></span>
            </div>
                
            <div class="input-group my-2">
                <span class="input-group-text w-25 text-wrap">Εκπαιδευτικός:</span>
                <input name="teacher_afm" id="teacher_afm" type="text" class="form-control" placeholder="Επιλέξτε Εκπαιδευτικό" aria-label="Εκπαιδευτικός" aria-describedby="basic-addon2" required list="teacherOptions">
                <datalist id="teacherOptions">
                    @foreach(App\Models\Teacher::all() as $teacher)
                        <option value="{{ $teacher->afm }}">{{ $teacher->surname }} {{ $teacher->name }}, {{ $teacher->klados }} {{ $teacher->afm }}</option>
                    @endforeach
                </datalist>
            </div>
            
            <div class="input-group my-2">
                <span class="input-group-text w-25 text-wrap">Α1, Α2 ή Β:</span>
                <input name="evaluation" id="evaluation" type="text" class="form-control" placeholder="Α1, Α2 ή Β" aria-label="Α1, Α2 ή Β" aria-describedby="basic-addon2" required>
            </div>
            <div class="input-group">
                <div class="input-group justify-content-center">
                <textarea name="comments" id="comments" class="form-control"></textarea>
                </div>
            </div>
            <div class="input-group">
                <button type="submit" class="btn btn-primary m-2"><div class="fa-solid fa-headset"></div> Υποβολή</button>
            </div>
        </form>
    </nav>
</div>

</x-layout>