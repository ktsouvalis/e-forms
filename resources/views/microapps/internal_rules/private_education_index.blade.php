<x-layout>
    @push('links')
        <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
    @endpush
    @push('scripts')
        <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
        <script src="{{asset('datatable_init.js')}}"></script>
        <script src="{{asset('toggle_signed_internal_rules.js')}}"></script>
        <script src="{{asset('datatable_init_internal_rules_second.js')}}"></script>
    @endpush
    @push('title')
        <title>Εσωτερικός Κανονισμός Ιδιωτικής Εκπαίδευσης</title>
    @endpush
    @php
        $schools = App\Models\School::where('is_active',1)->where('public',0)->get();
    @endphp
    <div class="h4">Εσωτερικός Κανονισμός Ιδιωτικής Εκπαίδευσης</div>
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
                @foreach($schools as $one_school)
                    @if($one_school->internal_rule <>null)
                    @php
                        $one = $one_school->internal_rule;
                    @endphp
                    @if(!($one->director_signed_file and $one->consultant_signed_file))
                        <tr @if($one->approved_by_consultant and $one->approved_by_director) class="table-success" @endif>
                            <td id="thereAreUnsigned"><strong>{{$one->school->name}}</strong></td>
                            @php
                                $text = $one->approved_by_director ? 'Εγκρίθηκε' : '-';
                            @endphp
                            @if(!$one->approved_by_director)
                                <td @if($one->approved_by_consultant) class="table-success" @endif>{{-- Έγκριση Συμβούλου Εκπαίδευσης --}}
                                    <div class="check_td_{{$one->id}}"> {{$text}}</div>
                                    @if($one->approved_by_consultant) Εγκεκριμένος από Σύμβουλο Εκπαίδευσης @endif
                                </td>
                            @else
                                <td> 
                                    @if($one->approved_by_consultant) 
                                        <br><strong>Εγκεκριμένος από Συμβ. Εκπ/σης & Δ/ντή Εκπ/σης</strong>
                                    @else 
                                        <div class="check_td_{{$one->id}}"> {{$text}}</div>
                                    @endif
                                </td>
                            @endif
                            <td>{{-- Αρχεία Σχολείου --}}
                                {{-- <form action="{{url("/internal_rules/download_file/$one->id/school_file")}}" method="get"> --}}
                                <form action="{{route("internal_rules.download_file", ['internal_rule' => $one->id, 'file_type' => 'school_file'])}}" method="get">
                                    <button class="btn btn-warning mb-2 bi bi-box-arrow-down" title="Λήψη αρχείου">@if($one->school_file2 or $one->school_file3)<del> @endif  {{$one->school_file}}</del></button>
                                </form>
                    
                                @if($one->school_file2)
                                    {{-- <form action="{{url("/internal_rules/download_file/$one->id/school_file2")}}" method="get"> --}}
                                        <form action="{{route("internal_rules.download_file", ['internal_rule' => $one->id, 'file_type' => 'school_file2'])}}" method="get">
                                        <button class="btn btn-warning mb-2 bi bi-box-arrow-down" title="Λήψη αρχείου">@if($one->school_file3)<del> @endif  {{$one->school_file2}}</del></button>
                                    </form>
                                @endif
                                @if($one->school_file3)
                                    {{-- <form action="{{url("/internal_rules/download_file/$one->id/school_file3")}}" method="get"> --}}
                                    <form action="{{route("internal_rules.download_file", ['internal_rule' => $one->id, 'file_type' => 'school_file3'])}}" method="get">
                                        <button class="btn btn-warning mb-2 bi bi-box-arrow-down" title="Λήψη αρχείου">  {{$one->school_file3}}</button>
                                    </form>
                                @endif
                            </td>
                            <td> {{-- Αρχεία Παρατηρήσεων --}}
                                @if($one->director_comments_file)
                                    <form action="{{route("internal_rules.download_file", ['internal_rule' => $one->id, 'file_type' => 'director_comments_file'])}}" method="get">
                                        <div class="mb-2">Δ/ντης Εκπ/σης: <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου">  {{$one->director_comments_file}}</button></div>
                                    </form>   
                                @endif
                                
                                @if($one->consultant_comments_file)
                                    <form action="{{route("internal_rules.download_file", ['internal_rule' => $one->id, 'file_type' => 'consultant_comments_file'])}}" method="get">
                                        <div class="mb-2">Συμβ. Εκπ/σης: <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου">  {{$one->consultant_comments_file}}</button></div>
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
                                    @if($one->director_signed_file)
                                        <form action="{{route("internal_rules.download_file", ['internal_rule' => $one->id, 'file_type' => 'director_signed_file'])}}" method="get">   
                                            <div class="mb-2">Δ/ντή Εκπ/σης: <button class="btn {{$director_color}} bi bi-box-arrow-down" title="Λήψη αρχείου">  {{$one->director_signed_file}}</button></div>
                                        </form> 
                                    @endif
                                    @if($one->consultant_signed_file)
                                        <form action="{{route("internal_rules.download_file", ['internal_rule' => $one->id, 'file_type' => 'consultant_signed_file'])}}" method="get">
                                            <div class="mb-2">Συμβ. Εκπ/σης: <button class="btn {{$consultant_color}} bi bi-box-arrow-down" title="Λήψη αρχείου">  {{$one->consultant_signed_file}}</button></div>
                                        </form>
                                    @endif
                                @endif
                            </td>
                            <td>{{$one->school->schregion->consultant->surname}} {{$one->school->schregion->consultant->name}}</td>
                        </tr>
                        @endif
                    @else
                    <tr>
                        <td><strong>{{$one_school->name}}</strong></td>
                        <td>Δεν έχει υποβληθεί</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$one_school->schregion->consultant->surname}} {{$one_school->schregion->consultant->name}}</td>
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
            <button class="btn btn-primary bi bi-arrows-expand mb-3" onClick="showSigned()" id="toggleSignedButton"></button>
            <div class="container" id="signed">
                <table  id="dataTable2" class="small display align-middle table table-sm table-secondary table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th id="search">Σχολείο</th>
                            <th>Τελικός Υπογεγραμμένος Εσωτερικός Κανονισμός</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($schools as $one_school)
                        @if($one_school->internal_rule <>null)
                        @php
                            $one = $one_school->internal_rule;
                        @endphp
                            @if($one->director_signed_file and $one->consultant_signed_file)
                                <tr><td><strong>{{$one->school->name}}</strong></td>
                                <td>
                                    <form action="{{route("internal_rules.download_file", ['internal_rule' => $one->id, 'file_type' => 'director_signed_file'])}}" method="get">
                                        <div class="mb-2"> <button class="btn btn-success bi bi-box-arrow-down" title="Λήψη αρχείου">  {{$one->director_signed_file}}</button></div>
                                    </form>
                                </td>
                                </tr>
                            @endif
                        @endif
                    @endforeach
                </table>
            </div>
</x-layout>