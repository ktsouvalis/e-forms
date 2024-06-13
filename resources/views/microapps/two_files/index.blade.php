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
    @endpush
    @php
        $microapp = App\Models\Microapp::where('url', '/two_files')->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        $two_files_schools = $microapp->stakeholders;
        //$two_files = App\Models\microapps\TwoFile::orderBy('created_at', 'desc')->get();
    @endphp
    @push('title')
        <title>{{$microapp->name}}</title>
    @endpush    
    @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
    <nav class="container navbar navbar-light bg-light">
        <div class="row">
            
            <div class="col-6 hstack gap-3"> 
            <form action="{{route("two_files.upload_file",['upload_file_name'=>"Two_Files_Template.xlsx"])}}" method="post" enctype="multipart/form-data" class="container-fluid">
                @csrf
                <div class="input-group">
                    <span class="input-group-text w-75"><strong>Αρχείο Excel για συμπλήρωση</strong></span>
                </div>
                <div class="input-group w-75">
                    <input name="file" type="file" class="form-control"><br>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                    <a href="{{route('two_files.index')}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                </div>
            </form>
            <form action="{{route('two_files.download_file', ['file' =>"Two_Files_Template.xlsx", 'download_file_name' => "Αρχείο_Δεικτών_για_συμπλήρωση.xlsx"] )}}" method="get">
                <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Αρχείο για συμπλήρωση </button>
            </form>
            </div>
        </div>
    </nav>
    <div class="table-responsive py-2">
        <table  id="dataTable" class="display table table-sm table-striped table-hover">
        <thead>
            <tr>
                <th id="search">Κωδικός</th>
                <th id="search">Σχολείο</th>
                <th id="search">Αρχείο xlsx</th>
                <th id="search">Αρχείο pdf</th>
                <th id="search">Ημερομηνία</th>
            </tr>
        </thead>
        <tbody>
            @foreach($two_files_schools as $school)
            {{-- dd($school->stakeholder); --}}
            <tr>
                <td>{{$school->stakeholder->code}}</td>
                <td>{{$school->stakeholder->name}}</td>
                @if($school->stakeholder->twoFile)
                    @php
                        $date = DateTime::createFromFormat('Y-m-d H:i:s', $school->stakeholder->twoFile->updated_at);
                        $timestamp = $date->getTimestamp();
                        $schoolCode = $school->stakeholder->code;

                    @endphp
                    
                        <td>
                            @if($school->stakeholder->twoFile->fileXlsx) 
                            <form action="{{route('two_files.download_file',['file'=>"TwoFiles_$schoolCode.xlsx", 'download_file_name' => $school->stakeholder->twoFile->fileXlsx])}}" method="get"class="container-fluid">
                                <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="">{{$school->stakeholder->twoFile->fileXlsx}}</button>
                            </form>
                            @endif
                        </td>
                        <td>
                            @if($school->stakeholder->twoFile->filePdf) 
                            <form action="{{route('two_files.download_file',['file'=>"TwoFiles_$schoolCode.pdf", 'download_file_name' => $school->stakeholder->twoFile->filePdf])}}" method="get"class="container-fluid">
                                <button class="btn btn-secondary bi bi-box-arrow-down" data-bs-toggle="tooltip" data-bs-placement="top" title="">{{$school->stakeholder->twoFile->filePdf}}</button>
                            </form>
                            @endif
                        </td>
                        <td>{{date('d/m/Y H:i:s', $timestamp)}}</td>
                @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                 
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>


</x-layout>