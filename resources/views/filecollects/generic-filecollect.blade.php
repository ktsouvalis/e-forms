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
                        url: '../filecollects/save_filecollect_stake_comment/'+stakeholderId,
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
@php
    $extension="";
    if($old_data->filecollect->fileMime == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
        $extension ='.xlsx';
    }
    else if($old_data->filecollect->fileMime == "application/pdf"){
        $extension = ".pdf";
    }
    else if($old_data->filecollect->fileMime == "application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
        $extension = ".docx";
    }
@endphp
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
                    <form action="{{url("/filecollects/dl_filecollect_file/$filecollect->id/base")}}" method="post">
                        @csrf
                        <div class="input-group">
                            <span class="input-group-text"><b>Σχετικό Έγγραφο</b></span>
                        </div>
                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$filecollect->base_file}} </button>
                    </form>
                @endif
            </div>
            <div class="col">
                @if($old_data->file)
                <div class="input-group">
                    <span class="input-group-text"><b>Αρχείο που έχετε υποβάλλει</b></span>
                </div>
                <div class="hstack gap-2">
                    <form action="{{url("/filecollects/dl_stake_file/$old_data->id")}}" method="post">
                        @csrf
                        <button class="btn btn-success bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$old_data->file}} </button>
                    </form>
                    @if($old_data->filecollect->accepts)
                    <form action="{{url("/filecollects/delete_stake_file/$old_data->id")}}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-danger bi bi-x-circle" title="Διαγραφή αρχείου" onclick="return confirm('ΠΡΟΣΟΧΗ! Θα διαγραφεί το αρχείο σας και θα μπορείτε να ανεβάσετε νέο μόνο αν η εφαρμογή δέχεται υποβολές')"> </button>
                    </form>
                    @endif
                </div>
                @endif
            </div>
            <div class="col">
                @if($filecollect->template_file)
                    <form action="{{url("/filecollects/dl_filecollect_file/$filecollect->id/template")}}" method="post">
                        @csrf
                        <div class="input-group">
                            <span class="input-group-text"><b>Πρότυπο Αρχείο</b></span>
                        </div>
                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$filecollect->template_file}} </button>
                    </form>
                @endif
            </div>
        </div>
        <hr>
        <form action="{{url("/filecollects/post_filecollect/$filecollect->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
            @csrf
            <div class="input-group">
                <span class="input-group-text"><strong>Αποστολή αρχείου για τη συλλογή {{$filecollect->name}} </strong></span>
            </div>
           
            <div class="input-group">
                <span class="input-group-text w-25" id="basic-addon4">Αρχείο</span>
                <input name="the_file" type="file" class="form-control" required><br>
            </div>
            @if(!$accepts)
                <div class='alert alert-warning text-center my-2'>
                    <strong> <i class="bi bi-bricks"> </i> Η συλλογή αρχείου {{$filecollect->name}} δε δέχεται υποβολές</strong>
                </div>
            @else
                <div class="input-group">
                    <span class="w-25"></span>
                    <button type="submit" class="btn btn-primary my-2 bi bi-plus-circle"> Υποβολή</button><small class="text-muted m-3">Δεκτό αρχείο: {{$extension}}</small>
                </div>
                
            @endif
        </form>
        
        @if($old_data->file)
            {{-- @if($old_data->checked)
                <div class='alert alert-success text-center'>
                    Το αρχείο σας έχει ελεγχθεί
                </div>
            @else
                <div class='alert alert-warning text-center'>
                    Το αρχείο σας δεν έχει ελεγχθεί
                </div>
            @endif --}}
            <div class="hstack gap-3">
            <div class="col-md-4 py-3" style="max-width:15rem">
                <div class="card py-3" style="background-color:rgb(144, 187, 226); text-decoration:none; text-align:center; font-size:small">
                    <div>Τελευταία ενημέρωση αρχείου <br><strong> {{$old_data->uploaded_at}}</strong></div>
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
                <div class="col-md-4 py-3" style="max-width:15rem">
                    <div class="card py-3" style="background-color:rgb(46, 157, 81); text-decoration:none; text-align:center; font-size:small">
                        <div>Σχόλιο από διαχειριστή στις {{$old_data->message_from_admin_at}}: <br><strong> {{$old_data->message_from_admin}}</strong></div>
                    </div>
                </div>
                <div class="col"></div>
            </div>
        @endif 