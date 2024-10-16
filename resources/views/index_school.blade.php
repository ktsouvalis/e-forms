<x-layout_school>
    @php
    
  @endphp
    <body class="bg-light">
    
    <div class="row hidden-md-up justify-content-center">
        @auth('school')
        @php 
            $school = Illuminate\Support\Facades\Auth::guard('school')->user();
            $active_microapp=false;
            if($school->microapps->count()){
                foreach($school->microapps as $microapp){
                    if($microapp->microapp->visible){
                    $active_microapp = true;
                    break;
                    }
                } 
            }
            
            $active_filecollect=false;
            if($school->filecollects->count()){
                foreach($school->filecollects as $filecollect){
                    if($filecollect->filecollect->visible){
                    $active_filecollect=true;
                    break;
                    }
                } 
            }
        @endphp
                        
            @push('title')
                <title>Καρτέλα {{$school->name}}</title>
            @endpush
            

            <div class="py-5">
                <div class="container">
                    <div class="row hidden-md-up justify-content-center">
                        {{--
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
                        <hr>
                        @endforeach --}}
                        @if($active_microapp or $active_filecollect)
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                            <div class="col-12 text-center">
                                <h3 class="fw-light">Υποβολή Στοιχείων</h3>
                            </div>
                            </div>
                        </div>
                        @endif
                        @foreach ($school->microapps as $one_microapp)
                        @if($one_microapp->microapp->visible)                      
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:{{$one_microapp->microapp->color}}; text-align:center;">
                                @php $resource = substr($one_microapp->microapp->url, 1); @endphp
                                {{-- <a  class="text-dark" style="text-decoration:none;" href="{{url($one_microapp->microapp->url."/create")}}"> --}}
                                <a  class="text-dark" style="text-decoration:none;" href="{{route("$resource.create")}}">
                                <div class="h5 card-title {{$one_microapp->microapp->icon}}"></div>
                                <div>{{$one_microapp->microapp->name}}</div>
                                </a> 
                            </div>
                        </div> 
                        @endif
                        @endforeach
                        @foreach($school->filecollects as $filecollect)
                            @php
                                $ffi = $filecollect->filecollect->id
                            @endphp
                            @if($filecollect->filecollect->visible)
                            <div class="col-md-4 py-2" style="max-width:15rem">
                                <div class="card py-5" style="background-color:#4bac97; text-align:center;">
                                    <a  class="text-dark" style="text-decoration:none;" href="{{url("/filecollects/$ffi")}}">
                                    <div class="h5 card-title fa-solid fa-file-pdf"></div>
                                    <div>{{$filecollect->filecollect->name}}</div>
                                    </a> 
                                </div>
                            </div>
                            @endif
                        @endforeach
                        @if(!(count($school->fileshares)==0))
                        <hr>
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                            <div class="col-12 text-center">
                                <h3 class="fw-light">Παραλαβή Εγγράφων</h3>
                            </div>
                            </div>
                        </div>
                        @endif
                        @foreach($school->fileshares as $fileshare)
                            @php
                                $ffi = $fileshare->fileshare->id
                            @endphp
                            <div class="col-md-4 py-2" style="max-width:15rem">
                                <div class="card py-5" style="background-color:#00bfff; text-align:center;">
                                    <a  class="text-dark" style="text-decoration:none;" href="{{url("/fileshares/$ffi")}}">
                                    <div class="h5 card-title fa-solid fa-file-pdf"></div>
                                    <div>{{$fileshare->fileshare->name}}</div>
                                    </a> 
                                </div>
                            </div>
                        @endforeach
                        <hr>
                        <div class="col-md-4 py-2" style="max-width:15rem">
                            <div class="card py-5" style="background-color:Gainsboro; text-decoration:none; text-align:center;">
                                <a class="text-dark" href="{{url('/slogout')}}">
                                <div class="h5 card-title fa-solid fa-arrow-right-from-bracket"></div>
                                <div>Αποσύνδεση</div>
                                </a> 
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        @endauth
        
        </div>
</x-layout_school>