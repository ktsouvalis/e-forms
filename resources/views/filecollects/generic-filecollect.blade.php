@if($old_data->file)
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('body').on('blur', '#stake_comment', function() {
                    
                    const textarea = document.getElementById('stake_comment');
                    const comment = textarea.value;
                    const stakeholderId = $(this).data('stakeholder-id');
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
    
                    $.ajax({
                        url: '../filecollects/save_stake_comment/'+stakeholderId,
                        type: 'POST',
                        data: {
                            stake_comment: comment
                        },
                        success: function(response) {
                            console.log("success");
                        },
                        error: function(error) {
                            console.log("An error occurred: " + error);
                        }
                    });
                });
            });
        </script>
    @endpush
@endif
    <div class="h4">{{$filecollect->name}}: {{$filecollect->department->name}}</div>
    <div class="container">
        @push('title')
        <title>{{$filecollect->name}}</title>
        @endpush
        @if($filecollect->comment)
        <div class="row">
            <div class="col"></div>
            <div class="col">
            <div class="card py-2" style="background-color: Gainsboro; text-decoration: none; font-size: small">
                <div class="m-1 post-text">{!!html_entity_decode($filecollect->comment)!!}</div>
            </div>
            </div>
            <div class="col">
                
            </div>
        </div>
        
        @endif
        <hr>
        <div class="row">
            <div class="col">
                @if($filecollect->base_file)
                    @php
                        $icon = "bi bi-box-arrow-down"; 
                        $filename = $filecollect->base_file;
                        if(substr($filename, -4) == "docx")
                            $icon = "bi bi-file-word";
                        else if(substr($filename, -4) == "xlsx")
                            $icon = "bi bi-file-excel";
                        else if(substr($filename, -4) == ".pdf")
                            $icon = "bi bi-file-pdf";
                    @endphp
                    <form action="{{url("/filecollects/download_admin_file/$filecollect->id/base")}}" method="get">
                        @csrf
                        <div class="input-group my-2">
                            <span ><b>Σχετικό Έγγραφο</b></span>
                        </div>
                        <button class="btn btn-warning {{$icon}}" title="Λήψη αρχείου"> {{$filecollect->base_file}} </button>
                    </form>
                @endif
            </div>
            <div class="col">
                @if($old_data->file)
                    <div class="form-group my-2">
                        <span ><b>Αρχεία που έχετε υποβάλλει</b></span>
                    </div>
                    <div class="vstack gap-2">
                        @foreach(json_decode($old_data->file, true) as $file)
                        @php
                            $icon = "bi bi-box-arrow-down";
                            $filename = $file['original_filename'];
                            if(substr($filename, -4) == "docx")
                                $icon = "bi bi-file-word";
                            else if(substr($filename, -4) == "xlsx")
                                $icon = "bi bi-file-excel";
                            else if(substr($filename, -4) == ".pdf")
                                $icon = "bi bi-file-pdf";
                        @endphp
                        <div class="hstack gap-2">
                            <form action="{{url("/filecollects/download_stake_file/$old_data->id/$filename")}}" method="get">
                                @csrf
                                <button class="btn btn-outline-success {{$icon}}" title="Λήψη αρχείου"> {{$filename}} </button>
                            </form>  
                        </div>
                        @endforeach
                        <form action="{{url("/filecollects/delete_stake_file/$old_data->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-danger bi bi-x-circle" onclick="return confirm('ΠΡΟΣΟΧΗ! Θα διαγραφούν ΟΛΑ τα αρχεία σας και θα μπορείτε να ανεβάσετε νέο μόνο αν η εφαρμογή δέχεται υποβολές')"> Διαγραφή Αρχείων </button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="col">
                @if($filecollect->template_file)
                    @php
                        $icon = "bi bi-box-arrow-down";
                        $filename = $filecollect->template_file;
                        if(substr($filename, -4) == "docx")
                            $icon = "bi bi-file-word";
                        else if(substr($filename, -4) == "xlsx")
                            $icon = "bi bi-file-excel";
                        else if(substr($filename, -4) == ".pdf")
                            $icon = "bi bi-file-pdf";
                    @endphp
                    <form action="{{url("/filecollects/download_admin_file/$filecollect->id/template")}}" method="get">
                        @csrf
                        <div class="input-group my-2">
                            <span ><b>Πρότυπο Αρχείο</b></span>
                        </div>
                        <button class="btn btn-warning {{$icon}}" title="Λήψη αρχείου"> {{$filecollect->template_file}} </button>
                    </form>
                @endif
            </div>
        </div>
        <hr>
        @php
            $pdf = json_decode($filecollect->fileMime, true)['pdf'];
            $xlsx = json_decode($filecollect->fileMime, true)['xlsx'];
            $docx = json_decode($filecollect->fileMime, true)['docx'];
        @endphp
        
        <form action="{{url("/filecollects/upload_stake_file/$filecollect->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
            @csrf
            <div class="input-group">
                <span class="input-group-text"><strong>Αποστολή αρχείων για τη συλλογή {{$filecollect->name}} </strong>@if($pdf+$xlsx+$docx>1) <div class="text-muted input-group mx-2"><small> (Τα αρχεία υποβάλλονται όλα μαζί)</small></div> @endif</span>

            </div>
            @for($i=1; $i<=$pdf; $i++)
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon4">{{$i}}ο Αρχείο pdf</span>
                    <input name="the_file_pdf{{$i}}" type="file" class="form-control" accept="application/pdf" required><br>
                </div>
            @endfor
            @for($i=1; $i<=$xlsx; $i++)
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon4">{{$i}}ο Αρχείο xlsx</span>
                    <input name="the_file_xlsx{{$i}}" type="file" class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required><br>
                </div>
            @endfor
            @for($i=1; $i<=$docx; $i++)
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon4">{{$i}}ο Αρχείο docx</span>
                    <input name="the_file_docx{{$i}}" type="file" class="form-control" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document" required><br>
                </div>
            @endfor
            @if(!$accepts)
                <div class='alert alert-warning text-center my-2'>
                    <strong> <i class="bi bi-bricks"> </i> Η συλλογή αρχείου {{$filecollect->name}} δε δέχεται υποβολές</strong>
                </div>
            @else
                <div class="input-group">
                    <span class="w-25"></span>
                    <button type="submit" class="btn btn-primary my-2 bi bi-plus-circle"> Υποβολή</button>
                </div>
                
            @endif
        </form>
        
        @if($old_data->file)
            <div class="hstack gap-3">
            <div class="col-md-4 py-3" style="max-width:15rem">
                <div class="card py-3" style="background-color:rgb(144, 187, 226); text-decoration:none; text-align:center; font-size:small">
                    <div>Τελευταία ενημέρωση αρχείων <br><strong> {{$old_data->uploaded_at}}</strong></div>
                </div>
            </div>
            @if($old_data->filecollect->accepts)
                <div class="input-group">
                    <span class="input-group-text w-25 text-wrap"><b>Παρατήρηση/Σημείωση</b> </span>
                    <textarea name="stake_comment" id="stake_comment" class="form-control" data-stakeholder-id="{{ $old_data->id }}" cols="30" rows="5" style="resize: none;" >@if($old_data){{$old_data->stake_comment}}@endif</textarea>
                </div>
                <small class="text-muted">Αποθηκεύεται αυτόματα αν κάνετε κλικ έξω από το πλαίσιο κειμένου</small>
            @else
                @if($old_data->stake_comment)
                <div class="col-md-4 py-3" style="max-width:15rem">
                    <div class="card py-3" style="background-color:rgb(144, 187, 226); text-decoration:none; text-align:center; font-size:small">
                        <div>Η παρατήρησή σας <br><strong> {{$old_data->stake_comment}}</strong></div>
                    </div>
                </div>
                @endif  
            @endif
              
        </div>
        
        @endif
        @if($old_data->message_from_admin)
            <div class="row">
                <div class="col"></div>
                <div class="col-md-4 py-3" style="max-width:20rem">
                    <div class="card py-3 bg bg-warning" style="text-decoration:none; text-align:center; font-size:small">
                        <div><strong>Σχόλιο από διαχειριστή</strong> στις {{$old_data->message_from_admin_at}}: <br> {{$old_data->message_from_admin}}</div>
                    </div>
                </div>
                <div class="col"></div>
            </div>
        @endif 