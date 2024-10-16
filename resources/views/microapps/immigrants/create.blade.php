<x-layout_school>
    @push('title')
        <title>Πρόσφυγες Μαθητές</title>
    @endpush
@php
    $school = Auth::guard('school')->user(); //check which school is logged in
    $active_month = App\Models\Month::getActiveMonth();
    $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
    $accepts = $microapp->accepts; //fetch microapp 'accepts' field
    $old_data = $school->immigrants->where('month_id', $active_month->id)->first(); 
@endphp
<div class="container">
    <div class="container px-5">  
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
                            {{-- <a href="{{url("/$appname/create")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a> --}}
                            <a href="{{route('immigrants.create')}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    @endif
                </form>
                
            </nav>
        </div> 
        <div class="container px-5 py-2">
            @if($old_data)
                {{-- <form action="{{url("/immigrants/download_file/$old_data->id")}}" method="get"> --}}
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
                        <form action="{{route("immigrants.download_file",["immigrant" => $one->id])}}" method="get">
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