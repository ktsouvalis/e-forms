$(document).ready(function () {
    $('[class^="3hideAndAppearOnTheFly"]').hide();
    $('[class^="0hideAndAppearOnTheFly"]').hide();
    $('[class^="1hideAndAppearOnTheFly"]').hide();
    $('body').on('change', '.changeTimetableStatus', function () {
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
                // Handle the response here, update the page as needed
                // $('#successMessage').text(response.message).show();
                var elementId = timetableFileId + '_' + fileCount;
                if (selectedValue == 2) {//Όταν αλλάξει η κατάσταση σε "Αναμονή Διορθώσεων" δείξε τη φόρμα για τα Σχόλια
                    $('#' + elementId).removeClass('btn-success').addClass('btn-info');
                    if ('.0hideAndAppearOnTheFly' + timetableFileId > 0) {
                        $('.0hideAndAppearOnTheFly' + timetableFileId).show();
                    } else {
                        $('.3hideAndAppearOnTheFly' + timetableFileId).hide();
                    }
                }
                else if (selectedValue == 3) {
                    $('#' + elementId).removeClass('btn-info').addClass('btn-success');
                    $('.hideAndAppearOnTheFly' + timetableFileId).hide();
                }
                else{
                    $('#' + elementId).removeClass('btn-suceess').addClass('btn-info');
                    $('.hideAndAppearOnTheFly' + timetableFileId).show();
                }
            },
            error: function (error) {
                // Handle errors
                console.log("An error occurred: " + error);
            }
        });
    });
});