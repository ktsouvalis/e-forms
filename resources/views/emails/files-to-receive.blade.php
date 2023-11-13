@php
    if($stakeholder->stakeholder_type=="App\Models\School"){
        $type="school";
    }
    else if($stakeholder->stakeholder_type=="App\Models\Teacher"){
        $type="teacher";
    } 
@endphp

Σας ενημερώνουμε ότι στην εφαρμογή <a href="{{env('APP_URL')."/".$type."/".$stakeholder->stakeholder->md5}}" target="_blank">"Φόρμα Υποβολής"</a> της Διεύθυνσης Π.Ε. Αχαΐας, στην ενότητα <strong>Διαμοιρασμός Αρχείων</strong> υπάρχει η υποενότητα <b>{{$stakeholder->fileshare->department->name}}, {{$stakeholder->fileshare->name}} </b> με αρχεία που σας αφορούν.
<br><br>
<div>
    <em>Διεύθυνση Πρωτοβάθμιας Εκπαίδευσης Ν. Αχαΐας</em>
    <hr>
    <b> Πληροφορίες </b><br>
    @foreach($stakeholder->fileshare->department->users as $user)
        <div>{{$user->display_name}} {{$user->telephone}}</div>
    @endforeach
    <hr>
</div>