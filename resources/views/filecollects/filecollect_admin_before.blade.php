
<div class="hstack gap-2">
<div class="h4">{{$filecollect->name}}: {{$filecollect->department->name}}</div>
@php
    $currentUrl = request()->url();
@endphp

{{-- @if (Str::contains($currentUrl, 'filecollect_profile'))
    <a class="btn btn-primary bi bi-eye px-1" data-toggle="tooltip" data-placement="top" title="Απαντήσεις" style="text-decoration: none;" href="{{ url("/admin".$filecollect->url) }}"></a>
@elseif (Str::contains($currentUrl, 'admin'))
    @can('update', $filecollect)
        <a class="btn btn-primary bi bi-pencil px-1" data-toggle="tooltip" data-placement="top" title="Επεξεργασία μικροεφαρμογής" style="text-decoration: none;" href="{{ url("/filecollect_profile/$filecollect->id") }}"></a>
    @endcan
@endif --}}
</div>
<hr>
@php
    //fetch all stakeholders of the filecollect to show them even if they have not submit some answer.
    if($filecollect->visible){
    
    $hidden_acc = "";
    $tooltip_vis = "Κλείσιμο ορατότητας";  
        if($filecollect->accepts){
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
    @if(!$filecollect->visible)
        <div class='alert alert-warning text-center'>
            Η συλλογή δεν είναι ορατή
        </div>
    @else
        <div class='alert alert-success text-center'>
            Η συλλογή είναι ορατή
        </div>
    @endif  
    @can('view', $filecollect)
    <form action="{{url("/filecollects/change_filecollect_status/$filecollect->id")}}" method="post">
        @csrf
        <input name="asks_to" type="hidden" value="ch_vis_status">
        <button type="submit" class="btn btn-secondary bi bi-binoculars"  onclick="return confirm('Με την αλλαγή της ορατότητας, η φόρμα δε θα δέχεται υποβολές\n')"> {{$tooltip_vis}}</button>
    </form>

    <form action="{{url("/filecollects/change_filecollect_status/$filecollect->id")}}" method="post">
        @csrf
        <input name="asks_to" type="hidden" value="ch_acc_status">
        <button type="submit" class="btn btn-secondary bi bi-journal-arrow-down"  {{$hidden_acc}}> {{$tooltip_acc}}</button>
    </form>
    @endcan
    @if(!$filecollect->accepts)
        <div class='alert alert-warning text-center'>
            Η συλλογή δε δέχεται υποβολές
        </div>
    @else
        <div class='alert alert-success text-center'>
            Η συλλογή δέχεται υποβολές
        </div>
    @endif        
</div>
</div>

