<div class="row">
    <div class="col-md-2">
        {{ $leave->surname }} {{ $leave->name }}
    </div>
    <div class="col-md-2">
        <a href="#" class="no-spinner" 
            data-get-teacher-leaves-url="{{ route('leaves.getTeacherLeavesApi', ['teacher_leave' => $leave->id]) }}"
            data-toggle="modal" data-target="#infoModal" data-leave-id="{{$leave->id}}">
            {{$leave->leave_type}}
        </a>
    </div>
    
    <div class="col-md-2">
        {{ $leave->leave_days }} 
        {{ $leave->leave_days == 1 ? 'ημέρα στις' : 'ημέρες από' }} 
        {{ $leave->leave_start_date->format('d/m/Y') }}
    </div>
    
    @if($leave->am != null)
        @if($leave->leave_type != 'Απουσία')
            {{-- File Upload --}}
            <div class="col-md-2">
                <form action="{{route('leaves.upload_files', ['teacher_leave' => $leave->id])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="files[]" class="form-control" multiple required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary" {{ $leave->submitted ? 'disabled' : '' }}>
                    <i class="bi bi-filetype-pdf"></i> Ανέβασμα αρχείου/ων
                </button>
                </form>
            </div>
            
            {{-- Submit to Directorate --}}
            <div class="col-md-2">
                @if(!$leave->submitted)
                    @if($leave->files_json) 
                        <form action="{{route('leaves.submit', ['leave' => $leave->id])}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-info" disabled>
                                <i class="bi bi-send"></i> Υποβολή στη Διεύθυνση
                            </button>
                        </form>
                    @endif
                @else
                    <button class="btn btn-success" disabled>
                        <i class="bi bi-check"></i>Αρ. Πρωτ. {{ $leave->protocol_number }} - {{ $leave->protocol_date->format('d-m-Y') }}
                    </button>
                @endif
            </div>
            
            {{-- Files List --}}
            @if($leave->files_json)
                @php 
                    $fileNames = json_decode($leave->files_json, true);
                @endphp
                <div class="d-flex flex-wrap">
                    @foreach($fileNames as $serverFileName => $databaseFileName)
                        <div class="d-flex justify-content-between align-items-center">
                            <form action="{{route('leaves.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName])}}" method="get">
                                <input type="submit" class="btn btn-info btn-block rounded-2 py-2 m-1" value="{{$databaseFileName}}">
                            </form>
                            @if(!$leave->submitted)
                                <form action="{{route('leaves.delete_file', ['teacher_leave' => $leave->id, 'serverFileName' => $serverFileName])}}" method="get">
                                    <input type="submit" class="btn btn-danger btn-block rounded-3" value="Χ">
                                </form>
                            @endif
                            <div class="m-2"></div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    @else
        <div class="col-md-2">
            <em>Αναπληρωτής εκπαιδευτικός (ανάρτηση στο invoices)</em>
        </div>
    @endif
</div>
<hr>