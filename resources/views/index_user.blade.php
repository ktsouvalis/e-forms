<x-layout>
    <body>
        <div class="row px-5 justify-content-evenly">
        @auth
            @push('scripts')
                <script src="{{asset('/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
                <script>
                    $(document).ready(function() {
                        $('a[data-toggle="modal1"]').on('click', function (event) {
                            event.preventDefault();
                            $('#monthModal').modal('show');
                        });
                        $('a[data-toggle="modal2"]').on('click', function (event) {
                            event.preventDefault();
                            $('#commandsModal').modal('show');
                        });
                    });
                </script>
            @endpush
            @push('title')
                <title>Φόρμες Υποβολής Στοιχείων</title>
            @endpush
            @php
                $user =  Illuminate\Support\Facades\Auth::user();
                // $fileshares = App\Models\Fileshare::all();
                // $filecollects = App\Models\Filecollect::all();
               
                if($user->isAdmin()){
                    $operations=App\Models\Operation::orderBy('menu_priority','ASC')->get(); //$operations is Operation model
                    $microapps=App\Models\Microapp::all(); //$microapps is Microapp model
                    $super_admin=true;
                }
                else {
                    $operations=$user->operations; //$operations is UsersOperations model
                    $microapps=$user->microapps; // $microapps is MicroappUser model
                    $super_admin=false;
                }      
            @endphp

            <div class="">
                @include('modals.month')
                @include('modals.commands')

                <div class="row hidden-md-up justify-content-left">
                    @if($user->isAdmin())
                        <div class="col-md-4 py-3" style="max-width:15rem">
                            <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
                                <a class="text-dark" style="text-decoration:none;" href="{{route("users.index")}}">
                                <div class="h5 card-title fa-solid fa-users"></div>
                                <div>Χρήστες Διεύθυνσης</div>
                                </a> 
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Μενού με βάση τα δικαιωματα πρόσβασης που έρχονται από τον πίνακα operations --}}
                <div class="row hidden-md-up justify-content-left">
                    @if($user->isAdmin())
                    
                        <div class="col-md-4 py-3" style="max-width:15rem">
                            <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
                                <a class="text-dark" style="text-decoration:none;" href="{{route('operations.index')}}">
                                <div class="h5 card-title fa-solid fa-toolbox"></div>
                                <div>Διαχείριση Λειτουργιών</div>
                                </a> 
                            </div>
                        </div>
                    @endif
                    @foreach ($operations as $operation)
                        @php
                            if(!$super_admin){
                                $one_operation = $operation->operation;
                            }
                            else{
                                $one_operation = $operation;
                            }
                        @endphp
                        <div class="col-md-4 py-3" style="max-width:15rem">
                            <div class="card" style="background-color:{{$one_operation->color}}; font-size:small">
                                <div style="width: 16px; height: 16px;">
                                @if ($user->isAdmin())
                                {{-- <a class="text-dark bi bi-pencil px-1" style="text-decoration:none;" href="{{ url("/manage/operations/".$one_operation->id."/edit") }}"></a> --}}
                                <a class="text-dark bi bi-pencil px-1" style="text-decoration:none;" href="{{ route('operations.edit', $one_operation->id) }}"></a>
                                @endif
                                
                                </div>
                                <div class="py-2" style="text-align:center">
                                @if($one_operation->url == "/month")
                                    <a  class="text-dark no-spinner" style="text-decoration:none;" href="#" data-toggle="modal1" data-target="#monthModal">
                                    <div class="h5 card-title {{$one_operation->icon}}"></div>
                                    <div >{{$one_operation->name}}</div>
                                    </a> 
                                @else
                                    @if($one_operation->url == "/commands")
                                        <a  class="text-dark no-spinner" style="text-decoration:none;" href="#" data-toggle="modal2" data-target="#commandsModal">
                                        <div class="h5 card-title {{$one_operation->icon}}"></div>
                                        <div >{{$one_operation->name}}</div>
                                        </a>
                                    @else
                                        <a  class="text-dark" style="text-decoration:none;" href="{{url($one_operation->url)}}">
                                            <div class="h5 card-title {{$one_operation->icon}}"></div>
                                            <div >{{$one_operation->name}}</div>
                                        </a>
                                    @endif
                                @endif 
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Μενού με βάση τα δικαιωματα πρόσβασης που έρχονται από τον πίνακα fileshares --}}
                <div class="row hidden-md-up justify-content-left">
                    <div class="col-md-4 py-3" style="max-width:15rem">
                        <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
                            <a class="text-dark" style="text-decoration:none;" href="{{url("/fileshares")}}">
                            <div class="h5 card-title fa-solid fa-file-pdf"></div>
                            <div>Διαμοιρασμός Αρχείων</div>
                            </a> 
                        </div>
                    </div>

                    @foreach($user->department->fileshares as $fileshare)
                    <div class="col-md-4 py-3" style="max-width:15rem">
                        <div class="card py-3" style="background-color:#00bfff; text-decoration:none; text-align:center; font-size:small">
                            @php
                                $fi = $fileshare->id;
                            @endphp
                            <a class="text-dark" style="text-decoration:none;" href="{{url("/fileshares/$fi/edit")}}">
                            <div class="h5 card-title fa-solid fa-file-pdf"></div>
                            <div>{{$fileshare->name}}</div>
                            </a> 
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="row hidden-md-up justify-content-left">
                    <div class="col-md-4 py-3" style="max-width:15rem">
                        <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
                            <a class="text-dark" style="text-decoration:none;" href="{{url("/filecollects")}}">
                            <div class="h5 card-title bi bi-filetype-xls"></div>
                            <div>Συλλογή Αρχείων</div>
                            </a> 
                        </div>
                    </div>
                    @foreach($user->department->filecollects as $filecollect)
                        <div class="col-md-4 py-3" style="max-width:15rem">
                            <div class="card py-3" style="background-color:#4bac97; text-decoration:none; text-align:center; font-size:small">
                                @php
                                    $fi = $filecollect->id;
                                @endphp
                                <a class="text-dark" style="text-decoration:none;" href="{{url("/filecollects/$fi/edit")}}">
                                <div class="h5 card-title fa-solid fa-file-pdf"></div>
                                <div>{{$filecollect->name}}</div>
                                </a> 
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Μενού με βάση τα δικαιωματα πρόσβασης που έρχονται από τον πίνακα microapps --}}
                <div class="row hidden-md-up justify-content-left">
                    <div class="col-md-4 py-3" style="max-width:15rem">
                        <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
                            {{-- <a class="text-dark" style="text-decoration:none;" href="{{url("/manage/microapps")}}"> --}}
                            <a class="text-dark" style="text-decoration:none;" href="{{route('microapps.index')}}">
                            <div class="h5 card-title fa-solid fa-microchip"></div>
                            <div>Μικροεφαρμογές</div>
                            </a> 
                        </div>
                    </div>
                    
                    @foreach ($microapps as $microapp)
                    @php
                        if(!$super_admin){
                            $one_microapp = $microapp->microapp;
                        }
                        else{
                            $one_microapp = $microapp;
                        }
                    @endphp
                    @if($one_microapp->active)
                        <div class="col-md-4 py-3" style="max-width:15rem">
                            <div class="card" style="background-color:{{ $one_microapp->color }}; font-size:small">
                                <div style="width: 16px; height: 16px;">
                                @if ($user->microapps->where('microapp_id', $one_microapp->id)->where('can_edit', 1)->first() || $user->isAdmin())
                                {{-- <a class="text-dark bi bi-pencil px-1" style="text-decoration:none;" href="{{ url("/manage/microapps/".$one_microapp->id."/edit") }}"></a> --}}
                                <a class="text-dark bi bi-pencil px-1" style="text-decoration:none;" href="{{ route('microapps.edit', $one_microapp->id) }}"></a>
                                @endif
                                </div>
                                <div class="py-2" style="text-align:center">
                                    @php $resource = substr($one_microapp->url, 1); @endphp
                                    {{-- <a class="text-dark" style="text-decoration:none;" href="{{ url($one_microapp->url) }}"> --}}
                                    {{-- <a class="text-dark" style="text-decoration:none;" href="{{ route("$resource.index") }}"> --}}
                                    <a class="text-dark" style="text-decoration:none;" href="{{ Route::has("$resource.index") ? route("$resource.index") : '#' }}">
                                        {{-- <div class="h5 card-title {{ $one_microapp->icon }}"></div> --}}
                                        <div class="h5 card-title {{ $one_microapp->icon }}"></div>
                                        <div @if (!$one_microapp->active) style="color:red" @endif>{{ $one_microapp->name }}</div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    @endforeach
                </div>
            </div>
        @else
            @push('title')
                    <title>Σύνδεση</title>
            @endpush
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <img src="{{ asset('favicon/android-chrome-512x512.png') }}" alt="Logo" width="80" class="mb-3">
                                    <h4 class="card-title">Σύνδεση Υπαλλήλου Διεύθυνσης</h4>
                                </div>
                                
                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                
                                <form method="POST" action="{{ url('/login') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Όνομα Χρήστη</label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                               id="username" name="username" required>
                                        @error('username')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Κωδικός Πρόσβασης</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password" required>
                                        @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6 offset-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                                <label class="form-check-label" for="remember">
                                                    {{ __('Μόνιμη Σύνδεση') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-sign-in-alt me-2"></i>Σύνδεση
                                        </button>
                                    </div>
                                </form>
                                
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Αν έχετε ξεχάσει τον κωδικό σας, επικοινωνήστε με το Τμήμα Πληροφορικής.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="row justify-content-md-center">
            <div class="col">
                    
            </div>
            <div class="col p-3">
                    <img src="{{asset("favicon/android-chrome-512x512.png")}}" width="200" height="200" alt="forms">
            </div>
            <div class="col p-3">
                <form action="{{url("/login")}}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">Όνομα Χρήστη</label>
                        <div class="">
                            <input type="text" value="{{old('username')}}" name="username" class="form-control">
                            @error('username')
                                {{$message}}
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Συνθηματικό</label>
                        <div class="">
                            <input type="password" name="password" class="form-control">
                            @error('password')
                                {{$message}}
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Είσοδος</button>
                </form>
            </div>
            <div class="col"></div> -->
        @endauth
        </div>
        </div>
</x-layout>