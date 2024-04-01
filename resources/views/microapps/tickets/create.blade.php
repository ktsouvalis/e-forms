<x-layout_school>
    @push('title')
        <title>Τεχνική Υποστήριξη</title>
    @endpush
    @push('links')
        <link href="{{asset("summernote-0.8.18-dist/summernote-lite.min.css")}}" rel="stylesheet">
    @endpush
    @push('scripts')
        <script src="{{asset("summernote-0.8.18-dist/summernote-lite.min.js")}}"></script>
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
        $school_tickets = App\Models\School::find($school->id)->tickets->sortByDesc('created_at');
    @endphp
    <div class="container">
            <div class="container px-5">
                <div class="table-responsive py-2">
                <table  id="" class="display table table-sm table-striped">
                {{-- <thead> --}}
                    <tr>
                        <th>Κωδικός</th>
                        <th>Θέμα</th>
                        <th>Ημερομηνία Δημιουργίας</th>
                        <th>Τελευταία ενημέρωση</th>
                        <th>Κατάσταση</th>
                    </tr>
                {{-- </thead> --}}
                {{-- <tbody> --}}
                
                    @foreach($school_tickets as $ticket)
                        @php
                            $maxPostUpdate = $ticket->posts->isNotEmpty() ? $ticket->posts->max('updated_at') : null;
                            $text = max($ticket->updated_at, $maxPostUpdate);
                        @endphp
                        <tr>
                            <td><a href="{{url("/microapps/tickets/$ticket->id/edit#bottom")}}">{{$ticket->id}}</a></td>
                            <td>{{$ticket->subject}}</td> 
                            <td>{{$ticket->created_at}} </td>
                            <td>{{$text}} </td>
                            @if($ticket->solved)
                                <td ><a style="color:green" href="{{url("/microapps/tickets/$ticket->id/edit#bottom")}}">Έχει επιλυθεί</a></td>
                            @else
                                <td ><a style="color:red" href="{{url("/microapps/tickets/$ticket->id/edit#bottom")}}">Προς επίλυση</a></td>
                            @endif
                        </tr> 
                    @endforeach   
                {{-- </tbody>   --}}
                </table>    
            </div>
        </div>
        <hr>
        <div class="container px-5">   
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/microapps/tickets")}}" method="post" enctype="multipart/form-data" class="container-fluid">
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
                        <div class='alert alert-warning text-center my-2'>
                           <strong> <i class="bi bi-bricks"> </i> Η εφαρμογή δε δέχεται υποβολές</strong>
                        </div>
                    @else
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary m-2"><div class="fa-solid fa-headset"></div> Υποβολή</button>
                            <div><small>Όταν δημιουργηθεί το δελτίο, μπορείτε να προσθέστε συνημμένα</small></div>
                        </div>
                    @endif
                </form>
            </nav>
    </div>
    </div>
</x-layout_school>