
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
<div class="d-flex justify-content-center">
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
    <form action="{{url("/change_microapp_status/$microapp->id")}}" method="post">
        @csrf
        <input name="asks_to" type="hidden" value="ch_vis_status">
        <button type="submit" class="btn btn-secondary bi bi-binoculars"  onclick="return confirm('Με την αλλαγή της ορατότητας, η φόρμα δε θα δέχεται υποβολές\n')"> {{$tooltip_vis}}</button>
    </form>

    <form action="{{url("/change_microapp_status/$microapp->id")}}" method="post">
        @csrf
        <input name="asks_to" type="hidden" value="ch_acc_status">
        <button type="submit" class="btn btn-secondary bi bi-journal-arrow-down"  {{$hidden_acc}}> {{$tooltip_acc}}</button>
    </form>
    
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

