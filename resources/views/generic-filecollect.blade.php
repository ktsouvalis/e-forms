
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
                    <form action="{{url("/dl_filecollect_file/$filecollect->id/base")}}" method="post">
                        @csrf
                        <div class="input-group">
                            <span class="input-group-text"><b>Συνημμένο Αρχείο</b></span>
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
                    <form action="{{url("/dl_stake_file/$old_data->id")}}" method="post">
                        @csrf
                        <button class="btn btn-success bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$old_data->file}} </button>
                    </form>
                    <form action="{{url("/delete_stake_file/$old_data->id")}}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-danger bi bi-x-circle" title="Διαγραφή αρχείου" onclick="return confirm('ΠΡΟΣΟΧΗ! Θα διαγραφεί το αρχείο σας και θα μπορείτε να ανεβάσετε νέο μόνο αν η εφαρμογή δέχεται υποβολές')"> </button>
                    </form>
                </div>
                @endif
            </div>
            <div class="col">
                @if($filecollect->template_file)
                    <form action="{{url("/dl_filecollect_file/$filecollect->id/template")}}" method="post">
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
        <form action="{{url("/post_filecollect/$filecollect->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
            @csrf
            <div class="input-group">
                <span class="input-group-text"><strong>Αποστολή αρχείου για τη συλλογή {{$filecollect->name}} </strong></span>
            </div>
            <div class="input-group">
                <span class="input-group-text w-25 text-wrap">Παρατηρήσεις</span>
                <textarea name="stake_comment" id="stake_comment" class="form-control" cols="30" rows="5" style="resize: none;" >@if($old_data){{$old_data->stake_comment}}@endif</textarea>
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
                    <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                </div>
            @endif
        </form>
        @if($old_data->file)
            @if($old_data->checked)
                <div class='alert alert-success text-center'>
                    Το αρχείο σας έχει ελεγχθεί
                </div>
            @else
                <div class='alert alert-warning text-center'>
                    Το αρχείο σας δεν έχει ελεγχθεί
                </div>
            @endif
        @endif