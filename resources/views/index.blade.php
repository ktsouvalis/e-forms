<x-layout>
    <body>
        <div class="row px-5 justify-content-evenly">
        @auth
            @push('title')
                <title>Αρχική</title>
            @endpush
            @php
                $user =  Illuminate\Support\Facades\Auth::user();
                $fileshares = App\Models\Fileshare::all();
               
                if(App\Models\Superadmin::where('user_id',$user->id)->exists()){
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
                {{-- <div class="container"> --}}
                    
                    <div class="row hidden-md-up justify-content-left">
                        @if(App\Models\Superadmin::where('user_id',$user->id)->exists())
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
                    {{-- <hr> --}}
                    <div class="row hidden-md-up justify-content-left">
                        @if(App\Models\Superadmin::where('user_id',$user->id)->exists())
                        
                            <div class="col-md-4 py-3" style="max-width:15rem">
                                <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
                                    <a class="text-dark" style="text-decoration:none;" href="{{url("/manage_operations")}}">
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
                                <div class="card py-3" style="background-color:{{$one_operation->color}}; text-align:center; font-size:small">
                                    <a  class="text-dark" style="text-decoration:none;" href="{{url($one_operation->url)}}">
                                    <div class="h5 card-title {{$one_operation->icon}}"></div>
                                    <div >{{$one_operation->name}}</div>
                                    </a> 
                                </div>
                            </div>  
                        @endforeach
                    </div>

                    {{-- Μενού με βάση τα δικαιωματα πρόσβασης που έρχονται από τον πίνακα fileshares --}}
                    {{-- <hr> --}}
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
                                <a class="text-dark" style="text-decoration:none;" href="{{url("/fileshare_profile/$fi")}}">
                                <div class="h5 card-title fa-solid fa-file-pdf"></div>
                                <div>{{$fileshare->name}}</div>
                                </a> 
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Μενού με βάση τα δικαιωματα πρόσβασης που έρχονται από τον πίνακα microapps --}}
                    {{-- <hr> --}}
                    <div class="row hidden-md-up justify-content-left">
                        <div class="col-md-4 py-3" style="max-width:15rem">
                            <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
                                <a class="text-dark" style="text-decoration:none;" href="{{url("/microapps")}}">
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
                                <div class="card py-3" style="background-color:{{$one_microapp->color}}; text-align:center; font-size:small">
                                    <a  class="text-dark" style="text-decoration:none;" href="{{url("/admin".$one_microapp->url)}}">
                                    <div class="h5 card-title {{$one_microapp->icon}}"></div>
                                    <div @if(!$one_microapp->active) style="color:red" @endif>@if(!$one_microapp->active) <strong> @endif{{$one_microapp->name}}</strong></div>
                                    </a> 
                                </div>
                            </div>  
                        @endif
                        @endforeach
                    </div>
                    {{-- <div class="row hidden-md-up justify-content-left">
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:Gainsboro; text-decoration:none; text-align:center;">
                                <a class="text-dark" href="{{url("/logout")}}">
                                <div class="h5 card-title fa-solid fa-arrow-right-from-bracket"></div>
                                <div>Αποσύνδεση</div>
                                </a> 
                            </div>
                        </div>
                    </div> --}}
                {{-- </div> --}}
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