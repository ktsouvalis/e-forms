    @php
        // $school = Auth::guard('school')->user(); //check which school is logged in
        $microapp = App\Models\Microapp::where('url', '/school_area')->first();
        $accepts = $microapp->accepts; 
        $name = $microapp->name;
        
        $old_data = $school->school_area;
        $data=array();
        if($old_data){
            $data = json_decode($old_data->data); 
        }
    @endphp
    @push('scripts')
    <script src="../../addfields.js"></script>
    @endpush
    @push('title')
        <title>{{$name}}</title>
    @endpush
<div class="container">
    <div class="container px-5"> 
       <h4>{{$school->name}}</h4>
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/save_school_area/$school->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text w-75 text-wrap"><strong>Καταχώρηση στοιχείων για τα γεωγραφικά όρια Σχολικής Περιφέρειας εγγραφής μαθητών.</strong></span>
                    </div>
                    @if(Auth::check())
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Γενική Παρατήρηση για τα όρια</span>
                        <textarea name="general_com" id="general_com" type="text" class=" w-50" >@if($old_data){{$old_data->comments}}@endif</textarea>
                    </div>
                    @endif
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-25">Οδός ή Περιοχή</span>
                        <span class="input-group-text w-25">Παρατήρηση</span>
                    </div>
                   
                    @if($data)
                        @php
                            $counter = 1;
                          
                        @endphp
                        <div id="fields" class="input-group">
                        @foreach ($data as $key=>$value)
                            <div id="choices{{$counter}}" class="input-group choices">
                                <span class="input-group-text w-25 text-wrap">Οδός ή Περιοχή {{$counter}}</span>
                                <input name="street{{$counter}}" id="street{{$counter}}" type="text" class="w-25" value="{{$value->street}}"><br>
                                <input name="comment{{$counter}}" id="comment{{$counter}}" type="text" class="w-25" value="{{$value->comment}}"><br>
                            </div>
                            @php
                                $counter++;
                            @endphp
                        @endforeach
                        </div>
                    @else
                       <div id="fields" class="input-group">
                            <div id="choices1" class="input-group choices">
                                <span class="input-group-text w-25 text-wrap">Οδός ή Περιοχή 1</span>
                                <input name="street1" id="street1" type="text" class="w-25" ><br>
                                <input name="comment1" id="comment1" type="text" class="w-25"><br>
                            </div>
                        </div>
                    @endif
                    @if(Auth::check())
                    <div class="hstack gap-3">
                    <button id="bn1" class="btn btn-primary bi bi-plus" type="button" onclick="addField()"></button>
                    <button  type='button' class='btn btn-secondary bi bi-dash' onclick='removeField()'></button>
                    </div>
                    @endif
                    <br><br>
                    @if(Auth::guard('school')->check())
                        @if(!$microapp->accepts)
                            <div class='alert alert-warning text-center my-2'>
                               <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                            </div>
                        @else
                            @if($old_data and !$old_data->confirmed)
                                <div class="input-group">
                                    <button type="submit" class="btn btn-primary m-2 bi bi-check2-square" onclick="return confirm('Η Διεύθυνση Π.Ε. Αχαΐας θα ενημερωθεί ότι επιβεβαιώνετε τα όρια του Σχολείου σας')"> Επιβεβαίωση ορίων</button>
                                </div>
                            @endif
                        @endif
                        @if($old_data and $old_data->confirmed)
                            <div class='alert alert-info text-center my-2'>
                                <i class="bi bi-info-circle"> </i>Έχετε κάνει την ενέργεια <strong>Επιβεβαίωση ορίων</strong><br>
                            </div>
                        @endif
                    @else
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary m-2 bi bi-save"> Αποθήκευση ορίων</button>
                            <a href="{{url("/school_area_profile/$school->id")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>    
                        </div>
                        @if($old_data and $old_data->confirmed)
                            <div class='alert alert-info text-center my-2'>
                                <i class="bi bi-info-circle"> </i>Το σχολείο έχει κάνει την ενέργεια <strong>Επιβεβαίωση ορίων</strong><br>
                            </div>
                        @endif
                    @endif
                </form>
                
            </nav>
        </div> 
    </div>
</div>