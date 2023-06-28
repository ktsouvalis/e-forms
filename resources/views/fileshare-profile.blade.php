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
    @endpush
    @push('title')
        <title>{{$fileshare->name}}</title>
    @endpush
    <div class="container py-5">
        
        <div class="container px-5">
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/fileshare_save/$fileshare->id")}}" method="post" class="container-fluid" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="asks_to" value="insert">
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Επεξεργασία Στοιχείων Διαμοιρασμού Αρχείων</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Τίτλος</span>
                        <input name="name" type="text" class="form-control" placeholder="Name" aria-label="Name" aria-describedby="basic-addon2" required value="{{$fileshare->name}}"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Κοινά αρχεία</span>
                        <input name="fileshare_common_files[]" type="file" class="form-control" multiple ><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Προσωπικά αρχεία</span>
                        <input name="fileshare_personal_files[]" type="file" class="form-control" multiple><br>
                    </div>
                    
                    <div class="input-group">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση</button>
                        <a href="{{url("/fileshare_profile/$fileshare->id")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                    
                </form>
            </nav>
            
            
        <hr>
        </div>
        <div class="container px-5">
            <div>
                <div class="hstack gap-2">
                <a href="{{url("/import_whocan/fileshare/$fileshare->id")}}" class="btn btn-primary"> Εισαγωγή Stakeholders</a>
                @if($fileshare->stakeholders->count())
                <form action="{{url("/send_mail_all_whocans/fileshare/$fileshare->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> Αποστολή email σε όλους τους ενδιαφερόμενους</button>
                </form>
                <form action="{{url("/delete_all_whocans/fileshare/$fileshare->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger bi bi-x-circle" onclick="return confirm('Επιβεβαίωση διαγραφής stakeholders!')"> Διαγραφή Stakeholders</button>
                </form>
                @endif
                </div>
            </div>
            <div class="table-responsive">
                <table  id="dataTable" class="table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th id="search">Αναγνωριστικό</th>
                        <th id="search">name</th>
                        <th id="search">Διαγραφή</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($fileshare->stakeholders as $one_stakeholder)
                <tr>
                    @if($one_stakeholder->stakeholder_type=="App\Models\School")
                        <td>{{$one_stakeholder->stakeholder->code}}</td>
                    @else
                        <td>{{$one_stakeholder->stakeholder->afm}}</td>
                    @endif
                    <td>{{$one_stakeholder->stakeholder->surname}} {{$one_stakeholder->stakeholder->name}}</td>
                    <td> 
                        <form action="{{url("/delete_one_whocan/fileshare/$one_stakeholder->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-danger bi bi-x-circle"> </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
                </table>
            </div>  
        </div> 
        <hr>
        @php
        $directory_common = '/fileshare'.$fileshare->id;
        $directory_personal = $directory_common.'/personal_files';
        $files_common=Storage::disk('local')->files($directory_common);
        $files_personal = Storage::disk('local')->files($directory_personal);
        @endphp
        
        <div class="hstack">
            <div class="vstack gap-2">
                <strong>Αρχεία κοινά για διαμοιρασμό</strong>
                    @foreach($files_common as $file_c)
                        @php
                            $fi = $fileshare->id
                        @endphp
                        <div class="hstack gap-1">
                        <form action="{{url("/dl_file/$fi")}}" method="post">
                        @csrf
                            <input type="hidden" name="filename" value="{{$file_c}}">
                            <button class="btn btn-secondary bi bi-box-arrow-down"> {{basename($file_c)}}</button>
                        </form>
                        <form action="{{url("/x_file/$fi")}}" method="post">
                        @csrf
                            <input type="hidden" name="filename" value="{{$file_c}}">
                            <button class="btn btn-danger bi bi-x-circle"></button>
                        </form>
                    </div>
                    @endforeach
            </div>
            <div class="vstack gap-2">
               <strong>Αρχεία προσωπικά για διαμοιρασμό</strong>
                    @foreach($files_personal as $file_p)
                        @php
                            $fi = $fileshare->id
                        @endphp
                        <div class="hstack gap-1">
                        <form action="{{url("/dl_file/$fi")}}" method="post">
                        @csrf
                            <input type="hidden" name="filename" value="{{$file_p}}">
                            <button class="btn btn-secondary bi bi-box-arrow-down"> {{basename($file_p)}}</button>
                        </form>
                        <form action="{{url("/x_file/$fi")}}" method="post">
                        @csrf
                            <input type="hidden" name="filename" value="{{$file_p}}">
                            <button class="btn btn-danger bi bi-x-circle"></button>
                        </form>
                        </div>
                    @endforeach
            </div>
        </div>
    </div>    
</x-layout>