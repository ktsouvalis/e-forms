<x-layout_consultant>
    <body class="bg-light">
    
    <div class="row hidden-md-up justify-content-center">
        @auth('consultant')
            @php 
                $consultant = Illuminate\Support\Facades\Auth::guard('consultant')->user(); 
            @endphp
                            
            @push('title')
                <title>Σελίδα Συμβούλου Εκπαίδευσης</title>
            @endpush
            

            <div class="py-5">
                <div class="container">
                    <div class="row hidden-md-up justify-content-center">
                        
                        {{-- @foreach ($teacher->forms as $one_form)
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:{{$one_form->form->color}}; text-align:center;">
                                @php
                                    $ofi = $one_form->form->id; 
                                @endphp
                                <a  class="text-dark" style="text-decoration:none;" href="{{url("/teacher_view/$ofi")}}">
                                <div class="h5 card-title {{$one_form->form->icon}}"></div>
                                <div>{{$one_form->form->name}}</div>
                                </a> 
                            </div>
                        </div>  
                        @endforeach
                        <hr> --}}
                        {{-- @foreach ($teacher->microapps as $one_microapp)
                        @if($one_microapp->microapp->visible)
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:{{$one_microapp->microapp->color}}; text-align:center;">
                                <a  class="text-dark" style="text-decoration:none;" href="{{url("/teacher_app".$one_microapp->microapp->url)}}">
                                <div class="h5 card-title {{$one_microapp->microapp->icon}}"></div>
                                <div>{{$one_microapp->microapp->name}}</div>
                                </a> 
                            </div>
                        </div> 
                        @endif
                        @endforeach
                        <hr>

                        
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:#00bfff; text-align:center;">
                                <a  class="text-dark" style="text-decoration:none;" href="{{url("/teacher_fileshare/$teacher->id")}}">
                                <div class="h5 card-title fa-solid fa-file-pdf"></div>
                                <div>Αρχεία Διεύθυνσης</div>
                                </a> 
                            </div>
                        </div>  --}}
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:MediumAquamarine; text-align:center;">
                                <a  class="text-dark" style="text-decoration:none;" href="{{url("/consultant_schools")}}">
                                <div class="h5 card-title fa-solid fa-school"></div>
                                <div>Σχολεία</div>
                                </a> 
                            </div>
                        </div> 
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:skyblue; text-align:center;">
                                <a  class="text-dark" style="text-decoration:none;" href="{{url("/consultant_directors")}}">
                                <div class="h5 card-title fa-solid fa-signature"></div>
                                <div>Διευθυντές</div>
                                </a> 
                            </div>
                        </div> 
                        <hr>
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:#f1948a; text-align:center;">
                                <a  class="text-dark" style="text-decoration:none;" href="{{url("/microapps/internal_rules/create")}}">
                                <div class="h5 card-title fa-solid fa-file-signature"></div>
                                <div>Εσωτερικός Κανονισμός</div>
                                </a> 
                            </div>
                        </div> 
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:#ff8f00; text-align:center;">
                                <a  class="text-dark" style="text-decoration:none;" href="{{url("/consultant_app/work_planning")}}">
                                <div class="h5 card-title fa-solid fa-map"></div>
                                <div>Προγραμματισμός Έργου</div>
                                </a> 
                            </div>
                        </div> 
                        <hr>
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:Gainsboro; text-decoration:none; text-align:center;">
                                <a class="text-dark" href="{{url('/clogout')}}">
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
                        <img src="{{url("/favicon/android-chrome-512x512.png")}}" width="200" height="200" alt="">
                </div>
                <div class="col m-5"> Πρέπει να συνδεθείτε με τον μοναδικό προσωπικό σύνδεσμό σας</div>
                <div class="col"></div>
            </div>
        @endauth
        
        </div>
</x-layout_consultant>