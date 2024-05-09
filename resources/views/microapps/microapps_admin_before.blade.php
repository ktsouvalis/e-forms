
<div class="hstack gap-2">
<div class="h4">{{$microapp->name}}</div>
@php
    $currentUrl = request()->url();
    $resource = substr($microapp->url, 1);
@endphp

@if (Str::contains($currentUrl, 'edit'))
    <a class="btn btn-primary bi bi-eye px-1" data-toggle="tooltip" data-placement="top" title="Απαντήσεις" style="text-decoration: none;" href="{{ route("$resource.index")}}"></a>
@else
    @can('update', $microapp)
        {{-- <a class="btn btn-primary bi bi-pencil px-1" data-toggle="tooltip" data-placement="top" title="Επεξεργασία μικροεφαρμογής" style="text-decoration: none;" href="{{ url("/manage/microapps/$microapp->id/edit") }}"></a> --}}
        <a class="btn btn-primary bi bi-pencil px-1" data-toggle="tooltip" data-placement="top" title="Επεξεργασία μικροεφαρμογής" style="text-decoration: none;" href="{{ route('microapps.edit', $microapp->id) }}"></a>
    @endcan
@endif
</div>
<hr>
@php
    //fetch all stakeholders of the microapp to show them even if they have not submit some answer. $fruits_schools is MicroappStakeholder object
    if($microapp->visible){
        $hidden_acc = "";
        $tooltip_vis = "Κλείσιμο ορατότητας";  
        if($microapp->accepts){
            $tooltip_acc="Κλείσιμο υποβολών";
        }
        else{
            $tooltip_acc="Άνοιγμα Υποβολών";
        }
    }
    else{
        $hidden_acc="hidden";
        $tooltip_acc="";
        $tooltip_vis = "Άνοιγμα ορατότητας";
    }
@endphp
<div class="d-flex mx-2 justify-content-center">
<div class="hstack gap-3 py-2">
    @if(!$microapp->visible)
        <div class='alert alert-warning text-center'>
            Η εφαρμογή δεν είναι ορατή
        </div>
    @else
        <div class='alert alert-success text-center'>
            Η εφαρμογή είναι ορατή
        </div>
    @endif  
    @can('update', $microapp)
    {{-- <form action="{{url("/manage/microapps/change_microapp_status/$microapp->id")}}" method="post"> --}}
    <form action="{{route('microapps.change_status', $microapp->id)}}" method="post">
        @csrf
        <input name="asks_to" type="hidden" value="ch_vis_status">
        <button type="submit" class="btn btn-primary bi bi-binoculars"  onclick="return confirm('Με την αλλαγή της ορατότητας, η φόρμα δε θα δέχεται υποβολές\n')"> {{$tooltip_vis}}</button>
    </form>

    {{-- <form action="{{url("/manage/microapps/change_microapp_status/$microapp->id")}}" method="post"> --}}
    <form action="{{route('microapps.change_status', $microapp->id)}}" method="post">
        @csrf
        <input name="asks_to" type="hidden" value="ch_acc_status">
        <button type="submit" class="btn btn-primary bi bi-journal-arrow-down"  {{$hidden_acc}}> {{$tooltip_acc}}</button>
    </form>
    @endcan
    @if(!$microapp->accepts)
        <div class='alert alert-warning text-center'>
            Η εφαρμογή δε δέχεται υποβολές
        </div>
    @else
        <div class='alert alert-success text-center'>
            Η εφαρμογή δέχεται υποβολές
        </div>
    @endif        
</div>
</div>

