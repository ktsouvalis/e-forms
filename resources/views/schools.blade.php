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
        <title>Σχολεία</title>
    @endpush
    @php
        $all_schools = App\Models\School::all();
    @endphp
<body>
<div class="container">
    <!--tabs-->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link @if(!session()->has('active_tab'))  {{'active'}} @endif" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="true">Αναζήτηση Σχολείου </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link @if(session()->has('active_tab'))  @if(session('active_tab')=='import') {{'active'}} @endif @endif" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="tab2" aria-selected="false">Μαζική Εισαγωγή Σχολείων</button>
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
                            <th id="search">Κωδικός</th>
                            <th id="search">Ονομασία</th>
                            <th id="search">email</th>
                            <th id="search">link</th>
                            <th id="search">Δημ/Νηπ</th>
                            <th id="search">Ειδικό</th>
                            <th id="search">Δημόσιο</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($all_schools as $school)
                        <tr>  
                            <td>{{$school->code}}</td>
                            <td>{{$school->name}}</td>
                            <td>{{$school->mail}}</td>
                            <td>{{$school->md5}}</td>
                            <td>{{$school->primary}}</td>
                            <td>{{$school->special_needs}}</td>
                            <td>{{$school->international}}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>

        
            <div class="tab-pane fade @if(session()->has('active_tab')) @if(session('active_tab')=='import') {{'show active'}} @endif @endif" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
            @if(!session()->has('asks_to'))
            <nav class="navbar navbar-light bg-light">
                <form action="{{url('/upload_schools_template')}}" method="post" class="container-fluid" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="import_schools" >    
                    <button type="submit" class="btn bi bi-filetype-xlsx btn-primary"> Αποστολή αρχείου</button>
                </form>
            </nav>
            @else
            <div style="p-3 mb-2 bg-info text-dark">
                Διαβάστηκαν τα ακόλουθα λάθη από το αρχείο:
            </div>
            
            <table class="table table-striped table-hover table-light">
                <tr>
                    <th id="search">Κωδικός</th>
                    <th id="search">Ονομασία</th>
                    <th id="search">email</th>
                    <th id="search">link</th>
                    <th id="search">Δημ/Νηπ</th>
                    <th id="search">Δήμος</th>
                    <th id="search">Ειδικό</th>
                    <th id="search">Δημόσιο</th>
                </tr>
                @php
                    $row = 2;
                @endphp
                @foreach(session('schools_array') as $school)
                    {{-- @if(!null==session("errors_array[$row]")) --}}
                    <tr>  
                        <td>{{$school['code']}}</td>
                        <td>{{$school['name']}}</td>
                        <td>{{$school['mail']}}</td>
                        <td>{{$school['md5']}}</td>
                        <td>{{$school['primary']}}</td>
                        <td>{{$school['municipality']}}</td>
                        <td>{{$school['special_needs']}}</td>
                        <td>{{$school['international']}}</td>
                        
                @endforeach
            </table>
                @if(session('asks_to')=='save')
                    Να προχωρήσει η εισαγωγή αυτών των στοιχείων;
                    <div class="row">
                        <form action="{{url('/insert_schools')}}" method="post" class="col container-fluid" enctype="multipart/form-data">
                        @csrf
                            <button type="submit" class="btn btn-primary bi bi-file-arrow-up"> Εισαγωγή</button>
                        </form>
                        <a href="{{url('/schools')}}" class="col">Ακύρωση</a>
                    </div>
                @else
                    <div class="row">
                        <div>
                            Διορθώστε τα σημειωμένα σφάλματα και υποβάλετε εκ νέου το αρχείο.
                        </div>
                        <a href="{{url('/schools')}}" class="col">Ακύρωση</a>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
</x-layout>