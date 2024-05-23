<x-layout>

    @push('links')
        <link href="{{ asset('DataTables-1.13.4/css/dataTables.bootstrap5.css') }}" rel="stylesheet"/>
        <link href="{{ asset('Responsive-2.4.1/css/responsive.bootstrap5.css') }}" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="{{ asset('DataTables-1.13.4/js/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('DataTables-1.13.4/js/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/dataTables.responsive.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/responsive.bootstrap5.js') }}"></script>
        <script src="{{ asset('datatable_init.js') }}"></script>
    @endpush
    @push('title')
        <title>Εγγραφές 2024-25 Ιδιωτικής Εκπαίδευσης</title>
    @endpush
        @php
            $microapp = App\Models\Microapp::where('url', '/enrollments')->first();
            $private_schools = App\Models\School::where('public',0)->get();
            $schoolIds = $private_schools->pluck('id');
            $enrollments = $microapp->stakeholders->whereIn('stakeholder_id', $schoolIds);
        @endphp
        <div class="h4">Εγγραφές 2024-25 Ιδιωτικής Εκπαίδευσης</div>
        <div class="table-responsive py-2" style="align-self:flex-start">
            <table  id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th id="search">Είδος</th>
                    <th id="search">Σχολείο</th>
                    <th id="search">Εγγραφέντες</th>
                    <th id="">Αρχείο</th>
                    
                    <th>Τελευταία ενημέρωση</th>
                    <th>Κωδικός</th>
                </tr>
            </thead>
            <tbody>
            
            @foreach($enrollments as $one_stakeholder)
                @php
                    $one_school = $one_stakeholder->stakeholder;
                    $one = $one_school->enrollments;
                    $school_name_filename = str_replace(' ','_',(str_replace('/', '', $one_school->name)));
                @endphp
                    <tr>
                        <td>@if($one_school->primary == 1) Δημοτικό @else Νηπιαγωγείο @endif</td>
                        <td> {{$one_school->name}}</td>
                    @if($one)
                        <td> {{$one->nr_of_students1}}</td>
                        <td>
                            <form action="{{route('enrollments.download_file', ['file' =>"enrollments1_$one_school->code.xlsx", 'download_file_name' => "Εγγραφέντες_$school_name_filename.xlsx"] )}} " method="get">
                            <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη">{{$one->enrolled_file1}} </button> 
                            </form>  
                        </td>
                        <td>{{$one->updated_at}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @endif
                    <td>{{$one_school->code}}</td>
                    </tr>
        @endforeach
        </tbody>
        </table>
    </div> <!-- table responsive closure -->
</x-layout>