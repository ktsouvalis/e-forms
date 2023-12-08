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
    @php
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; //fetch microapp 'accepts' field
        $tickets = App\Models\microapps\Ticket::orderBy('created_at', 'desc')->get();
        
    @endphp
    @push('title')
        <title>{{$microapp->name}}</title>
    @endpush    
    @include('microapps.microapps_admin_before') {{-- Visibility and acceptability buttons and messages --}}
    <div class="table-responsive py-2">
        <table  id="dataTable" class="display table table-sm table-striped table-hover">
        <thead>
            <tr>
                <th id="search">Κωδικός</th>
                <th id="search">Θέμα</th>
                <th id="search">Σχολείο</th>
                <th id="search">Κατάσταση</th>
                <th id="">Ημερομηνία Δημιουργίας</th>
                <th id="">Τελευταία ενημέρωση</th>
                <th id="search">Επίσκεψη</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
            @php
                $maxPostUpdate = $ticket->posts->isNotEmpty() ? $ticket->posts->max('updated_at') : null;
                $text = max($ticket->updated_at, $maxPostUpdate);
            @endphp
                <tr> 
                    <td><a href="{{url("/ticket_profile/$ticket->id#bottom")}}">{{$ticket->id}}</a></td>
                    <td>{{$ticket->subject}}</td> 
                    <td>{{$ticket->school->name}}</td>
                    @if($ticket->solved)
                        <td ><a style="color:green" href="{{url("/ticket_profile/$ticket->id#bottom")}}">Έχει επιλυθεί</a></td>
                    @else
                        <td ><a style="color:red" href="{{url("/ticket_profile/$ticket->id#bottom")}}">Προς επίλυση</a></td>
                    @endif
                    <td>{{$ticket->created_at}} </td>
                    <td>{{$text}} </td>
                    @if($ticket->needed_visit)
                        <td class="text-center"><strong>Ναι</strong></td>
                    @else
                        <td class="text-center">Όχι</td>
                    @endif
                </tr> 
            @endforeach   
        </tbody>  
        </table>    
    </div> <!-- table responsive closure -->
</x-layout>