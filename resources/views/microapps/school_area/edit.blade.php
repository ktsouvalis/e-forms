    @php
        $microapp = App\Models\Microapp::where('url', '/school_area')->first();
        $accepts = $microapp->accepts; 
        $name = $microapp->name;
        $old_data = $school->school_area;
        $data=array();
        if($old_data){
            $data = json_decode($old_data->data); 
        }
        // dd($old_data);
    @endphp
    @push('scripts')
    <script src="{{asset("addfields.js")}}"></script>
    @endpush
    @push('title')
        <title>{{$name}}</title>
    @endpush
<div class="container">
    <div class="container px-5"> 
       <h4>{{$school->name}}</h4>
            <div>
                <a href="https://dipeach.ddns.net/e-forms/school_areas" target="_blank">Καταγραφή Ορίων Σχολικής Περιφέρειας Σχολικών Μονάδων Π.Ε. Αχαΐας</a>
            </div>
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/school_area/$school->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @method('PUT')
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text w-75 text-wrap"><strong>Καταχώρηση στοιχείων για τα γεωγραφικά όρια Σχολικής Περιφέρειας εγγραφής μαθητών.</strong></span>
                    </div>
                    @if(Auth::check()) <!-- show div only to user of directorate -->
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
                                <input name="street{{$counter}}" id="street{{$counter}}" type="text" class="w-25" value="{{$value->street}}" @if(Auth::guard('school')->check()) disabled @endif><br>
                                <input name="comment{{$counter}}" id="comment{{$counter}}" type="text" class="w-25" value="{{$value->comment}}" @if(Auth::guard('school')->check()) disabled @endif><br>
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
                                <input name="street1" id="street1" type="text" class="w-25" @if(Auth::guard('school')->check()) disabled @endif><br>
                                <input name="comment1" id="comment1" type="text" class="w-25" @if(Auth::guard('school')->check()) disabled @endif><br>
                            </div>
                        </div>
                        Σημείωση: Δεν έχουν υποβληθεί όρια γεωγραφικής περιοχής για το Σχολείο σας.
                    @endif
                    @if(Auth::check()) <!-- show div to user of directorate only -->
                    <div class="hstack gap-3">
                    <button id="bn1" class="btn btn-primary bi bi-plus" type="button" onclick="addField()"></button>
                    <button  type='button' class='btn btn-secondary bi bi-dash' onclick='removeField()'></button>
                    </div>
                    @endif
                    <br><br>
                    @if(Auth::guard('school')->check())  <!-- show div to school only -->
                        @if(!$microapp->accepts)
                            <div class='alert alert-warning text-center my-2'>
                               <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                            </div>
                        @else
                            @if($old_data and !$old_data->confirmed)
                                <div class="input-group">
                                    <button type="submit" class="btn btn-primary m-2 bi bi-check2-square" onclick="return confirm('Η Διεύθυνση Π.Ε. Αχαΐας θα ενημερωθεί ότι επιβεβαιώνετε τα όρια του Σχολείου σας')"> Επιβεβαίωση ορίων</button>
                                </div>
                                    <div class="input-group">
                                        <span class="input-group-text w-25 text-wrap">Επισήμανση για διόρθωση/συμπλήρωση σχετικά με τα όρια ή/και τη διατύπωσή τους.</span>
                                        <textarea name="general_com" id="general_com" type="text" class=" w-50" >@if($old_data){{$old_data->comments}}@endif</textarea>
                                    </div>
                                    <div class="input-group">
                                        <button type="submit" class="btn btn-primary m-2 bi bi-check2-square" onclick="return confirm('Θα υποβληθούν οι επισημάνσεις σας και η Φόρμα θα κλειδώσει. Είστε σίγουροι;')"> Υποβολή επισημάνσεων</button>
                                    </div>
                                
                            @endif
                        @endif
                        @if($old_data and $old_data->confirmed)
                            <div class='alert alert-info text-center my-2'>
                                <i class="bi bi-info-circle"> </i>
                                @if($old_data->comments != "")
                                    Έχετε ολοκληρώσει τις απαιτούμενες ενέργειες<br> Με την επισήμανση ότι: {{$old_data->comments}}
                                @else
                                    Έχετε επιβεβαιώσει τα όρια όπως διατυπώνονται.
                                @endif
                            </div>
                        @endif
                    @else <!-- show div to user of directorate only -->
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary m-2 bi bi-save"> Αποθήκευση ορίων</button>
                            <a href="{{url("/school_area/$school->id/edit")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>    
                        </div>
                        @if($old_data and $old_data->confirmed)
                            <div class='alert alert-info text-center my-2'>
                                <i class="bi bi-info-circle"> </i>Το Σχολείο έχει δηλώσει ότι
                                @if($old_data->comments != "")
                                    {{$old_data->comments}}
                                @else
                                    Επιβεβαιώνει αυτά τα όρια.
                                @endif
                                <br>
                            </div>
                        @endif
                    @endif
                </form>
                
            </nav>
        </div> 
    </div>
</div>