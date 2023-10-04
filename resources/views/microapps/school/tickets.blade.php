<x-layout_school>
    @push('title')
        <title>Τεχνική Υποστήριξη</title>
    @endpush
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $accepts = App\Models\Microapp::where('url', '/'.$appname)->first()->accepts; //fetch microapp 'accepts' field
        $school_tickets = App\Models\School::find($school->id)->tickets;
        // dd(Auth::guard('school')->user()->tickets);
        //  dd(Auth::guard('school')->user() instanceof \App\Models\School)
    @endphp

    
    <div class="container">
        <div class="container px-5">   
                <nav class="navbar navbar-light bg-light">
                    <form action="{{url("/create_ticket/$school->id")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <div class="input-group">
                            <span class="input-group-text w-25"></span>
                            <span class="input-group-text w-75"><strong>Καταχώρηση νέου αιτήματος Τεχνικής Υποστήριξης</strong></span>
                        </div>
                        
                        <div class="input-group">
                            
                            <span class="input-group-text w-25 text-wrap">Θέμα:</span>
                            <input name="subject" id="subject" type="text" class="form-control" placeholder="Σύντομη Περιγραφή" aria-label="Θέμα" aria-describedby="basic-addon2" required><br>
                        </div>
                        
                        <div class="input-group">
                            <span class="input-group-text w-25 text-wrap">Περιγραφή</span>
                            <textarea name="comments" id="comments" class="form-control" cols="30" rows="5" style="resize: none;" placeholder="Αναλυτική Περιγραφή" ></textarea>
                        </div>
                        @if(!$accepts)
                            <div class="col-sm-2 btn btn-warning bi bi-bricks rounded text-light" style="text-align:center;">
                                Η εφαρμογή δε δέχεται υποβολές
                            </div>
                        @else
                            <div class="input-group">
                                <span class="w-25"></span>
                                <button type="submit" class="btn btn-primary m-2"><div class="fa-solid fa-headset"></div> Υποβολή</button>
                                <a href="{{url("/school_app/$appname")}}" class="btn btn-outline-secondary m-2">Ακύρωση</a>
                            </div>
                        @endif
                    </form>
                </nav>
            </div>
            <div class="container px-5">
                <div class="table-responsive py-2">
                <table  id="dataTable" class="display table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th id="search">Κωδικός</th>
                        <th id="search">Θέμα</th>
                        <th id="search">Ημερομηνία Δημιουργίας</th>
                        <th id="search">Τελευταία ενημέρωση</th>
                        <th id="search">Κατάσταση</th>
                    </tr>
                </thead>
                <tbody>
                
                    @foreach($school_tickets as $ticket)
                    {{-- $school is MicroappStakeholder object so method ->stakeholder fetches School object attributes) --}}
                        @php
                            // if($school->stakeholder->fruit){ // if school has a fruit record, get timestamp
                            //     $date = DateTime::createFromFormat('Y-m-d H:i:s', $school->stakeholder->fruit->updated_at);
                            //     $timestamp = $date->getTimestamp();
                            // }
                        @endphp
                        <tr>
                            <td><a href="{{url("/ticket_profile/$ticket->id")}}">{{$ticket->id}}</a></td>
                            <td>{{$ticket->subject}}</td> 
                            <td>{{$ticket->created_at}} </td>
                            <td>{{$ticket->updated_at}} </td>
                            @if($ticket->solved)
                                <td ><a style="color:green" href="{{url("/ticket_profile/$ticket->id")}}">Έχει επιλυθεί</a></td>
                            @else
                                <td ><a style="color:red" href="{{url("/ticket_profile/$ticket->id")}}">Προς επίλυση</a></td>
                            @endif
                        </tr> 
                    @endforeach   
                </tbody>  
                </table>    
            </div>
        </div>
    </div>
</x-layout_school>