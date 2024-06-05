<x-layout_teacher>
    @php
        $stakeholder = Illuminate\Support\Facades\Auth::guard('teacher')->user();  
    @endphp
    @include('interactions.create')
</x-layout_teacher>