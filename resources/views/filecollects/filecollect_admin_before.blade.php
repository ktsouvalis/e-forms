
<div class="hstack gap-2">
<div class="h4">{{$filecollect->name}}: {{$filecollect->department->name}}</div> <small class="text-muted">(Απαντήσεις: {{$filecollect->stakeholders->whereNotNull('file')->count()}} από {{$filecollect->stakeholders->count()}})</small>
@php
    $currentUrl = request()->url();
@endphp

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
    <form action="{{url("/filecollects/change_status/$filecollect->id")}}" method="post">
        @csrf
        <input name="asks_to" type="hidden" value="ch_vis_status">
        <button type="submit" class="btn btn-secondary bi bi-binoculars"  onclick="return confirm('Με την αλλαγή της ορατότητας, η φόρμα δε θα δέχεται υποβολές\n')"> {{$tooltip_vis}}</button>
    </form>

    <form action="{{url("/filecollects/change_status/$filecollect->id")}}" method="post">
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

