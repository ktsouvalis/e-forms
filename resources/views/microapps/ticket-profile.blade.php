@push('title')
    <title>Δελτίο {{$ticket->id}}</title>
@endpush
@push('links')
    <link href="../summernote-0.8.18-dist/summernote-lite.min.css" rel="stylesheet">
@endpush
@push('scripts')
    <script src="../summernote-0.8.18-dist/summernote-lite.min.js"></script>
    <script>
       $(document).ready(function () {
        $('#comments').summernote({
            height: 200, // Adjust the height as needed
            width:600,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['list', ['ul', 'ol']],
                ['link', ['link']]
            ],
            placeholder: 'Απάντηση...',
            lang: 'el-GR' // Set language to Greek
        });
        $('#comments').prop('width', '600px');
    });
    </script>
@endpush
@php
    $accepts = App\Models\Microapp::where('url', '/'.$appname)->first()->accepts; //fetch microapp 'accepts' field
@endphp
<div class="container">
<div class="container px-5">   
    <div class=" hstack gap-2"> 
        <div><b>Θέμα:</b>{{$ticket->subject}}</div>         
        <div><b>ID δελτίου:</b> {{$ticket->id}}</div>          
        <div><b>Τηλέφωνο Σχολείου:</b> {{$ticket->school->telephone}}</div>             
    </div>
    @if(!$ticket->solved)
        <div class='alert alert-warning text-center'>
            Το δελτίο είναι ανοιχτό
        </div>
    @else
        <div class='alert alert-success text-center'>
            Το δελτίο είναι κλειστό. Θα ανοίξει αυτόματα, αν προσθέσετε κάποιο σχόλιο
        </div>
    @endif
            <div class="container-fluid">
                <div class="row">
                    <div class="col"></div>
                    <div class="col-md-6 py-2" style="max-width:60rem">
                        <div style="font-size:small">
                            <i>{{$ticket->created_at}}</i>, <u>Αρχικό Μήνυμα</u> από<b> {{$ticket->school->name}}</b>: <br>
                        </div>
                        <div class="card py-2" style="background-color:Gainsboro; text-decoration:none; font-size:small">
                            <div class="m-1">{{$ticket->comments}}</div>
                        </div>  
                    </div>
                    <div class="col"></div>
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
                    $name = $one_post->ticketer->display_name;
                }  
            @endphp

                <div class="container-fluid my-3">
                    <div class="row">
                        <div class="col"></div>
                        <div class="col-md-6">
                            <div style="font-size: small">
                                <i>{{$one_post->created_at}}</i>, <b>{{$name}}</b>: <br>
                            </div>
                            <div class="card py-2" style="background-color: {{$color}}; text-decoration: none; font-size: small">
                                <div class="m-1">{!!html_entity_decode($one_post->text)!!}</div>
                            </div>
                        </div>
                        <div class="col"></div>
                    </div>
                </div>
        @endforeach
        <hr>
        <nav class="navbar navbar-light bg-light justify-content-center">
        <form action="{{url("/update_ticket/$ticket->id")}}" method="post" enctype="multipart/form-data" class="container-fluid justify-content-center">
            @csrf
            <span class="input-group-text"><strong>Προσθήκη Νέου Σχολίου </strong></span>
            <div class="input-group justify-content-center">
                <textarea name="comments" id="comments" class="form-control" cols="30" rows="5" style="width: 600px; resize: none;" placeholder="Απάντηση" required></textarea>
            </div>
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
    @endif
    @php
        if($ticket->posts<>null)
            $text = $ticket->updated_at;
        else
            $text = $ticket->posts->last()->created_at;
    @endphp
    <div class="col-md-4 py-3" style="max-width:15rem">
        <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
            <div>Τελευταία ενημέρωση δελτίου <br><strong> {{$text}}</strong></div>
        </div>
    </div>
    </div>
</div>
