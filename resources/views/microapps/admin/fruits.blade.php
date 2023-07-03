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
    @endpush

    @php
        //fetch microapp data
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();

        //fetch all stakeholders of the microapp to show them even if they have not submit some answer. $fruits_schools is MicroappStakeholder object
        $fruits_schools = $microapp->stakeholders; 

    @endphp
    <div class="container">
        @if(!$microapp->accepts)
        <div class="col-sm-2 btn btn-warning bi bi-bricks rounded text-light" style="text-align:center;">
            Η εφαρμογή δε δέχεται υποβολές
        </div>
        @endif
        <div class="table-responsive">
            <table  id="dataTable" class="display table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th id="search">Κωδικός Σχολείου</th>
                        <th id="search">Ονομασία Σχολείου</th>
                        <th id="search">Αριθμός Μαθητών</th>
                        <th id="search">Αριθμός Ουκρανών Μαθητών</th>
                        <th id="search">Σχόλια</th>
                        <th id="search">Ημερομηνία Υποβολής</th>
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
        <form action="{{url("/send_to_those_whocans_without_answer/microapp/$microapp->id")}}" method="post">
            @csrf
            <button class="btn btn-warning bi bi-eyeglasses"> Αποστολή υπενθύμισης σε όσους δεν έχουν υποβάλλει απάντηση</button>
        </form>
        </div>
    </div>
</x-layout>