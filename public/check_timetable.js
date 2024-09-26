$(document).ready(function () {
    
    // Cookies.set('name', 'checkedId');
    // console.log(Cookies.get('name')); // 'value'
    $('body').on('change', '.changeTimetableStatus', function () { //Όταν αλλάξει η κατάσταση του προγράμματος ανανέωσε την κατάσταση στη βάση
        var selectedValue = $(this).val();
        var timetableFileId = $(this).attr('name');
        var fileCount = $(this).attr('id');

        // Get the CSRF token from the meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const myurl =
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

        $.ajax({
            url: checkTimetableStatusUrl.replace("mpla", timetableFileId),
            type: 'POST',
            data: {
                // _method: 'PATCH', // Laravel uses PATCH for updates
                status: selectedValue,
            },
            success: function (response) {
                //update page  
                var elementId = timetableFileId + '_' + fileCount;
                if (selectedValue == 1) {//Όταν αλλάξει η κατάσταση σε "Αναμονή Διορθώσεων" δείξε τη φόρμα για τα Σχόλια
                    if ($('#' + elementId).hasClass('btn-success')) {
                        $('#' + elementId).removeClass('btn-success');
                    }
                    $('#' + elementId).removeClass('btn-success').addClass('btn-info');
                    $('.hideAndAppearOnTheFly' + timetableFileId).removeClass('d-none');
                }
                else if (selectedValue == 3) {
                    if ($('#' + elementId).hasClass('btn-info')) {
                        $('#' + elementId).removeClass('btn-info');
                    }
                    $('#' + elementId).removeClass('btn-info').addClass('btn-success');
                    $('.hideAndAppearOnTheFly' + timetableFileId).addClass('d-none'); 
                }
                else{ //if value = 0
                    if ($('#' + elementId).hasClass('btn-sucess')) {
                        $('#' + elementId).removeClass('btn-success');
                    }
                    $('.hideAndAppearOnTheFly' + timetableFileId).addClass('d-none'); 
                    $('#' + elementId).addClass('btn-info');
                    
                    
                }
            },
            error: function (error) {
                // Handle errors
                console.log("An error occurred: " + error);
            }
        });
    });
});

