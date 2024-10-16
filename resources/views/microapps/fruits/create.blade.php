<x-layout_school>
    @push('title')
        <title>Διανομή Φρούτων</title>
    @endpush
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        $old_data = $school->fruit;
    @endphp
    <div class="container">
        <div class="container px-5">
            
                <nav class="navbar navbar-light bg-light">
                    {{-- <form action="{{url("/fruits")}}" method="post" enctype="multipart/form-data" class="container-fluid"> --}}
                    <form action="{{route('fruits.store')}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <input type="hidden" name="asks_to" value="insert">
                        <div class="input-group">
                            <span class="input-group-text w-25"></span>
                            <span class="input-group-text w-75"><strong>Καταχώρηση στοιχείων για το πρόγραμμα φρούτων</strong></span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Αριθμός Μαθητών που συμμετέχουν στο πρόγραμμα φρούτων</span>
                            <input name="students_number" id="students_number" type="number" class="form-control" placeholder="αριθμός μαθητών" aria-label="αριθμός μαθητών" aria-describedby="basic-addon2" required value="@if($old_data){{$old_data->no_of_students}}@endif"><br>
                        </div>
                        <div class="input-group">
        
                            <span class="input-group-text w-25 text-wrap">Αριθμός μαθητών από την Ουκρανία που συμμετέχουν (και έχουν περιληφθεί στον προηγούμενο αριθμό με το σύνολο των μαθητών): </span>
                            <input name="ukr_students_number" id="ukr_students_number" type="number" class="form-control" placeholder="αριθμός Ουκρανών μαθητών" aria-label="αριθμός Ουκρανών μαθητών" aria-describedby="basic-addon2" required value="@if($old_data){{$old_data->no_of_ukr_students}}@endif"><br>
                        </div>
                        
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Παρατηρήσεις</span>
                            <textarea name="comments" id="comments" class="form-control" cols="30" rows="5" style="resize: none;" placeholder="π.χ. Δύο (2) μαθητές δυσανεξία στη λακτόζη" >@if($old_data){{$old_data->comments}}@endif</textarea>
                        </div>
                        @if(!$accepts)
                           <div class='alert alert-warning text-center my-2'>
                               <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                            </div>
                        @else
                            <div class="input-group">
                                <span class="w-25"></span>
                                <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Προσθήκη</button>
                                {{-- <a href="{{url("/$appname/create")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a> --}}
                                <a href="{{route('fruits.create')}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                            </div>
                        @endif
                    </form>
                </nav>
                @if($old_data)
                    <div class="col-md-4 py-3" style="max-width:15rem">
                        <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
                            <div>Τελευταία ενημέρωση <br><strong> {{$old_data->updated_at}}</strong></div>
                        </div>
                    </div>
                @endif
                <hr>
                @include('microapps.new_ticket_button')
            </div>       
    </div>
</x-layout_school>