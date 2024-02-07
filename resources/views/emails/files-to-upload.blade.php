@php
    if($stakeholder->stakeholder_type=="App\Models\School"){
        $type="school";
    }
    else if($stakeholder->stakeholder_type=="App\Models\Teacher"){
        $type="teacher";
    } 
@endphp

Σας ενημερώνουμε ότι μέσω της εφαρμογής <strong>Ηλεκτρονικές Φόρμες</strong> της Διεύθυνσης Π.Ε. Αχαΐας, στην ενότητα <strong>Συλλογή Αρχείων</strong>: <b>{{$stakeholder->filecollect->name}} </b>, πρέπει να μας αποστείλετε ένα αρχείο που αφορά το <strong>{{$stakeholder->filecollect->department->name}}</strong>.
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