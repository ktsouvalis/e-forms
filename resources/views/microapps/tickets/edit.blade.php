@push('title')
    <title>Δελτίο {{$ticket->id}}</title>
@endpush
@push('links')
    <link href="{{asset("summernote-0.8.18-dist/summernote-lite.min.css")}}" rel="stylesheet">
@endpush
@push('scripts')
    <script src="{{asset("summernote-0.8.18-dist/summernote-lite.min.js")}}"></script>
    <script>
        $(document).ready(function() {
            $('.summernote').each(function() {
                $(this).summernote({
                    width:"100%",
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['list', ['ul', 'ol']],
                        ['link', ['link']],
                    ],
                    lang: 'el-GR',
                });
            });
        });
    </script>
    <script>
        var ticketNeededVisitUrl = '{{ route("tickets.visit", ["ticket" =>"mpla"]) }}';
        var editPostUrl = '{{ route("tickets.update_post", ["ticket" =>"mpla"]) }}'
    </script>
    <script src="{{asset("ticket_visit.js")}}"></script>
    <script src="{{asset("ticket_edit_post.js")}}"></script>
    <script></script>
@endpush
@php
    $accepts = App\Models\Microapp::where('url', '/tickets')->first()->accepts; //fetch microapp 'accepts' field
@endphp
<div class="container">
<div class="">
    <div class="hstack gap-2"> 
        <div><b>Θέμα: </b>{{$ticket->subject}}</div>         
        <div><b>ID δελτίου: </b>{{$ticket->id}}</div>          
        <div><b>Τηλέφωνο Σχολείου: </b>{{$ticket->school->telephone}}</div>             
    </div>
    @if(!$ticket->solved)
        <div class='alert alert-warning text-center'>
            Το δελτίο είναι ανοιχτό
        </div>
    @else
        <div class='alert alert-dark text-center'>
            <b>Το δελτίο είναι κλειστό. Θα ανοίξει αυτόματα, αν προσθέσετε κάποιο σχόλιο</b>
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
                            <div class="m-1">{!!html_entity_decode($ticket->comments)!!}</div>
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
                    $showPencil = Auth::guard('school')->check() && Auth::guard('school')->user()->id == $one_post->ticketer->id;
                }  
                else{
                    $color = 'LightCyan';
                    $name = $one_post->ticketer->display_name;
                    $showPencil = Auth::guard('web')->check() && Auth::guard('web')->user()->id == $one_post->ticketer->id;
                }  
            @endphp

                <div class="container-fluid my-3">
                    <div class="row">
                        <div class="col"></div>
                        <div class="col-md-6">
                            <div style="font-size: small">
                                <i>{{$one_post->updated_at}}</i>, <b>{{$name}}</b>: <br>
                            </div>
                            <div class="card py-2" style="background-color: {{$color}}; text-decoration: none; font-size: small">
                                <div class="m-1 post-text">{!!html_entity_decode($one_post->text)!!}</div>
                                
                                <div class="post-editor" style="display: none;">
                                    <textarea class="summernote">{!!$one_post->text!!}</textarea>
                                    <button class="save-button" data-id="{{$one_post->id}}" data-ticket-id="{{ $ticket->id }}">Save</button>
                                    <button class="cancel-button">Cancel</button>
                                </div>
                            </div>
                            <div class="edited-label"></div>
                            @if($showPencil and $accepts)
                                <button class="edit-button"><i class="fa fa-pencil"></i></button>
                            @endif
                        </div>
                        <div class="col"></div>
                    </div>
                </div>
        @endforeach
        <hr>
        <nav class="navbar navbar-light bg-light justify-content-center">
        {{-- <form action="{{url("/tickets/$ticket->id")}}" method="post" enctype="multipart/form-data" class="container-fluid justify-content-center"> --}}
        <form action="{{route('tickets.update', ['ticket' => $ticket->id])}}" method="post" enctype="multipart/form-data" class="container-fluid justify-content-center">
            @method('PUT')
            @csrf
            <span class="input-group-text"><strong>Προσθήκη Νέου Σχολίου </strong></span>
            <div class="input-group justify-content-center">
                <textarea class="summernote" name="comments"  class="form-control"></textarea>
            </div>
            </div>
            @if(!$accepts)
                <div class='alert alert-warning text-center my-2'>
                    <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                </div>
            @else
                    <div class="hstack">
                    <div class="input-group ">
                        <span class="w-25"></span>
                        <span class="input-group-text ">Επισύναψη<br></span>
                        <input name="attachment" type="file" class="form-control">
                    </div>
                    <div class="input-group">
                        <span cl></span>
                        <button type="submit" class="btn btn-primary m-2"> <div class="fa-solid fa-headset"></div> Υποβολή</button>
                       
                    </div>
                    </div>
                    <div class="input-group ">
                    <span class="w-25"></span>
                    <small>Μπορείτε να επισυνάψετε αρχεία (jpeg, png, docx, pdf, xlsx) ή/και κάποιο σχόλιο</small>
                    </div>
            @endif
        </form>
    </nav>
    <hr>
    @if(!$ticket->solved)
        {{-- <form action="{{url("/tickets/mark_as_resolved/$ticket->id")}}" method="post"> --}}
        <form action="{{route('tickets.mark_as_resolved', ['ticket' => $ticket->id])}}" method="post">
            @csrf
            <strong>Αν θεωρείτε ότι λύθηκε το πρόβλημα: </strong>
            <button type="submit" class="btn btn-success bi bi-envelope"> Κλείσιμο δελτίου</button>
        </form>
    @endif
    @if(Auth::user())
        <input type="checkbox" id="visit" class="ticket-checkbox" data-ticket-id="{{ $ticket->id }}" {{ $ticket->needed_visit ? 'checked' : '' }}>
        <label for="visit"> Πραγματοποιήθηκε επίσκεψη;</label>
    @endif
    @php
        $maxPostUpdate = $ticket->posts->isNotEmpty() ? $ticket->posts->max('updated_at') : null;
        $text = max($ticket->updated_at, $maxPostUpdate);
    @endphp
    <div class="hstack gap-2">
    <div class="col-md-4 py-3" style="max-width:15rem">
        <div class="card py-3" style="background-color:Gainsboro; text-decoration:none; text-align:center; font-size:small">
            <div>Τελευταία ενημέρωση δελτίου <br><strong> {{$text}}</strong></div>
        </div>
    </div>

     @if(Auth::check())
        @push('scripts')
            <script src="{{asset('copylink.js')}}"></script>
        @endpush
        @php
            $url = $ticket->school->md5;    
        @endphp
        {{-- <button class="copy-button btn btn-outline-secondary bi bi-clipboard" data-clipboard-text="{{url("/school/$url")}}"> </button> --}}
        <button class="copy-button btn btn-outline-secondary bi bi-clipboard" data-clipboard-text="{{route('school_login', ['md5' => $url])}}"> </button>
    @endif
    </div>
    <div class="files m-2">
        @php
            $school = Auth::guard('school')->user();
            $directory = "/tickets/$ticket->id";
            $files=Storage::disk('local')->files($directory);          
        @endphp
        @if(count($files)>0)
        <hr>
        Συνημμένα αρχεία δελτίου:<br>
        <div class="hstack gap-3">
        @foreach($files as $file)
                                
            @php
                $basename = basename($file);
            @endphp
            {{-- <form action="{{url("/tickets/get_ticket_file/$ticket->id/$basename")}}" method="get"> --}}
            <form action="{{route('tickets.download_file', ['ticket' => $ticket->id, 'original_filename' => $basename])}}" method="get">
                <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$basename}}</button>
            </form> 
        @endforeach
        </div>
        @endif
    </div>
    </div>
<div id="bottom"></div>    
</div>
