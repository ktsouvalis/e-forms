<x-layout_school>
    <div class="container">
        @php
            $school = Auth::guard('school')->user();
            $existing_rec=App\Models\mAllDay::where('school_id', $school->id)->first();
            $accepts = App\Models\Microapp::where('url', '/'.$appname)->first()->accepts;
            $disabled = "";
            if(!$accepts)$disabled="disabled";
        @endphp
        @if(!$accepts)
        <div class="col-sm-2 btn btn-warning bi bi-bricks rounded text-light" style="text-align:center;">
            Η εφαρμογή δε δέχεται υποβολές
        </div>
        @endif
        <div class="container py-5">
        
        <form action="{{url("/save_all_day/$school->id")}}" method="post">
            @csrf
            <input type="hidden" name="microapp" value="{{$appname}}">
            <input type="text" name="input1" value="@if($existing_rec){{$existing_rec->first_number}}@endif" {{$disabled}}>
            <input type="text" name ="input2" value="@if($existing_rec){{$existing_rec->second_number}}@endif" {{$disabled}}>
            <button type="submit" {{$disabled}}>Submit</button>
        </form>
        </div>
    </div>
</x-layout_school>