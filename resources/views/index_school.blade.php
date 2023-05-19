<x-layout_school>
    <body class="bg-light">
    <div class="container ">
        
        <div class="row p-2 justify-content-evenly">
        @auth
        @php $school = App\Models\School::find(Auth::id()); @endphp
                        
            @push('title')
                <title>Υποβολή Στοιχείων</title>
            @endpush
            @php
             
            @endphp

            <div class="py-5">
                <div class="container">
                    <div class="row hidden-md-up justify-content-center">
        
                        {{$school->name}}
                        
                    </div>
                </div>
            </div>
            
        
        @else
            @if(session()->has('failure'))
            <div class='container container-narrow'>
            <div class='alert alert-danger text-center'>
                {{session('failure')}}
            </div>
            </div>
            @endif
            
        @endauth
        </div>
        </div>
</x-layout_school>