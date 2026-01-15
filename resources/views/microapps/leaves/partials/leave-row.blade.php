<div class="row align-items-center mb-3">
    {{-- Teacher Name --}}
    <div class="col-md-2">
        <strong>{{ $leave->surname }} {{ $leave->name }}</strong>
    </div>

    {{-- Leave Type --}}
    <div class="col-md-2">
        <a href="#" 
           class="no-spinner text-decoration-none" 
           data-get-teacher-leaves-url="{{ route('leaves.getTeacherLeavesApi', ['teacher_leave' => $leave->id]) }}"
           data-toggle="modal" 
           data-target="#infoModal" 
           data-leave-id="{{ $leave->id }}">
            <i class="bi bi-info-circle"></i> {{ $leave->leave_type }}
        </a>
    </div>
    
    {{-- Leave Duration --}}
    <div class="col-md-2">
        <span class="badge bg-secondary">
            {{ $leave->leave_days }} 
            {{ $leave->leave_days == 1 ? 'ημέρα στις' : 'ημέρες από' }} 
            {{ $leave->leave_start_date->format('d/m/Y') }}
        </span>
    </div>
    
    @if($leave->am != null)
        {{-- File Upload Section --}}
        <div class="col-md-6">
            <div class="row g-2">
                @if(!$leave->submitted)
                    {{-- Warning for unlocked leave with protocol --}}
                    @if($leave->protocol_number)
                        <div class="col-12">
                            <div class="alert alert-warning mb-2 py-2 d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Προσοχή!</strong> Η άδεια έχει ξεκλειδωθεί (Αρ. Πρωτ.: {{ $leave->protocol_number }}) 
                                και πρέπει να υποβληθεί ξανά στη Διεύθυνση.
                            </div>
                        </div>
                    @endif

                    {{-- Upload Form --}}
                    <div class="col-md-6">
                        <form action="{{ route('leaves.upload_files', ['teacher_leave' => $leave->id]) }}" 
                              method="post" 
                              enctype="multipart/form-data" 
                              class="d-flex gap-2">
                            @csrf
                            <input type="file" 
                                   name="files[]" 
                                   class="form-control form-control-sm" 
                                   multiple 
                                   required>
                            <button type="submit" class="btn btn-primary btn-sm text-nowrap">
                                <i class="bi bi-upload"></i> Ανέβασμα
                            </button>
                        </form>
                    </div>
                    
                    {{-- Submit to Directorate --}}
                    <div class="col-md-6">
                        @if($leave->files_json)
                            <form action="{{ route('leaves.submit', ['leave' => $leave->id]) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-info btn-sm w-100">
                                    <i class="bi bi-send-fill"></i> 
                                    {{ $leave->protocol_number ? 'Επανυποβολή' : 'Υποβολή' }} στη Διεύθυνση
                                </button>
                            </form>
                        @endif
                    </div>
                @else
                    {{-- Submitted Status --}}
                    <div class="col-md-6">
                        <div class="alert alert-success mb-0 py-2 d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Αρ. Πρωτ.:</strong> {{ $leave->protocol_number }} - 
                            {{ $leave->protocol_date->format('d-m-Y') }}
                        </div>
                    </div>
                    
                    {{-- Action Buttons --}}
                    <div class="col-md-6">
                        <div class="d-flex gap-2">
                            {{-- Unlock Button --}}
                            <form action="{{ route('leaves.leave_unlock', ['teacher_leave' => $leave->id]) }}" 
                                  method="post" 
                                  class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm w-100">
                                    <i class="bi bi-unlock-fill"></i> Διόρθωση
                                </button>
                            </form>
                            
                            {{-- Hide Leave Button --}}
                            <form action="#" 
                                  method="post" 
                                  class="flex-fill"
                                  onsubmit="return confirm('Είστε σίγουροι ότι θέλετε να αποκρύψετε αυτή την άδεια;')">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm w-100">
                                    <i class="bi bi-eye-slash-fill"></i> Απόκρυψη
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- Substitute Teacher Note --}}
        <div class="col-md-6">
            <div class="alert alert-info mb-0 py-2">
                <i class="bi bi-info-circle"></i>
                <em>Αναπληρωτής εκπαιδευτικός (ανάρτηση στο invoices)</em>
            </div>
        </div>
    @endif
</div>

{{-- Files List --}}
@if($leave->am != null && $leave->files_json)
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 mb-3">
                @php 
                    $fileNames = json_decode($leave->files_json, true);
                @endphp
                
                @foreach($fileNames as $serverFileName => $databaseFileName)
                    <div class="d-flex align-items-center border rounded p-1">
                        <form action="{{ route('leaves.download_file', [
                            'serverFileName' => $serverFileName, 
                            'databaseFileName' => $databaseFileName
                        ]) }}" method="get" class="mb-0">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> {{ $databaseFileName }}
                            </button>
                        </form>
                        
                        @if(!$leave->submitted)
                            <form action="{{ route('leaves.delete_file', [
                                'teacher_leave' => $leave->id, 
                                'serverFileName' => $serverFileName
                            ]) }}" method="get" class="mb-0 ms-1">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<hr class="my-3">