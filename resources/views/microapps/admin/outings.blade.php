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
                $('.outing-checkbox').on('change', function() {
                    
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
                        url: '/check_outing/'+outingId,
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
        {{-- <script>
            $(document).ready(function () {
                // Setup - add a text input for inclusion and exclusion to each header cell
                $('#dataTable thead tr #search').each(function () {
                    var title = $(this).text();
                    $(this).html(`
                        <div class="vstack gap-1">
                            <input type="text" class="include-search" style=" font-size:small;" placeholder="${title} +" />
                            <input type="text" class="exclude-search" style=" font-size:small;" placeholder="${title} - " />
                        </div>
                    `);
                });

                // DataTable
                var table = $('#dataTable').DataTable({
                    "order": [],
                    lengthMenu: [10, 25, 50, 100, -1], // Add -1 for "All"
                    pageLength: 10, // Set the initial page length
                    initComplete: function () {
                        // Apply the search
                        this.api().columns().every(function () {
                            var that = this;
                            var includeColumn = $('input.include-search', this.header());
                            var excludeColumn = $('input.exclude-search', this.header());

                            includeColumn.on('keyup change clear', function () {
                                var includeValue = this.value;
                                var excludeValue = excludeColumn.val();
                                var regex;

                                if (includeValue) {
                                    if (excludeValue) {
                                        regex = `^(?=.*${includeValue})(?!.*${excludeValue})`;
                                    } else {
                                        regex = `.*${includeValue}`;
                                    }
                                } else {
                                    regex = excludeValue ? `^(?!.*${excludeValue}).*` : '';
                                }

                                that.search(regex, true, false).draw();
                            }).on('click', function (e) {
                                e.stopPropagation();
                                column.search($(this).val()).draw();
                            });

                            excludeColumn.on('keyup change clear', function () {
                                var excludeValue = this.value;
                                var includeValue = includeColumn.val();
                                var regex;

                                if (excludeValue) {
                                    if (includeValue) {
                                        regex = `^(?=.*${includeValue})(?!.*${excludeValue})`;
                                    } else {
                                        regex = `^(?!.*${excludeValue}).*`;
                                    }
                                } else {
                                    regex = includeValue ? `.*${includeValue}` : '';
                                }

                                that.search(regex, true, false).draw();
                            }).on('click', function (e) {
                                e.stopPropagation();
                                column.search($(this).val()).draw();
                            });
                        });
                    },
                });
            });
        </script> --}}
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