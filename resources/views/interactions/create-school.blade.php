<x-layout_school>
    @php
        $stakeholder = Illuminate\Support\Facades\Auth::guard('school')->user();  
    @endphp
    @include('interactions.create')
</x-layout_school>