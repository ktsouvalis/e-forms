
<x-layout>
    @php
        $accepts = App\Models\Microapp::where('url', '/'.$appname)->first()->accepts; //fetch microapp 'accepts' field
    @endphp
    @include('microapps.ticket-profile')
</x-layout>


