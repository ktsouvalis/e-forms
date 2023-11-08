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
        <script>
            $(document).ready(function() {
                $('body').on('change', '.outing-checkbox', function() {
                    
                    const outingId = $(this).data('outing-id');
                    const isChecked = $(this).is(':checked');
                    // Get the CSRF token from the meta tag
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    $.ajax({
                        url: '../check_outing/'+outingId,
                        type: 'POST',
                        data: {
                            // _method: 'PATCH', // Laravel uses PATCH for updates
                            checked: isChecked
                        },
                        success: function(response) {
                            // Handle the response here, update the page as needed
                            $('#successMessage').text(response.message).show();
                            if(isChecked){
                                $('.check_td_'+outingId).html('Ελέγχθηκε')
                            }
                            else{
                                $('.check_td_'+outingId).html('Προς έλεγχο')
                            }

                        },
                        error: function(error) {
                            // Handle errors
                            console.log("An error occurred: " + error);
                        }
                    });
                });
            });
        </script>
    @endpush
    @push('title')
        <title>Εκδρομές</title> 
    @endpush  
    @php
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field 
        $outings = App\Models\microapps\Outing::orderBy('outing_date','desc')->get();
    @endphp
    {{-- <div class="container"> --}}
            @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
        
            <div class="table-responsive py-2">
                <table  id="dataTable" class="small display table table-sm table-striped table-hover text-center">
                <thead>
                    <tr>
                        <th id="search">Σχολείο</th>
                        <th id="search">Ημερομηνία (έτος/μήνας/μέρα)</th>
                        <th id="">Αρχείο</th>
                        <th id="search">Τύπος</th>
                        <th id="search">Έλεγχος</th>
                        <th id="">Τμήματα (πλήθος εκδρομών)</th>
                        <th id="">Δράση</th>
                        {{-- <th id="">Πρακτικό</th> --}}
                        <th>Ημερομηνία Υποβολής</th>
                        <th>Διαγραφή εκδρομής</th>
                        <th id="search">Κωδικός</th>
                    </tr>
                </thead>
                <tbody>
                
                    @foreach($outings as $outing)
                        @php
                            $my_date = Illuminate\Support\Carbon::parse($outing->outing_date);
                            $day=$my_date->day;
                            $month=$my_date->month;
                            $year=$my_date->year;
                            if($day<10){
                                $day='0'.$day;
                            }
                            if($month<10){
                                $month='0'.$month;
                            }
                        @endphp
                        <tr> 
                            <td>{{$outing->school->name}}</td>
                            <td>{{$year}}/{{$month}}/{{$day}} </td>
                            <td>
                                <div class="hstack gap-2">
                                
                                <form action="{{url("/download_record/$outing->id")}}" method="post">
                                    @csrf
                                    <button class="btn btn-secondary bi bi-box-arrow-down"> </button>
                                </form>
                                {{$outing->file}}
                                </div>
                            </td>
                            <td>{{$outing->type->description}}</td>
                            @php
                                $text = $outing->checked ? 'Ελέγχθηκε' : 'Προς έλεγχο';
                            @endphp
                            <td >
                                <input type="checkbox" class="outing-checkbox" data-outing-id="{{ $outing->id }}" {{ $outing->checked ? 'checked' : '' }}>
                                <div class="check_td_{{$outing->id}}"> {{$text}}</div>
                            </td>
                            <td>
                                @foreach($outing->sections as $section)
                                    {{$section->section->name}} (<b>{{$section->section->outings->count()}}</b>)<br>
                                @endforeach
                            </td>
                            <td>{{$outing->destination}}</td>
                            {{-- <td>{{$outing->record}} </td> --}}
                            <td>{{$outing->updated_at}}</td>
                            <td>
                                <form action="{{url("/delete_outing/$outing->id")}}" method="post">
                                    @csrf
                                    <button class="bi bi-x-circle btn btn-danger" type="submit" onclick="return confirm('Επιβεβαίωση διαγραφής εκδρομής;')"> </button>
                                </form>
                            </td>
                            <td>{{$outing->id}}</td>
                            
                        </tr> 
                    @endforeach   
                </tbody>  
                </table>    
            </div>
    {{-- </div> --}}

    @push('scripts')
    
    @endpush

</x-layout>