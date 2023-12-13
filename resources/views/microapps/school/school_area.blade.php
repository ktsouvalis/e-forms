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
    {{-- <script>
        function addStreet(id){
            let cameFromId = parseInt(id.replace('street', '')); 
            let allClasses = $('.streets').last().attr('class').split(' ');
            let streetsClass = allClasses.find(className => className.startsWith('numberOfStreet'));
            let streetsNumber = parseInt(streetsClass.replace('numberOfStreet', ''));  
            var counter = streetsNumber;      
            if(cameFromId == streetsNumber){
                let clonedDiv = $('.'+streetsClass).clone();

                clonedDiv.removeClass(streetsClass).addClass('numberOfStreet'+counter+1);

                clonedDiv.find('input').each(function() {
                    let name = $(this).attr('name').replace(counter, counter+1);
                    let id = $(this).attr('id').replace(counter, counter+1);

                    $(this).attr('name', name);
                    $(this).attr('id', id);
                    $(this).val(''); // Clear the value of the cloned inputs
                });

                clonedDiv.insertAfter('.'+streetsClass);
            };
        }
    </script> --}}
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
                        {{-- <span class="input-group-text w-25"></span> --}}
                        <span class="input-group-text w-75 text-wrap"><strong>Καταχώρηση στοιχείων για τα γεωγραφικά όρια Σχολικής Περιφέρειας εγγραφής μαθητών.</strong></span>
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
                   
                    {{-- <div class="streets numberOfStreet0 input-group">
                        <span class="input-group-text w-25 text-wrap">Οδός ή Περιοχή 1</span>
                        <input name="street0" id="street0" type="text" class=" w-25" value="" onclick="addStreet(this.id)"><br>
                        <input name="comment0" id="comment0" type="text" class="w-25" value=""><br>
                    </div> --}}
                    @if(sizeof($data)>0)
                        @php
                            $counter = 1;
                        @endphp
                        @foreach ($data as $key=>$value)
                            <div id="fields" class="input-group">
                                <div id="choices{{$counter}}" class="input-group choices">
                                    <span class="input-group-text w-25 text-wrap">Οδός ή Περιοχή {{$counter}}</span>
                                    <input name="street{{$counter}}" id="street{{$counter}}" type="text" class="w-25" value={{$value->street}}><br>
                                    <input name="comment{{$counter}}" id="comment{{$counter}}" type="text" class="w-25" value={{$value->comment}}><br>
                                </div>
                            </div>
                            @php
                                $counter++;
                            @endphp
                        @endforeach
                    @else
                       <div id="fields" class="input-group">
                            <div id="choices1" class="input-group">
                                <span class="input-group-text w-25 text-wrap">Οδός ή Περιοχή 1</span>
                                <input name="street1" id="street1" type="text" class="w-25" ><br>
                                <input name="comment1" id="comment1" type="text" class="w-25"><br>
                            </div>
                        </div>
                    @endif
                    <button id="bn1" class="btn btn-primary bi bi-plus" type="button" onclick="addField()"></button>
                    <br><br>
                    
                    
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