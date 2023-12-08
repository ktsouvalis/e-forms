<x-layout_school>
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        $outings = $school->outings;
    @endphp
    @push('links')
        <link href="../DataTables-1.13.4/css/dataTables.bootstrap5.css" rel="stylesheet"/>
        <link href="../Responsive-2.4.1/css/responsive.bootstrap5.css" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="../DataTables-1.13.4/js/jquery.dataTables.js"></script>
        <script src="../DataTables-1.13.4/js/dataTables.bootstrap5.js"></script>
        <script src="../Responsive-2.4.1/js/dataTables.responsive.js"></script>
        <script src="../Responsive-2.4.1/js/responsive.bootstrap5.js"></script>
        <script src="../datatable_init.js"></script>
        <script>
            var appname = "{{ $appname }}";
        </script>
        <script src="../../inside_microapps_new_ticket.js"></script>
    @endpush
    @push('title')
        <title>Εκδρομές</title>
    @endpush
        <div class="py-3">
            <nav class="navbar navbar-light bg-light">
                    <form action="{{url("/new_outing")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <div class="input-group">
                            <span class="input-group-text w-25"></span>
                            <span class="input-group-text w-75"><strong>Καταχώρηση στοιχείων εκδρομής</strong></span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25" id="basic-addon4">Τύπος Εκδρομής</span>
                            <select name="type" class="form-select" aria-label="Default select example">
                                @foreach(App\Models\microapps\OutingType::all() as $type)
                                @php
                                    $selected=null;
                                    if($type->id == 1)
                                        $selected="selected";   
                                @endphp
                                <option {{$selected}} value="{{$type->id}}">{{$type->description}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25" id="basic-addon2">Ημερομηνία</span>
                            <input name="outing_date" type="date" class="form-control"  aria-label="outing_date" aria-describedby="basic-addon1" required ><br>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Δράση: </span>
                            <input name="destination" id="destination" type="text" class="form-control" placeholder="π.χ. Πάρκο Κυκλοφοριακής Αγωγής" aria-label="Δράση" aria-describedby="basic-addon2" required><br>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Αριθμός πρακτικού: </span>
                            <input name="record" id="record" type="text" class="form-control" placeholder="π.χ. 15-11/9/2023" aria-label="αριθμός πρακτικού" aria-describedby="basic-addon3" required><br>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Τμήματα που συμμετέχουν</span>
                            @php
                                $sections = $school->sections;  
                            @endphp
                            <div class="v-stack gap-2">
                            @foreach($sections as $section)
                                <div class="form-check form-switch">
                                    <input class="form-check-input" role="switch" type="checkbox" name="section{{$section->id}}" value="{{$section->id}}" id="section{{$section->id}}">
                                    <label for="section{{$section->id}}"> {{$section->name}} </label>
                                </div>     
                            @endforeach
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25" id="basic-addon4">Απόσπασμα Πρακτικού</span>
                            <input name="record_file" type="file" class="form-control" required><br>
                        </div>
                        @if(!$accepts)
                            <div class="col-sm-2 btn btn-warning bi bi-bricks rounded text-dark" style="text-align:center;">
                                Η εφαρμογή δε δέχεται υποβολές
                            </div>
                        @else
                            <div class="input-group">
                                <span class="input-group-text w-25"><em>Μορφή αρχείου: .pdf < 10MB</em></span>
                                <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή Εκδρομής </button>
                                <a href="{{url("/school_app/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                            </div>
                        @endif
                    </form>
                </nav>
            </div> 
            <hr>
            @include('microapps.new_ticket_button')
            <hr>
            <div class=" py-3">
                <div class="table-responsive py-2">
                <table  id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th id="search">Έλεγχος Δ/νσης-ID</th>
                        <th id="search">Τύπος</th>
                        <th id="">Ημερομηνία</th>
                        <th id="">Δράση</th>
                        <th id="">Πρακτικό</th>
                        <th id="">Αρχείο</th>
                        <th id="">Τμήματα</th>
                        
                        <th>Επεξεργασία μελλοντικής εκδρομής</th>
                        <th>Διαγραφή μελλοντικής εκδρομής</th>
                    </tr>
                </thead>
                <tbody>
                
                    @foreach($outings as $outing)
                        @php
                            $my_date = Illuminate\Support\Carbon::parse($outing->outing_date); 
                            $today = Illuminate\Support\Carbon::now();
                        @endphp
                        <tr>
                            <td>
                                
                                @if($outing->checked)
                                    <div class="bi bi-check-circle btn btn-success"  style="color:white"> </div>
                                @else
                                    <div class="bi bi-hourglass-split btn btn-warning"> </div>
                                @endif
                                <div>{{$outing->id}}</div>
                    
                            </td>
                            <td>{{$outing->type->description}}</td> 
                            <td>{{$my_date->day}}/{{$my_date->month}}/{{$my_date->year}} </td>
                            <td>{{$outing->destination}}</td>
                            <td>{{$outing->record}}</td>
                            <td>
                                <div class="hstack gap-2">
                                
                                <form action="{{url("/download_record/$outing->id")}}" method="post">
                                    @csrf
                                    <button class="btn btn-secondary bi bi-box-arrow-down"> </button>
                                </form>
                                {{$outing->file}}
                                </div>
                            </td>
                            <td>
                                @foreach($outing->sections as $out_section)
                                    {{$out_section->section->name}}<br>
                                @endforeach
                            </td>
                            
                            @if(Illuminate\Support\Carbon::parse($outing->outing_date)->startOfDay()>=Illuminate\Support\Carbon::now()->startOfDay() and $accepts)
                            <td><a href="{{url("/outing_profile/$outing->id")}}" class="btn btn-primary bi bi-pencil-square "> </a></td>
                            <td>
                                <form action="{{url("/delete_outing/$outing->id")}}" method="post">
                                    @csrf
                                    <button class="bi bi-x-circle btn btn-danger" type="submit" style="color:white" onclick="return confirm('Επιβεβαίωση διαγραφής εκδρομής;')"> </button>
                                </form>
                            </td>
                            
                            @else
                            <td> - </td>
                            <td> - </td>
                            @endif
                        </tr> 
                    @endforeach   
                </tbody>  
                </table>    
            </div>
    </div>
    <div class="  py-3">
        <table class="display table table-sm table-striped table-bordered table-hover">
            <tr>
                <th>Τμήμα</th>
                <th>Πλήθος ολιγόωρων</th>
                <th>Πλήθος πολύωρων</th>
                <th>Πλήθος ολοήμερων</th>
                <th>Σύνολο</th>
            </tr>
            
            @foreach($school->sections as $one_section)
                @php
                    $ligo = 0;
                    $poly = 0;
                    $day = 0;
                @endphp
                @foreach($one_section->outings as $section_outing)
                    @php
                        if($section_outing->outing->outingtype_id==1)$ligo++;
                        if($section_outing->outing->outingtype_id==2)$poly++;
                        if($section_outing->outing->outingtype_id==3)$day++;
                    @endphp
                @endforeach  
            <tr>
                <td>{{$one_section->name}}</td>
                <td>{{ $ligo }}</td>
                <td>{{ $poly }}</td>
                <td>{{ $day }}</td>
                <td><strong>{{$ligo+$poly+$day}}</strong></td>
            </tr>
            @endforeach
        </table>
    </div>
</x-layout_school>