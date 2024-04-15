<x-layout_school>
    @push('title')
        <title>Θρανία</title>
    @endpush
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        $old_data = $school->desks;
    @endphp
    <div class="container">
        <div class="container px-5">
            <h5 class="py-5">
                Παρακαλούμε για την υποβολή αριθμού θρανίων προς διάθεση <strong>ΜΟΝΟ ΣΤΗΝ ΠΕΡΙΠΤΩΣΗ</strong> που υπάρχουν περισσευούμενα θρανία προς διάθεση σε άλλη Σχολική Μονάδα</p>
            </h5>
                <nav class="navbar navbar-light bg-light">
                    {{-- <form action="{{url("/fruits")}}" method="post" enctype="multipart/form-data" class="container-fluid"> --}}
                    <form action="{{route('desks.store')}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <div class="input-group">
                            <span class="input-group-text w-25"></span>
                            <span class="input-group-text w-75">Καταχώρηση στοιχείων για περισσευούμενα θρανία</span>
                            
                        </div>
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Αριθμός Θρανίων προς διάθεση</span>
                            <input name="number" id="number" type="number" class="form-control" placeholder="" aria-label="αριθμός μαθητών" aria-describedby="basic-addon2" required value="@if($old_data){{$old_data->number}}@endif">
                        </div>
                        
                        
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Παρατηρήσεις</span>
                            <textarea name="comments" id="comments" class="form-control" cols="30" rows="5" style="resize: none;" placeholder="π.χ. τα θρανία δεν είναι σε καλή κατάσταση" >@if($old_data){{$old_data->comments}}@endif</textarea>
                        </div>
                        @if(!$accepts)
                           <div class='alert alert-warning text-center my-2'>
                               <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                            </div>
                        @else
                            <div class="input-group">
                                <span class="w-25"></span>
                                @if($old_data)
                                    <button type="submit" action="save" value="update" class="btn btn-primary m-2 bi bi-pencil-square"> Ενημέρωση</button>
                                @else
                                    <button type="submit" action="save" value="create" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                                @endif
                               
                                <a href="{{route('desks.create')}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
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
            </div>       
    </div>
</x-layout_school>