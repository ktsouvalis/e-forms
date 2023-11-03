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
        <title>Εκδρομές</title> 
    @endpush  
    @php
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        $outings = App\Models\microapps\Outing::all();
        
    @endphp
    
    <div class="container">
            @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
        
            <div class="table-responsive py-2">
                <table  id="dataTable" class="small display table table-sm table-striped table-hover text-center">
                <thead>
                    <tr>
                        <th id="search">Κωδικός</th>
                        <th id="search">Σχολείο</th>
                        <th id="search">Έλεγχος</th>
                        <th id="search">Τύπος</th>
                        <th id="">Ημερομηνία</th>
                        <th id="">Τμήματα (πλήθος εκδρομών)</th>
                        <th id="">Δράση</th>
                        <th id="">Πρακτικό</th>
                        <th id="">Αρχείο</th>
                        <th>Ημερομηνία Υποβολής</th>
                        <th>Διαγραφή εκδρομής</th>
                    </tr>
                </thead>
                <tbody>
                
                    @foreach($outings as $outing)
                        @php
                            $my_date = Illuminate\Support\Carbon::parse($outing->outing_date); 
                        @endphp
                        <tr> 
                            <td>{{$outing->id}}</td>
                            <td>{{$outing->school->name}}</td> 
                            @if(!$outing->checked)
                            <td>
                                <form action="{{url("/check_outing/$outing->id")}}" method="post">
                                    @csrf
                                    <button class="bi bi-check btn btn-primary" type="submit" style="color:white"> </button>
                                </form>
                            </td>
                            
                            @else
                            <td> <div class="bi bi-check-circle btn btn-success"  style="color:white"> Ελέγχθηκε </div> </td>
                            @endif  
                            <td>{{$outing->type->description}}</td> 
                            <td>{{$my_date->day}}/{{$my_date->month}}/{{$my_date->year}} </td>
                            <td>
                                @foreach($outing->sections as $section)
                                    {{$section->section->name}} (<b>{{$section->section->outings->count()}}</b>)<br>
                                @endforeach
                            </td>
                            <td>{{$outing->destination}}</td>
                            <td>{{$outing->record}} </td>
                            <td>
                                <div class="hstack gap-2">
                                
                                <form action="{{url("/download_record/$outing->id")}}" method="post">
                                    @csrf
                                    <button class="btn btn-secondary bi bi-box-arrow-down"> </button>
                                </form>
                                {{$outing->file}}
                                </div>
                            </td>
                    
                             
                            <td>{{$outing->updated_at}}</td>
                            <td>
                                <form action="{{url("/delete_outing/$outing->id")}}" method="post">
                                    @csrf
                                    <button class="bi bi-x-circle btn btn-danger" type="submit" onclick="return confirm('Επιβεβαίωση διαγραφής εκδρομής;')"> </button>
                                </form>
                            </td>
                            
                        </tr> 
                    @endforeach   
                </tbody>  
                </table>    
            </div>
    </div>

</x-layout>