@if(!$$myapp->stakeholders->count())
<div class="vstack">
<form action="{{url("/import_whocan_criteria/$myapp/$myid")}}" method="post" class="container-fluid">
    @csrf
    <div class="input-group">
        <span class="input-group-text w-100"><strong>Εκπαιδευτικοί με κριτήρια</strong></span>
    </div>
    <div class="input-group">
        @php
            $sxeseis = App\Models\SxesiErgasias::all();
            $kladoi = App\Models\Teacher::all()->pluck('klados')->unique();
        @endphp
        <div class="row">
            <div class="col m-3">
                <label for="sxeseis" class="row m-1"><b>Σχέση Εργασίας</b></label>
                <select name="sxeseis[]" id="sxeseis" multiple size="8" class="form-select row" style="width: 400px;" required>
                    @foreach($sxeseis as $sxesi)
                        <option value="{{$sxesi->id}}" @if($$myapp->accessCriteria and in_array($sxesi->id, json_decode($$myapp->accessCriteria->criteria, true)['sxesi_ergasias_id'])) selected @endif>{{$sxesi->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col m-3">
                <label for="kladoi" class="row m-1"><b>Κλάδος</b></label>
                <select name="kladoi[]" id="kladoi" multiple size="10" class="form-select row" style="width: 150px;" required>
                    @foreach($kladoi as $klados)
                        <option value="{{$klados}}" @if($$myapp->accessCriteria and in_array($klados, json_decode($$myapp->accessCriteria->criteria, true)['klados'])) selected @endif>{{$klados}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col m-3">
                <label for="org_eae" class="row m-1"><b>Ειδική / Γενική</b></label>
                <select name="org_eae[]" id="org_eae" multiple size="2" class=" form-select row" style="width: 250px;" required>
                    <option value="1" @if($$myapp->accessCriteria and in_array(1, json_decode($$myapp->accessCriteria->criteria, true)['org_eae'])) selected @endif>Οργανική στην Ειδική</option>
                    <option value="0" @if($$myapp->accessCriteria and in_array(0, json_decode($$myapp->accessCriteria->criteria, true)['org_eae'])) selected @endif>Οργανική στην Γενική</option>
                </select>
            </div>
        </div>
    </div>
    @if($myapp=='microapp')
    <div class="input-group m-1">
        <input type="checkbox" name="inform_whocan_table" id="inform_whocan_table" class="m-1">
        <label for="inform_whocan_table"><b>Ενημέρωση πίνακα ενδιαφερόμενων;</b></label>
    </div>
    @endif
    <div class="input-group py-1 px-1">
        <button type="submit" class="btn btn-primary bi bi-database-add"> Ενημέρωση Κριτηρίων</button>
    </div>
    
</form>
<form action="{{url("/delete_whocan_criteria/$myapp/$myid")}}" method="POST" class="container-fluid">
    @csrf
    <div class="input-group py-1 px-1">
        <button type="submit" class="btn btn-danger bi bi-x-circle"> Διαγραφή Κριτηρίων</button>
    </div>
</form>
</div>
@endif