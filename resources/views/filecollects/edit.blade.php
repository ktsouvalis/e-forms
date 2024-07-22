<x-layout>
    @push('links')
        <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('summernote-0.8.18-dist/summernote-lite.min.css')}}" rel="stylesheet"/>
        <link href="{{asset('customCss/style.css')}}" rel="stylesheet"/>
    @endpush
    @push('scripts')
        <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
        <script src="{{asset('datatable_init.js')}}"></script>
        <script src="{{asset('summernote-0.8.18-dist/summernote-lite.min.js')}}"></script>
        <script src="{{asset('/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script>
           $(document).ready(function() {
                $(document).on('mousedown', 'a[data-toggle="modal"]', function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var stakeholderName = $(this).data('stakeholder-name');
                    $('#messageModal .modal-header').text('Μήνυμα προς ' + stakeholderName);

                    var stakeholderId = $(this).data('stakeholder-id');
                    $('#stakeholderId').val(stakeholderId);

                    setTimeout(function() {
                        $('#messageModal').modal('show');
                    }, 50);

                    $('#messageModal').on('shown.bs.modal', function() {
                        $('#message').focus();
                    });
                });
            });
        </script>
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
        <script>
            $(document).ready(function() {
                $('body').on('change', '.check-checkbox', function() {
                    
                    const stakeholderId = $(this).data('stakeholder-id');
                    const isChecked = $(this).is(':checked');
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    // var address = "{{url('/filecollect_checked')}}" + '/' + stakeholderId;
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
    
                    $.ajax({
                        url: '../filecollect_checked/'+stakeholderId,
                        type: 'POST',
                        data: {
                            checked: isChecked
                        },
                        success: function(response) {
                            console.log("success");
                        },
                        error: function(error) {
                            console.log("An error occurred: " + error);
                        }
                    });
                });
            });
        </script>
        <script>
            //if all checkoboxes in the page are selected, the select all checkbox is selected and disabled
            function updateCheckAll() {
                var all = $('.check-checkbox').length;
                var allChecked = $('.check-checkbox:not(:checked)').length === 0;
                if(all==0){
                    $('#check-all').prop('checked', false);
                    $('#check-all').prop('disabled', true);
                }
                else{
                    $('#check-all').prop('checked', allChecked);
                    $('#check-all').prop('disabled', allChecked);
                }
            }

            $('#check-all').click(function(e) { // when "Select All" is clicked
                e.stopPropagation(); // prevent the click event from being propagated to the table row
            }).change(function() {
                if (this.checked) { 
                    $('.check-checkbox:not(:checked)').prop('checked', true).trigger('change'); // check all unchecked checkboxes
                }
                updateCheckAll(); // disable if necessary
            });

            $('.check-checkbox').change(updateCheckAll); // update "Select All" every time a checkbox is clicked
            
            $(document).ready(function() {
                updateCheckAll();

                // Ensure DataTables has been initialized
                if ($.fn.dataTable.isDataTable('#dataTable')) {
                    // Retrieve existing DataTables instance
                    var table = $('#dataTable').DataTable();

                    // Update "Select All" checkbox when table is redrawn
                    table.on('draw.dt', function() {
                        updateCheckAll();
                    });
                }
            });
        </script>
        <script>//Change the file input text to the selected file name
            document.getElementById('file1').addEventListener('change', function() {
                var fileName = document.getElementById('file1').files[0].name;
                document.getElementById('file-chosen1').textContent = fileName;
                document.getElementById('file-chosen-label1').textContent = 'Επιλεγμένο αρχείο: ';
            });
            document.getElementById('file2').addEventListener('change', function() {
                var fileName = document.getElementById('file2').files[0].name;
                document.getElementById('file-chosen2').textContent = fileName;
                document.getElementById('file-chosen-label2').textContent = 'Επιλεγμένο αρχείο: ';
            });
        </script>
        <script src="{{asset('charcount.js')}}"></script>
    @endpush
    @push('title')
        <title>{{$filecollect->name}}</title>
    @endpush
    @if($filecollect->no_of_files<>0)
    @include('filecollects.filecollect_admin_before')
    @endif   
        <div class="container">
            @if(!$filecollect->no_of_files)
            <div class="hstack gap-2">
                <i class="bi bi-info-circle-fill text-wrap"></i>
                <div><small>Συμπληρώστε τους αριθμούς για να συνεχίσετε με τα υπόλοιπα στοιχεία της συλλογής. </small></div>
            </div>
            @endif
            <form action="{{url("/filecollects/$filecollect->id")}}" method="post" class="container-fluid" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="input-group">
                    <span class="input-group-text w-25"></span>
                    <span class="input-group-text w-75"><strong>Επεξεργασία Στοιχείων Συλλογής Αρχείων</strong></span>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="">Τίτλος</span>
                    <input name="name" type="text" class="form-control" placeholder="Name" required value="{{$filecollect->name}}">
                </div>
                <div class="input-group">
                    <span class="input-group-text text-wrap w-25" id="basic-addon2">Πόσα αρχεία pdf θα ζητηθούν; </span>
                    @php
                        $pdfCount = json_decode($filecollect->fileMime, true)['pdf'];
                    @endphp
                    <input name="no_of_pdf_files" type="number" class="form-control" required value="{{$pdfCount}}"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text text-wrap w-25" id="basic-addon2">πόσα excel (.xlsx);</span>
                    @php
                        $xlsxCount = json_decode($filecollect->fileMime, true)['xlsx'];
                    @endphp
                    <input name="no_of_xlsx_files" type="number" class="form-control" required value="{{$xlsxCount}}"><br>
                </div>
                <div class="input-group">
                    <span class="input-group-text text-wrap w-25" id="basic-addon2">και πόσα word (.docx);</span>
                    @php
                        $docxCount = json_decode($filecollect->fileMime, true)['docx'];
                    @endphp
                    <input name="no_of_docx_files" type="number" class="form-control" required value="{{$docxCount}}"><br>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση αλλαγών</button>
                    <a href="{{url("/filecollects/$filecollect->id/edit")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                </div>
            </form>
            
           
            <hr>
            @if($filecollect->no_of_files<>0)
            <div class="hstack gap-3">
                <form action="{{url("/filecollects/update_admin_file/$filecollect->id/base")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group py-2">
                        <span class="input-group-text w-75"><strong>Ενημέρωση εγκυκλίου συλλογής</strong></span>
                    </div>
                    <div class="input-group w-75">
                        <div class="custom-file-upload">
                            <input type="file" id="file1" class="file-input" name="base_file" required/>
                            <label for="file1" class="file-label" id="file-chosen-label1">Επιλέξτε αρχείο: </label>
                            <span id="file-chosen1">Δεν έχει επιλεγεί</span>
                        </div>
                        {{-- <input name="base_file" type="file" class="form-control" required><br> --}}
                    </div>
                    <div class="input-group">
                        <button type="submit" class="btn btn-primary m-2 bi bi-upload" > Ανέβασμα</button>
                    </div>
                </form>
                @if($filecollect->base_file)
                <form action="{{url("/filecollects/download_admin_file/$filecollect->id/base")}}" method="get">
                    @csrf
                    <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$filecollect->base_file}} </button>
                </form>
                <form action="{{url("/filecollects/delete_admin_file/$filecollect->id/base")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger bi bi-x-circle" onclick="return confirm('Επιβεβαίωση διαγραφής αρχείου')"> </button>
                </form>
                @endif
            </div>
            <hr>
            <div class="hstack gap-3">
            <form action="{{url("/filecollects/update_admin_file/$filecollect->id/template")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                @csrf
                <div class="input-group">
                    <span class="input-group-text w-75"><strong>Ενημέρωση πρότυπου αρχείου συλλογής</strong></span>
                </div>
                <div class="input-group w-75">
                    <div class="custom-file-upload">
                        <input type="file" id="file1" class="file-input" name="template_file" required/>
                        <label for="file1" class="file-label" id="file-chosen-label1">Επιλέξτε αρχείο: </label>
                        <span id="file-chosen1">Δεν έχει επιλεγεί</span>
                    </div>
                    {{-- <input name="template_file" type="file" class="form-control"><br> --}}
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary m-2 bi bi-upload" > Ανέβασμα</button>
                </div>
            </form>
            @if($filecollect->template_file)
            <form action="{{url("/filecollects/download_admin_file/$filecollect->id/template")}}" method="get">
                @csrf
                <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$filecollect->template_file}} </button>
            </form>
            <form action="{{url("/filecollects/delete_admin_file/$filecollect->id/template")}}" method="post">
                @csrf
                <button type="submit" class="btn btn-danger bi bi-x-circle" onclick="return confirm('Επιβεβαίωση διαγραφής αρχείου')"> </button>
            </form>
            @endif
            </div>
            <hr>
        
        @if(Auth::user()->isAdmin() and $xlsxCount==1)
       
            <form action="{{url("/filecollects/num_of_lines/$filecollect->id")}}" method="post" >
                @csrf
                <div class="input-group">
                    <span class="input-group-text w-50"><strong>Εξαγωγή συγκεντρωτικού αρχείου</strong></span>
                </div>
                <div class="input-group w-50">
                    <span class="input-group-text" id="basic-addon2">Αριθμός γραμμών για εξαγωγή</span>
                    <input name="lines" type="number" value="@if($filecollect->lines_to_extract){{$filecollect->lines_to_extract}}@endif" class="form-control" required>
                </div>
                
                <div class="input-group">
                    <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση</button>
                </div>
                <div class="hstack gap-2">
                    <i class="bi bi-info-circle-fill text-wrap"></i>
                    <div><small>Για την περίπτωση που δίνεται <u>1 αρχείο xlsx</u> ως πρότυπο</small></div>
                </div>
            </form>
            <hr>
        @endif
        <form action="{{url("/filecollects/update_comment/$filecollect->id")}}" method="post" enctype="multipart/form-data" class="container-fluid justify-content-center">
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
        @if($filecollect->comment)
            <div class="row">
                <div class="col"></div>
                <div class="col">
                <div class="card py-2" style="background-color: Gainsboro; text-decoration: none; font-size: small">
                    <div class="m-1 post-text">{!!html_entity_decode($filecollect->comment)!!}</div>
                </div>
                </div>
                <div class="col"></div>
            </div>
        @endif
        <hr>
        @php
            $myapp = 'filecollect';
            $myid = $filecollect->id;   
        @endphp
        <nav class="navbar navbar-light bg-light">
        <div class="vstack gap-3">
        @include('criteria_form')
        <form action="{{url("/import_whocan/filecollect/$filecollect->id")}}" method="post" class="container-fluid">
            @csrf
            <div class="input-group">
                <span class="input-group-text w-25"></span>
                <span class="input-group-text w-75"><strong>Ενδιαφερόμενοι</strong></span>
            </div>
            <div class="input-group">
                <span class="input-group-text w-25" id="basic-addon2">Name</span>
                <textarea name="afmscodes"  class="form-control" cols="122" rows="5" style="resize: none;" placeholder="ΑΦΜ εκπαιδευτικών ή/και κωδικοί σχολείων χωρισμένα με κόμμα (,) κενό ( ) ή αλλαγή γραμμής." required></textarea>
            </div>
            <div class="input-group py-1 px-1">
                <button type="submit" class="btn btn-primary bi bi-database-add"> Εισαγωγή Σχολείων/Εκπαιδευτικών</button>
            </div>
        </form>
        </div>
        </div>
        <div class="vstack gap-2 py-3">
            
            @if($filecollect->stakeholders->count())
            <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-light">
                            <h5 class="modal-title" id="messageModalLabel">Αποστολή μηνύματος</h5>
                        </div>
                        <form action="{{url("/filecollects/send_personal_message")}}" method="post">
                            @csrf
                            <div class="modal-body bg-light">
                                <input type="hidden" id="stakeholderId" name="stakeholder_id">
                                <div class="form-group">
                                    <textarea class="form-control" id="message" name="message"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
                                <button type="submit" class="btn btn-outline-primary bi bi-send"> Αποστολή</button>
                            </div>
                        </form>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                @if(Auth::user()->department->filecollects->find($filecollect->id))
                    <input type="checkbox" id="check-all"><strong> Σημείωση όλων των αρχείων που έχουν υποβληθεί (και φαίνονται στη σελίδα) ως ελεγμένα</strong>
                @endif
                <table  id="dataTable" class="align-middle table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th>Αποστολή υπενθύμισης</th>
                        <th id="search">Αναγνωριστικό</th>
                        <th id="search">name</th>
                        <th id="search">mail</th>
                        <th id="search">Έχει υποβάλλει</th>
                        <th>Σχόλιο</th>
                        <th>Ημερομηνία Υποβολής</th>
                        <th>Έλεγχος</th>
                        <th class="align-middle">Διαγραφή</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($filecollect->stakeholders as $one_stakeholder)
                <tr>
                    <td style="text-align:center">
                        <form action="{{url("/filecollect_personal_mail/$filecollect->id/$one_stakeholder->id")}}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> </button>
                        </form>
                    </td>
                    @if($one_stakeholder->stakeholder_type=="App\Models\School")
                        <td>{{$one_stakeholder->stakeholder->code}}</td>
                    @else
                        <td>{{$one_stakeholder->stakeholder->afm}}</td>
                    @endif
                    @php
                        if($one_stakeholder->stakeholder instanceof App\Models\School)
                            $name = $one_stakeholder->stakeholder->name;
                        else
                            $name = $one_stakeholder->stakeholder->surname . ' ' . $one_stakeholder->stakeholder->name;
                    @endphp
                    <td>
                        <a href="#" class="no-spinner" data-toggle="modal" data-target="#messageModal" data-stakeholder-id="{{$one_stakeholder->id}}" data-stakeholder-name = "{{$name}}">
                            {{$one_stakeholder->stakeholder->surname}} {{$one_stakeholder->stakeholder->name}}
                        </a>
                    </td>
                    <td>{{$one_stakeholder->stakeholder->mail}}</td>
                    
                    @if($one_stakeholder->file)
                        <td>   
                            <div class="vstack gap-2">
                                @foreach(json_decode($one_stakeholder->file, true) as $file)
                                    @php
                                        $icon = "bi bi-box-arrow-down";
                                        $filename = $file['original_filename'];
                                        if(substr($filename, -4) == "docx")
                                            $icon = "bi bi-file-word";
                                        else if(substr($filename, -4) == "xlsx")
                                            $icon = "bi bi-file-excel";
                                        else if(substr($filename, -4) == ".pdf")
                                            $icon = "bi bi-file-pdf";
                                    @endphp
                                    <form action="{{url("/filecollects/download_stake_file/$one_stakeholder->id/$filename")}}" method="get">
                                        @csrf
                                        <button class="btn btn-outline-success {{$icon}}" title="Λήψη αρχείου"> {{$filename}} </button>
                                    </form>
                                @endforeach
                            </div>
                        </td>
                    @else
                        <td style="colοr:red"> ΟΧΙ </td>
                    @endif
                    @if($one_stakeholder->stake_comment)
                        <td>
                            {{$one_stakeholder->stake_comment}}
                        </td>
                    @else
                        <td> - </td>
                    @endif
                    @if($one_stakeholder->uploaded_at)
                    <td>{{$one_stakeholder->uploaded_at}}</td>
                    @else
                    <td>-</td>
                    @endif
                    
                    @if($one_stakeholder->file and Auth::user()->department->filecollects->find($filecollect->id))
                        <td style="text-align:center;">
                            <input type="checkbox" class="check-checkbox" data-stakeholder-id="{{ $one_stakeholder->id }}" {{ $one_stakeholder->checked ? 'checked' : '' }}>
                        </td>
                    @else
                        <td style="text-align:center;">-</td>
                    @endif
                    <td> 
                        <form action="{{url("/delete_one_whocan/filecollect/$one_stakeholder->id")}}" method="post">
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
                <a href="{{url("/preview_mail_all_whocans/filecollect/$filecollect->id")}}" class="btn btn-outline-secondary bi bi-binoculars no-spinner" target="_blank"> Προεπισκόπηση email</a>
                <form action="{{url("/send_mail_all_whocans/filecollect/$filecollect->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> Αποστολή email σε όλους</button>
                </form>
                <form action="{{url("/mail_submitted/$filecollect->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> Αποστολή email σε όσους έχουν στείλει αρχείο</button>
                </form>
                <form action="{{url("/mail_not_submitted/$filecollect->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση αποστολής email;')"> Αποστολή email σε όσους <strong>δεν</strong> έχουν στείλει αρχείο</button>
                </form>
                <form action="{{url("/filecollects/download_directory/$filecollect->id")}}" method="post" data-export>
                    @csrf
                    <button type="submit" class="btn btn-success bi bi-cloud-download" > Λήψη αρχείων</button>
                </form>
                @if(Auth::user()->isAdmin() and $filecollect->lines_to_extract)
                <form action="{{url("/filecollects/extract_xlsx_file/$filecollect->id")}}" method="post" data-export>
                    @csrf
                    <button type="submit" class="btn btn-success bi bi-filetype-xlsx" > Εξαγωγή συγκεντρωτικού αρχείου</button>
                </form>
                @endif
                <form action="{{url("/delete_all_whocans/filecollect/$filecollect->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger bi bi-x-circle" onclick="return confirm('Επιβεβαίωση διαγραφής stakeholders!')"> Διαγραφή όλων</button>
                </form>
            </div>
            @endif 
            <hr>
        @endif
        </div> 
</x-layout>