<x-layout_school>
    @push('scripts')
    <!-- <script src="{{asset('/bootstrap/js/bootstrap.bundle.min.js')}}"></script> -->
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
                    //echo 'Σε Ισχύ: ' . $leave1->surname . ' ' . $leave1->leave_type . ' ' . $leave1->leave_start_date .  ' ' . $leave1->leave_days . ' / 
                    //Ανακλημένη: '   . $leave2->surname . ' ' . $leave2->leave_type .   ' ' . $leave2->leave_start_date .  ' ' . $leave2->leave_days . '<br>';
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
       <div>
                <h4>Εσωτερικός Κανονισμός Σχολικής Μονάδας</h4>
                <div>
                    <button class="btn btn-primary m-3" type="button" data-bs-toggle="collapse" data-bs-target="#instructionsList" aria-expanded="false" aria-controls="instructionsList">
                        <h6>Για αναλυτική περιγραφή της διαδικασίας υποβολής και έγκρισης Αδειών Εκπαιδευτικών πατήστε εδώ</h6>
                    </button>
                    <div class="collapse" id="instructionsList">
                        
                        <ul class="list-group m-3">
                            <li class="list-group-item">1) Καταχώρηση άδειας από το Σχολείο στο mySchool. 
                            </li>
                            <li class="list-group-item">2) Εμφάνιση των καταχωρημένων αδειών στις Ηλεκτρονικές Φόρμες καθημερινά στις 10π.μ. και στις 12μ.
                                <br><em></em>
                            </li>
                            <li class="list-group-item">3) Ανέβασμα απαραίτητων αρχείων από τη Σχολική Μονάδα στις Ηλεκτρονικές Φόρμες 
                                <br><em>(π.χ. ιατρικές βεβαιώσεις, δικαιολογητικά κλπ)</em>
                            </li>
                            <li class="list-group-item">4) Υποβολή άδειας από τη Σχολική Μονάδα στη Διεύθυνση Π.Ε. Αχαΐας
                                <br><em>(Η άδεια πρωτοκολείται αυτόματα και χρεώνεται στον αρμόδιο υπάλληλο. Στη συνέχεια
                                    , κατά περίπτωση, προχωράει η διαδικασία ενημέρωσης του φακέλου του εκπαιδευτικού, έγκριση της άδειας και διαβίβαση στην υγειονομική επιτροπή ανάλογα με το είδος και τη διάρκεια της άδειας.   )</em>
                            </li>
                            <li class="list-group-item">5) Με την τελική έγκριση της άδειας (όπου απαιτείται) από τη Διεύθυνση Π.Ε. Αχαΐας, η Σχολική Μονάδα ενημερώνεται αυτόματα μέσω email και η έγκριση εμφανίζεται στην αντίστοιχη άδεια στην παρούσα καρτέλα.
                            </li>
                            
                        </ul>
                        <div class="alert alert-info" role="alert">
                            <strong><i class="bi bi-info-circle"></i> Σημείωση:</strong> Οι άδειες των εκπαιδευτικών που είναι αποσπασμένοι από άλλους νομούς υποβάλλονται απευθείας από το Σχολείο στην αντίστοιχη διεύθυνση οργανικής του εκπαιδευτικού
                            και όχι στη Διεύθυνση Π.Ε. Αχαΐας.
                        </div>
                    </div>
                </div>
            </div>

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

    
    @forelse ($leaves as $leave)
        
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
                {{ Carbon\Carbon::parse($leave->leave_start_date)->format('d/m/Y') }}
            </div>
            @if($leave->am != null)
                @if($leave->leave_type != 'Απουσία')
                    <div class="col-md-2">
                        <form action="{{route('leaves.upload_files', ['teacher_leave' => $leave->id])}}" method="post" enctype="multipart/form-data">
                        @csrf
                            <input type="file" name="files[]" class="form-control" multiple required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary" @if($leave->submitted==1) disabled @endif>
                            <i class="bi bi-filetype-pdf"></i> Ανέβασμα αρχείου/ων
                        </button>
                        </form>
                    </div>
                    <div class="col-md-2">
                        @if($leave->submitted==0)
                            @if($leave->files_json != Null) 
                                <form action="{{route('leaves.submit', [ 'leave' => $leave->id ])}}" method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-info">
                                        <i class="bi bi-send"></i> Υποβολή στη Διεύθυνση
                                    </button>
                                </form>
                            @endif
                        @else
                            @php
                                $formattedDate = Carbon\Carbon::parse($leave->protocol_date)->format('d-m-Y');
                            @endphp
                            <button class="btn btn-success" disabled>
                                <i class="bi bi-check"></i>Αρ. Πρωτ. {{ $leave->protocol_number }} - {{ $formattedDate }}
                            </button>
                        @endif
                    </div>
                @if($leave->files_json) {{-- if Files exist --}}
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
                            @if($leave->submitted==0)
                                <form action="{{route('leaves.delete_file', ['teacher_leave' => $leave->id, 'serverFileName' => $serverFileName ])}}" method="get">
                                    <input type="submit" class="btn btn-danger btn-block rounded-3" value="Χ" >
                                </form>
                            @endif
                            <div class="m-2"></div>
                        </div>
                    @php $count++; @endphp
                    @endforeach
                    </div>
                @endif {{-- end of if Files exist--}}
                @endif {{-- end of if absence--}}
            @else {{-- if teacher is not permanent (doesn't have am)--}}
                <div class="col-md-2">
                    <em>Αναπληρωτής εκπαιδευτικός (ανάρτηση στο invoices)</em>
                </div>
            @endif {{-- end of if teacher is permanent (doesn't have am)--}}   
    </div> {{-- ROW END --}}
    <hr>
    @empty
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i>
            Δεν υπάρχουν καταχωρημένες άδειες αυτή τη στιγμή. Ξεκινήστε καταχωρόντας τις άδειες στο mySchool.
        </div>
    @endforelse
    </div> {{-- Container END--}}
</x-layout_school>