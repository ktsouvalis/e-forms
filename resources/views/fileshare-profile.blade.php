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
        <script src="../copylink.js"></script>
        <script>
            $(document).ready(function() {
                $('body').on('change', '.fileshare-checkbox', function() {
                    
                    const fileshareId = $(this).data('fileshare-id');
                    const isChecked = $(this).is(':checked');
                    // Get the CSRF token from the meta tag
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    $.ajax({
                        url: '../fileshare_allow_schools/'+fileshareId,
                        type: 'POST',
                        data: {
                            // _method: 'PATCH', // Laravel uses PATCH for updates
                            checked: isChecked
                        },
                        success: function(response) {
                            // Handle the response here, update the page as needed
                            console.log("success");

                        },
                        error: function(error) {
                            // Handle errors
                            console.log("An error occurred: " + error);
                        }
                    });
                });
            });
        </script>
    @endpush
    @push('title')
        <title>{{$fileshare->name}}</title>
    @endpush
    
        
        <div class="container">
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
                        <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση αλλαγών</button>
                        <a href="{{url("/fileshare_profile/$fileshare->id")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                    
                </form>
            </nav> 
        <hr>
        <nav class="navbar navbar-light bg-light">
                <form action="{{url("/import_whocan/fileshare/$fileshare->id")}}" method="post" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Ενδιαφερόμενοι</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Name</span>
                        <textarea name="afmscodes"  class="form-control" cols="122" rows="5" style="resize: none;" placeholder="ΑΦΜ εκπαιδευτικών ή/και κωδικοί σχολείων χωρισμένα με κόμμα (,)" required></textarea>
                    </div>
                    <div class="input-group py-1 px-1">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-primary bi bi-database-add"> Εισαγωγή Σχολείων/Εκπαιδευτικών</button>
                    </div>
                </form>
                <form action="{{url("/auto_update_fileshare_whocan/$fileshare->id")}}" method="post" class="container-fluid">
                    @csrf
                    <div class="input-group py-1 px-1">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-warning bi bi-database-add"> Αυτόματη Εισαγωγή Ενδιαφερόμενων για τα προσωπικά αρχεία</button>
                    </div>
                </form>
            </nav> 
            <input type="checkbox" id="allow" class="fileshare-checkbox" data-fileshare-id="{{ $fileshare->id }}" {{ $fileshare->allow_school ? 'checked' : '' }}>
            <label for="allow"> <strong> Τα σχολεία μπορούν να προσθέτουν τους εκαιδευτικούς στους ενδιαφερόμενους του fileshare;</strong></label> 
        </div>
        <div class="container px-5 vstack gap-2 py-3">
            
            @if($fileshare->stakeholders->count())
            <div class="table-responsive">
                <table  id="dataTable" class="align-middle table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th>Σύνδεσμος</th>
                        <th id="search">Αναγνωριστικό</th>
                        <th id="search">name</th>
                        <th id="search">mail</th>
                        <th id="search">added by</th>
                        <th id="search">visited</th>
                        <th class="align-middle">Διαγραφή</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($fileshare->stakeholders as $one_stakeholder)
                @php
                    $md = $one_stakeholder->stakeholder->md5;
                    if($one_stakeholder->stakeholder_type=="App\Models\School")
                        $text = url("/school/$md");
                    else
                        $text = url("/teacher/$md");
                @endphp
                <tr>
                    <td style="text-align:center">
                        <button class="copy-button btn btn-outline-secondary bi bi-clipboard" data-clipboard-text="{{$text}}"> </button>
                    </td>
                    @if($one_stakeholder->stakeholder_type=="App\Models\School")
                        <td>{{$one_stakeholder->stakeholder->code}}</td>
                    @else
                        <td>{{$one_stakeholder->stakeholder->afm}}</td>
                    @endif
                    <td>{{$one_stakeholder->stakeholder->surname}} {{$one_stakeholder->stakeholder->name}}</td>
                    <td>{{$one_stakeholder->stakeholder->mail}}</td>
                    @if($one_stakeholder->addedby_type=="App\Models\School")
                        <td>{{$one_stakeholder->addedby->name}}</td>
                    @else
                        <td>{{$one_stakeholder->addedby->username}}</td>
                    @endif
                    @if($one_stakeholder->visited_fileshare)
                        <td>ΝΑΙ</td>
                    @else
                        <td>ΟΧΙ</td>
                    @endif
                    <td> 
                        <form action="{{url("/delete_one_whocan/fileshare/$one_stakeholder->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger bi bi-x-circle"> </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
                </table>
            </div> 
            <div class="hstack gap-2">
                <a href="{{url("/preview_mail_all_whocans/fileshare/$fileshare->id")}}" class="btn btn-outline-secondary bi bi-binoculars" target="_blank"> Προεπισκόπηση email</a>
                <form action="{{url("/send_mail_all_whocans/fileshare/$fileshare->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> Αποστολή email σε όλους</button>
                </form>
                <form action="{{url("/delete_all_whocans/fileshare/$fileshare->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger bi bi-x-circle" onclick="return confirm('Επιβεβαίωση διαγραφής stakeholders!')"> Διαγραφή όλων</button>
                </form>
            </div>
            @endif 
            <hr>
        </div> 
        
       
        @php
        $directory_common = '/fileshare'.$fileshare->id;
        $directory_personal = $directory_common.'/personal_files';
        $files_common=Storage::disk('local')->files($directory_common);
        $files_personal = Storage::disk('local')->files($directory_personal);
        $not_found = Session::pull('not_found', []);
        @endphp
        @if($not_found)
            <div class='container container-narrow'>
                <div class='alert alert-warning'>
                    <strong>Αναγνωριστικά που δεν βρέθηκαν</strong><br>
                    @isset($not_found)
                        @foreach($not_found as $identifier)
                            {{$identifier}}
                            <br>
                        @endforeach  
                    @endisset
                </div>
            </div>
        <hr>
        @endif
        <div class="container px-5 py-3">
        <div class="hstack">
            @if($files_common)
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
            @endif
            @if($files_personal)
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
            @endif
        </div>
        <hr>
        @if(session()->has('stakeholders_array'))
            @php
                $stakeholders_array = Session::pull('stakeholders_array', []);
            @endphp
            <table>
                <tr>
                <th>Αρχείο</th>
                <th>Ενδιαφερόμενος</th>
                </tr>
            @foreach($stakeholders_array as $one)
            
            <tr>
                <td>{{$one['filename']}}</td>
                <td>{{ isset($one['stakeholder']) ? $one['stakeholder'] : 'N/A' }}</td>
            </tr>
            @endforeach
            </table>
        @endif
    </div>
       
</x-layout>