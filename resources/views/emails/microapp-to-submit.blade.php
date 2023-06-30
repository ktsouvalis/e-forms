@php
    if($stakeholder->stakeholder_type=="App\Models\School"){
        $type="school";
    }
    else if($stakeholder->stakeholder_type=="App\Models\Teacher"){
        $type="teacher";
    } 
@endphp
Σας ενημερώνουμε ότι στην εφαρμογή <a href="{{env('APP_URL')."/".$type."/".$stakeholder->stakeholder->md5}}" target="_blank">"Φόρμα Υποβολής"</a> της Διεύθυνσης Π.Ε. Αχαΐας υπάρχει μια νέα ενότητα <a href="{{env('APP_URL')."/".$type."_app".$stakeholder->microapp->url}}" target="_blank"><b> {{$stakeholder->microapp->name}} </b></a> προς συμπλήρωση από εσάς.<br>
<br><br>
<div>
    <em>Διεύθυνση Πρωτοβάθμιας Εκπαίδευσης Ν. Αχαΐας</em>
    <hr>
    <b> Πληροφορίες </b><br>
    
    @foreach($stakeholder->microapp->users as $user)
        @if($user->user->id!=1 and $user->user->id!=2)
            <div>{{$user->user->display_name}} {{$user->user->telephone}}</div>
        @endif
    @endforeach
    <hr>
    <div class="text-muted">
    <small>
    Για τεχνικά θέματα της Φόρμας Υποβολής:<br>
    Κωνσταντίνος Στεφανόπουλος 2610229262<br>
    Κωνσταντίνος Τσούβαλης 26100229209<br>
    </small>
    </div>
</div>