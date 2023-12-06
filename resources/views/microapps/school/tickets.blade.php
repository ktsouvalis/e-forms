<x-layout_school>
    @push('title')
        <title>Τεχνική Υποστήριξη</title>
    @endpush
    @push('links')
        <link href="../summernote-0.8.18-dist/summernote-lite.min.css" rel="stylesheet">
    @endpush
    @push('scripts')
    <script src="../summernote-0.8.18-dist/summernote-lite.min.js"></script>
    <script>
       $(document).ready(function () {
        $('#comments').summernote({
            height: 200, // Adjust the height as needed
            width:600,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['list', ['ul', 'ol']],
                ['link', ['link']]
            ],
            placeholder: 'Περιγραφή...',
            lang: 'el-GR' // Set language to Greek
        });
    });
    </script>
    @endpush
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $accepts = App\Models\Microapp::where('url', '/'.$appname)->first()->accepts; //fetch microapp 'accepts' field
        $school_tickets = App\Models\School::find($school->id)->tickets;
    @endphp
    <div class="container">
        <div class="container px-5">   
                <nav class="navbar navbar-light bg-light">
                    <form action="{{url("/create_ticket")}}" method="post" enctype="multipart/form-data" class="container-fluid">
                        @csrf
                        <div class="input-group">
                            <span class="input-group-text w-25"></span>
                            <span class="input-group-text w-75"><strong>Καταχώρηση νέου αιτήματος Τεχνικής Υποστήριξης</strong></span>
                        </div>
                        
                        <div class="input-group my-2">
                            
                            <span class="input-group-text w-25 text-wrap">Θέμα:</span>
                            <input name="subject" id="subject" type="text" class="form-control" placeholder="Θέμα" aria-label="Θέμα" aria-describedby="basic-addon2" required><br>
                        </div>
                        
                        <div class="input-group">
                            <div class="input-group justify-content-center">
                            <textarea name="comments" id="comments" class="form-control" required></textarea>
                            </div>
                        </div>
                        @if(!$accepts)
                            <div class="col-sm-2 btn btn-warning bi bi-bricks rounded text-light" style="text-align:center;">
                                Η εφαρμογή δε δέχεται υποβολές
                            </div>
                        @else
                            <div class="input-group">
                                <span class="w-25"></span>
                                <button type="submit" class="btn btn-primary m-2"><div class="fa-solid fa-headset"></div> Υποβολή</button>
                                <div><small>Όταν δημιουργηθεί το δελτίο, μπορείτε να προσθέστε συνημμένα</small></div>
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