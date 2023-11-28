@if (session()->has('success'))
    <div class='container container-narrow'>
        <div class='alert alert-success text-center'>
        {{session('success')}}
        </div>
    </div>
    @endif

    @if(session()->has('failure'))
    <div class='container container-narrow'>
    <div class='alert alert-danger text-center'>
        {{session('failure')}}
    </div>
    </div>
    @endif
    
    @if(session()->has('warning'))
    <div class='container container-narrow'>
    <div class='alert alert-warning text-center'>
        {{session('warning')}}
    </div>
    </div>
    @endif 

    @if(session()->has('command'))
    <div class='container container-narrow'>
    <div class='alert alert-dark text-center fw-bold'>
        {{session('command')}}
    </div>
    </div>
@endif 