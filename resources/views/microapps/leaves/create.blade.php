<x-layout_school>
    @push('scripts')
    <script src="{{asset('/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset("leaves.js")}}"></script>
    @endpush
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $microapp = App\Models\Microapp::where('url', '/leaves')->first();
        $accepts = $microapp->accepts;
        $name = $microapp->name;
        $leaves = $school->leaves;
        $revokedLeaves = $school->revokedLeaves;
        // dd($revokedLeaves);
        $leavesToReplace = [];
        foreach($leaves as $leave1){
            foreach($revokedLeaves as $leave2){
                if($leave1->leave_protocol_number == $leave2->leave_protocol_number && $leave1->leave_protocol_date == $leave2->leave_protocol_date){
                    echo 'Σε Ισχύ: ' . $leave1->surname . ' ' . $leave1->leave_type . ' ' . $leave1->leave_start_date .  ' ' . $leave1->leave_days . ' / 
                    Ανακλημένη: '   . $leave2->surname . ' ' . $leave2->leave_type .   ' ' . $leave2->leave_start_date .  ' ' . $leave2->leave_days . '<br>';
                }
            }
        }
    @endphp
    @push('scripts')
        <script>
            var leaves = @json($leaves);
        </script>
    @endpush
    @push('title')
        <title>{{$name}}</title>
    @endpush
    <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title " id="messageModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <p></p>   
                    <p></p>
                    <p></p>
                    <hr>
                    <p></p>
                    <p></p>
                    <br>
                    <small style="text-align: end"><p class="text-muted"></p></small>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
                </div>
            </div>
        </div>
    </div>

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
                {{ $leave->surname }} {{ $leave->name }}
            </div>
            <div class="col-md-2">
                <a href="#" class="no-spinner" 
                data-get-teacher-leaves-url="{{ route('leaves.getTeacherLeavesApi', ['teacher_leave' => $leave->id]) }}"
                data-toggle="modal" data-target="#infoModal" data-leave-id="{{$leave->id}}">{{$leave->leave_type}}</a>
                {{-- {{ $leave->leave_type }} --}}
            </div>
            
            <div class="col-md-2">
                {{ $leave->leave_days }} 
                @if($leave->leave_days == 1) 
                    ημέρα στις 
                @else 
                    ημέρες από 
                @endif 
                {{$leave->leave_start_date}}
            </div>
            @if($leave->am != null)
                @if($leave->leave_type != 'Απουσία')
                    <div class="col-md-2">
                        <form action="{{route('leaves.upload_files', ['teacher_leave' => $leave->id])}}" method="post" enctype="multipart/form-data">
                        @csrf
                            <input type="file" name="files[]" class="form-control" multiple>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary" @if($leave->submitted==1) disabled @endif>
                            <i class="bi bi-filetype-pdf"></i> Ανέβασμα
                        </button>
                        </form>
                    </div>
                    <div class="col-md-2">
                        @if($leave->submitted==0)
                        <form action="{{route('leaves.submit', [ 'leave' => $leave->id ])}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-send"></i> Υποβολή
                            </button>
                        </form>
                        @else
                            @php
                                $formattedDate = Carbon\Carbon::parse($leave->protocol_date)->format('d-m-Y');
                            @endphp
                            <button class="btn btn-success" disabled>
                                <i class="bi bi-check"></i>Αρ. Πρωτ. {{ $leave->protocol_number }} - {{ $formattedDate }}
                            </button>
                        @endif
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
                @endif {{-- end of if Files exist--}}
                @endif {{-- end of if absence--}}
            @endif {{-- end of if teacher is permanent (doesn't have am)--}}
            
        
            
    </div> {{-- ROW END --}}
        <hr>
    @endforeach
    </div> {{-- Container END--}}
    

        

</x-layout_school>