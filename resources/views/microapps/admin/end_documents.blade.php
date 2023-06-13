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
        $end_documents_informed_records = App\Models\mEndDocument::all();
        // dd(App\Models\Microapp::where('url', '/'.$appname));
        $end_documents_all = App\Models\Microapp::where('url', '/'.$appname)->first()->stakeholders;
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
                        <th id="search">ΑΦΜ εκπαιδευτικού</th>
                        <th id="search">Ονοματεπώνυμο Εκπαιδευτικού</th>
                        <th id="search">Δήλωση Παραλαβής </th>
                        <th id="search">Ημερομηνία Δήλωσης Παραλαβής</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($end_documents_all as $edteacher)
                @php
                    if($edteacher->stakeholder->end_document){
                        $date = DateTime::createFromFormat('Y-m-d H:i:s', $edteacher->stakeholder->end_document->updated_at);
                        $timestamp = $date->getTimestamp();
                    }
                @endphp
                <tr>
                    <td>{{$edteacher->stakeholder->afm}}</td>
                    <td>{{$edteacher->stakeholder->surname}} {{$edteacher->stakeholder->name}}</td>
                    @if($edteacher->stakeholder->end_document)
                        <td>Ενημέρωσε ότι παρέλαβε</td>
                        <td>{{date('d/m/Y H:i:s', $timestamp)}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                    @endif
                </tr> 
                @endforeach   
                </tbody>  
            </table> 
        </div>
    </div>
</x-layout>