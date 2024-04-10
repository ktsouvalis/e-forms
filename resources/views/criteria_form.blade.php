@if(!$$myapp->stakeholders->count())
@push('scripts')
<script>
    document.getElementById('selectAllSxeseis').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('#sxeseis input[type="checkbox"]').forEach(el => el.checked = true);
    });

    document.getElementById('deselectAllSxeseis').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('#sxeseis input[type="checkbox"]').forEach(el => el.checked = false);
    });

    document.getElementById('selectAllKladoi').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('#kladoi input[type="checkbox"]').forEach(el => el.checked = true);
    });

    document.getElementById('deselectAllKladoi').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('#kladoi input[type="checkbox"]').forEach(el => el.checked = false);
    });

    document.getElementById('revertSelectionSxeseis').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('#sxeseis input[type="checkbox"]').forEach(el => el.checked = !el.checked);
    });

    document.getElementById('revertSelectionKladoi').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('#kladoi input[type="checkbox"]').forEach(el => el.checked = !el.checked);
    });
</script>
@endpush
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
            <div class="col-6">
            <table id="sxeseis" class="">
                <tr>
                    <td colspan="2">
                        <a href="#" id="selectAllSxeseis">Όλα</a> |
                        <a href="#" id="deselectAllSxeseis">Κανένα</a> |
                        <a href="#" id="revertSelectionSxeseis">Αντιστροφή</a>
                    </td>
                </tr>
                @foreach($sxeseis as $sxesi)
                <tr><td>
                    <input type="checkbox" name="sx-{{$sxesi->id}}" value="{{$sxesi->id}}" id="sxesi{{$sxesi->id}}" @if($$myapp->accessCriteria and in_array($sxesi->id, json_decode($$myapp->accessCriteria->criteria, true)['sxesi_ergasias_id'])) checked @endif>
                    <label for="sxesi{{$sxesi->id}}">{{$sxesi->name}}</label>
                </td></tr>
                @endforeach
            </table>
            </div>
            <div class="col-2">
            <table id="kladoi">
                <tr>
                    <td colspan="2">
                        <a href="#" id="selectAllKladoi">Όλα</a> |
                        <a href="#" id="deselectAllKladoi">Κανένα</a> |
                        <a href="#" id="revertSelectionKladoi">Αντιστροφή</a>   
                    </td>
                </tr>
                @foreach($kladoi as $klados)
                <tr><td>
                    <input type="checkbox" name="kl-{{$klados}}" value="{{$klados}}" id="klados{{$klados}}" @if($$myapp->accessCriteria and in_array($klados, json_decode($$myapp->accessCriteria->criteria, true)['klados'])) checked @endif>
                    <label for="klados{{$klados}}">{{$klados}}</label>
                </td></tr>
                @endforeach
            </table>
            </div>
            <div class="col">
            <table>
            <tr><td>
            <input type="checkbox" class="m-1" name="eae_yes" id="eae_yes" @if($$myapp->accessCriteria and in_array(1, json_decode($$myapp->accessCriteria->criteria, true)['org_eae'])) checked @endif>
            <label for="eae_yes">Οργανική στην Ειδική</label>
            </td></tr>
            <tr><td>
            <input type="checkbox" class="m-1" name="eae_no" id="eae_no" @if($$myapp->accessCriteria and in_array(0, json_decode($$myapp->accessCriteria->criteria, true)['org_eae'])) checked @endif>
            <label for="eae_no">Οργανική στην Γενική</label>
            </td></tr>
            </table>
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