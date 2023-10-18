<x-layout_school>
    @push('links')
        <link href="../Responsive-2.4.1/css/responsive.bootstrap5.css" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="../Responsive-2.4.1/js/dataTables.responsive.js"></script>
        <script src="../Responsive-2.4.1/js/responsive.bootstrap5.js"></script>
    @endpush
    @push('title')
        <title>Ολοήμερο και Πρωινή Υποδοχή</title>
    @endpush
@php
    $school = Auth::guard('school')->user(); //check which school is logged in
    $active_month = App\Models\Month::getActiveMonth();
    $accepts = App\Models\Microapp::where('url', '/'.$appname)->first()->accepts; //fetch microapp 'accepts' field
    $old_data = $school->all_day_schools->where('month_id', $active_month->id)->first(); 
@endphp
<div class="container">
    <div class="container px-5">  
            <form action="{{url("/dl_all_day_template")}}" method="post">
                @csrf
                <button class="btn btn-secondary bi bi-box-arrow-down"> Πίνακας προς συμπλήρωση </button>
            </form>      
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/save_all_day_school/$school->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        {{-- <span class="input-group-text w-25"></span> --}}
                        <span class="input-group-text w-75"><strong>Καταχώρηση στοιχείων για το Ολοήμερο Πρόγραμμα για τον Μήνα {{$active_month->name}}</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Αριθμός τμημάτων έως τις 15:00</span>
                        <input name="nr_class_3" id="nr_class_3" type="number" class="form-control" required value="@if($old_data){{$old_data->nr_of_class_3}}@endif"><br>
                        @if($old_data)
                            <label class="form-control text-muted">Καταμετρήθηκαν {{$old_data->nr_of_pupils_3}} μαθητές</label>
                        @endif
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Αριθμός τμημάτων 15:00 - 16:00</span>
                        <input name="nr_class_4" id="nr_class_4" type="number" class="form-control" required value="@if($old_data){{$old_data->nr_of_class_4}}@endif"><br>
                        @if($old_data)
                            <label class="form-control text-muted">Καταμετρήθηκαν {{$old_data->nr_of_pupils_4}} μαθητές</label>
                        @endif
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Αριθμός τμημάτων Διευρυμένου Ολοήμερου</span>
                        <input name="nr_class_5" id="nr_class_5" type="number" class="form-control" required value="@if($old_data){{$old_data->nr_of_class_5}}@endif"><br>
                        @if($old_data)
                            <label class="form-control text-muted">Καταμετρήθηκαν {{$old_data->nr_of_pupils_5}} μαθητές</label>
                        @endif
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
                        <div class="col-sm-2 btn btn-warning bi bi-bricks rounded text-light" style="text-align:center;">
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
        <div class="container px-5 py-2">
            @if($old_data)
                    <div class="hstack gap-3">
                    <label class="form-control  w-25"><strong> Καταμέτρηση πρωινής υποδοχής: {{$old_data->nr_morning}}</strong></label>
                    {{-- <label class="form-control text-success"> Αρχείο που έχετε υποβάλλει: {{$old_data->file}} </label> --}}
                    <form action="{{url("/dl_all_day_file/$old_data->id")}}" method="post">
                        @csrf
                        <button class="btn btn-success bi bi-box-arrow-down"> Αρχείο που έχετε υποβάλλει: {{$old_data->file}}</button> 
                    </form>
                    
                    </div>
                @endif
        </div>

        <div class="py-5">
            <div class="table-responsive py-2">
                <table  id="" class="small text-center display table table-sm table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th id="search">Μήνας</th>
                        <th id="search">Λειτουργία</th>
                        <th id="">Μαθητές Πρωινή Υποδοχή</th>
                        <th id="">Τμήματα 15.00</th>
                        <th id="">Μαθητές 15.00</th>
                        <th id="">Τμήματα 16.00</th>
                        <th id="">Μαθητές 16.00</th>
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
                    <td> {{$one->nr_of_class_3}}</td>
                    <td> {{$one->nr_of_pupils_3}}</td>
                    <td> {{$one->nr_of_class_4}}</td>
                    <td> {{$one->nr_of_pupils_4}}</td>
                    <td> {{$one->nr_of_class_5}}</td>
                    <td> {{$one->nr_of_pupils_5}}</td>
                    <td> {{$one->comments}}</td>
                    <td>
                        <form action="{{url("/dl_all_day_file/$one->id")}}" method="post">
                        @csrf
                        <button class="btn btn-secondary bi bi-box-arrow-down"> </button> 
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