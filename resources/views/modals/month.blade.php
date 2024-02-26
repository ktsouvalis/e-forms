@php
    $all_months = App\Models\Month::all();
    $active_month = App\Models\Month::getActiveMonth()->id;
@endphp
<div class="modal fade" id="monthModal" tabindex="-1" role="dialog" aria-labelledby="monthModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="monthModalLabel">Αλλαγή Ενεργού Μήνα</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                {{-- <span aria-hidden="true">&times;</span> --}}
            </button>
        </div>
        <form action="{{url('/set_active_month')}}" method="post">
            @csrf
            <div class="modal-body">
                <div class="form-group">
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
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary bi bi-calendar2-heart"> Αλλαγή</button>
            </div>   
        </form>
    </div>
    </div>
</div>