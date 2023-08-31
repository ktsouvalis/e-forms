
<x-layout_school>
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $accepts = App\Models\Microapp::where('url', '/'.$appname)->first()->accepts; //fetch microapp 'accepts' field
    @endphp
    @include('microapps.ticket-profile')
</x-layout_school>


