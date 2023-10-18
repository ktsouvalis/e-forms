<x-layout>

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
    @endpush
    @push('title')
        <title>Ολοήμερο και Πρωινή Υποδοχή</title>
    @endpush
    @php
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
    @endphp

   
    <div class="container">  
        @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
            <form action="{{url("/dl_all_day_template")}}" method="post">
                @csrf
                <button class="btn btn-secondary bi bi-box-arrow-down"> Πίνακας προς συμπλήρωση </button>
            </form>
            @if(Auth::user()->isAdmin())      
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/update_all_day_template")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        {{-- <span class="input-group-text w-25"></span> --}}
                        <span class="input-group-text w-50"><strong>Ενημέρωση πρότυπου αρχείου</strong></span>
                    </div>
                    <div class="input-group w-50">
                        <input name="template_file" type="file" class="form-control"><br>
                    </div>
                    <div class="input-group">
                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                        <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                    </div>
                </form>
                
            </nav>
            @endif
    </div> 
        
        @php
            $all_day_schools = App\Models\microapps\AllDaySchool::all()->sortByDesc('month_id');
        @endphp
        <div class="container py-5">
            <div class="table-responsive py-2">
                <table  id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th id="search">Μήνας</th>
                        <th id="search">Σχολείο</th>
                        <th id="search">Λειτουργία</th>
                        <th id="">Μαθητές Πρωινή Υποδοχή</th>
                        <th id="">Τμήματα 15.00</th>
                        <th id="">Μαθητές 15.00</th>
                        <th id="">Τμήματα 16.00</th>
                        <th id="">Μαθητές 16.00</th>
                        <th id="">Τμήματα 17.30</th>
                        <th id="">Μαθητές 17.30</th>
                        <th id="">Σχόλια</th>
                        <th>Αρχείο</th>
                        <th>Τελευταία ενημέρωση</th>
                    </tr>
                </thead>
                <tbody>
                
                @foreach($all_day_schools as $one)
                    <tr>
                    <td> {{$one->month->name}}</td>
                    <td> {{$one->school->name}}</td>
                    <td> {{$one->functionality}}</td>
                    <td> {{$one->nr_morning}}</td>
                    <td> {{$one->nr_of_class_3}}</td>
                    <td> {{$one->nr_of_pupils_3}}</td>
                    <td> {{$one->nr_of_class_4}}</td>
                    <td> {{$one->nr_of_pupils_4}}</td>
                    <td> {{$one->nr_of_class_5}}</td>
                    <td> {{$one->nr_of_pupils_5}}</td>
                    <td> {{$one->comments}}</td>
                    <td>
                        <form action="{{url("/dl_all_day_file/$one->id")}}" method="post">
                        @csrf
                        <button class="btn btn-secondary bi bi-box-arrow-down"> </button> 
                        </form>   
                    </td>
                    <td>{{$one->updated_at}}</td>
                    </tr>
                @endforeach
                </tbody>
                </table>
            </div>
            @include('microapps.microapps_admin_after') {{-- email to those who haven't submitted an answer --}}
        </div>  
</x-layout>