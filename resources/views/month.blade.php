<x-layout>
    @php
        $all_months=App\Models\Month::all();
        $active_month = App\Models\Month::getActiveMonth()->id;        
    @endphp
    <div class="container">
        <form action="{{url('/set_active_month')}}" method="post">
            @csrf
            <div class="hstack gap-2">
            <div class="input-group">
                <span class="input-group-text w-25">Ενεργός μήνας</span>
                <select name="active_month" class="form-select" aria-label="">
                    @foreach($all_months as $month)
                    @php
                        $selected=null;
                        if($month->id == $active_month)
                            $selected="selected";   
                    @endphp
                    <option {{$selected}} value="{{$month->id}}">{{$month->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group">
                <button class="btn btn-primary bi bi-calendar2-heart"> Αλλαγή Ενεργού Μήνα</button>
            </div>
            </div>
        </form>
    </div>
</x-layout>