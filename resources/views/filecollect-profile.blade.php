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
        <script src="../canedit.js"></script>
        <script src="../copycolumn2.js"></script>
    @endpush
    @push('title')
        <title>{{$filecollect->name}}</title>
    @endpush
    @include('microapps.filecollect_admin_before')
        
        <div class="container">
            <nav class="navbar navbar-light bg-light">
                <div class="input-group">
                    <span class="input-group-text w-25"></span>
                    <span class="input-group-text w-75"><strong>Επεξεργασία Στοιχείων Συλλογής Αρχείων</strong></span>
                </div>
                <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Πρότυπο αρχείο (υπάρχον): </span>
                        <form action="{{url("/dl_filecollect_template/$filecollect->id")}}" method="post">
                            @csrf
                            <button class="btn btn-secondary bi bi-box-arrow-down"> Πρότυπο αρχείο </button>
                        </form>
                        <span class="input-group-text w-10">Ανανέωση Προτύπου: </span>
                        <input name="filecollect_original_file" type="file" class="form-control">
                        
                        {{-- <a href="{{url('app/filecollects/42/12consultant_programm.xlsx')}}">Πρότυπο αρχείο</a> --}}
                        <br>
                    </div>
                <form action="{{url("/filecollect_save/$filecollect->id")}}" method="post" class="container-fluid" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="asks_to" value="insert">
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Τίτλος</span>
                        <input name="name" type="text" class="form-control" placeholder="Name" aria-label="Name" aria-describedby="basic-addon2" required value="{{$filecollect->name}}"><br>
                    </div>
                    
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Τύπος Δεκτών Αρχείων</span>
                        <select name="filecollect_mime" class="form-control" required>
                            <option value="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 
                            @if($filecollect->fileMime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') selected @endif
                            >Excel (.xlsx)</option>
                            <option value="application/pdf"
                            @if($filecollect->fileMime == 'application/pdf') selected @endif
                            >Pdf (.pdf)</option>
                            <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            @if($filecollect->fileMime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') selected @endif
                            >Word (.docx)</option>
                        </select>
                    </div>
            </nav> 
        <hr>
        <div class="input-group">
            <span class="input-group-text w-25" id="basic-addon5">Χρήστες</span>
            @php
                $users = App\Models\User::all();   
            @endphp
            <table>
            @foreach($users as $user)
            @if(!App\Models\Superadmin::where('user_id',$user->id)->exists())
                @php
                    $checked_checkbox="";
                    $checked_radio_can="";
                    $checked_radio_cant="";
                    
                    if($filecollect->users->where('user_id', $user->id)->count()){
                        $checked_checkbox="checked";
                        if($filecollect->users->where('user_id', $user->id)->first()->can_edit){
                            $checked_radio_can = "checked";  
                        }
                        else{
                            $checked_radio_cant = "checked";    
                        }
                    }  
                @endphp
            <tr>
                <td>
                <div class="hstack gap-2">
                    <div class="form-check form-switch">
                    <input class="form-check-input" role="switch" type="checkbox" name="user{{$user->id}}" value="{{$user->id}}" id="user{{$user->id}}" onChange="show_edit_option({{$user->id}}, '{{$checked_radio_can}}')" {{$checked_checkbox}}>
                    @php
                        $existed_user = $filecollect->users->where('user_id', $user->id);
                    @endphp
                    <label for="user{{$user->id}}">@if($existed_user->count() and $existed_user->first()->can_edit) <strong> @endif  {{$user->display_name}}</strong> </label>
                    <div id="space{{$user->id}}">
                        
                    </div>
                    </div>
                </div>
                </td>
            </tr>
            @endif
            @endforeach
            </table>
        </div>
        <div class="input-group">
            <span class="w-25"></span>
            <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση αλλαγών</button>
            <a href="{{url("/filecollect_profile/$filecollect->id")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
        </div>
    </form>
        <hr>
        <nav class="navbar navbar-light bg-light">
                <form action="{{url("/import_whocan/filecollect/$filecollect->id")}}" method="post" class="container-fluid">
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
            </nav> 
        </div>
        <div class="container px-5 vstack gap-2 py-3">
            
            @if($filecollect->stakeholders->count())
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
                @foreach($filecollect->stakeholders as $one_stakeholder)
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
                    @if($one_stakeholder->visited_filecollect)
                        <td>ΝΑΙ</td>
                    @else
                        <td>ΟΧΙ</td>
                    @endif
                    <td> 
                        <form action="{{url("/delete_one_whocan/filecollect/$one_stakeholder->id")}}" method="post">
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
                <a href="{{url("/preview_mail_all_whocans/filecollect/$filecollect->id")}}" class="btn btn-outline-secondary bi bi-binoculars" target="_blank"> Προεπισκόπηση email</a>
                <form action="{{url("/send_mail_all_whocans/filecollect/$filecollect->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> Αποστολή email σε όλους</button>
                </form>
                <form action="{{url("/delete_all_whocans/filecollect/$filecollect->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger bi bi-x-circle" onclick="return confirm('Επιβεβαίωση διαγραφής stakeholders!')"> Διαγραφή όλων</button>
                </form>
            </div>
            @endif 
            <hr>
        </div> 
        
       
        @php
        $directory_common = '/filecollect'.$filecollect->id;
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
                    <div class="hstack gap-1">
                        @php
                            $basename = basename($file_c);
                        @endphp
                        <form action="{{url("/get_file/$filecollect->id/$basename")}}" method="post">
                        @csrf
                            <input type="hidden" name="personal" value="0">
                            <button class="btn btn-secondary bi bi-box-arrow-down"> {{$basename}}</button>
                        </form>
                        <form action="{{url("/del_file/$filecollect->id/$basename")}}" method="post">
                        @csrf
                            <input type="hidden" name="personal" value="0">
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
                    <div class="hstack gap-1">
                        @php
                            $basename = basename($file_p);
                        @endphp
                        <form action="{{url("/get_file/$filecollect->id/$basename")}}" method="post">
                        @csrf
                            <input type="hidden" name="personal" value="1">
                            <button class="btn btn-secondary bi bi-box-arrow-down"> {{$basename}}</button>
                        </form>
                        <form action="{{url("/del_file/$filecollect->id/$basename")}}" method="post">
                        @csrf
                            <input type="hidden" name="personal" value="1">
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