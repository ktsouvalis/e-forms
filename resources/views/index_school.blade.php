<x-layout_school>
    <body class="bg-light">
    
    <div class="row hidden-md-up justify-content-center">
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
                        @foreach ($school->forms as $one_form)
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:{{$one_form->form->color}}; text-align:center;">
                                @php
                                    $ofi = $one_form->form->id; 
                                @endphp
                                <a  class="text-dark" style="text-decoration:none;" href="{{url("/school_view/$ofi")}}">
                                <div class="h5 card-title {{$one_form->form->icon}}"></div>
                                <div>{{$one_form->form->name}}</div>
                                </a> 
                            </div>
                        </div>  
                        @endforeach
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:Gainsboro; text-decoration:none; text-align:center;">
                                <a class="text-dark" href="{{url('/tlogout')}}">
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
                        <img src="{{url("/favicon/index.png")}}" width="200" height="200" alt="books">
                </div>
                <div class="col m-5"> Πρέπει να συνδεθείτε με τον μοναδικό προσωπικό σύνδεσμό σας</div>
                <div class="col"></div>
            </div>
        @endauth
        
        </div>
</x-layout_school>