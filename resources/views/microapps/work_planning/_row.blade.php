@php
    $workplan = $user->workplans()->where('yearWeek', $yearWeek)->first();
    if($workplan)
        $programm = json_decode($workplan->programm);
@endphp
    <hr>
    <div class="row">
        <div class="col">
            <span class="fw-bold">{{ $user->display_name ?? $user->surname.' '.$user->name }}</span>
            @if($workplan)
                    <br>Δημ.: {{$workplan->created_at}} <br>
                    Αναν.:{{$workplan->updated_at}}
                @endif
        </div>
        <div class="col">
            <div><textarea name="mon" id="mon" @if($workplan) rows="10" @endif>@if($workplan){{$programm->mon}}@endif</textarea></div>
        </div>
        <div class="col">
            <div><textarea name="tue" id="tue" @if($workplan) rows="10" @endif>@if($workplan){{$programm->tue}}@endif</textarea></div>
        </div>
        <div class="col">
            <div><textarea name="wed" id="wed" @if($workplan) rows="10" @endif>@if($workplan){{$programm->wed}}@endif</textarea></div>
        </div>
        <div class="col">
            <div><textarea name="thu" id="thu" @if($workplan) rows="10" @endif>@if($workplan){{$programm->thu}}@endif</textarea></div>
        </div>
        <div class="col">
            <div><textarea name="fri" id="fri" @if($workplan) rows="10" @endif>@if($workplan){{$programm->fri}}@endif</textarea></div>
        </div>
        <div class="col">
            <div>
                <textarea name="comments" @if($workplan) rows="10" @endif>@if($workplan){{$workplan->comments}}@endif</textarea>
            </div>
        </div>
    </div>