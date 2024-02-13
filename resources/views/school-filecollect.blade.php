<x-layout_school>
    @php
        $school = Illuminate\Support\Facades\Auth::guard('school')->user();
        $old_data = $school->filecollects->where('filecollect_id', $filecollect->id)->first();
        // dd($old_data);
        $accepts = $filecollect->accepts;
    @endphp
   @include('generic-filecollect')
</x-layout_school>