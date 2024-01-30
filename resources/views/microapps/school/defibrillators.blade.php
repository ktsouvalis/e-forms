<x-layout_school>
    @push('title')
        <title>Ενδιαφέρον για απινιδωτή</title>
    @endpush
@php
    $school = Auth::guard('school')->user(); //check which school is logged in
    $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
    $accepts = $microapp->accepts; //fetch microapp 'accepts' field
    if($school->defibrillators){
        $old_data = $school->defibrillators; 
    }else{
        $old_data = null;
    }
    
@endphp
<div class="container">
    <div class="container px-5">  
            <form action="{{url("/dl_defibrillators_document")}}" method="post">
                @csrf
                <button class="btn btn-secondary bi bi-box-arrow-down"> Σχετική εγκύκλιος </button>
            </form>      
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/save_defibrillators")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        {{-- <span class="input-group-text w-25"></span> --}}
                        <span class="input-group-text w-75"><strong>Εκδήλωση ενδιαφέροντος, μέσω υποβολής πρακτικού, για <my_text class="text-success">παροχή απινιδωτή</my_text></strong></span>
                    </div>
                   
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Παρατηρήσεις</span>
                        <textarea name="comments" id="comments" class="form-control" cols="30" rows="2" style="resize: none;" >@if($old_data){{$old_data->comments}}@endif</textarea>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon4">Υποβολή πρακτικού</span>
                        <input name="record_file" type="file" class="form-control" @if(!$old_data) {{"required"}} @endif><br>
                    </div>
                    @if(!$accepts)
                        <div class='alert alert-warning text-center my-2'>
                            <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                        </div>
                        <div>
                            <span class="input-group-text w-100">Η δυνατότητα υποβολής απενεργοποιήθηκε 29 Ιανουαρίου 2024 και ώρα 12:00</span>
                        </div>
                    @else
                        <div class="input-group">
                            <span class="w-25"></span>
                            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            <a href="{{url("/school_app/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                        <div>
                            <span class="input-group-text w-100">Η φόρμα θα είναι ενεργή για υποβολές/τροποποιήσεις μέχρι 29 Ιανουαρίου 2024 και ώρα 12:00</span>
                        </div>
                    @endif
                </form>
                
            </nav>
        </div> 
        <div class="container px-5 py-2">
            @if($old_data)
                <form action="{{url("/dl_defibrillators_file/$old_data->id")}}" method="post">
                    @csrf
                   Αρχείο που έχετε υποβάλλει: <button class="btn btn-success bi bi-box-arrow-down">  {{$old_data->file}}</button> 
                </form>   
            @endif
            {{-- @include('microapps.new_ticket_button') --}}
        </div>  
</div>
</x-layout_school>