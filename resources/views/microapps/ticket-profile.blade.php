<div class="container">
<div class="container px-5">   
    @if(!$ticket->solved)
        <div class='alert alert-warning text-center'>
            Το δελτίο είναι ανοιχτό
        </div>
    @else
        <div class='alert alert-success text-center'>
            Το δελτίο είναι κλειστό. Θα ανοίξει αυτόματα, αν προσθέσετε κάποιο σχόλιο
        </div>
    @endif
    <nav class="navbar navbar-light bg-light">
        <form action="{{url("/update_ticket/$ticket->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
            @csrf
            <div class="input-group">
                <span class="input-group-text w-25"></span>
                <span class="input-group-text w-75"><strong>Επεξεργασία αιτήματος Τεχνικής Υποστήριξης</strong></span>
            </div>
            
            <div class="input-group">
                <span class="input-group-text w-25 text-wrap">Θέμα:</span>
                <input name="subject" id="subject" type="text" class="form-control" placeholder="Σύντομη Περιγραφή" aria-label="Θέμα" aria-describedby="basic-addon2" value="{{$ticket->subject}}" disabled><br>
            </div>
            
            <div class="input-group">
                <span class="input-group-text w-25 text-wrap">Περιγραφή</span>
                <textarea name="old_comments" id="old_comments" class="form-control" cols="30" rows="20" style="resize: none;" disabled>{{$ticket->comments}}</textarea>
            </div>

            <div class="input-group">
                <span class="input-group-text w-25 text-wrap">Απάντηση</span>
                <textarea name="comments" id="comments" class="form-control" cols="30" rows="5" style="resize: none;" placeholder="Απάντηση" required></textarea>
            </div>
            @if(!$accepts)
                <div class="col-sm-2 btn btn-warning bi bi-bricks rounded text-light" style="text-align:center;">
                    Η εφαρμογή δε δέχεται υποβολές
                </div>
            @else
                <div class="input-group">
                    <span class="w-25"></span>
                    <button type="submit" class="btn btn-primary m-2"> <div class="fa-solid fa-headset"></div> Υποβολή</button>
                    <a href="{{url("/ticket_profile/$ticket->id")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                </div>
            @endif
        </form>
    </nav>
    @if(!$ticket->solved)
        <form action="{{url("/mark_as_resolved/$ticket->id")}}" method="post">
            @csrf
            <button type="submit" class="btn btn-success bi bi-envelope"> Κλείσιμο δελτίου</button>
        </form>
    {{-- @else
        <form action="{{url("/mark_as_open/$ticket->id")}}" method="post">
            @csrf
            <button type="submit" class="btn btn-warning bi bi-envelope-open"> Άνοιγμα δελτίου</button>
        </form> --}}
    @endif
    </div>
</div>