<x-layout_school>
    @push('title')
        <title>Πρόσφυγες Μαθητές</title>
    @endpush
@php
    $school = Auth::guard('school')->user(); //check which school is logged in
    $active_month = App\Models\Month::getActiveMonth();
    //$active_month = App\Models\Month::getCustomMonth(4); //change also custom month in immigrants controller
    $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
    $accepts = $microapp->accepts; //fetch microapp 'accepts' field
    $old_data = $school->immigrants->where('month_id', $active_month->id)->first(); 
    //dd($old_data);
@endphp
<div class="container">
    <div class="container px-5">  
            <div class="form-check ms-2 m-5 d-flex justify-content-center align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="mx-5 mb-3 p-3 bg-light border-start border-info border-4">
    <p class="mb-2 text-secondary">
        Αν κατά τον τρέχοντα μήνα δεν υπάρχουν πρόσφυγες μαθητές στο Σχολείο σας πατήστε την ακόλουθη δήλωση:
    </p>
    <div class="d-flex align-items-center">
        <input class="form-check-input me-2 m-3" type="checkbox" id="no_refugees" name="no_refugees" 
            @if($old_data && $old_data->no_refugees == 1) checked @endif>
        <label class="form-check-label" for="no_refugees">
            <strong>Δεν έχω πρόσφυγες μαθητές</strong>
        </label>
    </div>
    
</div>
                </div>
            </div>
            </div>
            {{-- <form action="{{url("/immigrants/download_template/yes")}}" method="get"> --}}
            <form action="{{route('immigrants.download_template')}}" method="get">
                <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Πίνακας προς συμπλήρωση </button>
            </form>      
            <nav class="navbar navbar-light bg-light">
                {{-- <form action="{{url("/immigrants")}}" method="post" enctype="multipart/form-data" class="container-fluid"> --}}
                <form action="{{route('immigrants.store')}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        {{-- <span class="input-group-text w-25"></span> --}}
                        <span class="input-group-text w-75"><strong>Καταχώρηση στοιχείων για τους Πρόσφυγες Μαθητές για τον Μήνα <my_text class="text-success">{{$active_month->name}}</my_text></strong></span>
                    </div>
                   
                    <div class="input-group">
                        <span class="input-group-text w-25 text-wrap">Παρατηρήσεις</span>
                        <textarea name="comments" id="comments" class="form-control" cols="30" rows="5" style="resize: none;" >@if($old_data){{$old_data->comments}}@endif</textarea>
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
                            <a href="{{route('immigrants.create')}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    @endif
                </form>
            </nav>
        </div>
        <div class="container px-5 py-2">
            @if($old_data && $old_data->file)
                <form action="{{route("immigrants.download_file", ['immigrant' => $old_data->id])}}" method="get">
                   Αρχείο που έχετε υποβάλλει: <button class="btn btn-success bi bi-box-arrow-down" title="Λήψη αρχείου">  {{$old_data->file}}</button> 
                </form>   
            @endif
            {{-- @include('microapps.new_ticket_button') --}}
        </div>

        <div class="py-3">
            <div class="table-responsive py-2">
                <table  id="" class="small text-center display table table-sm table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th id="search">Μήνας</th>
                        <th id="">Σχόλια</th>
                        <th>Αρχείο</th>
                        <th>Τελευταία ενημέρωση</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($school->immigrants->sortByDesc('month_id') as $one)
                    <tr>
                    <td> {{$one->month->name}}</td>
                    <td> {{$one->comments}}</td>
                    <td>
                        {{-- <form action="{{url("/immigrants/download_file/$one->id")}}" method="get"> --}}
                        @if($one->file)
                        <form action="{{route("immigrants.download_file",["immigrant" => $one->id])}}" method="get">
                            <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> </button> 
                        </form>   
                        @endif
                    </td>
                    <td>{{$one->updated_at}}</td>
                    </tr>
                @endforeach
                </tbody>
                </table>
            </div>
        
        </div>  
</div>
@push('scripts')
<script>
$(document).ready(function() {
    $('#no_refugees').change(function() {
        const isChecked = $(this).is(':checked');
        
        $.ajax({
            url: '{{ route("immigrants.no_refugees") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                no_refugees: isChecked ? 1 : 0,
                month_id: {{ $active_month->id }}
            },
            success: function(response) {
                if(response.success) {
                    alert('Η απάντησή σας καταγράφηκε. Δε χρειάζεται να υποβάλετε πίνακα ούτε να πατήσετε το κουμπί υποβολής.');
                }
            },
            error: function(xhr) {
                console.log(xhr);
                alert('Σφάλμα κατά την αποθήκευση. Παρακαλώ δοκιμάστε ξανά.');
            }
        });
    });
});
</script>
@endpush
</x-layout_school>