<x-layout>
    @php
        $consultants = App\Models\Consultant::where('is_local', true)->get();
        
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
    @endphp
    @push('title')
        <title>{{$microapp->name}}</title>
    @endpush    
    @include('microapps.microapps_admin_before') {{--Visibility and acceptability buttons and messages--}} 
    @php
        if(request()->has('date')){
            $selected_day = Carbon\CarbonImmutable::parse(request()->input('date'));
        }else{
            $selected_day = Carbon\CarbonImmutable::now();
        }
        $year = $selected_day->year;
        $week = $selected_day->isoFormat('W');
        $yearWeek=$year.$week;
    @endphp
<div class="container">
    <span class="" id="basic-addon2">Ημερομηνία</span>
    <input name="outing_date" type="date" class=""  aria-label="outing_date" aria-describedby="basic-addon1" value="{{$selected_day->isoFormat('YYYY-MM-DD')}}" onChange="location = window.location.pathname + '?date=' +this.value";>

</div>

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
            @include('microapps.work_planning._row', ['user' => $user])
        @endif
    @endforeach

    @php
        $workplanUsers = App\Models\User::whereIn('department_id', [8, 9, 10])->get();
    @endphp

    <hr>
    <div class="row h5 mt-4">Υπάλληλοι</div>

    @foreach($workplanUsers as $wpUser)
        @include('microapps.work_planning._row', ['user' => $wpUser])
    @endforeach
</div>
</x-layout>