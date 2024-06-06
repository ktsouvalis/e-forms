@php
    $interactions = $stakeholder->interaction;  
    // $department = App\Models\Department::find(5);
    // dd($department->interactions)
@endphp
@push('links')
    <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
    <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
    <link href="{{asset("summernote-0.8.18-dist/summernote-lite.min.css")}}" rel="stylesheet">
@endpush
@push('scripts')
    <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
    <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
    <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
    <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
    <script src="{{asset('datatable_init.js')}}"></script>
    <script src="{{asset("summernote-0.8.18-dist/summernote-lite.min.js")}}"></script>
    <script>
        $(document).ready(function () {
            var maxChars = 5000;
            $('#text').summernote({
                height: 200, // Adjust the height as needed
                width:600,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['list', ['ul', 'ol']],
                ],
                lang: 'el-GR', // Set language to Greek
                callbacks: {
                    onChange: function(contents, $editable) {
                        var currentChars = contents.length;
                        var remainingChars = maxChars - currentChars;

                        // Display the remaining characters
                        $('#charCount').text(remainingChars);
                    }
                }
            });
        });
    </script>
@endpush
@push('title')
    <title>Αιτήματα προς τη Διεύθυνση Π.Ε. Αχαΐας</title>
@endpush
<div class="container">
<form method="POST" action="{{ route('interactions.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="form-group my-2">
        <label for="type"><strong>Θέμα</strong></label>
        <select id="type" name="interaction_type_id" class="form-control">
            @foreach($interaction_types as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
            <option value="0">Άλλο</option>
        </select>
    </div>
    <div class="form-group my-2">
        <label for="text"><strong>Κείμενο</strong></label>
        <textarea name="text" id="text" class="form-control" required></textarea>
    </div>
    <div class="form-group my-2">
        <label for="files"><strong>Συνημμένο</strong> (προαιρετικό)</label>
        <input type="file" id="files" name="files[]" class="form-control" accept=".pdf, .xlsx" multiple>
    </div>
    <span id="charCount">5000</span> χαρακτήρες απομένουν
    <div class="input-group my-3">
        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Υποβολή Αιτήματος</button>
    </div>
</form>
</div>
