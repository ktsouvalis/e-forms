@php
    if($stakeholder->stakeholder_type=="App\Models\School"){
        $type="school";
    }
    else if($stakeholder->stakeholder_type=="App\Models\Teacher"){
        $type="teacher";
    } 
@endphp

Σας ενημερώνουμε ότι στην εφαρμογή <a href="{{env('APP_URL')."/".$type."/".$stakeholder->stakeholder->md5}}" target="_blank">"Φόρμα Υποβολής"</a> της Διεύθυνσης Π.Ε. Αχαΐας, στην ενότητα <a href="{{env('APP_URL')."/".$type."_fileshare/".$stakeholder->stakeholder->id}}" target="_blank"><strong>Διαμοιρασμός Αρχείων</strong></a> υπάρχει η υποενότητα <b>{{$stakeholder->fileshare->department->name}}, {{$stakeholder->fileshare->name}} </b> με αρχεία που σας αφορούν.
<br><br>
<div>
    <em>Διεύθυνση Πρωτοβάθμιας Εκπαίδευσης Ν. Αχαΐας</em>
    <hr>
    <b> Πληροφορίες </b><br>
    @foreach($stakeholder->fileshare->department->users as $user)
        <div>{{$user->display_name}} {{$user->telephone}}</div>
    @endforeach
    <hr>
    <div>
    <small>
    Για τεχνικά θέματα της Φόρμας Υποβολής:<br>
    Κωνσταντίνος Στεφανόπουλος 2610229262<br>
    Κωνσταντίνος Τσούβαλης 26100229209<br>
    </small>
    </div>
</div>