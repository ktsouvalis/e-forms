<x-layout>
    @push('links')
        <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
    @endpush
    @push('scripts')
        <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
        <script src="{{asset('datatable_init.js')}}"></script>
        <script src="{{asset('copylink.js')}}"></script>
    @endpush

    @push('title')
        <title>Συλλογή Αρχείων</title>
    @endpush
    
<body>
    <div class="container">
            @php      
                $all_filecollects = App\Models\Filecollect::all();
                $all_departments = App\Models\Department::all();
            @endphp
            <div class="table-responsive">
            <table  id="dataTable" class="align-middle table table-striped table-hover">
            <thead>
                <tr >
                    <th id="search">ID</th>
                    <th id="search">Name</th>
                    <th id="search">Τμήμα</th>
                    <th id="">Ορατή</th>
                    <th id="">Δέχεται</th>
                    <th>Απαντήσεις</th>
                    <th>Διαγραφή</th>
                </tr>
            </thead>
                <tbody>
                    @foreach($all_filecollects as $one_filecollect)              
                        @can('view', $one_filecollect)
                            <tr >  
                                <td>{{$one_filecollect->id}}</td>
                                
                                <td><div class="text-wrap"><a href="{{url("/filecollects/$one_filecollect->id/edit")}}" style="color:black;">{{$one_filecollect->name}}</a></div></td>
                                <td>
                                    {{$one_filecollect->department->name}}
                                </td>
                                @php
                                    
                                if($one_filecollect->visible){
                                    $opacity_vis = "";
                                    $hidden_acc = "";
                                    $tooltip_vis = "Κλείσιμο ορατότητας";  
                                    if($one_filecollect->accepts){
                                        $opacity_acc="";
                                        $tooltip_acc="Κλείσιμο υποβολών";
                                    }
                                    else{
                                        $opacity_acc = "opacity: 0.4";
                                        $tooltip_acc="Άνοιγμα Υποβολών";
                                    }
                                }
                                else{
                                    $opacity_vis = "opacity: 0.4";
                                    $hidden_acc="hidden";
                                    $opacity_acc="";
                                    $tooltip_acc="";
                                    $tooltip_vis = "Άνοιγμα ορατότητας";
                                }
                                @endphp
                            
                            <td >
                                <form action="{{url("/filecollects/change_status/$one_filecollect->id")}}" method="post">
                                @csrf
                                <input name="asks_to" type="hidden" value="ch_vis_status">
                                <button type="submit" class="btn btn-secondary bi bi-binoculars" data-toggle="tooltip" title="{{$tooltip_vis}}" style="{{$opacity_vis}}" onclick="return confirm('Με την αλλαγή της ορατότητας, η φόρμα δε θα δέχεται υποβολές\n')"> </button>
                                </form>
                            </td>
                            <td >
                                <form action="{{url("/filecollects/change_status/$one_filecollect->id")}}" method="post">
                                @csrf
                                <input name="asks_to" type="hidden" value="ch_acc_status">
                                <button type="submit" class="btn btn-secondary bi bi-journal-arrow-down" style="{{$opacity_acc}}" data-toggle="tooltip" title="{{$tooltip_acc}}" {{$hidden_acc}}></button>
                                </form>
                            </td>
                            <td>
                                {{$one_filecollect->stakeholders->whereNotNull('file')->count()}}/{{$one_filecollect->stakeholders->count()}} 
                            </td>
                            <td>
                                <form action="{{url("/filecollects/$one_filecollect->id")}}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-danger bi bi-x-circle" onclick="return confirm('ΠΡΟΣΟΧΗ! ΘΑ ΔΙΑΓΡΑΦΟΥΝ ΟΛΑ τα στοιχεία της συλλογής καθώς ΚΑΙ ΤΑ ΑΡΧΕΙΑ που έχουν ανεβάσει οι ενδιαφερόμενοι')"> </button>
                                </form>
                            </td>
                        @endcan
                        
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="container py-5">
        <div class="container px-5">
        <nav class="navbar navbar-light bg-light">
            <form action="{{url("/filecollects")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                @csrf
                <input type="hidden" name="asks_to" value="insert">
                <div class="input-group">
                    <span class="input-group-text w-25"></span>
                    <span class="input-group-text"><strong>Εισαγωγή νέας Συλογής Αρχείου</strong></span>
                </div>
                <div class="input-group">
                    <span class="input-group-text w-25" id="basic-addon2">Ονομασία</span>
                    <input name="filecollect_name" type="text" class="form-control" placeholder="π.χ. Τμήμα Ένταξης, Εργαστήριο Πληροφορικής, κ.α." aria-label="filecollectname" aria-describedby="basic-addon2" required value="@isset($dberror){{$old_data['filecollect_name']}}@endisset"><br>
                </div>
                @can('chooseDepartment',App\Models\Filecollect::class)
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon4">Τμήμα</span>
                        <select name="department" class="form-select" aria-label="Default select example">
                            @foreach($all_departments as $department)
                            @php
                                $selected=null;
                                if($department->id == 5)
                                    $selected="selected";   
                            @endphp
                            <option {{$selected}} value="{{$department->id}}">{{$department->name}}</option>
                            @endforeach
                        </select>
                    </div>
                @endcan
                <div class="input-group">
                    <span class="w-25"></span>
                    <button type="submit" class="btn btn-primary m-2 bi bi-plus-circle"> Προσθήκη</button>
                    <a href="{{url("/filecollects")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                </div>
            </form>
        </nav>
        </div></div>
        
    </div>
</x-layout>