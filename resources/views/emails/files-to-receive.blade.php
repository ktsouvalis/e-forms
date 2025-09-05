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
Σας ενημερώνουμε ότι μέσω της εφαρμογής <strong>Ηλεκτρονικές Φόρμες</strong> της Διεύθυνσης Π.Ε. Αχαΐας, έχει ανοίξει η δυνατότητα παραλαβής αρχείου στην ενότητα <b>{{$stakeholder->fileshare->name}}</b>.
<br><br>

Η σύνδεση στις Ηλεκτρονικές Φόρμες για Σχολεία και Εκπαιδευτικούς πραγματοποιείται με χρήση των κωδικών του Πανελλήνιου Σχολικού Δικτύου.

<br><br>

https://dipeach.sch.gr/e-forms
<hr>
<div>
    <p><b>Από τη Διεύθυνση Πρωτοβάθμιας Εκπαίδευσης Ν. Αχαΐας</b></p>
</div>