

<x-layout>
    <body class="bg-light"></body>
    <br><br><br><br><br><br>
    <div class="container">
        <div class="row p-2 justify-content-evenly">
        @auth
            @push('title')
                <title>Αρχική</title>
            @endpush
            
           <a class="col-lg-3 card w-3 text-bg-primary mb-3" style="max-width: 20rem; opacity: 0.4; text-decoration:none;" href="/manage_users">
                <div class="card-body" style="text-align: center; padding: 5rem">
                <div class="h5 card-title fa-solid fa-users"></div>
                <div>Διαχείριση Χρηστών</div>
                <p class="card-text"></p>
                </div> 
            </a>
        
        @else
            @push('title')
                    <title>Σύνδεση</title>
            @endpush
            <div class="row justify-content-md-center">
            <div class="col">
                    
            </div>
            {{-- <div class="col p-3">
                    <img src="/favicon/index.png" width="200" height="131" alt="books">
            </div> --}}
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
            <div class="col">
                    
            </div>
            </div>
        @endauth
</x-layout>