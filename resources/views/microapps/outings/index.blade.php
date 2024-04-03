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
        <script src="{{asset('datatable_init_outings.js')}}"></script>
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
                        url: '/outings/check/'+outingId,
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
        // $outings = App\Models\microapps\Outing::where('checked', 0)
        //     ->orWhere('outing_date', '>=', \Carbon\Carbon::today())
        //     ->orderBy('checked', 'asc')
        //     ->orderBy('outing_date', 'desc')
        //     ->get();
        $outings = App\Models\microapps\Outing::all();
    @endphp
        @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
    
        <div class="table-responsive py-2">
            <table  id="dataTable" class="small display table table-sm table-hover text-center text-wrap">
            <thead>
                <tr>
                    <th id="search">Σχολείο</th>
                    <th id="search">Ημερομηνία (έτος/μήνας/μέρα)</th>
                    <th id="">Αρχείο</th>
                    <th id="search">Τύπος</th>
                    <th id="search">Έλεγχος</th>
                    {{-- <th id="">Τμήματα (πλήθος εκδρομών)</th> --}}
                    <th id="search">Δράση</th>
                    <th id="search">Ημερομηνία Υποβολής</th>
                    <th>Διαγραφή εκδρομής</th>
                </tr>
            </thead>
            <tbody>
            
                @foreach($outings as $outing)
                    @php
                        $my_date = Illuminate\Support\Carbon::parse($outing->outing_date)->isoFormat('YYYY-MM-DD');
                    @endphp
                    <tr> 
                        <td>{{$outing->school->name}}</td>
                        <td>{{$my_date}}</td>
                        <td>
                            <div class="vstack gap-2">
                            
                            <form action="{{url("/outings/download_file/$outing->id")}}" method="get">
                                @csrf
                                <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> </button>
                            </form>
                            {{$outing->school->telephone}}
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
                        {{-- <td>
                            @foreach($outing->sections as $section)
                                {{$section->section->name}} (<b>{{$section->section->outings->count()}}</b>)<br>
                            @endforeach
                        </td> --}}
                        <td>{{$outing->destination}}</td>
                        {{-- <td>{{$outing->record}} </td> --}}
                        <td>{{$outing->updated_at}}</td>
                        <td>
                            <form action="{{url("/outings/$outing->id")}}" method="post">
                                @method('DELETE')
                                @csrf
                                <button class="bi bi-x-circle btn btn-danger" type="submit" onclick="return confirm('Επιβεβαίωση διαγραφής εκδρομής;')"> </button>
                            </form>
                        </td>
                    </tr> 
                @endforeach   
            </tbody>  
            </table>  
            {{-- {{$outings->links()}}   --}}
        </div> <!-- table responsive closure -->
</x-layout>