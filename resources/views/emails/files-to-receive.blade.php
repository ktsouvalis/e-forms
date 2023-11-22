@php
    if($stakeholder->stakeholder_type=="App\Models\School"){
        $type="school";
    }
    else if($stakeholder->stakeholder_type=="App\Models\Teacher"){
        $type="teacher";
    } 
@endphp

Σας ενημερώνουμε ότι στην εφαρμογή <a href="{{env('APP_URL')."/".$type."/".$stakeholder->stakeholder->md5}}" target="_blank">Ηλεκτρονικές Φόρμες</a> της Διεύθυνσης Π.Ε. Αχαΐας, στην ενότητα <strong>Διαμοιρασμός Αρχείων</strong>
: <b>{{$stakeholder->fileshare->department->name}}, {{$stakeholder->fileshare->name}} </b> έχουν προστεθεί αρχεία που σας αφορούν.
<br><br>
Παρακαλούμε επισκεφτείτε την <a href="{{env('APP_URL')."/".$type."/".$stakeholder->stakeholder->md5}}" target="_blank">εφαρμογή</a> προκειμένου να παραλάβετε τα αρχεία.
<br>
<div>
    <p><b>Διεύθυνση Πρωτοβάθμιας Εκπαίδευσης Ν. Αχαΐας - Ηλεκτρονικές Υπηρεσίες</b></p>
    
</div>
<small>
<em>
    Μπορείτε κάθε στιγμή να αποκτήσετε πρόσβαση στις "Ηλεκτρονικές Φόρμες" με τους εξής τρόπους:
    <ul>
        <li>
            Μέσω <a href="{{env('APP_URL')."/".$type."/".$stakeholder->stakeholder->md5}}" target="_blank">προσωποποιημένου συνδέσμου</a> που αποστέλλεται με mail αποκλειστικά για εσάς.
        </li>
        
        <p>Να ανακτήσετε τον προσωποποιημένο σύνδεσμο:</p>
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