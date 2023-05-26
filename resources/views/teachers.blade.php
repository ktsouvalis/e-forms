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
    @endpush
    @push('title')
        <title>Εκπαιδευτικοί</title>
    @endpush
    @php
        $all_teachers = App\Models\Teacher::all();
    @endphp
<body>
<div class="container">
    <!--tabs-->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link @if(!session()->has('active_tab'))  {{'active'}} @endif" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="true">Αναζήτηση Εκπαιδευτικού </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link @if(session()->has('active_tab'))  @if(session('active_tab')=='import') {{'active'}} @endif @endif" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="tab2" aria-selected="false">Μαζική Εισαγωγή Εκπαιδευτικών</button>
        </li>
    </ul>
    <!--tab content-->
    <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade @if(!session()->has('active_tab')) {{'show active'}}  @endif" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
            <!-- 1st tab's content-->
            <div class="table-responsive">
                <table  id="dataTable" class=" table table-sm table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th id="search">AΜ</th>
                            <th id="search">ΑΦΜ</th>
                            <th id="search">Επώνυμο</th>
                            <th id="search">Όνομα</th>
                            <th id="search">Πατρώνυμο</th>
                            <th id="search">Μητρώνυμο</th>
                            <th id="search">Κλάδος</th>
                            <th id="search">email</th>
                            <th id="search">email ΠΣΔ</th>
                            <th id="search">Τηλέφωνο</th>
                            <th id="search">Σχέση Εργασίας</th>
                            <th id="search">Οργανική</th>
                            {{-- <th id="search">Υπηρέτηση</th> --}}
                            <th id="search">Οργανική στην Ειδική Αγωγή</th>
                            <th id="search">link</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($all_teachers as $teacher)
                        <tr>  
                            <td>{{$teacher->am}}</td>
                            <td>{{$teacher->afm}}</td>
                            <td>{{$teacher->surname}}</td>
                            <td>{{$teacher->name}}</td>
                            <td>{{$teacher->fname}}</td>
                            <td>{{$teacher->mname}}</td>
                            <td>{{$teacher->klados}}</td>
                            <td>{{$teacher->email}}</td>
                            <td>{{$teacher->sch_mail}}</td>
                            <td>{{$teacher->telephone}}</td>
                            <td>{{$teacher->sxesi_ergasias->name}}</td>
                            <td>{{$teacher->organiki->name}}</td>
                            {{-- <td>{{$teacher->ypiretisi->name}}</td> --}}
                            @if($teacher->org_eae)
                                <td>ΝΑΙ</td>
                            @else
                                <td> </td>
                            @endif
                            <td>{{$teacher->md5}}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>

        
            <div class="tab-pane fade @if(session()->has('active_tab')) @if(session('active_tab')=='import') {{'show active'}} @endif @endif" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
            @if(!session()->has('asks_to'))
            <nav class="navbar navbar-light bg-light">
                <form action="{{url('/upload_teacher_template')}}" method="post" class="container-fluid" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="import_teachers_organiki" >    
                    <button type="submit" class="btn bi bi-filetype-xlsx btn-primary"> Αποστολή αρχείου</button>
                </form>
            </nav>
            @else
            <div style="p-3 mb-2 bg-info text-dark">
                Διαβάστηκαν τα ακόλουθα λάθη από το αρχείο:
            </div>
            
            <table class="table table-striped table-hover table-light">
                <tr>
                    <th id="search">AΜ</th>
                    <th id="search">ΑΦΜ</th>
                    <th id="search">Επώνυμο</th>
                    <th id="search">Όνομα</th>
                    <th id="search">Πατρώνυμο</th>
                    <th id="search">Μητρώνυμο</th>
                    <th id="search">Κλάδος</th>
                    <th id="search">email</th>
                    <th id="search">email ΠΣΔ</th>
                    <th id="search">Τηλέφωνο</th>
                    <th id="search">Σχέση Εργασίας</th>
                    <th id="search">Οργανική</th>
                    {{-- <th id="search">Υπηρέτηση</th> --}}
                    <th id="search">Οργανική στην Ειδική Αγωγή</th>
                </tr>
                @php
                    $row = 2;
                @endphp
                @foreach(session('teachers_array') as $teacher)
                    {{-- @if(!null==session("errors_array[$row]")) --}}
                    <tr>  
                        <td>{{$teacher['am']}}</td>
                        <td>{{$teacher['afm']}}</td>
                        <td>{{$teacher['surname']}}</td>
                        <td>{{$teacher['name']}}</td>
                        <td>{{$teacher['fname']}}</td>
                        <td>{{$teacher['mname']}}</td>
                        <td>{{$teacher['klados']}}</td>
                        <td>{{$teacher['mail']}}</td>
                        <td>{{$teacher['sch_mail']}}</td>
                        <td>{{$teacher['telephone']}}</td>

                        @php
                            $sxesi = $teacher['sxesi_ergasias']!="Κενό πεδίο" ? App\Models\SxesiErgasias::find($teacher['sxesi_ergasias'])->name : $teacher['sxesi_ergasias'] ;
                        @endphp
                        <td @if($sxesi=="Κενό πεδίο") style='color: red' @endif>{{$sxesi}}</td>

                        @isset($teacher['organiki_type'])
                            @php
                                $organiki = $teacher['organiki_type']::find($teacher['organiki']);   
                            @endphp
                            <td >{{$organiki->name}}</td>
                        @else
                            <td @if($teacher['organiki']=="Άγνωστος κωδικός οργανικής") style='color: red' @endif> {{$teacher['organiki']}} </td>
                        @endisset
                        
                        {{-- <td>{{$teacher['ypiretisi']}}</td> --}}
                        <td>{{$teacher['org_eae']}}</td>
                    </tr>
                    {{-- @endif --}}
                    @php
                        $row++;
                    @endphp
                @endforeach
            </table>
                @if(session('asks_to')=='save')
                    Να προχωρήσει η εισαγωγή αυτών των στοιχείων;
                    <div class="row">
                        <form action="{{url('/insert_teachers')}}" method="post" class="col container-fluid" enctype="multipart/form-data">
                        @csrf
                            <button type="submit" class="btn btn-primary bi bi-file-arrow-up"> Εισαγωγή</button>
                        </form>
                        <a href="{{url('/teachers')}}" class="col">Ακύρωση</a>
                    </div>
                @else
                    <div class="row">
                        <div>
                            Διορθώστε τα σημειωμένα σφάλματα και υποβάλετε εκ νέου το αρχείο.
                        </div>
                        <a href="{{url('/teachers')}}" class="col">Ακύρωση</a>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
</x-layout>