<x-layout_school>
    @php
        $stakeholder = Illuminate\Support\Facades\Auth::guard('school')->user();  
        $interaction_types = App\Models\InteractionType::where('active',1)->where('stakes_to', 's')->get();
    @endphp
    @include('interactions.create')
</x-layout_school>