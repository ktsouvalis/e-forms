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
                        {{-- <span class="input-group-text w-25"></span> --}}
                        <span class="input-group-text w-75"><strong>Καταχώρηση στοιχείων για τα γεωγραφικά όρια Σχολικής Περιφέρειας εγγραφής μαθητών.</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Γενική Παρατήρηση για τα όρια</span>
                        <input name="general_com" id="general_com" type="text" class=" w-50" value="@if($old_data){{$old_data->comments}}@endif"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-25">Οδός ή Περιοχή</span>
                        <span class="input-group-text w-25">Παρατήρηση</span>
                    </div>
                    @for($i=0;$i<=0;$i++)
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Οδός ή Περιοχή 1</span>
                        <input name="street.{{$i}}" id="street" type="text" class=" w-25" value="@if(sizeof($data)>0){{$data[$i]->street}}@endif"><br>
                        <input name="comment.{{$i}}" id="comment1" type="text" class="w-25" value="@if(sizeof($data)>0){{$data[$i]->comment}}@endif"><br>
                    </div>
                    @endfor
                    {{-- <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Οδός ή Περιοχή 2</span>
                        <input name="street2" id="street2" type="text" class=" w-25" value="@if(sizeof($data)>0){{$data[0][2]}}@endif"><br>
                        <input name="comment2" id="comment2" type="text" class="w-25" value="@if(sizeof($data)>0){{$data[1][2]}}@endif"><br>
                    </div> --}}
                    {{-- @if(sizeof($data)>0)

                    @endif --}}
                    @if(!$accepts)
                        <div class="col-sm-2 btn btn-warning bi bi-bricks rounded text-dark" style="text-align:center;">
                            Η εφαρμογή δε δέχεται υποβολές
                        </div>
                    @else
                        <div class="input-group">
                            <span class="w-25"></span>
                            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            <a href="{{url("/school_app/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    @endif
                </form>
                
            </nav>
        </div> 
    </div>
</div>
</x-layout_school>