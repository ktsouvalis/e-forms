<x-layout_school>
    @push('title')
        <title>Δείκτες</title>
    @endpush
@php
    $school = Auth::guard('school')->user(); //check which school is logged in
    $schoolCode = $school->code; //fetch the school's code
    $microapp = App\Models\Microapp::where('url', '/two_files')->first();
    $accepts = $microapp->accepts; //fetch microapp 'accepts' field
    $old_data = $school->twoFile()->first(); //fetch the school's data for this microapp
@endphp
<div class="container mt-5">
    <h4>Υποβολή αρχείου ΔΕΙΚΤΩΝ σε μορφή .xlsx και .pdf</h4>
    @if(file_exists(storage_path('app/two_files/Two_Files_Template.xlsx')))
    <div class="row">
        <div class="col-md-6 p-3">
            <h6>Πρότυπο για συμπλήρωση:</h6>
            <form action="{{route('two_files.download_file',['file'=>'Two_Files_Template.xlsx', 'download_file_name' => 'Αρχείο_Δεικτών_για_συμπλήρωση.xlsx'])}}" method="get"class="container-fluid">
                <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title=""> Λήψη αρχείου για συμπλήρωση</button>
            </form>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-6 p-3">Υποβολή αρχείου σε μορφή .xlsx</div>
        <div class="col-6 p-3">Υποβολή αρχείου σε μορφή .pdf</div>
    </div>
    <div class="row">
        <div class="col-6 p-3">
            <form action="{{ route('two_files.upload_file', ['upload_file_name' => 'Two_files_$schoolCode.xlsx']) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="fileXlsx">Αρχείο .xlsx</label>
                    <input type="file" class="form-control-file" id="fileXlsx" name="fileXlsx" required>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Υποβολή .xlsx</button>
            </form>
        </div>
        
        <div class="col-6 p-3">
            <form action="{{ route('two_files.upload_file', ['upload_file_name' => 'Two_files_$schoolCode.pdf']) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="filePdf">Αρχείο .pdf</label>
                    <input type="file" class="form-control-file" id="filePdf" name="filePdf" required>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Υποβολή .pdf</button>
            </form>
        </div>
    </div>
    @if($old_data)
    <div class="row">
        <div class="col-md-6 p-3">
            <h6>Τρέχον αρχείο:</h6>
            @if($old_data->fileXlsx)
            <form action="{{route('two_files.download_file',['file'=>"TwoFiles_$schoolCode.xlsx", 'download_file_name' => $old_data->fileXlsx])}}" method="get"class="container-fluid">
                <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="">{{$old_data->fileXlsx}}</button>
            </form>
            @else
            <p>Δεν έχει υποβληθεί αρχείο .xlsx</p>
            @endif
        </div>
        <div class="col-md-6 p-3">
            <h6>Τρέχον αρχείο:</h6>
            @if($old_data->filePdf)
            <form action="{{route('two_files.download_file',['file'=>"TwoFiles_$schoolCode.pdf", 'download_file_name' => "$old_data->filePdf"])}}" method="get"class="container-fluid">
                <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="">{{$old_data->filePdf}}</button>
            </form>
            @else
            <p>Δεν έχει υποβληθεί αρχείο .pdf</p>
            @endif
        </div>
    </div>
    @endif
</div>

</x-layout_school>