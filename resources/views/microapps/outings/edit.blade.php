<x-layout_school>
    @push('title')
        <title>Επεξεργασία εκδρομής {{$outing->id}}</title>
    @endpush
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $accepts = App\Models\Microapp::where('url', '/outings')->first()->accepts; //fetch microapp 'accepts' field
        $my_date =Illuminate\Support\Carbon::parse($outing->outing_date);
    @endphp
    <div class="py-3">
            <nav class="navbar navbar-light bg-light">
                    <form action="{{url("/microapps/outings/$outing->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @method('PUT')
                        @csrf
                        <div class="input-group">
                            <span class="input-group-text w-25"></span>
                            <span class="input-group-text w-75"><strong>Επεξεργασία στοιχείων εκδρομής</strong></span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25" id="basic-addon4">Τύπος Εκδρομής</span>
                            <select name="type" class="form-select" aria-label="Default select example">
                                @foreach(App\Models\microapps\OutingType::all() as $type)
                                @php
                                    $selected="";
                                    if($type->id==$outing->outingtype_id)
                                        $selected="selected";   
                                @endphp
                                <option {{$selected}} value="{{$type->id}}">{{$type->description}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25" id="basic-addon2">Νέα Ημερομηνία</span>
                            <input name="outing_date" type="date" class="form-control"  aria-label="outing_date" aria-describedby="basic-addon1">
                            <label class="form-control text-muted">{{$my_date->day}}/{{$my_date->month}}/{{$my_date->year}}</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Δράση: </span>
                            <input name="destination" id="destination" type="text" class="form-control" placeholder="π.χ. Πάρκο Κυκλοφοριακής Αγωγής" aria-label="Δράση" aria-describedby="basic-addon2" value="{{$outing->destination}}" required><br>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Αριθμός πρακτικού: </span>
                            <input name="record" id="record" type="text" class="form-control" placeholder="π.χ. 15-11/9/2023" aria-label="αριθμός πρακτικού" aria-describedby="basic-addon3" value="{{$outing->record}}" required><br>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Τμήματα που συμμετέχουν</span>
                            @php
                                $sections = $school->sections;  
                            @endphp
                            <div class="v-stack gap-2">
                            @foreach($sections as $section)
                                @php
                                    $checked="";
                                    if($outing->sections->contains('section_id', $section->id))
                                        $checked="checked";   
                                @endphp
                                <div class="form-check mx-1">
                                    <input class="form-check-input" type="checkbox" name="section{{$section->id}}" value="{{$section->id}}" id="section{{$section->id}}" {{$checked}}>
                                    <label for="section{{$section->id}}"> {{$section->name}} </label>
                                </div>     
                            @endforeach
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25" id="basic-addon4"><strong>Νέο</strong> Πρακτικό</span>
                            <input name="record_file" type="file" class="form-control">
                            <label class="form-control text-muted">{{$outing->file}}</label>
                        </div>
                        @if(!$accepts)
                            <div class='alert alert-warning text-center my-2'>
                               <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                            </div>
                        @else
                            <div class="input-group">
                                <span class="input-group-text w-25"><em>Μορφή αρχείου: .pdf < 10MB</em></span>
                                <button type="submit" class="btn btn-primary m-2 bi bi-save"> Αποθήκευση</button>
                                <a href="{{url("/microapps/outings/$outing->id/edit")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Ακύρωση</a>
                            </div>
                        @endif
                    </form>
                </nav>
            </div>
</x-layout_school>