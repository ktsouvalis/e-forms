<x-layout_school>
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $microapp = App\Models\Microapp::where('url', '/leaves')->first();
        $accepts = $microapp->accepts;
        $name = $microapp->name;
        $leaves = $school->leaves;
    @endphp
    
    @push('title')
        <title>{{$name}}</title>
    @endpush
    <div class="container">
    <h3>Υποβολή αδειών εκπαιδευτικών στη Διεύθυνση Π.Ε. Αχαΐας</h3>
    <div class="row">
        <div class="col-md-2">
            <strong>Επώνυμο</strong>
        </div>
        <div class="col-md-2">
            <strong>Τύπος Άδειας</strong>
        </div>
        <div class="col-md-2">
            <strong>Ημέρες Άδειας</strong>
        </div>
        <div class="col-md-2">
            <strong>Δικαιολογητικό/ά</strong>
        </div>
        <div class="col-md-2">
            <strong>Αποστολή</strong>
        </div>
    </div>
    @foreach ($leaves as $leave)
        <div class="row">
            <div class="col-md-2">
                {{$leave->surname}} {{$leave->name}}
            </div>
            <div class="col-md-2">
                {{$leave->leave_type}}
            </div>
            
            <div class="col-md-2">
                {{$leave->leave_days}} 
                @if($leave->leave_days == 1) 
                    ημέρα στις 
                @else 
                    ημέρες από 
                @endif 
                {{$leave->leave_start_date}}
            </div>
            <div class="col-md-2">
                <form action="{{route('leaves.upload_files', ['teacher_leave' => $leave->id])}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="file" name="files[]" class="form-control" multiple>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filetype-pdf"></i> Ανέβασμα
                </button>
            </form>
            </div>
            
            <div class="col-md-2">
                <form action="{{route('leaves.submit', [ 'leave' => $leave->id ])}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-send"></i> Υποβολή
                    </button>
                </form>
            </div>
        </div>
        
        @if($leave->files_json)
            @php 
                $count = 1;
                $fileNames = json_decode($leave->files_json, true);
            @endphp
            <div class="d-flex flex-wrap">
            @foreach($fileNames as $serverFileName => $databaseFileName)    
        
            <div class="d-flex justify-content-between align-items-center">
                <form action="{{route('leaves.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName])}}" method="get">
                    <input type="submit" class="btn btn-info btn-block rounded-2 py-2 m-1" value="{{$databaseFileName}}" >
                </form>
                <form action="{{route('leaves.delete_file', ['teacher_leave' => $leave->id, 'serverFileName' => $serverFileName ])}}" method="get">
                    <input type="submit" class="btn btn-danger btn-block rounded-3" value="Χ" >
                </form>
                <div class="m-2"></div>
            </div>
            @php $count++; @endphp
            @endforeach
            </div>
        @endif

        <hr>
    @endforeach
    </div>
    

        

</x-layout_school>