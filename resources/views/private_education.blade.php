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
    @endpush
    @push('title')
        <title>Ιδιωτική Εκπαίδευση</title>
    @endpush
    @php
        $private_schools = App\Models\School::where('public',0)->get();
        $schoolIds = $private_schools->pluck('id');
        $microapps = App\Models\Microapp::whereHas('stakeholders', function ($query) use ($schoolIds) {
            $query->whereIn('stakeholder_id', $schoolIds)->where('stakeholder_type', 'App\\Models\\School');
        })->get(); // Get microapps that are visible to private schools
    @endphp
    <div class="h4">Υποβολές Σχολείων Ιδιωτικής Εκπαίδευσης</div>
    <div class="hstack gap-3 p-2">
    @foreach($microapps as $microapp)
    @if($microapp->active)
        <div class="badge text-wrap py-2" style="width: 10rem; background-color:{{$microapp->color}}; text-align:center;">
            <div class="text-dark {{$microapp->icon}}"></div>
            <a  href="{{url("/private_education".$microapp->url)}}" style="color:black; text-decoration:none;" target="_blank" class="no-spinner"> {{$microapp->name}} </a>
        </div>
    @endif  
    @endforeach
    </div>
</x-layout>