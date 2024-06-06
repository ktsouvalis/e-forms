<x-layout_teacher>
    @php
        $stakeholder = Illuminate\Support\Facades\Auth::guard('teacher')->user();  
        $interaction_types = App\Models\InteractionType::where('active',1)->where('stakes_to', 't')->get();
    @endphp
    @endphp
    @include('interactions.create')
</x-layout_teacher>