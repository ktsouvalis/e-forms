<x-layout_consultant>
    @php
        $user = Auth::guard('consultant')->user(); //check which user is logged in
        if(!empty($_GET)){
            $selected_day = Carbon\CarbonImmutable::parse($_GET['date']);
        }else{
            $selected_day = Carbon\CarbonImmutable::now();
        }
    @endphp
    <div class="container">
        <span class="" id="basic-addon2">Ημερομηνία</span>
    <input name="outing_date" type="date" class=""  aria-label="outing_date" aria-describedby="basic-addon1" value="{{$selected_day->isoFormat('YYYY-MM-DD')}}" onChange="location = window.location.pathname + '?date=' +this.value";><br>
    </div>
    @php        
        $selected_day7 = $selected_day->add(7,'day');
        $day_array = [$selected_day,$selected_day7];
        //έχουμε καταλήξει στην ημερομηνίa
    
        // $workplan = $user->workplans()->where('yearWeek', $yearWeek)->first();
        // $programm = json_decode($workplan->programm);

    @endphp
    @foreach($day_array as $today)
    <hr>
    @php
        $year = $today->year;
        $week = $today->isoFormat('W');
        $yearWeek=$year.$week;
        $workplan = $user->workplans()->where('yearWeek', $yearWeek)->first();
        if($workplan)
            $programm = json_decode($workplan->programm);
    @endphp
    <div class="row">
        Εβδομάδα: {{$today->startOfWeek()->isoFormat('DD-MM-YYYY')}} έως {{$today->startOfWeek()->add(4, 'day')->isoFormat('DD-MM-YYYY')}}
    </div>
    <form action="{{url("/consultant_app/save_work_plan/$yearWeek")}}"  method="post">
        @csrf
        <div class="row">
            <div class="col">
                <div>Δευτέρα {{$today->startOfWeek()->isoFormat('DD-MM-YYYY')}}</div>
                <div><textarea name="mon" id="mon" cols="28" rows="8">@if($workplan){{$programm->mon}}@endif</textarea></div>
            </div>
            <div class="col">
                <div>Τρίτη {{$today->startOfWeek()->add(1, 'day')->isoFormat('DD-MM-YYYY')}}</div>
                <div><textarea name="tue" id="tue" cols="28" rows="8">@if($workplan){{$programm->tue}}@endif</textarea></div>
            </div>
            <div class="col">
                <div>Τετάρτη {{$today->startOfWeek()->add(2, 'day')->isoFormat('DD-MM-YYYY')}}</div>
                <div><textarea name="wed" id="wed" cols="28" rows="8">@if($workplan){{$programm->wed}}@endif</textarea></div>
            </div>
            <div class="col">
                <div>Πέμπτη {{$today->startOfWeek()->add(3, 'day')->isoFormat('DD-MM-YYYY')}}</div>
                <div><textarea name="thu" id="thu" cols="28" rows="8">@if($workplan){{$programm->thu}}@endif</textarea></div>
            </div>
            <div class="col">
                <div>Παρασκευή {{$today->startOfWeek()->add(4, 'day')->isoFormat('DD-MM-YYYY')}}</div>
                <div><textarea name="fri" id="fri" cols="28" rows="8">@if($workplan){{$programm->fri}}@endif</textarea></div>
            </div>
        </div>
            <div class="grid-container">
                <div class="item1">
                <label for="comments">Παρατηρήσεις: </label><br>
                    <textarea name="comments" cols = "100" rows="3" placeholder="Σημείωση για κάποια γενική παρατήρηση/σχόλιο" >@if($workplan){{$workplan->comments}}@endif</textarea>
                </div>
                <div class="item2" style="color:grey">
                    <br>
                    @if($workplan)
                        Δημιουργήθηκε: {{$workplan->created_at}} <br>
                        Ανανεώθηκε:{{$workplan->updated_at}}
                    @endif
                </div>
            </div>	
            <div class="input-group">
            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Αποθήκευση</button>
            </div>
    </form> 
  
    @endforeach 
        

        
</x-layout_consultant>