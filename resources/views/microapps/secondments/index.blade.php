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
        <script src="{{asset('secondments_allow_extra_files.js')}}"></script>
        <script>
            var allowExtraFilesURL = '{{ route("secondments.allow_extra_files", ["secondment" =>"mpla"]) }}'
        </script>
        
    @endpush
    @push('title')
        <title>Αποσπάσεις</title>
    @endpush
    @php
        //fetch microapp data
        $microapp = App\Models\Microapp::where('url', '/secondments')->first();
        $secondments = App\Models\microapps\Secondment::all();
    @endphp
    @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
    <div class="container">
        {{-- <form action="{{route("school_area.export_xlsx")}}" method="post">
            @csrf
            <button type="submit" class="btn btn-primary bi bi-filetype-xlsx"> Εξαγωγή σε Excel</button>
        </form> --}}
    </div>
    <div class="table-responsive py-2">
        <table  id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">Ονοματεπώνυμο</th>
                    <th id="search">Κλάδος</th>
                    <th id="search">Αίτηση Μετάθεσης</th>
                    <th id="search">Κατάσταση Αίτησης</th>
                    <th id="search">Ειδική Κατηγορία</th>
                    <th>Άνοιγμα για δικαιολογητικά</th>
                    <th>Σχόλια (Κριτήρια)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($secondments as $secondment)
                    <tr>
                        <td>{{$secondment->teacher->surname}} {{$secondment->teacher->name}}</td>
                        <td>{{$secondment->teacher->klados}}</td>
                        <td>{{$secondment->application_for_reposition==1?'ΝΑΙ':'οχι'}}</td>
                        @if($secondment->criteria_submitted == 1)  {{-- Κατάσταση Αίτησης --}}
                            @if($secondment->submitted == 1)
                                <td>ΟΡΙΣΤΙΚΟΠΟΙΗΜΕΝΗ (και Σχολεία)</td>
                            @else
                                <td>Οριστική Υποβολή Κριτηρίων</td>
                            @endif
                        @else
                            <td>Προσωρινή Αποθήκευση</td>
                        @endif
                        <td>{{$secondment->special_category==1?'ΝΑΙ':'οχι'}}</td>
                        <td>
                            @if($secondment->criteria_submitted == 1 && $secondment->extra_files_allowed == 0)
                                <input type="checkbox" class="secondment-extra-files-checkbox" data-secondment-id="{{ $secondment->id }}" >
                                {{-- <div class="check_td_{{$outing->id}}"> {{$text}}</div> --}}
                            @elseif($secondment->criteria_submitted == 1 && $secondment->extra_files_allowed == 1)
                                <input type="checkbox" class="secondment-extra-files-checkbox" data-secondment-id="{{ $secondment->id }}" checked >
                                {{-- <div class="check_td_{{$outing->id}}"> {{$text}}</div> --}}
                            @else
                                Προσωρινή Αποθήκευση
                            @endif
                        </td>
                        <td>{{$secondment->teacher->comments}}</td>
                    </tr>
                @endforeach   
            </tbody>  
        </table>    
    </div> <!-- table responsive closure -->
    
</x-layout>