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
    @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
        @if(Auth::user()->isAdmin())      
        <nav class="navbar navbar-light bg-light">
            <div class="vstack gap-2">
                <div class="hstack gap-3">
                <form action="{{url("/update_all_day_template/dim")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        {{-- <span class="input-group-text w-25"></span> --}}
                        <span class="input-group-text w-75"><strong>Ενημέρωση πρότυπου αρχείου Δημοτικών</strong></span>
                    </div>
                    <div class="input-group w-75">
                        <input name="template_file" type="file" class="form-control"><br>
                    </div>
                    <div class="input-group">
                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                        <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                    </div>
                </form>
                <form action="{{url("/dl_all_day_template/1")}}" method="post">
                    @csrf
                    <button class="btn btn-secondary bi bi-box-arrow-down"> Πίνακας δημοτικών προς συμπλήρωση </button>
                </form>
                </div>
                <div class="hstack gap-3">
                <form action="{{url("/update_all_day_template/nip")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        {{-- <span class="input-group-text w-25"></span> --}}
                        <span class="input-group-text w-75"><strong>Ενημέρωση πρότυπου αρχείου Νηπιαγωγείων</strong></span>
                    </div>
                    <div class="input-group w-75">
                        <input name="template_file" type="file" class="form-control"><br>
                    </div>
                    <div class="input-group">
                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                        <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                    </div>
                </form>
                <form action="{{url("/dl_all_day_template/0")}}" method="post">
                    @csrf
                    <button class="btn btn-secondary bi bi-box-arrow-down"> Πίνακας νηπιαγωγείων προς συμπλήρωση </button>
                </form>
            </div>
            </div>
        </nav>
        @endif

        
        @php
            $all_day_schools = $microapp->stakeholders;
            $months = [];
            $number = App\Models\Month::getActiveMonth()->number;
            $i=$number;
            if($number >=9){
                for($i; $i>=9; $i--)
                    array_push($months, $i);
            }
            else{
                for($i; $i>=1; $i--)
                    array_push($months, $i);
                array_push($months,12,11,10,9);
            }
        @endphp
        <div class="table-responsive py-2" style="align-self:flex-start">
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
                @php
                    // dd($months);   
                @endphp
                
                @foreach($months as $month_number) 
                    @foreach($all_day_schools as $one_stakeholder)
                    @php
                        $one_school = $one_stakeholder->stakeholder;
                        $one = $one_school->all_day_schools->where('month_id', $month_number)->first();
                    @endphp
                        <tr>
                        @if($one)
                            <td> {{$one->month->name}}</td>
                            <td> {{$one->school->name}}</td>
                            <td> {{$one->functionality}}</td>
                            <td> {{$one->nr_morning}}</td>
                            <td> {{$one->nr_of_class_3}}</td>
                            <td> {{$one->nr_of_pupils_3 + $one->nr_of_pupils_4+ $one->nr_of_pupils_5}}</td>
                            <td> {{$one->nr_of_class_4}}</td>
                            <td> {{$one->nr_of_pupils_4+ $one->nr_of_pupils_5}}</td>
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
                        @else
                            <td> {{App\Models\Month::find($month_number)->name}}</td>
                            <td> {{$one_school->name}}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        @endif
                        </tr>
                    {{-- @endforeach --}}
                @endforeach
            @endforeach
            </tbody>
            </table>
        </div> <!-- table responsive closure -->
        @include('microapps.microapps_admin_after') {{-- email to those who haven't submitted an answer --}}
</x-layout>