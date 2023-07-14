@if($microapp->accepts)
    <form action="{{url("/send_to_those_whocans_without_answer/microapp/$microapp->id")}}" method="post">
        @csrf
        <button class="btn btn-warning bi bi-eyeglasses"> Αποστολή υπενθύμισης σε όσους δεν έχουν υποβάλλει απάντηση</button>
    </form>
@endif