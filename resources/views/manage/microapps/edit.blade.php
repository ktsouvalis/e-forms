<x-layout>
    @push('links')
        <link href="{{ asset('DataTables-1.13.4/css/dataTables.bootstrap5.css') }}" rel="stylesheet"/>
        <link href="{{ asset('Responsive-2.4.1/css/responsive.bootstrap5.css') }}" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="{{ asset('DataTables-1.13.4/js/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('DataTables-1.13.4/js/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/dataTables.responsive.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/responsive.bootstrap5.js') }}"></script>
        <script src="{{ asset('datatable_init.js') }}"></script>
        <script src="{{ asset('canedit.js') }}"></script>
    @endpush

    @push('title')
    <title>Επεξεργασία χαρακτηριστικών {{$microapp->name}}</title>
    @endpush
    @include('microapps.microapps_admin_before')
    <div class="container">
        @if(Auth::user()->isAdmin())
            <hr>
            <nav class="navbar navbar-light bg-light">
                {{-- <form action="{{url("/manage/microapps/$microapp->id")}}" method="post" class="container-fluid"> --}}
                <form action="{{route('microapps.update', $microapp->id)}}" method="post" class="container-fluid">
                    @method('PUT')
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Επεξεργασία Χαρακτηριστικών Μικροεφαρμογής</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Name</span>
                        <input name="name" type="text" class="form-control" placeholder="Name" aria-label="Name" aria-describedby="basic-addon2" required value="{{$microapp->name}}"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon3">Url</span>
                        <input name="url" type="text" class="form-control" placeholder="Url" aria-label="Url" aria-describedby="basic-addon3" required value="{{$microapp->url}}"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon4">Color</span>
                        <input name="color" type="text" class="form-control" placeholder="Color" aria-label="color" aria-describedby="basic-addon4" required value="{{$microapp->color}}" ><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon5">Icon</span>
                        <input name="icon" type="text" class="form-control" placeholder="Icon" aria-label="Icon" aria-describedby="basic-addon5" required value="{{$microapp->icon}}" ><br>
                    </div>
                    <input type="hidden" name="microapp_id" value="{{$microapp->id}}">
                    @can('addUser', App\Models\Microapp::class)
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon5">Χρήστες</span>
                        @php
                            $all_users = App\Models\User::all();
                        @endphp
                        <table>
                        @foreach($all_users as $user)
                            @if(!App\Models\Superadmin::where('user_id',$user->id)->exists())
                                @php
                                
                                    $checked_checkbox="";
                                    $checked_radio_can="";
                                    $checked_radio_cant="";
                                    
                                    if($microapp->users->where('user_id', $user->id)->count()){
                                        $checked_checkbox="checked";
                                        if($microapp->users->where('user_id', $user->id)->first()->can_edit){
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
                                        $existed_user = $microapp->users->where('user_id', $user->id);
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
                    @endcan
                    <div class="input-group">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση αλλαγών</button>
                        {{-- <a href="{{url("/manage/microapps/$microapp->id/edit")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a> --}}
                        <a href="{{route('microapps.edit', $microapp->id)}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                    
                </form>
                
            </nav>
        @endif
            <hr>
            
            
            <nav class="navbar navbar-light bg-light">
                @php
                    $myapp ='microapp';
                    $myid = $microapp->id;
                @endphp
                <div class="vstack gap-3">
                @include('criteria_form')
                <form action="{{url("/import_whocan/microapp/$microapp->id")}}" method="post" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Ενδιαφερόμενοι (ΑΜ ή ΑΦΜ εκπαιδευτικών ή/και Κωδικοί Σχολείων)</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Name</span>
                        <textarea name="afmscodes"  class="form-control" cols="122" rows="5" style="resize: none;" placeholder="ΑΜ ή ΑΦΜ εκπαιδευτικών ή/και Κωδικοί Σχολείων χωρισμένα με κόμμα (,)" required></textarea>
                    </div>
                    <div class="input-group py-1 px-1">
                        <button type="submit" class="btn btn-primary bi bi-database-add"> Εισαγωγή Σχολείων/Εκπαιδευτικών</button>
                    </div>
                </form>
                </div>
            </nav>  
        <div class="vstack gap-2 py-3">
            @if($microapp->stakeholders->count())
            
            <div class="table-responsive py-3">
                {{-- <button class="btn btn-secondary bi bi-clipboard my-2" id="copyMailButton"> Αντιγραφή emails ενδιαφερόμενων</button> --}}
                <table  id="dataTable" class="align-middle display table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th id="search">Τύπος</th>
                        <th id="search">Αναγνωριστικό</th>
                        <th id="search">name</th>
                        <th class="align-middle">Διαγραφή</th>
                        <th class="search">email</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($microapp->stakeholders as $one_stakeholder)
                <tr>
                    @if($one_stakeholder->stakeholder_type=="App\Models\School")
                        <td>Σχολείο</td>
                        <td>{{$one_stakeholder->stakeholder->code}}</td>
                    @else
                        <td>Εκπαιδευτικός</td>
                        <td>{{$one_stakeholder->stakeholder->afm}}</td>
                    @endif
                    <td>{{$one_stakeholder->stakeholder->surname}} {{$one_stakeholder->stakeholder->name}}</td>
                    <td>
                        <form action="{{url("/delete_one_whocan/microapp/$one_stakeholder->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger bi bi-x-circle"> </button>
                        </form>
                    </td>
                    <td>{{$one_stakeholder->stakeholder->mail}}</td>
                    
                </tr>
                @endforeach
                </tbody>
                </table>
            </div>   
            <div class="hstack gap-2">
                <a href="{{url("/preview_mail_all_whocans/microapp/$microapp->id")}}" class="btn btn-outline-secondary bi bi-binoculars" target="_blank"> Προεπισκόπηση email</a>
                <form action="{{url("/send_mail_all_whocans/microapp/$microapp->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> Αποστολή email σε όλους </button>
                </form>
                <form action="{{url("/delete_all_whocans/microapp/$microapp->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger bi bi-x-circle" onclick="return confirm('Επιβεβαίωση διαγραφής stakeholders;')"> Διαγραφή όλων</button>
                </form>
            </div>
            @endif       
        </div>
        <hr>
        @php
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
        </div>
</x-layout>