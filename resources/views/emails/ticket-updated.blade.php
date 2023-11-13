Ενημέρωση Δελτίου Τεχνικής Υποστήριξης από <b>{{$who_updated}}</b> <br> 
με θέμα <b>{{$ticket->subject}}</b>:<br><br>
<em>{!!html_entity_decode($new_string)!!}</em><br><br>

@if($link)
    Για να συνδεθείτε πατήστε <a href="{{url("/school/$link")}}"> εδώ </a> <br><br>
@endif
Αν είστε ήδη συνδεδεμένοι, για να συνεχίσετε ή για να κλείσετε το δελτίο πατήστε <a href="{{url("/ticket_profile/$ticket->id")}}">εδώ</a><br><br>


<strong><u>ΜΗΝ ΑΠΑΝΤΗΣΕΤΕ ΣΕ ΑΥΤΟ ΤΟ MAIL</u></strong>
