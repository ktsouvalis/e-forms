<x-layout>
    @push('links')
        <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('summernote-0.8.18-dist/summernote-lite.min.css')}}" rel="stylesheet">
    @endpush
    @push('scripts')
        <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
        <script src="{{asset('datatable_init.js')}}"></script>
        <script src="{{asset('copylink.js')}}"></script>
        <script src="{{asset('summernote-0.8.18-dist/summernote-lite.min.js')}}"></script>
        <script>
            $(document).ready(function () {
                // Get the maximum character limit
                var maxChars = 5000;
                // Initialize Summernote with callback
                $('.summernote').summernote({
                    width: "100%",
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['list', ['ul', 'ol']],
                        ['link', ['link']],
                    ],
                    lang: 'el-GR',
                    callbacks: {
                        onChange: function(contents, $editable) {
                            var currentChars = contents.length;
                            var remainingChars = maxChars - currentChars;

                            // Display the remaining characters
                            $('#charCount').text(remainingChars);
                        }
                    }
                });
            });
        </script>
        <script src="{{asset('charcount.js')}}"></script>
        <script>
            $(document).ready(function() {
                $('body').on('change', '.fileshare-checkbox', function() {
                    
                    const fileshareId = $(this).data('fileshare-id');
                    const isChecked = $(this).is(':checked');
                    // Get the CSRF token from the meta tag
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    $.ajax({
                        url: '../allow_schools/'+fileshareId,
                        type: 'POST',
                        data: {
                            // _method: 'PATCH', // Laravel uses PATCH for updates
                            checked: isChecked
                        },
                        success: function(response) {
                            // Handle the response here, update the page as needed
                            console.log("success");

                        },
                        error: function(error) {
                            // Handle errors
                            console.log("An error occurred: " + error);
                        }
                    });
                });
            });
        </script>
        // Add this script to your blade file
        <script>
        $(document).ready(function() {
            // Chunked upload for common files
            $('input[name="fileshare_common_files[]"]').fileupload({
                url: '{{ url("/fileshares/chunked_upload/$fileshare->id/common") }}',
                formData: {_token: '{{ csrf_token() }}'},
                dataType: 'json',
                done: function (e, data) {
                    console.log('Upload finished', data.result);
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#common-progress .progress-bar').css('width', progress + '%').text(progress + '%');
                },
                chunkSize: 5 * 1024 * 1024 // 5MB chunks
            });

            // Chunked upload for personal files
            $('input[name="fileshare_personal_files[]"]').fileupload({
                url: '{{ url("/fileshares/chunked_upload/$fileshare->id/personal") }}',
                formData: {_token: '{{ csrf_token() }}'},
                dataType: 'json',
                done: function (e, data) {
                    console.log('Upload finished', data.result);
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#personal-progress .progress-bar').css('width', progress + '%').text(progress + '%');
                },
                chunkSize: 5 * 1024 * 1024 // 5MB chunks
            });
        });
        </script>
    @endpush
    @push('title')
        <title>{{$fileshare->name}}</title>
    @endpush
    
        
        <div class="container">
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/fileshares/$fileshare->id")}}" method="post" class="container-fluid" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="asks_to" value="insert">
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Επεξεργασία Στοιχείων Διαμοιρασμού Αρχείων</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Τίτλος</span>
                        <input name="name" type="text" class="form-control" placeholder="Name" aria-label="Name" aria-describedby="basic-addon2" required value="{{$fileshare->name}}"><br>
                    </div>
                    {{-- ── Common files ──────────────────────────────────────────────── --}}
                    <div class="input-group flex-column mb-2">
                        <div class="d-flex w-100">
                            <span class="input-group-text w-25">Κοινά αρχεία</span>
                            <label for="fileshare_common_files"
                                class="form-control d-flex align-items-center gap-2"
                                style="cursor:pointer; background:var(--bs-body-bg);"
                                id="common-drop-zone">
                                <i class="bi bi-cloud-upload"></i>
                                <span id="common-file-label">Επιλογή ή σύρσιμο αρχείων (300+)</span>
                            </label>
                            <input id="fileshare_common_files"
                                name="fileshare_common_files[]"
                                type="file" multiple
                                class="d-none"
                                onchange="handleFileSelect(this, 'common')">
                        </div>
                        <div class="w-100 mt-1" id="common-progress-wrap" style="display:none!important">
                            <div class="progress" style="height:8px;">
                                <div id="common-progress-bar"
                                    class="progress-bar progress-bar-striped progress-bar-animated"
                                    role="progressbar" style="width:0%"></div>
                            </div>
                            <small id="common-progress-label" class="text-muted"></small>
                        </div>
                        <div id="common-result" class="mt-1"></div>
                    </div>

                    {{-- ── Personal files ────────────────────────────────────────────── --}}
                    <div class="input-group flex-column mb-2">
                        <div class="d-flex w-100">
                            <span class="input-group-text w-25">Προσωπικά αρχεία</span>
                            <label for="fileshare_personal_files"
                                class="form-control d-flex align-items-center gap-2"
                                style="cursor:pointer; background:var(--bs-body-bg);"
                                id="personal-drop-zone">
                                <i class="bi bi-cloud-upload"></i>
                                <span id="personal-file-label">Επιλογή ή σύρσιμο αρχείων (300+)</span>
                            </label>
                            <input id="fileshare_personal_files"
                                name="fileshare_personal_files[]"
                                type="file" multiple
                                class="d-none"
                                onchange="handleFileSelect(this, 'personal')">
                        </div>
                        <div class="w-100 mt-1" id="personal-progress-wrap" style="display:none!important">
                            <div class="progress" style="height:8px;">
                                <div id="personal-progress-bar"
                                    class="progress-bar progress-bar-striped progress-bar-animated"
                                    role="progressbar" style="width:0%"></div>
                            </div>
                            <small id="personal-progress-label" class="text-muted"></small>
                        </div>
                        <div id="personal-result" class="mt-1"></div>
                    </div>

                    <div class="input-group">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση αλλαγών</button>
                        <a href="{{url("/fileshares/$fileshare->id/edit")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                    
                </form>
            </nav> 
        <hr>
        <form action="{{url("/fileshares/save_comment/$fileshare->id")}}" method="post" enctype="multipart/form-data" class="container-fluid justify-content-center">
            @csrf
            <span class="input-group-text"><strong>Προσθήκη μηνύματος για ενδιαφερόμενους</strong></span>
            <div class="input-group justify-content-center">
                <textarea class="summernote" name="comment"  class="form-control"></textarea>
            </div>
            <span id="charCount">5000</span>
            <div class="input-group">
                <button type="submit" class="btn btn-primary m-2"> <i class="fa-regular fa-comment-dots"></i> Υποβολή</button>
            </div>
        </form>
        @if($fileshare->comment)
            <div class="row">
                <div class="col"></div>
                <div class="col">
                <div class="card py-2" style="background-color: Gainsboro; text-decoration: none; font-size: small">
                    <div class="m-1 post-text">{!!html_entity_decode($fileshare->comment)!!}</div>
                </div>
                </div>
                <div class="col"></div>
            </div>
        @endif
        <hr>
        <nav class="navbar navbar-light bg-light">
            <div class="vstack gap-3">
                @php
                    $myapp = 'fileshare';
                    $myid = $fileshare->id;
                @endphp
                @include('criteria_form')
                <form action="{{url("/import_whocan/fileshare/$fileshare->id")}}" method="post" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Ενδιαφερόμενοι</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Name</span>
                        <textarea name="afmscodes"  class="form-control" cols="122" rows="5" style="resize: none;" placeholder="ΑΦΜ εκπαιδευτικών ή/και κωδικοί σχολείων χωρισμένα με κόμμα (,)" required></textarea>
                    </div>
                    <div class="input-group py-1 px-1">
                        {{-- <span class="w-25"></span> --}}
                        <button type="submit" class="btn btn-primary bi bi-database-add"> Εισαγωγή Σχολείων/Εκπαιδευτικών</button>
                    </div>
                </form>
                </div>
                <form action="{{url("/fileshares/auto_update_whocan/$fileshare->id")}}" method="post" class="container-fluid">
                    @csrf
                    <div class="input-group py-1 px-1">
                        {{-- <span class="w-25"></span> --}}
                        <button type="submit" class="btn btn-warning bi bi-database-add"> Αυτόματη Εισαγωγή Ενδιαφερόμενων για τα προσωπικά αρχεία</button>
                    </div>
                </form>
            </nav> 
            @if($fileshare->stakeholders->count() and in_array('App\Models\School', $fileshare->stakeholders->pluck('stakeholder_type')->toArray()))
            <input type="checkbox" id="allow" class="fileshare-checkbox" data-fileshare-id="{{ $fileshare->id }}" {{ $fileshare->allow_school ? 'checked' : '' }}>
            <label for="allow"> <strong> Τα σχολεία μπορούν να προσθέτουν τους εκπαιδευτικούς στους ενδιαφερόμενους του fileshare;</strong></label> 
            @endif
        </div>
        <hr>
        <div class="container px-5 vstack gap-2 py-3">
            
            @if($fileshare->stakeholders->count())
            <div class="table-responsive">
                <table  id="dataTable" class="align-middle table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        @if(Auth::user()->isAdmin())
                        <th>Σύνδεσμος</th>
                        @endif
                        <th>Αποστολή υπενθύμισης</th>
                        <th id="search">Αναγνωριστικό</th>
                        <th id="search">name</th>
                        <th id="search">mail</th>
                        <th id="search">added by</th>
                        <th id="search">visited</th>
                        <th class="align-middle">Διαγραφή</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($fileshare->stakeholders as $one_stakeholder)
                @php
                    $md = $one_stakeholder->stakeholder->md5;
                    if($one_stakeholder->stakeholder_type=="App\Models\School")
                        $text = url("/school/$md");
                    else
                        $text = url("/teacher/$md");
                @endphp
                <tr>
                    @if(Auth::user()->isAdmin())
                    <td style="text-align:center">
                        <button class="copy-button btn btn-outline-secondary bi bi-clipboard" data-clipboard-text="{{$text}}"> </button>
                    </td>
                    @endif
                      
                    <td style="text-align:center">
                        <form action="{{url("/fileshare_personal_mail/$fileshare->id/$one_stakeholder->id")}}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> </button>
                        </form>
                    </td>
                    @if($one_stakeholder->stakeholder_type=="App\Models\School")
                        <td>{{$one_stakeholder->stakeholder->code}}</td>
                    @else
                        <td>{{$one_stakeholder->stakeholder->afm}}</td>
                    @endif
                    <td>{{$one_stakeholder->stakeholder->surname}} {{$one_stakeholder->stakeholder->name}}</td>
                    <td>{{$one_stakeholder->stakeholder->mail}}</td>
                    @if($one_stakeholder->addedby_type=="App\Models\School")
                        <td>{{$one_stakeholder->addedby->name}}</td>
                    @else
                        <td>{{$one_stakeholder->addedby->username}}</td>
                    @endif
                    @if($one_stakeholder->visited_fileshare)
                        <td>ΝΑΙ</td>
                    @else
                        <td>ΟΧΙ</td>
                    @endif
                    <td> 
                        <form action="{{url("/delete_one_whocan/fileshare/$one_stakeholder->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger bi bi-x-circle"> </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
                </table>
            </div> 
            <div class="hstack gap-2">
                <a href="{{url("/preview_mail_all_whocans/fileshare/$fileshare->id")}}" class="btn btn-outline-secondary bi bi-binoculars no-spinner" target="_blank"> Προεπισκόπηση email</a>
                <form action="{{url("/send_mail_all_whocans/fileshare/$fileshare->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> Αποστολή email σε όλους</button>
                </form>
                <form action="{{url("/mail_visited/$fileshare->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> Αποστολή email σε όσους έχουν επισκεφθεί</button>
                </form>
                <form action="{{url("/mail_not_visited/$fileshare->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> Αποστολή email σε όσους <strong>δεν</strong> έχουν επισκεφθεί</button>
                </form>
                <form action="{{url("/delete_all_whocans/fileshare/$fileshare->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger bi bi-x-circle" onclick="return confirm('Επιβεβαίωση διαγραφής stakeholders!')"> Διαγραφή όλων</button>
                </form>
            </div>
            @endif 
            <hr>
        </div> 
        
       
        @php
        $directory_common = '/fileshare'.$fileshare->id;
        $directory_personal = $directory_common.'/personal_files';
        $files_common=Storage::disk('local')->files($directory_common);
        $files_personal = Storage::disk('local')->files($directory_personal);
        $not_found = Session::pull('not_found', []);
        @endphp
        @if($not_found)
            <div class='container container-narrow'>
                <div class='alert alert-warning'>
                    <strong>Αναγνωριστικά που δεν βρέθηκαν</strong><br>
                    @isset($not_found)
                        @foreach($not_found as $identifier)
                            {{$identifier}}
                            <br>
                        @endforeach  
                    @endisset
                </div>
            </div>
        <hr>
        @endif
        <div class="container px-5 py-3">
        <div class="hstack">
            @if($files_common)
            <div class="vstack gap-2">
                <strong>Αρχεία κοινά για διαμοιρασμό</strong>
                    @foreach($files_common as $file_c)
                    <div class="hstack gap-1">
                        @php
                            $basename = basename($file_c);
                        @endphp
                        <form action="{{url("/fileshares/download_file/$fileshare->id/$basename")}}" method="get">
                        @csrf
                            <input type="hidden" name="personal" value="0">
                            <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$basename}}</button>
                        </form>
                        <form action="{{url("/fileshares/delete_file/$fileshare->id/$basename")}}" method="post">
                        @csrf
                            <input type="hidden" name="personal" value="0">
                            <button class="btn btn-danger bi bi-x-circle"></button>
                        </form>
                    </div>
                    @endforeach
            </div>
            @endif
            @if($files_personal)
            <div class="vstack gap-2">
               <strong>Αρχεία προσωπικά για διαμοιρασμό</strong>
                    @foreach($files_personal as $file_p)
                    <div class="hstack gap-1">
                        @php
                            $basename = basename($file_p);
                        @endphp
                        <form action="{{url("/fileshares/download_file/$fileshare->id/$basename")}}" method="get">
                        @csrf
                            <input type="hidden" name="personal" value="1">
                            <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$basename}}</button>
                        </form>
                        <form action="{{url("/fileshares/delete_file/$fileshare->id/$basename")}}" method="post">
                        @csrf
                            <input type="hidden" name="personal" value="1">
                            <button class="btn btn-danger bi bi-x-circle"></button>
                        </form>
                    </div>
                    @endforeach
            </div>
            @endif
        </div>
        <hr>
        @if(session()->has('stakeholders_array'))
            @php
                $stakeholders_array = Session::pull('stakeholders_array', []);
            @endphp
            <table>
                <tr>
                <th>Αρχείο</th>
                <th>Ενδιαφερόμενος</th>
                </tr>
            @foreach($stakeholders_array as $one)
            
            <tr>
                <td>{{$one['filename']}}</td>
                <td>{{ isset($one['stakeholder']) ? $one['stakeholder'] : 'N/A' }}</td>
            </tr>
            @endforeach
            </table>
        @endif
    </div>

    {{-- ── Batch-upload script ───────────────────────────────────────── --}}
    @push('scripts')
    <script>
    function buildBatches(files, maxBytes = 6 * 1024 * 1024) { // 6 MB cap, safely under 8 MB
        const batches = [];
        let current = [], currentSize = 0;
        for (const f of files) {
            if (current.length && currentSize + f.size > maxBytes) {
                batches.push(current);
                current = [];
                currentSize = 0;
            }
            current.push(f);
            currentSize += f.size;
        }
        if (current.length) batches.push(current);
        return batches;
    }
    const UPLOAD_URL    = '{{ url("/fileshares/batch_upload/$fileshare->id") }}';
    const CSRF_TOKEN    = '{{ csrf_token() }}';
    
    /* Drag-and-drop wiring */
    ['common','personal'].forEach(type => {
        const zone = document.getElementById(type + '-drop-zone');
        zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('border-primary'); });
        zone.addEventListener('dragleave', ()  => zone.classList.remove('border-primary'));
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.classList.remove('border-primary');
            const input = document.getElementById('fileshare_' + type + '_files');
            /* DataTransfer → FileList shim */
            const dt = e.dataTransfer;
            input._files = Array.from(dt.files);
            handleFileSelect(input, type);
        });
    });
    
    function handleFileSelect(input, type) {
        const files = input._files || Array.from(input.files);
        if (!files.length) return;
    
        const label  = document.getElementById(type + '-file-label');
        label.textContent = files.length + ' αρχεία επιλέχθηκαν – κάντε κλικ στο Αποθήκευση για αποστολή';
    
        /* Store on the input element so the save button can access them */
        input._pendingFiles = files;
        input._type = type;
    }
    
    /* Called by the save button – see the modified form below */
    async function uploadPendingFiles(type) {
        const input  = document.getElementById('fileshare_' + type + '_files');
        const files  = input._pendingFiles;
        if (!files || !files.length) return { ok: true, skipped: true };
    
        const batchList = buildBatches(files);   // array of arrays
        const batches   = batchList.length;
        const wrap     = document.getElementById(type + '-progress-wrap');
        const bar      = document.getElementById(type + '-progress-bar');
        const lbl      = document.getElementById(type + '-progress-label');
        const resultEl = document.getElementById(type + '-result');
    
        wrap.style.display = '';   /* override the !important hide */
        wrap.removeAttribute('style');
        wrap.style.display = 'block';
    
        let uploaded = 0, errors = [];
    
        for (let b = 0; b < batches; b++) {
            const batch = batchList[b];
            const fd    = new FormData();
            fd.append('_token', CSRF_TOKEN);
            fd.append('file_type', type);
            batch.forEach(f => fd.append('files[]', f));
    
            lbl.textContent = `Τα αρχεία χωρίζονται σε ομάδες και ανεβαίνουν τμηματικά. Ομάδα ${b + 1} / ${batches}  ( Συνολικά: ${uploaded} / ${files.length} αρχεία)`;
            bar.style.width = Math.round((b / batches) * 100) + '%';
    
            try {
                const resp = await fetch(UPLOAD_URL, { method: 'POST', body: fd });
                const data = await resp.json();
    
                if (!resp.ok) {
                    errors.push(`Batch ${b + 1}: ${data.message || 'Άγνωστο σφάλμα'}`);
                } else {
                    uploaded += data.uploaded ?? batch.length;
                    if (data.errors && data.errors.length) {
                        errors.push(...data.errors);
                    }
                }
            } catch (err) {
                errors.push(`Batch ${b + 1}: δικτυακό σφάλμα`);
            }
        }
    
        bar.style.width = '100%';
        bar.classList.remove('progress-bar-animated', 'progress-bar-striped');
        bar.classList.add(errors.length ? 'bg-warning' : 'bg-success');
        lbl.textContent = `Ολοκληρώθηκε: ${uploaded} / ${files.length} αρχεία`;
    
        if (errors.length) {
            resultEl.innerHTML =
                `<div class="alert alert-warning py-1 mt-1" style="font-size:.85rem">` +
                errors.map(e => `<div>${e}</div>`).join('') +
                `</div>`;
        }
    
        return { ok: errors.length === 0, uploaded, errors };
    }
    
    /* Intercept the main form submit to run file uploads first */
    document.addEventListener('DOMContentLoaded', () => {
        const mainForm = document.querySelector('form[action*="fileshares/"][method="post"]');
        if (!mainForm) return;
    
        mainForm.addEventListener('submit', async function (e) {
            const commonPending  = document.getElementById('fileshare_common_files')._pendingFiles?.length   > 0;
            const personalPending= document.getElementById('fileshare_personal_files')._pendingFiles?.length > 0;
    
            if (!commonPending && !personalPending) return; /* let the form submit normally */
    
            e.preventDefault();
    
            /* Disable submit button while uploading */
            const btn = mainForm.querySelector('[type=submit]');
            const origText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Αποστολή αρχείων...';
    
            await uploadPendingFiles('common');
            await uploadPendingFiles('personal');
    
            btn.disabled = false;
            btn.innerHTML = origText;
    
            /* Now submit the rest of the form (name change etc.) without files */
            document.getElementById('fileshare_common_files').value   = '';
            document.getElementById('fileshare_personal_files').value = '';
            mainForm.submit();
        });
    });
    </script>
    @endpush

       
</x-layout>