{{-- Check if this is a revoked leave with protocol --}}
@php
    $isRevokedWithProtocol = ($leave->leave_state == '5-Ανακλήθηκε' && $leave->protocol_number);
@endphp

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
        
        @if($isRevokedWithProtocol)
            {{-- Revoked Leave Alert --}}
            <div class="col-md-6">
                <div class="alert alert-danger mb-0 py-2 d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Ανακλήθηκε!</strong> Η άδεια πρέπει να υποβληθεί εκ νέου στο mySchool
                    (Αρ. Πρωτ.: {{ $leave->protocol_number }})
                </div>
            </div>
        @else

        {{-- File Upload Section --}}
        <div class="col-md-5">
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
                    <div class="col-12">
                        
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
                    
                    {{-- Submit to Directorate Button --}}
                    @if($leave->files_json)
                        <div class="col-12">
                            <form action="{{ route('leaves.submit', ['leave' => $leave->id]) }}" 
                                method="post"
                                data-leave-id="{{ $leave->id }}">
                                @csrf
                                <button type="submit" class="btn btn-info btn-sm w-100">
                                    <i class="bi bi-send-fill"></i> 
                                    {{ $leave->protocol_number ? 'Επανυποβολή' : 'Υποβολή' }} στη Διεύθυνση
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    {{-- Submitted Status --}}
                    <div class="col-12">
                        <div class="alert alert-success mb-0 py-2 d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Αρ. Πρωτ.:</strong> {{ $leave->protocol_number }} - 
                            {{ $leave->protocol_date->format('d-m-Y') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        {{-- Secondary Actions (Hide/Unlock) - Moved to separate column --}}
        <div class="col-md-1">
            @if(!$leave->submitted)
                {{-- Hide Button (only when submitted=0 and no files) --}}
                @if(!$leave->files_json)
                    <form action="{{ route('leaves.hide', ['teacher_leave' => $leave->id]) }}" 
                          method="post" 
                          class="w-100"
                          onsubmit="return confirm('Μπορείτε να προβάλετε τις μη εφανείς άδειες στο κάτω μέρος αυτής της σελίδας.')">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-eye-slash"></i>
                            <small class="d-block">Μη εμφάνιση</small>
                        </button>
                    </form>
                @endif
            @else
                {{-- Action Buttons for Submitted Leaves --}}
                <div class="d-flex flex-column gap-1">
                    {{-- Unlock Button OR approved leave --}}
                    @if($leavesStatus[$leave->protocol_number . "_" . $leave->protocol_date->format('Y')] === 'Εγκρίθηκε')
                        <div class="text-center text-success small px-1">
                            <i class="bi bi-patch-check-fill fs-5"></i>
                            <small class="d-block fw-semibold">Έγκεκριμένη από ΔΙΠΕ</small>
                            <small class="d-block text-muted" style="font-size: 0.7rem;">Δεν επιτρέπεται τροποποίηση</small>
                        </div>
                    @else
                        <form action="{{ route('leaves.leave_unlock', ['teacher_leave' => $leave->id]) }}" 
                            method="post">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                <i class="bi bi-unlock"></i>
                                <small class="d-block">Διόρθωση</small>
                            </button>
                        </form>
                    @endif
                    
                    {{-- Hide Leave Button --}}
                    <form action="{{ route('leaves.hide', ['teacher_leave' => $leave->id]) }}" 
                          method="post"
                          onsubmit="return confirm('Είστε σίγουροι ότι θέλετε να αποκρύψετε αυτή την άδεια;')">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-eye-slash"></i>
                            <small class="d-block">Μη εμφάνιση</small>
                        </button>
                    </form>
                </div>
            @endif
        </div>
        @endif
    @else
        {{-- Substitute Teacher Note --}}
        <!-- <div class="col-md-6">
            <div class="alert alert-info mb-0 py-2">
                <i class="bi bi-info-circle"></i>
                <em>Αναπληρωτής εκπαιδευτικός (ανάρτηση στο invoices)</em>
            </div>
        </div> -->
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