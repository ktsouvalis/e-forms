<x-layout_consultant>
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
        $user = Auth::guard('consultant')->user(); //check which user is logged in
        $microapp = App\Models\Microapp::where('url', '/internal_rules')->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
    @endphp
    @php
        $schools = App\Models\School::whereIn('id', $user->schregion->schools->pluck('id'))->get();
    @endphp
    <div class="container">
        <div class="container px-5">
            <table  id="dataTable" class="small display align-middle table table-sm table-secondary table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th id="search">Σχολείο</th>
                        <th id="search">Έγκριση</th>
                        <th id="">Αρχεία Σχολείου</th>
                        <th>Αρχεία με Παρατηρήσεις/Διορθώσεις <br>(Συμβούλου Εκπ/σης ή Διευθυντή Εκπ/σης)</th>
                        <th>Υπογεγραμμένα αρχεία</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($schools as $one_school)
                    @if($one_school->internal_rule <>null)
                    @php
                        $one = $one_school->internal_rule;
                    @endphp
                    <tr @if($one->approved_by_consultant and $one->approved_by_director) class="table-success" @endif>
                        <td><strong>{{$one->school->name}}</strong></td>
                        @if(!$one->approved_by_consultant)
                            <td @if($one->approved_by_director) class="table-success" @endif>{{-- Έγκριση Διευθυντή Εκπαίδευσης --}}
                                <form action="{{url("/approve_int_rule/consultant/$one->id")}}" method="post">
                                    @csrf
                                    <button class="bi bi-check btn btn-outline-success" type="submit"> Έγκριση</button>
                                </form>
                                @if($one->approved_by_director) Εγκεκριμένος από Διευθυντή Εκπαίδευσης @endif
                            </td>
                        @else
                            <td> 
                                @if($one->approved_by_director) <br><strong>Εγκεκριμένος από Συμβ. Εκπ/σης & Δ/ντή Εκπ/σης</strong>
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
                            @if(!$one->consultant_comments_file)
                                @if(!$one->approved_by_consultant)
                                    <form action="{{url("/upload_consultant_comments_file/$one->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                                        @csrf                           
                                        <input name="consultant_comment_file" type="file" class="form-control" required>
                                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                    </form>
                                @endif
                            @else
                                <form action="{{url("/dl_internal_rules_file/$one->id/consultant_comments_file")}}" method="post">
                                    @csrf
                                    <div class="mb-2">Σύμβ. Εκπ/σης: <button class="btn btn-secondary bi bi-box-arrow-down">  {{$one->consultant_comments_file}}</button></div>
                                </form>   
                            @endif
                            
                            @if($one->director_comments_file)
                                <form action="{{url("/dl_internal_rules_file/$one->id/director_comments_file")}}" method="post">
                                    @csrf
                                    <div class="mb-2">Δ/ντης Εκπ/σης: <button class="btn btn-secondary bi bi-box-arrow-down">  {{$one->director_comments_file}}</button></div>
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
                                @if(!$one->consultant_signed_file)
                                    <form action="{{url("/upload_consultant_signed_file/$one->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                                        @csrf                           
                                        <input name="consultant_signed_file" type="file" class="form-control" required>
                                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                    </form>
                                @else {{-- Έχω υπογεγραμμένο αρχείο Συμβούλου--}}
                                    <form action="{{url("/dl_internal_rules_file/$one->id/consultant_signed_file")}}" method="post">
                                        @csrf
                                        <div class="mb-2">Σύμβ. Εκπ/σης: <button class="btn {{$consultant_color}} bi bi-box-arrow-down">  {{$one->consultant_signed_file}}</button></div>
                                    </form>
                                @endif
                                @if($one->director_signed_file)
                                    <form action="{{url("/dl_internal_rules_file/$one->id/director_signed_file")}}" method="post">
                                        @csrf
                                        <div class="mb-2">Δ/ντη Εκπ/σης: <button class="btn {{$director_color}} bi bi-box-arrow-down">  {{$one->director_signed_file}}</button></div>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td><strong>{{$one_school->name}}</strong></td>
                        <td>Δεν έχει υποβληθεί</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
    </div>
    </div>
</x-layout_consultant>