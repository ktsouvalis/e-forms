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
        <title>Διανομή Φρούτων</title>
    @endpush
    @php
        //fetch microapp data
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $fruits_schools = $microapp->stakeholders; 
    @endphp
    @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}

    <div class="table-responsive py-2">
        <table  id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">Κωδικός Σχολείου</th>
                    <th id="search">Ονομασία Σχολείου</th>
                    <th id="">Αριθμός Μαθητών</th>
                    <th id="">Αριθμός Ουκρανών Μαθητών</th>
                    <th id="">Σχόλια</th>
                    <th id="">Ημερομηνία Υποβολής</th>
                </tr>
            </thead>
            <tbody>
            
                @foreach($fruits_schools as $school)
                {{-- $school is MicroappStakeholder object so method ->stakeholder fetches School object attributes) --}}
                    @php
                        if($school->stakeholder->fruit){ // if school has a fruit record, get timestamp
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $school->stakeholder->fruit->updated_at);
                            $timestamp = $date->getTimestamp();
                        }
                    @endphp
                    <tr>
                        <td>{{$school->stakeholder->code}}</td> 
                        <td>{{$school->stakeholder->name}} </td>
                        @if($school->stakeholder->fruit) {{-- if school has a fruit record, get record data --}}
                            <td>{{$school->stakeholder->fruit->no_of_students}}</td> {{--$school(MicroappStakeholder)->stakeholder(belongsTo: School)->fruit(hasOne: Fruit)->no_of_students --}}
                            <td>{{$school->stakeholder->fruit->no_of_ukr_students}}</td>
                            <td class="text-wrap"  style="width: 12rem;">{{$school->stakeholder->fruit->comments}}</td>
                            <td>{{date('d/m/Y H:i:s', $timestamp)}}</td>
                        @else
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
</x-layout>