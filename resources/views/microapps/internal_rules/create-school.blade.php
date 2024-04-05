<x-layout_school>
    @push('title')
        <title>Εσωτερικός Κανονισμός</title>
    @endpush
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        $old_data = $school->internal_rule;
    @endphp
    @push('scripts')
        <script>
            var appname = "{{ $appname }}";
        </script>
    @endpush
    <div class="container">
    <div class="container px-5">  
            <nav class="navbar navbar-light bg-light">
                {{-- <form action="{{url("/internal_rules")}}" method="post" enctype="multipart/form-data" class="container-fluid"> --}}
                    <form action="{{route('internal_rules.store')}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text w-75"><strong>Καταχώρηση Αρχείου Εσωτερικού Κανονισμού </strong></span>
                    </div>
                   
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon4">Αρχείο</span>
                        <input name="int_rules_file" type="file" class="form-control" required><br>
                    </div>
                    @if(!$accepts)
                        <div class='alert alert-warning text-center my-2'>
                            <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                        </div>
                    @else
                        @if($old_data)
                            @if($old_data->approved_by_consultant and $old_data->approved_by_director)
                                <div class='alert alert-info text-center'>
                                    Ο Εσωτερικός Κανονισμός που έχετε υποβάλλει, έχει εγκριθεί από τον Σύμβουλο και τον Διευθυντή Εκπαίδευσης.
                                    @if(!$old_data->signed_file2)Ο τελικός υπογεγραμμένος Εσωτερικός Κανονισμός θα εμφανιστεί στη σελίδα όταν υπογραφεί και από τους δύο.@endif
                                </div>
                            @else
                                <div class="input-group">
                                    <span class="w-25"></span>
                                    <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                    {{-- <a href="{{url("/$appname/create")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a> --}}
                                    <a href="{{route("$appname.create")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                                </div>
                            @endif
                        @else
                            <div class="input-group">
                                <span class="w-25"></span>
                                <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                {{-- <a href="{{url("/$appname/create")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a> --}}
                                <a href="{{route("$appname.create")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                            </div>
                        @endif
                    @endif
                </form> 
            </nav>
        </div> 
        
        <div class="container px-5 py-2">
            @if($old_data)
            {{-- <div class="vstack gap-2"> --}}
                <table class="table table-striped">
                    
                <tbody>
                {{-- <form action="{{url("/internal_rules/download_file/$old_data->id/school_file")}}" method="get"> --}}
                <form action="{{route('internal_rules.download_file', ['internal_rule'=>$old_data->id, 'file_type'=>'school_file'])}}" method="get">
                    <tr><td>Αρχείο που έχετε υποβάλλει:</td><td> <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου">@if($old_data->school_file2 or $old_data->school_file3)<del> @endif  {{$old_data->school_file}}</del></button> </td></tr>
                </form>
                @if($old_data->school_file2)
                    {{-- <form action="{{url("/internal_rules/download_file/$old_data->id/school_file2")}}" method="get"> --}}
                    <form action="{{route('internal_rules.download_file', ['internal_rule'=>$old_data->id, 'file_type'=>'school_file2'])}}" method="get">
                        <tr><td>Διορθωμένο αρχείο: </td><td><button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου">@if($old_data->school_file3)<del> @endif  {{$old_data->school_file2}}</del></button> </td></tr>
                    </form>
                @endif
                @if($old_data->school_file3)
                    {{-- <form action="{{url("/internal_rules/download_file/$old_data->id/school_file3")}}" method="get"> --}}
                    <form action="{{route('internal_rules.download_file', ['internal_rule'=>$old_data->id, 'file_type'=>'school_file3'])}}" method="get">
                       <tr><td> Τελικό αρχείο: </td><td><button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου">  {{$old_data->school_file3}}</button> </td></tr>
                    </form>
                @endif
                
                @if($old_data->consultant_comments_file)
                    {{-- <form action="{{url("/internal_rules/download_file/$old_data->id/consultant_comments_file")}}" method="get"> --}}
                    <form action="{{route('internal_rules.download_file', ['internal_rule'=>$old_data->id, 'file_type'=>'consultant_comments_file'])}}" method="get">
                       <tr><td> Παρατηρήσεις Συμβούλου Εκπαίδευσης: </td><td><button class="btn btn-warning bi bi-box-arrow-down" title="Λήψη αρχείου">  {{$old_data->consultant_comments_file}}</button> </td></tr>
                    </form> 
                @endif
                @if($old_data->director_comments_file)
                    {{-- <form action="{{url("/internal_rules/download_file/$old_data->id/director_comments_file")}}" method="get"> --}}
                    <form action="{{route('internal_rules.download_file', ['internal_rule'=>$old_data->id, 'file_type'=>'director_comments_file'])}}" method="get">
                       <tr><td> Παρατηρήσεις Διευθυντή Εκπαίδευσης:</td><td> <button class="btn btn-warning bi bi-box-arrow-down" title="Λήψη αρχείου">  {{$old_data->director_comments_file}}</button> </td></tr>
                    </form>  
                @endif
                </tbody>
                </table>
                
                @if($old_data->director_signed_file and $old_data->consultant_signed_file)
                    <strong>Υπογεγραμμένο αρχείο από Διευθυντή και Σύμβουλο Εκπαίδευσης</strong> 
                            
                    @if($old_data->consultant_signed_file and $old_data->director_signed_file) 
                        @if($old_data->consultant_singed_at > $old_data->director_signed_at)
                            {{-- <form action="{{url("/internal_rules/download_file/$old_data->id/consultant_signed_file")}}" method="get"> --}}
                            <form action="{{route('internal_rules.download_file', ['internal_rule'=>$old_data->id, 'file_type'=>'consultant_signed_file'])}}" method="get">
                                <button class="btn btn-success bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$old_data->consultant_signed_file}}</button>
                            </form>
                        @else 
                            {{-- <form action="{{url("/internal_rules/download_file/$old_data->id/director_signed_file")}}" method="get"> --}}
                            <form action="{{route('internal_rules.download_file', ['internal_rule'=>$old_data->id, 'file_type'=>'director_signed_file'])}}" method="get">
                                <button class="btn btn-success bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$old_data->director_signed_file}}</button>
                            </form>
                        @endif
                    @endif
                    <hr>
                @endif
            {{-- </div>    --}}
            
        </div>
        @if($old_data->approved_by_consultant)
            <div class='container container-narrow'>
            <div class='alert alert-success text-center'>
                Ο Εσωτερικός Κανονισμός έχει εγκριθεί από τον Σύμβουλο Εκπαίδευσης
            </div>
            </div>
        @endif
        @if($old_data->approved_by_director)
            <div class='container container-narrow'>
            <div class='alert alert-success text-center'>
                Ο Εσωτερικός Κανονισμός έχει εγκριθεί από τον Διευθυντή Εκπαίδευσης
            </div>
            </div>
        @endif
        @endif
</x-layout_school>