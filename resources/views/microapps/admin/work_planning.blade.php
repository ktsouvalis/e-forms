<x-layout>
    @php
    if(request()->has('date')){
        $selected_day = Carbon\CarbonImmutable::parse(request()->input('date'));
    }else{
        $selected_day = Carbon\CarbonImmutable::now();
    }
@endphp
<div class="container">
    <div class="h4">Προγραμματισμός και υλοποίηση έργου Συμβούλου Εκπαίδευσης</div>
    <span class="" id="basic-addon2">Ημερομηνία</span>
    <input name="outing_date" type="date" class=""  aria-label="outing_date" aria-describedby="basic-addon1" value="{{$selected_day->isoFormat('YYYY-MM-DD')}}" onChange="location = window.location.pathname + '?date=' +this.value";>

</div>
@php
    $consultants = App\Models\Consultant::all();
@endphp
@php
    $year = $selected_day->year;
    $week = $selected_day->isoFormat('W');
    $yearWeek=$year.$week;
@endphp
<div class="px-5">
    <div class="row h5">
        Εβδομάδα: {{$selected_day->startOfWeek()->isoFormat('DD-MM-YYYY')}} έως {{$selected_day->startOfWeek()->add(4, 'day')->isoFormat('DD-MM-YYYY')}}
    </div>
    <div class="row">
        <div class="col">
            Σύμβουλος Εκπαίδευσης
        </div>
        <div class="col">
            <div class="fw-bold">Δευτέρα {{$selected_day->startOfWeek()->isoFormat('DD-MM-YYYY')}}</div>
        </div>
        <div class="col">
            <div class="fw-bold">Τρίτη {{$selected_day->startOfWeek()->add(1, 'day')->isoFormat('DD-MM-YYYY')}}</div>
        </div>
        <div class="col">
            <div class="fw-bold">Τετάρτη {{$selected_day->startOfWeek()->add(2, 'day')->isoFormat('DD-MM-YYYY')}}</div>
        </div>
        <div class="col">
            <div class="fw-bold">Πέμπτη {{$selected_day->startOfWeek()->add(3, 'day')->isoFormat('DD-MM-YYYY')}}</div>
        </div>
        <div class="col">
            <div class="fw-bold">Παρασκευή {{$selected_day->startOfWeek()->add(4, 'day')->isoFormat('DD-MM-YYYY')}}</div>
        </div>
        <div class="col">
            Παρατηρήσεις
        </div>
       
    </div>
@foreach($consultants as $user)
@if($user->id!==11)
@php
    $workplan = $user->workplans()->where('yearWeek', $yearWeek)->first();
    if($workplan)
        $programm = json_decode($workplan->programm);
@endphp
    <hr>
    <div class="row">
        <div class="col">
            <span class="fw-bold">{{$user->name}}  {{$user->surname}}</span>
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
@endif
@endforeach
</div>
</x-layout>