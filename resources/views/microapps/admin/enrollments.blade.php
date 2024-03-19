<x-layout>

    @push('links')
        <link href="../DataTables-1.13.4/css/dataTables.bootstrap5.css" rel="stylesheet"/>
        <link href="../Responsive-2.4.1/css/responsive.bootstrap5.css" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="../DataTables-1.13.4/js/jquery.dataTables.js"></script>
        <script src="../DataTables-1.13.4/js/dataTables.bootstrap5.js"></script>
        <script src="../Responsive-2.4.1/js/dataTables.responsive.js"></script>
        <script src="../Responsive-2.4.1/js/responsive.bootstrap5.js"></script>
        <script src="../datatable_init.js"></script>
    @endpush
    @push('title')
        <title>Εγγραφές 2024-25</title>
    @endpush
    @php
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
    @endphp
    @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
        @if(Auth::user()->isAdmin())      
        <nav class="container navbar navbar-light bg-light">
            <div class="row">
                {{-- Αρχείο Δημοτικού Εγγραφέντων στην Α' Τάξη --}}
                <div class="col-6 hstack gap-3"> 
                <form action="{{url("school_app/enrollments/upload_file/1_enrollments_primary_school.xlsx")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        {{-- <span class="input-group-text w-25"></span> --}}
                        <span class="input-group-text w-75"><strong>Δημοτικό Εγγραφές στην Α' Τάξη</strong></span>
                    </div>
                    <div class="input-group w-75">
                        <input name="file" type="file" class="form-control"><br>
                    </div>
                    <div class="input-group">
                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                        <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                    </div>
                </form>
                <form action="{{url("/school_app/enrollments/1_enrollments_primary_school.xlsx/1_enrollments_primary_school.xlsx")}}" method="get">
                    <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Δημοτικό Εγγραφές στην Α' Τάξη </button>
                </form>
                </div>
                {{-- Αρχείο Νηπιαγωγείου Εγγραφέντων --}}
                <div class="col-6 hstack gap-3">
                    <form action="{{url("school_app/enrollments/upload_file/1_enrollments_nursery_school.xlsx")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <div class="input-group">
                            {{-- <span class="input-group-text w-25"></span> --}}
                            <span class="input-group-text w-75"><strong>Νηπιαγωγείο Εγγραφές</strong></span>
                        </div>
                        <div class="input-group w-75">
                            <input name="file" type="file" class="form-control"><br>
                        </div>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    </form>
                    <form action="{{url("/school_app/enrollments/1_enrollments_nursery_school.xlsx/1_enrollments_nursery_school.xlsx")}}" method="get">
                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Νηπιαγωγείο Εγγραφές </button>
                    </form>
                </div>
            </div>
            <div class="row">
                {{-- Αρχείο Δημοτικού (χωρίς διευρυμένο Ολοήμερο) Εγγραφέντων Α' Τάξης στo Ολοήμερο --}}
                <div class="col-6 hstack gap-3">
                    <form action="{{url("school_app/enrollments/upload_file/2_enrollments_primary_all_day_school.xlsx")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <div class="input-group">
                            {{-- <span class="input-group-text w-25"></span> --}}
                            <span class="input-group-text w-75"><strong>Δημοτικό χωρίς διευρυμένο Ολ. - Εγγραφές στο Ολοήμερο</strong></span>
                        </div>
                        <div class="input-group w-75">
                            <input name="file" type="file" class="form-control"><br>
                        </div>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    </form>
                    <form action="{{url("/school_app/enrollments/2_enrollments_primary_all_day_school.xlsx/2_enrollments_primary_all_day_school.xlsx")}}" method="get">
                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Δημοτικό χωρίς διευρυμένο Ολ. - Εγγραφές Ολοήμερου </button>
                    </form>
                </div>
                {{-- Αρχείο Νηπιαγωγείου (χωρίς διευρυμένο Ολοήμερο) εγγραφέντων στο Ολοήμερο --}}
                <div class="col-6 hstack gap-3">
                    <form action="{{url("school_app/enrollments/upload_file/2_enrollments_nursery_all_day_school.xlsx")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <div class="input-group">
                            {{-- <span class="input-group-text w-25"></span> --}}
                            <span class="input-group-text w-75"><strong>Νηπιαγωγείο χωρίς διευρυμένο Ολ - Εγγραφές Ολοήμερου</strong></span>
                        </div>
                        <div class="input-group w-75">
                            <input name="file" type="file" class="form-control"><br>
                        </div>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    </form>
                    <form action="{{url("/school_app/enrollments/2_enrollments_nursery_all_day_school.xlsx/2_enrollments_nursery_all_day_school.xlsx")}}" method="get">
                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Νηπιαγωγείο χωρίς διευρυμένο Ολ. - Εγγραφές Ολ. </button>
                    </form>
                </div>
            </div>
            <div class="row">
                {{-- Αρχείο Δημοτικού (με διευρυμένο Ολοήμερο) Εγγραφέντων Α' Τάξης στο Ολοήμερο --}}
                <div class="col-6 hstack gap-3">
                    <form action="{{url("school_app/enrollments/upload_file/2_enrollments_primary_ext_all_day_school.xlsx")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <div class="input-group">
                            {{-- <span class="input-group-text w-25"></span> --}}
                            <span class="input-group-text w-75"><strong>Δημοτικό με διευρυμένο Ολ. - Εγγραφές στο Ολοήμερο</strong></span>
                        </div>
                        <div class="input-group w-75">
                            <input name="file" type="file" class="form-control"><br>
                        </div>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    </form>
                    <form action="{{url("/school_app/enrollments/2_enrollments_primary_ext_all_day_school.xlsx/2_enrollments_primary_ext_all_day_school.xlsx")}}" method="get">
                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Δημοτικό με διευρυμένο Ολ. - Εγγραφές Ολ </button>
                    </form>
                </div>   
                {{-- Αρχείο Νηπιαγωγείου (με διευρυμένο Ολοήμερο) εγγραφέντων στο Ολοήμερο --}}
                <div class="col-6 hstack">
                    <form action="{{url("school_app/enrollments/upload_file/2_enrollments_nursery_ext_all_day_school.xlsx")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <div class="input-group">
                            {{-- <span class="input-group-text w-25"></span> --}}
                            <span class="input-group-text w-75"><strong>Νηπιαγωγείο με διευρυμένο Ολοήμερο - Εγγραφές</strong></span>
                        </div>
                        <div class="input-group w-75">
                            <input name="file" type="file" class="form-control"><br>
                        </div>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    </form>
                    <form action="{{url("/school_app/enrollments/2_enrollments_nursery_ext_all_day_school.xlsx/2_enrollments_nursery_ext_all_day_school.xlsx")}}" method="get">
                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Νηπιαγωγείο με διευρυμένο Ολοήμερο - Εγγραφές Ολ. </button>
                    </form>
                </div>
            </div>
            <div class="row">
                {{-- Δημοτικό - Αρχείο για αίτημα δημιουργίας επιπλέον τμήματος --}}
                <div class="col hstack gap-3">
                    <form action="{{url("school_app/enrollments/upload_file/3_enrollments_extra_section_dim.docx")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <div class="input-group">
                            {{-- <span class="input-group-text w-25"></span> --}}
                            <span class="input-group-text w-75"><strong> Δημ. - Αίτημα Δημιουργίας Επιπλέον Τμήματος</strong></span>
                        </div>
                        <div class="input-group w-75">
                            <input name="file" type="file" class="form-control"><br>
                        </div>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    </form>
                    <form action="{{url("/school_app/enrollments/3_enrollments_extra_section_dim.docx/3_enrollments_extra_section_dim.docx")}}" method="get">
                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Δημ. - Αίτημα Δημιουργίας Επιπλέον Τμήματος </button>
                    </form>
                </div>
                {{-- Νηπιαγωγείο - Αρχείο για αίτημα δημιουργίας επιπλέον τμήματος --}}
                <div class="col hstack gap-3">
                    <form action="{{url("school_app/enrollments/upload_file/3_enrollments_extra_section_nip.docx")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <div class="input-group">
                            {{-- <span class="input-group-text w-25"></span> --}}
                            <span class="input-group-text w-75"><strong>Νηπ. - Αίτημα δημιουργίας επιπλέον τμήματος</strong></span>
                        </div>
                        <div class="input-group w-75">
                            <input name="file" type="file" class="form-control"><br>
                        </div>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                            <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                        </div>
                    </form>
                    <form action="{{url("/school_app/enrollments/3_enrollments_extra_section_nip.docx/3_enrollments_extra_section_nip.docx")}}" method="get">
                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Νηπ. - Αίτημα δημιουργίας επιπλέον τμήματος </button>
                    </form>
                </div>
            </div>
            <div class="row gap-2">
            {{-- Δημοτικό - Μαθητές στα όρια --}}
            <div class="col hstack gap-3">
                <form action="{{url("school_app/enrollments/upload_file/4_boundary_students_dim.xlsx")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        {{-- <span class="input-group-text w-25"></span> --}}
                        <span class="input-group-text w-75"><strong> Δημ. - Μαθητές στα όρια</strong></span>
                    </div>
                    <div class="input-group w-75">
                        <input name="file" type="file" class="form-control"><br>
                    </div>
                    <div class="input-group">
                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                        <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                    </div>
                </form>
                <form action="{{url("/school_app/enrollments/4_boundary_students_dim.xlsx/4_boundary_students_dim.xlsx")}}" method="get">
                    <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Δημ. - Μαθητές στα όρια </button>
                </form>
            </div>
            {{-- Νηπιαγωγείο - Μαθητές στα όρια --}}
            <div class="col hstack gap-3">
                <form action="{{url("school_app/enrollments/upload_file/4_boundary_students_nip.xlsx")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                    @csrf
                    <div class="input-group">
                        {{-- <span class="input-group-text w-25"></span> --}}
                        <span class="input-group-text w-75"><strong>Νηπ. - Μαθητές στα όρια</strong></span>
                    </div>
                    <div class="input-group w-75">
                        <input name="file" type="file" class="form-control"><br>
                    </div>
                    <div class="input-group">
                        <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Υποβολή</button>
                        <a href="{{url("/admin/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                    </div>
                </form>
                <form action="{{url("/school_app/enrollments/4_boundary_students_nip.xlsx/4_boundary_students_nip.xlsx")}}" method="get">
                    <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> Νηπ. - Μαθητές στα όρια </button>
                </form>
            </div>
        </div>
        </div>
        </div>
        </nav>
        @endif

        
        @php
            $enrollments = $microapp->stakeholders;
        @endphp
        <div class="table-responsive py-2" style="align-self:flex-start">
            <table  id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th id="search">Σχολείο</th>
                    <th id="search">Εγγραφέντες</th>
                    <th id="">Αρχείο</th>
                    <th id="">Ολοήμερο</th>
                    <th id="">Αρχείο Ολ.</th>
                    <th id="">Αίτημα επιπλ. τμ.</th>
                    <th id="">Μαθητές στα όρια</th>
                    <th id=""></th>
                    <th id=""></th>
                    <th id=""></th>
                    <th>Τελευταία ενημέρωση</th>
                </tr>
            </thead>
            <tbody>
                @php
                   // dd($enrollments);   
                @endphp
                
                @foreach($enrollments as $one_stakeholder)
                    @php
                        $one_school = $one_stakeholder->stakeholder;
                        $one = $one_school->enrollments;
                    @endphp
                        <tr>
                            <td> {{$one_school->name}}</td>
                        @if($one)
                            
                            <td> {{$one->nr_of_students1}}</td>
                            <td>
                                <form action="{{url("/school_app/enrollments/enrollments1_$one_school->code.xlsx/Εγγραφέντες_$one_school->name.xlsx")}}" method="get">
                                <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη">{{$one->enrolled_file1}} </button> 
                                </form>   
                            </td>
                            <td>
                                @if($one->nr_of_students1_all_day1)
                                    {{$one->nr_of_students1_all_day1}}
                                @endif
                            </td>
                            <td>
                                @if($one->all_day_file1)
                                    <form action="{{url("/school_app/enrollments/enrollments2_$one_school->code.xlsx/Ολοήμερο_$one_school->name.xlsx")}}" method="get">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη">{{$one->all_day_file1}} </button> 
                                    </form>   
                                @endif
                            </td>
                            <td>
                                @if($one->extra_section_file1)
                                    <form action="{{url("/school_app/enrollments/enrollments3_$one_school->code.pdf/Επιπλέον_Τμ_$one_school->name.pdf")}}" method="get">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη">{{$one->extra_section_file1}} </button> 
                                    </form>
                                @endif   
                            </td>
                            <td>
                                @if($one->boundaries_st_file1)
                                    <form action="{{url("/school_app/enrollments/enrollments4_$one_school->code.xlsx/Μαθητές_στα_όρια_$one_school->name.xlsx")}}" method="get">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη">{{$one->boundaries_st_file1}} </button> 
                                    </form>
                                @endif
                                </td>
                            <td></td>
                            <td></td>
                            <td>{{$one->updated_at}}</td>
                        @else
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        @endif
                        </tr>
            @endforeach
            </tbody>
            </table>
        </div> <!-- table responsive closure -->
        @include('microapps.microapps_admin_after') {{-- email to those who haven't submitted an answer --}}
        <p class="fw-bold">Σημ: Με την επιλογή αυτή αποστέλλεται mail σε όλα τα Σχολεία που δεν έχουν κάνει υποβολή πίνακα τον τρέχοντα / ενεργό Μήνα</p>
</x-layout>