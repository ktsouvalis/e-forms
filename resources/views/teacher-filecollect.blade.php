<x-layout_teacher>
    @php
        $teacher = Illuminate\Support\Facades\Auth::guard('teacher')->user();
        $old_data = $teacher->filecollects->where('filecollect_id', $filecollect->id)->first();
        $accepts = $filecollect->accepts;
    @endphp
    @include('generic-filecollect')
</x-layout_teacher>