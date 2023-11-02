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
        <title>Εσωτερικός Κανονισμός</title>
    @endpush
    @php
        $user = Auth::user(); //check which user is logged in
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
    @endphp
    @php
        $internal_rules = App\Models\microapps\InternalRule::all();
    @endphp
    <div class="container">
        @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}} 
            <table  id="dataTable" class="small display align-middle table table-sm table-secondary table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th id="search">Σχολείο</th>
                        <th id="search">Έγκριση</th>
                        <th id="">Αρχεία Σχολείου</th>
                        <th>Αρχεία με Παρατηρήσεις/Διορθώσεις <br>(Συμβούλου Εκπ/σης ή Διευθυντή Εκπ/σης)</th>
                        <th>Υπογεγραμμένα αρχεία</th>
                        <th id="search">Σύμβουλος Εκπαίδευσης</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($internal_rules as $one)
                    <tr @if($one->approved_by_consultant and $one->approved_by_director) class="table-success" @endif>
                        <td><strong>{{$one->school->name}}</strong></td>
                        @if(!$one->approved_by_director)
                            <td @if($one->approved_by_consultant) class="table-success" @endif>{{-- Έγκριση Συμβούλου Εκπαίδευσης --}}
                                <form action="{{url("/approve_int_rule/director/$one->id")}}" method="post">
                                    @csrf
                                    <button class="bi bi-check btn btn-outline-success" type="submit" > Έγκριση</button>
                                </form>
                                @if($one->approved_by_consultant) Εγκεκριμένος από Σύμβουλο Εκπαίδευσης @endif
                            </td>
                        @else
                            <td> 
                                @if($one->approved_by_consultant) <br><strong>Εγκεκριμένος από Συμβ. Εκπ/σης & Δ/ντή Εκπ/σης</strong>
                                @else <div class="bi bi-check-circle btn btn-success"  style="color:white"> Εγκρίθηκε </div>
                                @endif
                            </td>
                        @endif
                        <td>{{-- Αρχεία Σχολείου --}}
                            <form action="{{url("/dl_internal_rules_file/$one->id/school_file")}}" method="post">
                                @csrf
                                <button class="btn btn-warning mb-2 bi bi-box-arrow-down">@if($one->school_file2 or $one->school_file3)<del> @endif  {{$one->school_file}}</del></button>
                            </form>
                
                            @if($one->school_file2)
                                <form action="{{url("/dl_internal_rules_file/$one->id/school_file2")}}" method="post">
                                    @csrf
                                    <button class="btn btn-warning mb-2 bi bi-box-arrow-down">@if($one->school_file3)<del> @endif  {{$one->school_file2}}</del></button>
                                </form>
                            @endif
                            @if($one->school_file3)
                                <form action="{{url("/dl_internal_rules_file/$one->id/school_file3")}}" method="post">
                                    @csrf
                                    <button class="btn btn-warning mb-2 bi bi-box-arrow-down">  {{$one->school_file3}}</button>
                                </form>
                            @endif
                        </td>
                        <td> {{-- Αρχεία Παρατηρήσεων --}}
                            @if(!$one->director_comments_file)
                                @if(!$one->approved_by_director)
                                    <form action="{{url("/upload_director_comments_file/$one->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                                        @csrf                           
                                        <input name="director_comment_file" type="file" class="form-control" required>
                                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                    </form>
                                @endif
                            @else
                                <form action="{{url("/dl_internal_rules_file/$one->id/director_comments_file")}}" method="post">
                                    @csrf
                                    <div class="mb-2">Δ/ντης Εκπ/σης: <button class="btn btn-secondary bi bi-box-arrow-down">  {{$one->director_comments_file}}</button></div>
                                </form>   
                            @endif
                            
                            @if($one->consultant_comments_file)
                                <form action="{{url("/dl_internal_rules_file/$one->id/consultant_comments_file")}}" method="post">
                                    @csrf
                                    <div class="mb-2">Συμβ. Εκπ/σης: <button class="btn btn-secondary bi bi-box-arrow-down">  {{$one->consultant_comments_file}}</button></div>
                                </form>
                            @endif
                        </td>
                        @php 
                            $consultant_color = "btn-secondary"; $director_color = "btn-secondary";
                            if($one->consultant_singed_at > $one->director_signed_at)
                                $consultant_color = "btn-danger";
                            else 
                                $director_color = "btn-danger"; 
                        @endphp
                        <td>
                            @if($one->approved_by_consultant and $one->approved_by_director) {{-- Αρχεία Υπογεγραμμένα--}}
                                @if(!$one->director_signed_file)
                                    <form action="{{url("/upload_director_signed_file/$one->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                                        @csrf                           
                                        <input name="director_signed_file" type="file" class="form-control" required>
                                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                    </form>
                                @else {{-- Έχω υπογεγραμμένο αρχείο Διευθυντή--}}
                                    <form action="{{url("/dl_internal_rules_file/$one->id/director_signed_file")}}" method="post">
                                        @csrf
                                        <div class="mb-2">Δ/ντή Εκπ/σης: <button class="btn {{$director_color}} bi bi-box-arrow-down">  {{$one->director_signed_file}}</button></div>
                                    </form> 
                                @endif
                                @if($one->consultant_signed_file)
                                    <form action="{{url("/dl_internal_rules_file/$one->id/consultant_signed_file")}}" method="post">
                                        @csrf
                                        <div class="mb-2">Συμβ. Εκπ/σης: <button class="btn {{$consultant_color}} bi bi-box-arrow-down">  {{$one->consultant_signed_file}}</button></div>
                                    </form>
                                @endif
                            @endif
                        </td>
                        <td>{{$one->school->schregion->consultant->surname}} {{$one->school->schregion->consultant->name}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
    </div>
</x-layout>