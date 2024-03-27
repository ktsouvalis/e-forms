<x-layout_school>
    @php
        $appname = 'all_day_school';
        $school = Auth::guard('school')->user(); //check which school is logged in
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $active_month = App\Models\Month::getActiveMonth();
        $vmonth = $school->vmonth;
        $accepts = $microapp->accepts; 
        $name = $microapp->name;
        if(!$school->vmonth or $school->vmonth->vmonth == 0){
            $month_to_store = $active_month->id;
        }
        else{
            $month_to_store = $vmonth->vmonth;
        }
        $old_data = $school->all_day_schools->where('month_id', $month_to_store)->first(); 
    @endphp
    
    @push('title')
        <title>{{$name}}</title>
    @endpush
<div class="container">
    <div class="container px-5">
        {{-- <div class="alert alert-warning text-center">
            <strong>ΣΗΜΑΝΤΙΚΟ</strong><br><br> Σας ενημερώνουμε ότι <u>δεν υπάρχει υποχρέωση ανάρτησης πινάκων στη Φόρμα Υποβολής</u> για την επικαιροποίηση δηλώσεων ολοήμερου 
            που πραγματοποιείται τον Νοέμβριο και τον Φεβρουάριο.
        </div>  --}}
        <div class="alert alert-warning text-center">
            <strong>ΣΗΜΑΝΤΙΚΟ</strong><br><br> Σας ενημερώνουμε ότι λόγω προβλημάτων στην καταμέτρηση των πεδίων ώρας/ημερομηνίας στα αρχεία excel, 
            θα πρέπει <strong>οι αριθμοί των μαθητών που αποχωρούν</strong> σε κάθε ζώνη του Ολοήμερου Προγράμματος αλλά και <strong>o αριθμός των μαθητών της Πρωινής Υποδοχής</strong> να συμπληρώνονται από εσάς στα αντίστοιχα νέα πεδία.
        </div>  
            <form action="{{url("/microapps/all_day_school/download_template/$school->primary")}}" method="get">
                @csrf
                <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Πίνακας προς συμπλήρωση </button>
            </form>      
            <nav class="navbar navbar-light bg-light">
                @if(!$old_data)<small><u class="text-muted">Συμπληρώστε <b>όλα</b> τα πεδία βάζοντας 0 όπου δεν υπάρχουν μαθητές/τμήματα</u></small>@endif
                <form action="{{url("/microapps/$appname")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        {{-- <span class="input-group-text w-25"></span> --}}
                        <span class="input-group-text w-75"><strong>Καταχώρηση στοιχείων για το Ολοήμερο Πρόγραμμα για τον Μήνα <my_text class="text-success">{{App\Models\Month::find($month_to_store)->name}}</my_text></strong></span>
                    </div>
                    @if($school->primary)
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Αριθμός τμημάτων έως 14:50 ή 15:00</span>
                            <input name="nr_class_3" id="nr_class_3" type="number" class="form-control" required value="@if($old_data){{$old_data->nr_of_class_3}}@endif"><br> 
                            @if($old_data)
                                <label class="form-control text-muted">{{$old_data->nr_of_pupils_3 + $old_data->nr_of_pupils_4 + $old_data->nr_of_pupils_5}} μαθητές σε {{$old_data->nr_of_class_3}} τμήματα</label>
                            @endif
                        </div>
                         <div class="input-group">
                            <span class="input-group-text w-25 text-wrap"> Στις 14:50 ή στις 15:00 αποχωρούν:</span>
                            <input name="nr_pupils_3" id="nr_pupils_3" type="number" class="form-control" required value="@if($old_data){{$old_data->nr_of_pupils_3}}@endif"><br>
                        </div>
                    @endif
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Αριθμός τμημάτων έως 15:50 ή 16:00</span>
                        <input name="nr_class_4" id="nr_class_4" type="number" class="form-control" required value="@if($old_data){{$old_data->nr_of_class_4}}@endif"><br>
                        @if($old_data)
                            <label class="form-control text-muted">{{$old_data->nr_of_pupils_4 + $old_data->nr_of_pupils_5}} μαθητές σε {{$old_data->nr_of_class_4}} τμήματα</label>
                        @endif
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Στις 15:50 ή στις 16:00 αποχωρούν:</span>
                        <input name="nr_pupils_4" id="nr_pupils_4" type="number" class="form-control" required value="@if($old_data){{$old_data->nr_of_pupils_4}}@endif"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Αριθμός τμημάτων Διευρυμένου Ολοήμερου</span>
                        <input name="nr_class_5" id="nr_class_5" type="number" class="form-control" required value="@if($old_data){{$old_data->nr_of_class_5}}@endif"><br>
                        @if($old_data)
                            <label class="form-control text-muted">{{$old_data->nr_of_pupils_5}} μαθητές σε {{$old_data->nr_of_class_5}} τμήματα</label>
                        @endif
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Στις 17:30 αποχωρούν:</span>
                        <input name="nr_pupils_5" id="nr_pupils_5" type="number" class="form-control" required value="@if($old_data){{$old_data->nr_of_pupils_5}}@endif"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Αριθμός μαθητών στην Πρωινή Υποδοχή: </span>
                        <input name="nr_morning" id="nr_morning" type="number" class="form-control" required value="@if($old_data){{$old_data->nr_morning}}@endif"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Παρατηρήσεις</span>
                        <textarea name="comments" id="comments" class="form-control" cols="30" rows="5" style="resize: none;" >@if($old_data){{$old_data->comments}}@endif</textarea>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Λειτούργησε: </span>
                        <div class="hstack gap-4">
                            <div>
                                <input name="functionality" type="radio" id="fully" value="ΠΛΗΡΩΣ" @if($old_data) @if($old_data->functionality=="ΠΛΗΡΩΣ") {{'checked'}}@endif @else {{'checked'}} @endif>
                                <label for="fully">ΠΛΗΡΩΣ</label>   
                            </div>
                            <div>   
                                <input name="functionality" type="radio" id="partially" value="ΜΕΡΙΚΩΣ" @if($old_data) @if($old_data->functionality=="ΜΕΡΙΚΩΣ") {{'checked'}}@endif @endif>
                                <label for="partially">ΜΕΡΙΚΩΣ</label>
                            </div>
                            <div>
                                <input name="functionality" type="radio" id="no" value="ΚΑΘΟΛΟΥ" @if($old_data) @if($old_data->functionality=="ΚΑΘΟΛΟΥ") {{'checked'}}@endif @endif>
                                <label for="no">ΚΑΘΟΛΟΥ</label>
                            </div>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon4">Πίνακας</span>
                        <input name="table_file" type="file" class="form-control" @if(!$old_data) {{"required"}} @endif><br>
                    </div>
                    @if(!$accepts)
                        <div class='alert alert-warning text-center my-2'>
                            <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                        </div>
                    @else
                        <div class="input-group">
                            <span class="w-25"></span>
                            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            <a href="{{url("/microapps/$appname/create")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    @endif
                </form>
                
            </nav>
        </div> 
        <div class="container px-5 py-2">
            @if($old_data)
                <form action="{{url("/microapps/all_day_school/download_file/$old_data->id")}}" method="get">
                    @csrf
                <div class="hstack gap-2"><label><strong>Αρχείο που έχετε υποβάλλει για τον μήνα {{$old_data->month->name}}:</strong></label> <button class="btn btn-success bi bi-box-arrow-down" title="Λήψη αρχείου">  {{$old_data->file}}</button> </div>
                </form>
            @endif
            @include('microapps.new_ticket_button')
        </div>
        <div class="py-3">
            <div class="table-responsive py-2">
                <table  id="" class="small text-center display table table-sm table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th id="search">Μήνας</th>
                        <th id="search">Λειτουργία</th>
                        <th id="">Μαθητές Πρωινή Υποδοχή</th>
                        @if($school->primary)
                            <th id="">Τμήματα 14.50 ή 15.00</th>                        
                            <th id="">Μαθητές 14.50 ή 15.00</th>
                        @endif
                        <th id="">Τμήματα 15.50 ή 16.00</th>
                        <th id="">Μαθητές 15.50 ή 16.00</th>
                        <th id="">Τμήματα 17.30</th>
                        <th id="">Μαθητές 17.30</th>
                        <th id="">Σχόλια</th>
                        <th>Αρχείο</th>
                        <th>Τελευταία ενημέρωση</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($school->all_day_schools->sortByDesc('month_id') as $one)
                    <tr>
                    <td> {{$one->month->name}}</td>
                    <td> {{$one->functionality}}</td>
                    <td> {{$one->nr_morning}}</td>
                    @if($school->primary)
                        <td> {{$one->nr_of_class_3}}</td>
                        <td> {{$one->nr_of_pupils_3 + $one->nr_of_pupils_4 + $one->nr_of_pupils_5}}</td>
                    @endif
                    <td> {{$one->nr_of_class_4}}</td>
                    <td> {{$one->nr_of_pupils_4 + $one->nr_of_pupils_5}}</td>
                    <td> {{$one->nr_of_class_5}}</td>
                    <td> {{$one->nr_of_pupils_5}}</td>
                    <td> {{$one->comments}}</td>
                    <td>
                        <form action="{{url("/microapps/all_day_school/download_file/$one->id")}}" method="get">
                        @csrf
                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> </button> 
                        </form>   
                    </td>
                    <td>{{$one->updated_at}}</td>
                    </tr>
                @endforeach
                </tbody>
                </table>
            </div>
        
        </div>  
</div>
</x-layout_school>