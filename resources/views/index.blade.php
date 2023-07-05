<x-layout>
    <body class="bg-light">
    <div class="container ">
        
        <div class="row p-2 justify-content-evenly">
        @auth
            @push('title')
                <title>Αρχική</title>
            @endpush
            @php
                $user =  Illuminate\Support\Facades\Auth::user();
                $fileshares = App\Models\Fileshare::all();
            @endphp

            <div class="py-5">
                <div class="container">
                    
                    <div class="row hidden-md-up justify-content-left">
                        @if(App\Models\Superadmin::where('user_id',$user->id)->exists())
                        <hr>
                            <div class="col-md-4 py-2" style="max-width:15rem">
                                <div class="card py-5" style="background-color:Gainsboro; text-decoration:none; text-align:center;">
                                    <a class="text-dark" style="text-decoration:none;" href="{{url("/manage_users")}}">
                                    <div class="h5 card-title fa-solid fa-users"></div>
                                    <div>Χρήστες Διεύθυνσης</div>
                                    </a> 
                                </div>
                            </div>
                        @endif
                    </div>

                     {{-- Μενού με βάση τα δικαιωματα πρόσβασης που έρχονται από τον πίνακα operations --}}
                    <hr>
                    <div class="row hidden-md-up justify-content-left">
                        @if(App\Models\Superadmin::where('user_id',$user->id)->exists())
                        
                            <div class="col-md-4 py-2" style="max-width:15rem">
                                <div class="card py-5" style="background-color:Gainsboro; text-decoration:none; text-align:center;">
                                    <a class="text-dark" style="text-decoration:none;" href="{{url("/manage_operations")}}">
                                    <div class="h5 card-title fa-solid fa-toolbox"></div>
                                    <div>Διαχείριση Λειτουργιών</div>
                                    </a> 
                                </div>
                            </div>
                        @endif
                        @foreach ($user->operations as $one_operation)
                            <div class="col-md-4 py-2" style="max-width:15rem">
                                <div class="card py-5" style="background-color:{{$one_operation->operation->color}}; text-align:center;">
                                    <a  class="text-dark" style="text-decoration:none;" href="{{url($one_operation->operation->url)}}">
                                    <div class="h5 card-title {{$one_operation->operation->icon}}"></div>
                                    <div >{{$one_operation->operation->name}}</div>
                                    </a> 
                                </div>
                            </div>  
                        @endforeach
                    </div>

                    {{-- Μενού με βάση τα δικαιωματα πρόσβασης που έρχονται από τον πίνακα fileshares --}}
                    <hr>
                    <div class="row hidden-md-up justify-content-left">
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:Gainsboro; text-decoration:none; text-align:center;">
                                <a class="text-dark" style="text-decoration:none;" href="{{url("/fileshares")}}">
                                <div class="h5 card-title fa-solid fa-file-pdf"></div>
                                <div>Διαμοιρασμός Αρχείων</div>
                                </a> 
                            </div>
                        </div> 

                        @foreach($user->department->fileshares as $fileshare)
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:#00bfff; text-decoration:none; text-align:center;">
                                @php
                                    $fi = $fileshare->id;
                                @endphp
                                <a class="text-dark" style="text-decoration:none;" href="{{url("/fileshare_profile/$fi")}}">
                                <div class="h5 card-title fa-solid fa-file-pdf"></div>
                                <div>{{$fileshare->department->name}}: {{$fileshare->name}}</div>
                                </a> 
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Μενού με βάση τα δικαιωματα πρόσβασης που έρχονται από τον πίνακα microapps --}}
                    <hr>
                    <div class="row hidden-md-up justify-content-left">
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:Gainsboro; text-decoration:none; text-align:center;">
                                <a class="text-dark" style="text-decoration:none;" href="{{url("/microapps")}}">
                                <div class="h5 card-title fa-solid fa-microchip"></div>
                                <div>Μικροεφαρμογές</div>
                                </a> 
                            </div>
                        </div>
                        
                        @foreach ($user->microapps as $one_microapp)
                        @can('beViewedByAdmins', $one_microapp->microapp)
                            <div class="col-md-4 py-2" style="max-width:15rem">
                                <div class="card py-5" style="background-color:{{$one_microapp->microapp->color}}; text-align:center;">
                                    <a  class="text-dark" style="text-decoration:none;" href="{{url("/admin".$one_microapp->microapp->url)}}">
                                    <div class="h5 card-title {{$one_microapp->microapp->icon}}"></div>
                                    <div @if(!$one_microapp->microapp->active) style="color:red" @endif>@if(!$one_microapp->microapp->active) <strong> @endif{{$one_microapp->microapp->name}}</strong></div>
                                    </a> 
                                </div>
                            </div>  
                        @endcan
                        @endforeach
                    </div>
                    <hr>
                    <div class="row hidden-md-up justify-content-left">
                    {{-- Μενού για όλους --}}
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:Gainsboro; text-decoration:none; text-align:center;">
                                <a class="text-dark" href="{{url("/logout")}}">
                                <div class="h5 card-title fa-solid fa-arrow-right-from-bracket"></div>
                                <div>Αποσύνδεση</div>
                                </a> 
                            </div>
                        </div>
                    </div>
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
                    <img src="{{url("/favicon/index.png")}}" width="200" height="200" alt="forms">
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
                        <label for="password" class="form-label">Κωδικός</label>
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