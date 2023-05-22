<x-layout_school>
    <body class="bg-light">
    <div class="container ">
        
        <div class="row p-2 justify-content-evenly">
        @auth('school')
        @php $school = Illuminate\Support\Facades\Auth::guard('school')->user(); @endphp
                        
            @push('title')
                <title>Υποβολή Στοιχείων</title>
            @endpush
            @php
             
            @endphp

            <div class="py-5">
                <div class="container">
                    <div class="row hidden-md-up justify-content-center">
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:Gainsboro; text-decoration:none; text-align:center;">
                                <a class="text-dark" href="/slogout">
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
                <div class="col"></div>
                <div class="col p-3">
                        <img src="/favicon/index.png" width="200" height="200" alt="books">
                </div>
                <div class="col m-5"> Πρέπει να συνδεθείτε με τον μοναδικό σύνδεσμο του σχολείου σας</div>
                <div class="col"></div>
            </div>
        @endauth
        
        </div>
</x-layout_school>