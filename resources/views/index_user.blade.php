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
                    $operations=App\Models\Operation::all(); //$operations is Operation model
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
                                <a class="text-dark" style="text-decoration:none;" href="{{url("/manage_users")}}">
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
                                <a class="text-dark" style="text-decoration:none;" href="{{url("/manage/operations")}}">
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
                                <a class="text-dark bi bi-pencil px-1" style="text-decoration:none;" href="{{ url("/operation_profile/".$one_operation->id) }}"></a>
                                @endif
                                
                                </div>
                                <div class="py-2" style="text-align:center">
                                @if($one_operation->url == "/month")
                                    <a  class="text-dark" style="text-decoration:none;" href="#" data-toggle="modal1" data-target="#monthModal">
                                    <div class="h5 card-title {{$one_operation->icon}}"></div>
                                    <div >{{$one_operation->name}}</div>
                                    </a> 
                                @else
                                    @if($one_operation->url == "/commands")
                                        <a  class="text-dark" style="text-decoration:none;" href="#" data-toggle="modal2" data-target="#commandsModal">
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
                            <a class="text-dark" style="text-decoration:none;" href="{{url("/manage/microapps")}}">
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
                                <a class="text-dark bi bi-pencil px-1" style="text-decoration:none;" href="{{ url("/microapp_profile/".$one_microapp->id) }}"></a>
                                @endif
                                </div>
                                <div class="py-2" style="text-align:center">
                                    <a class="text-dark" style="text-decoration:none;" href="{{ url("/microapps".$one_microapp->url) }}">
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
            <div class="row justify-content-md-center">
            <div class="col">
                    
            </div>
            <div class="col p-3">
                    <img src="{{url("/favicon/android-chrome-512x512.png")}}" width="200" height="200" alt="forms">
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
            <div class="col"></div>
        @endauth
        </div>
        </div>
</x-layout>