<x-layout>
    @push('links')
        <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
        <style>
            .hide-button {
                display: none;
            }
        </style>
    @endpush
    @push('scripts')
        <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
        <script src="{{asset('datatable_init_outings.js')}}"></script>
        <script>
            var checkOutingUrl = '{{ route("outings.check", ["outing" =>"mpla"]) }}';
            var deleteOutingUrl = '{{ route("outings.destroy", ["outing" =>"mpla"]) }}';
            var countSectionsUrl ='{{ route("outings.count_sections", ["outing" =>"mpla"]) }}'
        </script>
        <script src="{{asset('check_outing.js')}}"></script>
        <script src="{{asset('delete_outing.js')}}"></script>
        <script src="{{asset('count_outing_sections.js')}}"></script>
    @endpush
    @push('title')
        <title>Εκδρομές</title> 
    @endpush  
    @php
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts;
        $outings = App\Models\microapps\Outing::orderBy('outing_date', 'desc')->get();
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
                    <th id="">Τμήματα (πλήθος εκδρομών)</th>
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
                    <tr id="outing-{{$outing->id}}"> 
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
                        <td>
                            <button id= "calc_button-{{$outing->id}}" class="bi bi-arrow-bar-down btn btn-primary outing-calcbox" data-outing-id="{{ $outing->id }}"> </button>
                            <div id="calc_td_{{$outing->id}}"> </div>
                            <button id= "hide_button-{{$outing->id}}" class="bi bi-arrow-bar-up btn btn-primary outing-hidebox hide-button" data-outing-id="{{ $outing->id }}" > </button>
                        </td>
                        <td>{{$outing->destination}}</td>
                        <td>{{$outing->updated_at}}</td>
                        <td >
                            <button class="bi bi-x-circle btn btn-danger outing-delbox" onclick="return confirm('Επιβεβαίωση διαγραφής εκδρομής;')" data-outing-id="{{ $outing->id }}">
                        </td>
                    </tr> 
                @endforeach   
            </tbody>  
            </table>  
        </div>
</x-layout>