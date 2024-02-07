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
    @endpush
    @push('title')
        <title>{{$filecollect->name}}</title>
    @endpush
    @include('filecollect_admin_before')
        
        <div class="container">
            <div class="hstack gap-3">
                <form action="{{url("/update_filecollect_file/$filecollect->id/base")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text w-75"><strong>Ενημέρωση εγκυκλίου συλλογής</strong></span>
                    </div>
                    <div class="input-group w-75">
                        <input name="base_file" type="file" class="form-control"><br>
                    </div>
                    <div class="input-group">
                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                    </div>
                </form>
                @if($filecollect->base_file)
                <form action="{{url("/dl_filecollect_file/$filecollect->id/base")}}" method="post">
                    @csrf
                    <button class="btn btn-secondary bi bi-box-arrow-down"> {{$filecollect->base_file}} </button>
                </form>
                @endif
            </div>
                <hr>
                <div class="hstack gap-3">
                <form action="{{url("/update_filecollect_file/$filecollect->id/template")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text w-75"><strong>Ενημέρωση πρότυπου αρχείου συλλογής</strong></span>
                    </div>
                    <div class="input-group w-75">
                        <input name="template_file" type="file" class="form-control"><br>
                    </div>
                    <div class="input-group">
                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                    </div>
                </form>
                @if($filecollect->template_file)
                <form action="{{url("/dl_filecollect_file/$filecollect->id/template")}}" method="post">
                    @csrf
                    <button class="btn btn-secondary bi bi-box-arrow-down"> {{$filecollect->template_file}} </button>
                </form>
                @endif
                </div>
                <hr>
                <form action="{{url("/filecollect_save/$filecollect->id")}}" method="post" class="container-fluid" enctype="multipart/form-data">
                @csrf
                <div class="input-group">
                    <span class="input-group-text w-25"></span>
                    <span class="input-group-text w-75"><strong>Επεξεργασία Στοιχείων Συλλογής Αρχείων</strong></span>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="">Τίτλος</span>
                    <input name="name" type="text" class="form-control" placeholder="Name" required value="{{$filecollect->name}}">
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">Τύπος Δεκτών Αρχείων</span>
                    <select name="filecollect_mime" class="form-control" required>
                        <option value="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 
                        @if($filecollect->fileMime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') selected @endif
                        >Excel (.xlsx)</option>
                        <option value="application/pdf"
                        @if($filecollect->fileMime == 'application/pdf') selected @endif
                        >Pdf (.pdf)</option>
                        <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                        @if($filecollect->fileMime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') selected @endif
                        >Word (.docx)</option>
                    </select>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση αλλαγών</button>
                    <a href="{{url("/filecollect_profile/$filecollect->id")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                </div>
                </form>
        <hr>
        <form action="{{url("/update_filecollect_comment/$filecollect->id")}}" method="post" enctype="multipart/form-data" class="container-fluid justify-content-center">
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
        
        <form action="{{url("/import_whocan/filecollect/$filecollect->id")}}" method="post" class="container-fluid">
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
                <button type="submit" class="btn btn-primary bi bi-database-add"> Εισαγωγή Σχολείων/Εκπαιδευτικών</button>
            </div>
        </form>
        </div>
        <div class="container px-5 vstack gap-2 py-3">
            
            @if($filecollect->stakeholders->count())
            <div class="table-responsive">
                <table  id="dataTable" class="align-middle table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th>Αποστολή υπενθύμισης</th>
                        <th id="search">Αναγνωριστικό</th>
                        <th id="search">name</th>
                        <th id="search">mail</th>
                        <th id="search">Έχει υποβάλλει</th>
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
                    <td>{{$one_stakeholder->stakeholder->surname}} {{$one_stakeholder->stakeholder->name}}</td>
                    <td>{{$one_stakeholder->stakeholder->mail}}</td>
                    
                    @if($one_stakeholder->file)
                        <td>
                            <form action="{{url("/dl_stake_file/$one_stakeholder->id")}}" method="post">
                                @csrf
                                <div class="input-group">
                                    <span class="input-group-text"></span>
                                </div>
                                <button class="btn btn-success bi bi-box-arrow-down"> {{$one_stakeholder->file}} </button>
                            </form>
                        </td>
                    @else
                        <td> - </td>
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
                <a href="{{url("/preview_mail_all_whocans/filecollect/$filecollect->id")}}" class="btn btn-outline-secondary bi bi-binoculars" target="_blank"> Προεπισκόπηση email</a>
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
                <form action="{{url("/delete_all_whocans/filecollect/$filecollect->id")}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger bi bi-x-circle" onclick="return confirm('Επιβεβαίωση διαγραφής stakeholders!')"> Διαγραφή όλων</button>
                </form>
            </div>
            @endif 
            <hr>
        </div> 
</x-layout>