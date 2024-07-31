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
                <form action="{{route('leaves.upload_files')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="file" name="leaves_files" class="form-control" multiple>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filetype-pdf"></i> Ανέβασμα
                </button>
            </form>
            </div>
            
            <div class="col-md-2">
                <form action="{{route('leaves.send_to_protocol')}}" method="post">
                    @csrf
                <button type="submit" class="btn btn-info">
                    <i class="bi bi-send"></i> Υποβολή
                </button>
            </form>
            </div>
        </div>
        <hr>
    @endforeach
    </div>
    

        

</x-layout_school>