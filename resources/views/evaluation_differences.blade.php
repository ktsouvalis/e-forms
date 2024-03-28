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
                <button type="submit" class="btn btn-primary">Αξιολόγηση</button>
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
                <th id="search">Όνομα</th>
                <th id="search">Επώνυμο</th>
                <th id="search">ΑΦΜ</th>
                <th id="search">Α1</th>                
                <th id="search">Α2</th>
                <th id="search">Β</th>
                <th id="search">Α1 Επώνυμο</th>
                <th id="search">Α1 ΑΦΜ</th>
                <th id="search">Α1 Ολοκλ</th>
                {{-- <th id="search">Σχολείο</th>
                <th id="search">mail Σχολείου</th> --}}
                <th id="search">Α2 Όνομα</th>
                <th id="search">Α2 Επώνυμο</th>
                <th id="search">Α2 ΑΦΜ</th>
                <th id="search">Α2 Ολοκλ</th>
                <th id="search">Κατηγορία</th>
                <th id="">Β Αξ1</th>
                <th id="">Β Αξ2</th>
                <th id="">Β Ολοκλ</th>
                <th id="">Κλάδος</th>
                <th id="">ΑΜ</th>
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
         
         @foreach($teachersAfms as $afm)
                @php
                    $teacher = App\Models\Teacher::where('afm', $afm)->first();
                    if($teacher == null){
                        continue;
                    }   
                @endphp
                 <tr>
                     <td>{{$teacher->name}}</td>
                     <td> {{$teacher->surname}}</td>
                     <td> {{$teacher->afm}}</td>
                     @php
                        $a1 = DB::table('evaluation_a1')->where('teacher_afm', $teacher->afm)->first();
                    @endphp
                    @if($a1)
                        <td>Α1</td>
                    @else
                        <td>-</td>
                    @endif  
                    @php
                    $a2 = DB::table('evaluation_a2')->where('teacher_afm', $teacher->afm)->first();
                    @endphp
                    @if($a2)
                        <td>Α2</td>
                    @else
                        <td>-</td>
                    @endif 
                    @php
                        $b = DB::table('evaluation_b')->where('teacher_afm', $teacher->afm)->first();
                    @endphp
                    @if($b)
                        <td>Β</td>
                    @else
                        <td>-</td>
                    @endif
                     <td> </td>
                        <td></td>
                        
                        <td></td>
                        <td></td>
                        <td></td>
                        
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>      
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