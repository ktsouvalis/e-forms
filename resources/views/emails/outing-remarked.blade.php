<p>Προς: {{ $outing->school->name }}</p>
<p>Η Διεύθυνση καταχώρισε παρατήρηση στην εκδρομή:</p>
<p>
    <strong>Προορισμός:</strong> {{ $outing->destination }}<br>
    <strong>Ημερομηνία:</strong> {{ \Illuminate\Support\Carbon::parse($outing->outing_date)->format('d/m/Y') }}
</p>
<p><strong>Παρατήρηση:</strong> {{ $remark }}</p>
<p>Μπορείτε να τη δείτε στις <a href="{{ route('outings.create') }}">Ηλεκτρονικές Φόρμες</a>.</p>