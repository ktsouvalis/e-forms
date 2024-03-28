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
                    <td><a href="{{url("/microapps/tickets/$ticket->id/edit#bottom")}}">{{$ticket->id}}</a></td>
                    <td>{{$ticket->subject}}</td> 
                    <td>{{$ticket->school->name}}</td>
                    @if($ticket->solved)
                        <td ><a style="color:green" href="{{url("/microapps/tickets/$ticket->id/edit#bottom")}}">Έχει επιλυθεί</a></td>
                    @else
                        <td ><a style="color:red" href="{{url("/microapps/tickets/$ticket->id/edit#bottom")}}">Προς επίλυση</a></td>
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
    <div>
        <nav class="navbar navbar-light bg-light">
            <form action="{{url("/microapps/tickets/admin_create_ticket")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                @csrf
                <div class="input-group">
                    <span class="input-group-text w-25"></span>
                    <span class="input-group-text w-75"><strong>Καταχώρηση νέου αιτήματος Τεχνικής Υποστήριξης</strong></span>
                </div>
                    
                <div class="input-group my-2">
                    <span class="input-group-text w-25 text-wrap">Σχολείο:</span>
                    <input name="school" id="school" type="text" class="form-control" placeholder="Επιλέξτε Σχολείο" aria-label="Σχολείο" aria-describedby="basic-addon2" required list="schoolOptions">
                    <datalist id="schoolOptions">
                        @foreach(App\Models\School::all() as $school)
                            <option value="{{ $school->name }}">{{ $school->name }}</option>
                        @endforeach
                    </datalist>
                </div>
                
                <div class="input-group my-2">
                    <span class="input-group-text w-25 text-wrap">Θέμα:</span>
                    <input name="subject" id="subject" type="text" class="form-control" placeholder="Θέμα" aria-label="Θέμα" aria-describedby="basic-addon2" required>
                </div>
                <div class="input-group">
                    <div class="input-group justify-content-center">
                    <textarea name="comments" id="comments" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary m-2"><div class="fa-solid fa-headset"></div> Υποβολή</button>
                </div>
            </form>
        </nav>
    </div>
</x-layout>