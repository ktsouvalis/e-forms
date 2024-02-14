@php
    if($stakeholder->stakeholder_type=="App\Models\School"){
        $type="school";
        $show_name = $stakeholder->stakeholder->name;
    }
    else if($stakeholder->stakeholder_type=="App\Models\Teacher"){
        $type="teacher";
        $show_name = "κ. ".$stakeholder->stakeholder->surname." ".$stakeholder->stakeholder->name;
    }
@endphp
<p>
Αποδέκτης: {{$show_name}}
</p>
Σας ενημερώνουμε ότι μέσω της εφαρμογής <strong>Ηλεκτρονικές Φόρμες</strong> της Διεύθυνσης Π.Ε. Αχαΐας, έχει ανοίξει η δυνατότητα υποβολής αρχείου στην ενότητα <b>{{$stakeholder->filecollect->name}}</b>.
<br><br>
Πατώντας στον παρακάτω σύνδεσμο μπορείτε να συνδεθείτε για περαιτέρω ενέργειες 
@if($type=="school")
    στη <strong>μοναδική καρτέλα </strong>του σχολείου.
@else
    στην <strong>προσωπική σας καρτέλα</strong>.
@endif

<p><a href="{{env('APP_URL')."/".$type."/".$stakeholder->stakeholder->md5}}" target="_blank"> {{env('APP_URL')."/".$type."/".$stakeholder->stakeholder->md5}}</a></p>
<br>

</div>

<small>
<em>
    Μπορείτε κάθε στιγμή να αποκτήσετε πρόσβαση @if($type=="school")
    στη μοναδική καρτέλα του Σχολείου 
@else
    στην προσωπική σας καρτέλα 
@endif
για τις "Ηλεκτρονικές Φόρμες" με τους εξής τρόπους:
    <ul>
        <li>
            Μέσω της Ιστοσελίδας της Δ/νσης <a href="https://dipe.ach.sch.gr" target="_blank">https://dipe.ach.sch.gr</a> στην ενότητα Ηλεκτρονικές Υπηρεσίες -> Υπηρεσίες για Εκπαιδευτικούς
        </li>    
        <li>
            Απευθείας <a href="https://dipeach.ddns.net/e-forms" target="_blank">https://dipeach.ddns.net/e-forms</a>
        </li>
    </ul> 
</em>
</small>

<hr>
<div>
    <p><b>Από τη Διεύθυνση Πρωτοβάθμιας Εκπαίδευσης Ν. Αχαΐας</b></p>
</div>