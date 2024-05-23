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
    @push('title')
        <title>Πρόσφυγες Μαθητές Ιδιωτικής Εκπαίδευσης</title>
    @endpush
        
    @php
        $private_schools = App\Models\School::where('public',0)->get();
        $schoolIds = $private_schools->pluck('id');
        $immigrants = App\Models\microapps\Immigrant::whereIn('school_id', $schoolIds)->get()->sortByDesc('updated_at');
    @endphp
    <div class="h4">Πρόσφυγες Μαθητές Ιδιωτικής Εκπαίδευσης</div>
        <div class="table-responsive py-2">
            <table  id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th id="search">Μήνας</th>
                    <th id="search">Σχολείο</th>
                    <th id="">Σχόλια</th>
                    <th>Αρχείο</th>
                    <th>Τελευταία ενημέρωση</th>
                </tr>
            </thead>
            <tbody>
            
            @foreach($immigrants as $one)
                <tr>
                <td> {{$one->month->name}}</td>
                <td> {{$one->school->name}}</td>
                <td> {{$one->comments}}</td>
                <td>
                    <form action="{{route("immigrants.download_file", ["immigrant" => $one->id])}}" method="get">
                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> </button> 
                    </form>   
                </td>
                <td>{{$one->updated_at}}</td>
                </tr>
            @endforeach
            </tbody>
            </table>
        </div> <!-- table responsive closure -->
        
</x-layout>