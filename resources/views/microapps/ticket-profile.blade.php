@push('title')
    <title>Δελτίο {{$ticket->id}}</title>
@endpush
@push('links')
    <link href="../summernote-0.8.18-dist/summernote-lite.min.css" rel="stylesheet">
@endpush
@push('scripts')
    <script src="../summernote-0.8.18-dist/summernote-lite.min.js"></script>
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
                    lang: 'el-GR', // Set language to Greek
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('body').on('change', '.ticket-checkbox', function() {
                
                const ticketId = $(this).data('ticket-id');
                const isChecked = $(this).is(':checked');
                // Get the CSRF token from the meta tag
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                $.ajax({
                    url: '../ticket_needed_visit/'+ticketId,
                    type: 'POST',
                    data: {
                        // _method: 'PATCH', // Laravel uses PATCH for updates
                        checked: isChecked
                    },
                    success: function(response) {
                        // Handle the response here, update the page as needed
                        console.log("success");

                    },
                    error: function(error) {
                        // Handle errors
                        console.log("An error occurred: " + error);
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.summernote').summernote();

            $('.edit-button').click(function() {
                $(this).siblings('.card').find('.post-text').hide();
                $(this).hide();
                $(this).siblings('.card').find('.post-editor').show();
            });

            $('.save-button').click(function() {
                var markup = $(this).siblings('.summernote').summernote('code');
                var postId = $(this).data('id');

                $.ajax({
                    url: '../update-post',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: postId,
                        text: markup
                    },
                    success: (function(postTextElement) {
                        return function() {
                            // postTextElement.html(markup);
                            // postTextElement.show();
                            // postTextElement.siblings('.post-editor').hide();
                            location.reload();
                        }
                    })($(this).parent().siblings('.post-text'))
                }); 
            });
            $('.cancel-button').click(function() {
                $(this).parent().hide();
                $(this).parent().siblings('.post-text').show();
                $('.edit-button').show();
            });
        });
</script>
@endpush
@php
    $accepts = App\Models\Microapp::where('url', '/'.$appname)->first()->accepts; //fetch microapp 'accepts' field
@endphp
<div class="container">
<div class="container px-5">   
    <div class=" hstack gap-2"> 
        <div><b>Θέμα: </b>{{$ticket->subject}}</div>         
        <div><b>ID δελτίου: </b>{{$ticket->id}}</div>          
        <div><b>Τηλέφωνο Σχολείου: </b>{{$ticket->school->telephone}}</div>             
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
                                    <button class="save-button" data-id="{{$one_post->id}}">Save</button>
                                    <button class="cancel-button">Cancel</button>
                                </div>
                            </div>
                            <div class="edited-label"></div>
                            @if($showPencil)
                                <button class="edit-button"><i class="fa fa-pencil"></i></button>
                            @endif
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
                <textarea class="summernote" name="comments"  class="form-control"></textarea>
            </div>
            </div>
            @if(!$accepts)
                <div class="col-sm-2 btn btn-warning bi bi-bricks rounded text-light" style="text-align:center;">
                    Η εφαρμογή δε δέχεται υποβολές
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
                    <small>Μπορείτε να επισυνάψετε αρχεία jpeg, png, docx, pdf, xlsx</small>
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
    @if(Auth::user())
        <input type="checkbox" id="visit" class="ticket-checkbox" data-ticket-id="{{ $ticket->id }}" {{ $ticket->needed_visit ? 'checked' : '' }}>
        <label for="visit"> Πραγματοποιήθηκε επίσκεψη;</label>
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
    <div class="files">
        @php
            $school = Auth::guard('school')->user();
            $directory = "/tickets/$ticket->id";
            $files=Storage::disk('local')->files($directory);          
        @endphp
        @if(count($files)>0)
        Συνημμένα αρχεία δελτίου:<br>
        <div class="hstack gap-3">
        @foreach($files as $file)
                                
            @php
                $basename = basename($file);
            @endphp
            <form action="{{url("/get_ticket_file/$ticket->id/$basename")}}" method="post">
            @csrf
                <button class="btn btn-secondary bi bi-box-arrow-down"> {{$basename}}</button>
            </form> 
        @endforeach
        </div>
        @endif
    </div>
    </div>
</div>
