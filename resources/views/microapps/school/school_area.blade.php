<x-layout_school>
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; 
        $name = $microapp->name;
        $data=array();
        $old_data = $school->school_area; 
        if($old_data){
            $data = json_decode($old_data->data); 
        }
    @endphp
    @push('scripts')
    <script src="../addfields.js"></script>
    @endpush
    @push('title')
        <title>{{$name}}</title>
    @endpush
<div class="container">
    <div class="container px-5">
        {{-- σε αυτό το div μπορούμε να εμφανίσουμε κάποιο μήνυμα. 
        <div class="alert alert-warning text-center">
            <strong>ΣΗΜΑΝΤΙΚΟ</strong><br><br> 
        </div>   --}}   
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/save_school_area")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        
                        <span class="input-group-text w-75 text-wrap"><strong>Καταχώρηση στοιχείων για τα γεωγραφικά όρια Σχολικής Περιφέρειας εγγραφής μαθητών.</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Γενική Παρατήρηση για τα όρια</span>
                        <textarea name="general_com" id="general_com" type="text" class=" w-50" >@if($old_data){{$old_data->comments}}@endif</textarea>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-25">Οδός ή Περιοχή</span>
                        <span class="input-group-text w-25">Παρατήρηση</span>
                    </div>
                   
                   
                    @if(sizeof($data)>0)
                        @php
                            $counter = 1;
                        @endphp
                        <div id="fields" class="input-group">
                        @foreach ($data as $key=>$value)
                            
                            <div id="choices{{$counter}}" class="input-group choices">
                                <span class="input-group-text w-25 text-wrap">Οδός ή Περιοχή {{$counter}}</span>
                                <input name="street{{$counter}}" id="street{{$counter}}" type="text" class="w-25" value={{$value->street}}><br>
                                <input name="comment{{$counter}}" id="comment{{$counter}}" type="text" class="w-25" value={{$value->comment}}><br>
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
                    <div class="hstack gap-3">
                    <button id="bn1" class="btn btn-primary bi bi-plus" type="button" onclick="addField()"></button>
                    <button  type='button' class='btn btn-secondary bi bi-dash' onclick='removeField()'></button>
                    </div>
                    <br><br>
                    
                    
                    @if(!$accepts)
                        <div class="col-sm-2 btn btn-warning bi bi-bricks rounded text-dark" style="text-align:center;">
                            Η εφαρμογή δε δέχεται υποβολές
                        </div>
                    @else
                        <div class="input-group">
                            <span class="w-25"></span>
                            <button type="submit" class="btn btn-primary m-2 bi bi-save"> Αποθήκευση αλλαγών</button>
                            <a href="{{url("/school_app/$appname")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                        </div>
                    @endif
                </form>
                
            </nav>
        </div> 
    </div>
</div>
</x-layout_school>