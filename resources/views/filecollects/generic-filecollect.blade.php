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
                            // Show success feedback
                            const feedback = document.getElementById('comment-feedback');
                            feedback.classList.remove('d-none', 'alert-danger');
                            feedback.classList.add('alert-success');
                            feedback.innerHTML = '<i class="bi bi-check-circle me-2"></i>Η παρατήρηση αποθηκεύτηκε επιτυχώς';
                            setTimeout(() => feedback.classList.add('d-none'), 3000);
                        },
                        error: function(error) {
                            // Show error feedback
                            const feedback = document.getElementById('comment-feedback');
                            feedback.classList.remove('d-none', 'alert-success');
                            feedback.classList.add('alert-danger');
                            feedback.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Παρουσιάστηκε σφάλμα κατά την αποθήκευση';
                        }
                    });
                });
            });
        </script>
    @endpush
@endif

@push('title')
<title>{{$filecollect->name}}</title>
@endpush

@push('styles')
<style>
    .file-collection-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0.5rem;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .status-open {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-closed {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .file-card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.15s ease-in-out;
        border-radius: 0.75rem;
    }
    
    .file-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    
    .upload-zone {
        border: 2px dashed #dee2e6;
        border-radius: 0.75rem;
        padding: 2rem;
        text-align: center;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .upload-zone:hover {
        border-color: #6c757d;
        background-color: #e9ecef;
    }
    
    .file-icon {
        width: 2rem;
        height: 2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        margin-right: 0.75rem;
    }
    
    .file-icon.pdf { background-color: #dc3545; color: white; }
    .file-icon.docx { background-color: #2b579a; color: white; }
    .file-icon.xlsx { background-color: #217346; color: white; }
    
    .comment-section {
        background-color: #f8f9fa;
        border-radius: 0.75rem;
        padding: 1.5rem;
        border-left: 4px solid #007bff;
    }
    
    .admin-message {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        border-radius: 0.75rem;
        padding: 1.5rem;
        border-left: 4px solid #fd7e14;
    }
    
    .timeline-item {
        border-left: 2px solid #dee2e6;
        padding-left: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 0;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #007bff;
    }
</style>
@endpush

<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="file-collection-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-2">{{$filecollect->name}}</h1>
                    <p class="mb-0 opacity-75">
                        <i class="bi bi-building me-2"></i>
                        {{$filecollect->department->name}}
                    </p>
                </div>
                <div class="col-md-2">

                </div>
                <div class="col-md-4 text-md-end">
                    <span class="status-badge {{ $accepts ? 'status-open' : 'status-closed' }}">
                        <i class="bi {{ $accepts ? 'bi-check-circle' : 'bi-x-circle' }} me-2"></i>
                        {{ $accepts ? 'Δέχεται υποβολές' : 'Δεν δέχεται υποβολές' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Description Section -->
    @if($filecollect->comment)
    <div class="row mb-4">
        <div class="col">
            <div class="card file-card border-info">
                <div class="card-header bg-info bg-opacity-10 border-info">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Περιγραφή
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-muted">{!!html_entity_decode($filecollect->comment)!!}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Comments Section -->
    @if($old_data->file)
    <div class="row mb-4">
        <div class="col">
            @if($old_data->filecollect->accepts)
                <div class="comment-section">
                    <h5 class="mb-3">
                        <i class="bi bi-chat-text me-2"></i>
                        Παρατήρηση/Σημείωση
                    </h5>
                    <textarea name="stake_comment" id="stake_comment" class="form-control mb-2" 
                              data-stakeholder-id="{{ $old_data->id }}" rows="4" 
                              placeholder="Προσθέστε τη δική σας παρατήρηση...">{{ $old_data->stake_comment ?? '' }}</textarea>
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Αποθηκεύεται αυτόματα όταν κάνετε κλικ έξω από το πλαίσιο κειμένου
                    </small>
                    <div id="comment-feedback" class="alert d-none mt-2"></div>
                </div>
            @else
                @if($old_data->stake_comment)
                <div class="comment-section">
                    <h5 class="mb-3">
                        <i class="bi bi-chat-text me-2"></i>
                        Η Παρατήρησή σας
                    </h5>
                    <div class="bg-white p-3 rounded border">
                        {{ $old_data->stake_comment }}
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
    @endif

    <div class="row g-4">
        <!-- Reference Documents -->
        <div class="col-lg-4">
            @if($filecollect->base_file || $filecollect->template_file)
            <div class="card file-card h-100">
                <div class="card-header bg-primary bg-opacity-10">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        Έγγραφα Αναφοράς
                    </h5>
                </div>
                <div class="card-body">
                    @if($filecollect->base_file)
                        @php
                            $icon = "bi-download";
                            $iconClass = "";
                            $filename = $filecollect->base_file;
                            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            
                            switch($extension) {
                                case 'docx':
                                    $icon = "bi-file-word";
                                    $iconClass = "docx";
                                    break;
                                case 'xlsx':
                                    $icon = "bi-file-excel";
                                    $iconClass = "xlsx";
                                    break;
                                case 'pdf':
                                    $icon = "bi-file-pdf";
                                    $iconClass = "pdf";
                                    break;
                            }
                        @endphp
                        <div class="d-flex align-items-center mb-3">
                            <div class="file-icon {{$iconClass}}">
                                <i class="bi {{$icon}}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">Σχετική εγκύκλιος:</div>
                                <small class="text-muted">{{$filename}}</small>
                            </div>
                        </div>
                        <form action="{{url("/filecollects/download_admin_file/$filecollect->id/base")}}" method="get">
                            @csrf
                            <button class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-download me-2"></i>Λήψη
                            </button>
                        </form>
                    @endif

                    @if($filecollect->template_file)
                        @php
                            $icon = "bi-download";
                            $iconClass = "";
                            $filename = $filecollect->template_file;
                            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            
                            switch($extension) {
                                case 'docx':
                                    $icon = "bi-file-word";
                                    $iconClass = "docx";
                                    break;
                                case 'xlsx':
                                    $icon = "bi-file-excel";
                                    $iconClass = "xlsx";
                                    break;
                                case 'pdf':
                                    $icon = "bi-file-pdf";
                                    $iconClass = "pdf";
                                    break;
                            }
                        @endphp
                        <div class="d-flex align-items-center mb-3 {{ $filecollect->base_file ? 'mt-4' : '' }}">
                            <div class="file-icon {{$iconClass}}">
                                <i class="bi {{$icon}}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">Αρχείο για συμπλήρωση:</div>
                                <small class="text-muted">{{$filename}}</small>
                            </div>
                        </div>
                        <form action="{{url("/filecollects/download_admin_file/$filecollect->id/template")}}" method="get">
                            @csrf
                            <button class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-download me-2"></i>Λήψη
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Upload Section -->
        <div class="col-lg-4">
            @php
                $pdf = json_decode($filecollect->fileMime, true)['pdf'] ?? 0;
                $xlsx = json_decode($filecollect->fileMime, true)['xlsx'] ?? 0;
                $docx = json_decode($filecollect->fileMime, true)['docx'] ?? 0;
                $totalFiles = $pdf + $xlsx + $docx;
            @endphp

            @if($totalFiles > 0)
            <div class="card file-card h-100">
                <div class="card-header bg-warning bg-opacity-10">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-upload me-2"></i>
                        Υποβολή Αρχείων
                    </h5>
                </div>
                <div class="card-body">
                    @if(!$accepts)
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Η συλλογή δε δέχεται υποβολές</strong>
                        </div>
                    @else
                        <form action="{{url("/filecollects/upload_stake_file/$filecollect->id")}}" method="post" enctype="multipart/form-data">
                            @csrf
                            
                            @if($totalFiles > 1)
                                <div class="alert alert-info mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <small>Τα αρχεία υποβάλλονται όλα μαζί</small>
                                </div>
                            @endif

                            @for($i = 1; $i <= $pdf; $i++)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <div class="file-icon pdf d-inline-flex me-2">
                                            <i class="bi bi-file-pdf"></i>
                                        </div>
                                        {{$i}}{{ $i == 1 ? 'ο' : 'ο' }} Αρχείο PDF
                                    </label>
                                    <input name="the_file_pdf{{$i}}" type="file" class="form-control" 
                                           accept="application/pdf" required>
                                </div>
                            @endfor

                            @for($i = 1; $i <= $xlsx; $i++)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <div class="file-icon xlsx d-inline-flex me-2">
                                            <i class="bi bi-file-excel"></i>
                                        </div>
                                        {{$i}}{{ $i == 1 ? 'ο' : 'ο' }} Αρχείο Excel
                                    </label>
                                    <input name="the_file_xlsx{{$i}}" type="file" class="form-control" 
                                        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                </div>
                            @endfor

                            @for($i = 1; $i <= $docx; $i++)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <div class="file-icon docx d-inline-flex me-2">
                                            <i class="bi bi-file-word"></i>
                                        </div>
                                        {{$i}}{{ $i == 1 ? 'ο' : 'ο' }} Αρχείο Word
                                    </label>
                                    <input name="the_file_docx{{$i}}" type="file" class="form-control" 
                                           accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                                </div>
                            @endfor

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-upload me-2"></i>Υποβολή Αρχείων
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Submitted Files -->
        <div class="col-lg-4">
            @if($old_data->file)
            <div class="card file-card h-100">
                <div class="card-header bg-success bg-opacity-10">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-check-circle me-2"></i>
                        Υποβληθέντα Αρχεία
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline-item">
                        <small class="text-muted">Τελευταία ενημέρωση</small>
                        <div class="fw-semibold text-primary">{{$old_data->uploaded_at}}</div>
                    </div>

                    <div class="mb-3">
                        @foreach(json_decode($old_data->file, true) as $file)
                            @php
                                $icon = "bi-download";
                                $iconClass = "";
                                $filename = $file['original_filename'];
                                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                
                                switch($extension) {
                                    case 'docx':
                                        $icon = "bi-file-word";
                                        $iconClass = "docx";
                                        break;
                                    case 'xlsx':
                                        $icon = "bi-file-excel";
                                        $iconClass = "xlsx";
                                        break;
                                    case 'pdf':
                                        $icon = "bi-file-pdf";
                                        $iconClass = "pdf";
                                        break;
                                }
                            @endphp
                            <div class="d-flex align-items-center mb-2">
                                <div class="file-icon {{$iconClass}}">
                                    <i class="bi {{$icon}}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{$filename}}</div>
                                </div>
                                <form action="{{url("/filecollects/download_stake_file/$old_data->id/$filename")}}" method="get" class="ms-2">
                                    @csrf
                                    <button class="btn btn-outline-success btn-sm" title="Λήψη αρχείου">
                                        <i class="bi bi-download"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <form action="{{url("/filecollects/delete_stake_file/$old_data->id")}}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100" 
                                onclick="return confirm('ΠΡΟΣΟΧΗ! Θα διαγραφούν ΟΛΑ τα αρχεία σας και θα μπορείτε να ανεβάσετε νέο μόνο αν η εφαρμογή δέχεται υποβολές')">
                            <i class="bi bi-trash me-2"></i>Διαγραφή Αρχείων
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>



    <!-- Admin Message -->
    @if($old_data->message_from_admin)
    <div class="row mt-4">
        <div class="col">
            <div class="admin-message">
                <h5 class="mb-3">
                    <i class="bi bi-person-badge me-2"></i>
                    Σχόλιο από Διαχειριστή
                </h5>
                <div class="mb-2">
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>
                        {{ $old_data->message_from_admin_at }}
                    </small>
                </div>
                <div class="bg-white bg-opacity-75 p-3 rounded">
                    {{ $old_data->message_from_admin }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>