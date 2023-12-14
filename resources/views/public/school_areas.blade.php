
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @stack('title')
    <link rel="stylesheet" href="{{url('/bootstrap/css/bootstrap.css')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
    <link rel="icon" href="{!! asset(url('/favicon/favicon.ico')) !!}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{url('/favicon/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{url("/favicon/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{url("/favicon/favicon-16x16.png")}}">
    <link rel="manifest" href="{{url('/favicon/site.webmanifest')}}">
    <link rel="mask-icon" href="{{url('/favicon/safari-pinned-tab.svg')}}" color="#5bbad5">
    <link href="{{url('/fontawesome-free-6.4.2-web/css/fontawesome.css')}}" rel="stylesheet">
    <link href="{{url('/fontawesome-free-6.4.2-web/css/brands.css')}}" rel="stylesheet">
    <link href="{{url('/fontawesome-free-6.4.2-web/css/solid.css')}}" rel="stylesheet">
    <link href="{{url('/fontawesome-free-6.4.2-web/css/regular.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    @stack('links')
  </head>
  <body>
  
  
  <div class="mx-5"> <!-- Custom container --> 
  @include('components/messages')    <!-- Show Notifications --> 

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
        <title>Όρια Σχολείων Π.Ε. Αχαΐας</title>
    @endpush
    @php
        //fetch microapp data
        $school_areas = App\Models\microapps\SchoolArea::orderBy('school_id', 'asc')->get();
    @endphp
    <h2>Γεωγραφικά Όρια εγγραφής μαθητών στις Σχολικές Μονάδες της Διεύθυνσης Π.Ε. Αχαΐας</h2>
    <h5>Μπορείτε να κάνετε αναζήτηση σε οποιοδήποτε πεδίο, αναζήτηση με το όνομα του Σχολείου, το Δήμο και τις οδούς ή περιοχές που αναφέρονται στα όρια των Σχολικών Μονάδων</h5>
    <div class="table-responsive py-2">
        <table  id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">Ονομασία Σχολείου</th>
                    <th id="search">Δήμος</th>
                    <th id="search">Γεωγραφικά Όρια / οδός /περιοχή</th>
                    <th id="">Παρατηρήσεις</th>
                    <th id="">Τελευταία ενημέρωση</th>
                    <th id="search">Κωδικός Σχολείου</th>
                </tr>
            </thead>
            <tbody>
            
                @foreach($school_areas as $school)
                {{-- $school is MicroappStakeholder object so method ->stakeholder fetches School object attributes) --}}
                    @php
                        $school_data = App\Models\School::where('id', $school->school_id)->first();
                        if($school->updated_at != ""){ // if school has a school_area record, get timestamp
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $school->updated_at);
                            $timestamp = $date->getTimestamp();
                        }else{
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', '2021-03-01 10:00:00');
                            $timestamp = $date->getTimestamp();
                        }
                    @endphp
                    <tr>
                        <td>{{$school_data->name}} </td>
                        <td>{{$school_data->municipality->name}} </td>
                        @if($school->data) {{-- if school has a school_area record, get record data --}}
                            <td>
                                @php
                                if($school->data != ""){
                                    $data = json_decode($school->data);
                                    foreach($data as $one_record){
                                        echo $one_record->street;
                                        if($one_record->comment != "")
                                            echo " (".$one_record->comment.")";
                                        echo "<br>";
                                    }
                                }
                                
                                @endphp
                            </td> {{--$school(MicroappStakeholder)->stakeholder(belongsTo: School)->school_area(hasOne: school_area)->data --}}
                            <td class="text-wrap"  style="width: 12rem;">{{$school->comments}}</td>
                            <td>{{date('d/m/Y H:i:s', $timestamp)}}</td>
                        @else
                            <td><em><small>Δεν έχουν δηλωθεί</small></em></td>
                            <td>-</td>
                            <td>-</td>
                        @endif
                        <td>{{$school_data->code}}</td> 
                    </tr> 
                @endforeach   
            </tbody>  
        </table>    
    </div> <!-- table responsive closure -->

</div> <!-- End of custom container --> 

<!-- footer begins -->
<footer class="border-top text-center small text-muted py-3">
  <p class="m-0">Copyright &copy; {{Illuminate\Support\Carbon::now()->year}} <a href="{{url("/")}}" class="text-muted">e-forms</a>. Διεύθυνση Π.Ε. Αχαΐας - Τμήμα Πληροφορικής & Νέων Τεχνολογιών - Ηλεκτρονικές Υπηρεσίες.</p>
</footer>
<script src="{{url('/bootstrap/js/bootstrap.js')}}"></script>
<script
  src="https://code.jquery.com/jquery-3.6.4.min.js"
  integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
  crossorigin="anonymous">
</script>
@stack('scripts')
</div> <!-- container closing -->
  <div class="d-flex justify-content-center"><p class="h3" style="color:black"> {{env('APP_NAME')}}</p></div>
  @stack('copy_script')
</body>
</html>
