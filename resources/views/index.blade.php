

<x-layout>
    <body class="bg-light">
    
    <div class="container">
        <div class="row p-2 justify-content-evenly">
        @auth
            @push('title')
                <title>Αρχική</title>
            @endpush
            @php
                $user = App\Models\User::where('id', Illuminate\Support\Facades\Auth::id())->first();
            @endphp
            
            <div class="py-5">
                <div class="container">
                    <div class="row hidden-md-up justify-content-center">
        
                    @foreach ($user->menus as $one_menu)
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:{{$one_menu->menu->color}}; text-align:center;">
                                <a  class="text-dark" style="text-decoration:none;" href="{{$one_menu->menu->url}}">
                                <div class="h5 card-title {{$one_menu->menu->icon}}"></div>
                                <div >{{$one_menu->menu->name}}</div>
                                </a> 
                            </div>
                        </div>  
                    @endforeach
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
                    <img src="/favicon/index.png" width="200" height="200" alt="books">
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
            
            
        @endauth
        </div>
</x-layout>