<x-layout_teacher>
    
    <body class="bg-light">
    
    <div class="row hidden-md-up justify-content-center">
        @auth('teacher')
            @php 
                $teacher = Illuminate\Support\Facades\Auth::guard('teacher')->user(); 
                $active_microapp=false;
                if($teacher->microapps->count()){
                    foreach($teacher->microapps as $microapp){
                        if($microapp->microapp->visible){
                        $active_microapp = true;
                        break;
                        }
                    } 
                }

                $satisfiesCriteria = false;
                foreach(App\Models\Microapp::all() as $one_microapp){
                    if($one_microapp->accessCriteria){
                        $criteria = json_decode($one_microapp->accessCriteria->criteria, true);
                        $satisfiesCriteria = true;
                        foreach ($criteria as $key => $value) {
                            if (!in_array($teacher->$key, $value)) {
                                $satisfiesCriteria = false;
                                break;
                            }  
                        }
                    }
                    if($satisfiesCriteria){
                        $active_microapp = true;
                    }
                }
                
                $active_filecollect=false;
                if($teacher->filecollects->count()){
                    foreach($teacher->filecollects as $filecollect){
                        if($filecollect->filecollect->visible){
                        $active_filecollect=true;
                        break;
                        }
                    } 
                }
            @endphp
                            
            @push('title')
                <title>Καρτέλα {{$teacher->surname}} {{$teacher->name}}</title>
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
                        @if($active_microapp or $active_filecollect)
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                            <div class="col-12 text-center">
                                <h3 class="fw-light">Υποβολή Στοιχείων</h3>
                            </div>
                            </div>
                        </div>
                        @endif

                        @foreach ($teacher->microapps as $one_microapp)
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

                        @foreach(App\Models\Microapp::all() as $one_microapp)
                            @if($one_microapp->accessCriteria)
                                @php
                                    $criteria = json_decode($one_microapp->accessCriteria->criteria, true);
                                    $satisfiesCriteria = true;
                                    $active_microapp = true;
                                    foreach ($criteria as $key => $value) {
                                        if (!in_array($teacher->$key, $value)) {
                                            $satisfiesCriteria = false;
                                            $active_microapp = false;
                                            break;
                                        }  
                                    }
                                     
                                @endphp
                               
                                @if ($satisfiesCriteria)
                                    @if(!$teacher->microapps->where('microapp_id', $one_microapp->id)->count())
                                    <div class="col-md-4 py-2" style="max-width:15rem">
                                        <div class="card py-5" style="background-color:{{$one_microapp->color}}; text-align:center;">
                                            @php $resource = substr($one_microapp->url, 1); @endphp
                                            {{-- <a  class="text-dark" style="text-decoration:none;" href="{{url($one_microapp->microapp->url."/create")}}"> --}}
                                            <a  class="text-dark" style="text-decoration:none;" href="{{route("$resource.create")}}">
                                            <div class="h5 card-title {{$one_microapp->icon}}"></div>
                                            <div>{{$one_microapp->name}}</div>
                                            </a> 
                                        </div>
                                    </div> 
                                    @endif
                                @endif
                            @endif
                        @endforeach
                        @php
                            //  dd($satisfiesCriteria)  
                        @endphp
                        @foreach($teacher->filecollects as $filecollect)
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
                        @if(!(count($teacher->fileshares)==0))
                        <hr>
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                            <div class="col-12 text-center">
                                <h3 class="fw-light">Παραλαβή Εγγράφων</h3>
                            </div>
                            </div>
                        </div>
                        @endif
                        @foreach($teacher->fileshares as $fileshare)
                            @php
                                $fid = $fileshare->fileshare->id
                            @endphp
                            <div class="col-md-4 py-2" style="max-width:15rem">
                                <div class="card py-5" style="background-color:#00bfff; text-align:center;">
                                    <a  class="text-dark" style="text-decoration:none;" href="{{url("/fileshares/$fid")}}">
                                    <div class="h5 card-title fa-solid fa-file-pdf"></div>
                                    <div>{{$fileshare->fileshare->name}}</div>
                                    </a> 
                                </div>
                            </div>
                        @endforeach
                        <hr>
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
        @endauth
        
        </div>
</x-layout_teacher>