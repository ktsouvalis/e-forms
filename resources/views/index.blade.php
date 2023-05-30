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
            @endphp

            <div class="py-5">
                <div class="container">
                    <div class="row hidden-md-up justify-content-center">

                    {{-- Μενού μόνο για τους σούπερ δαχειριστές --}}
                    @if(Illuminate\Support\Facades\Auth::id()==1 or Illuminate\Support\Facades\Auth::id()==2)
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:Gold; text-decoration:none; text-align:center;">
                                <a class="text-dark" style="text-decoration:none;" href="/manage_operations">
                                <div class="h5 card-title bi bi-menu-button-wide"></div>
                                <div>Διαχείριση Λειτουργιών</div>
                                </a> 
                            </div>
                        </div> 
                    @endif

                    {{-- Μενού με βάση τα δικαιωματα πρόσβασης που έρχονται από τον πίνακα operations --}}
                    @foreach ($user->operations as $one_operation)
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:{{$one_operation->operation->color}}; text-align:center;">
                                <a  class="text-dark" style="text-decoration:none;" href="{{$one_operation->operation->url}}">
                                <div class="h5 card-title {{$one_operation->operation->icon}}"></div>
                                <div >{{$one_operation->operation->name}}</div>
                                </a> 
                            </div>
                        </div>  
                    @endforeach
                    
                    {{-- Μενού για όλους --}}
                    <div class="col-md-4 py-2" style="max-width:15rem">
                        <div class="card py-5" style="background-color:Gainsboro; text-decoration:none; text-align:center;">
                            <a class="text-dark" href="/logout">
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
                    <img src="/favicon/index.png" width="200" height="200" alt="forms">
            </div>
            <div class="col p-3">
                <form action="/login" method="post">
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