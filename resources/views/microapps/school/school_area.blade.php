<x-layout_school>
@php $school = Auth::guard('school')->user(); @endphp
{{-- <a href="{{url('/school_area_profile')}}">klik</a> --}}
@include('microapps.school_area_profile')
</x-layout_school>