<x-layout>
    @push('links')
        <link href="{{ asset('DataTables-1.13.4/css/dataTables.bootstrap5.css') }}" rel="stylesheet"/>
        <link href="{{ asset('Responsive-2.4.1/css/responsive.bootstrap5.css') }}" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="{{ asset('DataTables-1.13.4/js/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('DataTables-1.13.4/js/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/dataTables.responsive.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/responsive.bootstrap5.js') }}"></script>
        <script src="{{ asset('datatable_init.js') }}"></script>
    @endpush
    @push('title')
        <title>Θρανία</title>
    @endpush
    @php
        //fetch microapp data
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
    @endphp
    @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}

    <div class="table-responsive py-2">
        <table  id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">Κωδικός Σχολείου</th>
                    <th id="search">Ονομασία Σχολείου</th>
                    <th id="">Αριθμός Θρανίων</th>
                    <th id="">Σχόλια</th>
                    <th id="">Ημερομηνία Υποβολής</th>
                </tr>
            </thead>
            <tbody>
            
                @foreach(App\Models\microapps\Desks::all() as $desks)
                    <tr>
                        <td>{{$desks->school->code}}</td> 
                        <td>{{$desks->school->name}} </td>
                        <td>{{$desks->number}}</td> {{--$school(MicroappStakeholder)->stakeholder(belongsTo: School)->fruit(hasOne: Fruit)->no_of_students --}}
                        <td class="text-wrap"  style="width: 12rem;">{{$desks->comments}}</td>
                        <td>{{$desks->updated_at}}</td>
                    </tr> 
                @endforeach   
            </tbody>  
        </table>    
    </div> <!-- table responsive closure -->
</x-layout>