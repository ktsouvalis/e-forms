@php
    $accepts = App\Models\Microapp::where('url', '/'.$appname)->first()->accepts; //fetch microapp 'accepts' field
@endphp
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
            <div class="container-fluid">
                <div class="col-md-4 py-2" style="max-width:60rem">
                    <div style="font-size:small">
                        <i>{{$ticket->created_at}}</i>, <b>{{$ticket->school->name}}</b>: <br>
                    </div>
                    <div class="card py-2" style="background-color:Gainsboro; text-decoration:none; font-size:small">
                        <div class="m-1">{{$ticket->comments}}</div>
                    </div>  
                </div>
            </div>
            <hr>
        @foreach($ticket->posts as $one_post)
            @php
                if($one_post->ticketer_type=='App\Models\School'){
                    $color = 'Gainsboro';
                    $name = $one_post->ticketer->name;
                }  
                else{
                    $color = 'LightCyan';
                    $name = $one_post->ticketer->username;
                }  
            @endphp
                <div class="container-fluid">
                    <div class="col-md-4 py-2" style="max-width:rem">
                        <div style="font-size:small">
                            <i>{{$one_post->created_at}}</i>, <b>{{$name}}</b>: <br>
                        </div>
                        <div class="card py-2" style="background-color:{{$color}}; text-decoration:none; font-size:small">
                            <div class="m-1">{{$one_post->text}}</div>
                        </div>  
                    </div>
                </div>
                <hr>
        @endforeach
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

    <div class="col-md-4 py-3" style="max-width:15rem">
        <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
            <div>Τελευταία ενημέρωση δελτίου <br><strong> {{$ticket->updated_at}}</strong></div>
        </div>
    </div>
    </div>
</div>