<hr>
<form action="/microapp_create_ticket/{{$appname}}" method="post">
    @csrf
    <strong>Δημιουργία Δελτίου Τεχνικής Δημιουργία Δελτίου <u>Τεχνικής</u> Υποστήριξης <br> για την ενότητα "{{$microapp->name}}"<br></strong>
    <input class="my-1" style="width:100%;" type="text" name="comments" placeholder="Περιγραφή" required><br>
    @if($appname=="all_day_school" and App\Models\microapps\AllDaySchool::where('school_id',Auth::guard('school')->id())->where('month_id', App\Models\Month::getActiveMonth()->id)->count())
        <input type="checkbox" name="attachment" id="attachment">
        <label for="attachment">Να συμπεριληφθεί το αρχείο της τελευταίας υποβολής</label><br>
    @endif
    <button class="btn btn-warning my-1"><i class="fa-solid fa-headset"></i> Δημιουργία</button>
</form>
<hr>