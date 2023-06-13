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
        $all_day_records = App\Models\mAllDay::all();
        $accepts = App\Models\Microapp::where('url', '/'.$appname)->first()->accepts;
    @endphp
    <div class="container">
        @if(!$accepts)
        <div class="col-sm-2 btn btn-warning bi bi-bricks rounded text-light" style="text-align:center;">
            Η εφαρμογή δε δέχεται υποβολές
        </div>
        @endif
        <div class="table-responsive">
            <table  id="dataTable" class="display table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th id="search">Κωδικός σχολείου</th>
                        <th id="search">Ονομασία Σχολείου</th>
                        <th id="search">Απάντηση 1</th>
                        <th id="search">Απάντηση 2</th>
                        <th id="search">Τελευταία ενημέρωση</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($all_day_records as $adschool)
                @php
                    $date = DateTime::createFromFormat('Y-m-d H:i:s', $adschool->updated_at);
                    $timestamp = $date->getTimestamp();

                @endphp
                <tr>
                    <td>{{$adschool->school->code}}</td>
                    <td>{{$adschool->school->name}}</td>
                    <td>{{$adschool->first_number}}</td>
                    <td>{{$adschool->second_number}}</td>
                    <td>{{date('d/m/Y H:i:s', $timestamp)}}</td>
                </tr> 
                @endforeach   
                </tbody>  
            </table> 
        </div>
    </div>
</x-layout>