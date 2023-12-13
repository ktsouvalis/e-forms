
function addField() {
    let lastElement = $('.choices').last();
    let lastElementId = lastElement.attr('id');
    let numberOfElements = parseInt(lastElementId.replace('choices', ''));  
    numberOfElements++;
    var suffix = numberOfElements.toString();
    var newDivId = 'choices' + suffix;
    // var newStreetId = 'street' + suffix;
    // var newCommentId = 'comment' + suffix;
    var newRemoveButtonId = 'br' + suffix;

    $('#fields').append($("<div class='input-group choices' id='" + newDivId + "'></div>"));
    $('#' + newDivId).append($("<span class='input-group-text w-25 text-wrap'>Οδός ή Περιοχή " +numberOfElements+" </span>"));
    $('#' + newDivId).append($("<input name='street" + numberOfElements + "' id='street" + numberOfElements +"' type='text' class='w-25'><br>"));
    $('#' + newDivId).append($("<input name='comment" + numberOfElements + "' id='comment" + numberOfElements + "' type='text' class='w-25'><br>"));
    // $('#' + newDivId).append($("<button id='" + newRemoveButtonId + "' type='button' class='btn btn-secondary bi bi-minus' onclick='removeField(" + numberOfElements + ")'>-</button>"));
    $('#' + newDivId).append($("<br><br>"));
}

function removeField() {
    let lastElement = $('.choices').last();
    let lastElementId = lastElement.attr('id');
    let numberOfElements = parseInt(lastElementId.replace('choices', ''));
    if(numberOfElements!=1){
        if(!($('.choices').last().children('input')[0].value=="" && $('.choices').last().children('input')[1].value=="")){
            if(confirm("Θέλετε να διαγράψετε το τελευταίο πεδίο; Οι τιμές που έχετε εισάγει θα χαθούν όταν γίνει αποθήκευση."))
                $('.choices').last().remove();
        }
        else{
            $('.choices').last().remove();
        }
    }
    else{
        alert("Δεν μπορείτε να διαγράψετε το πρώτο πεδίο.");
    }
}