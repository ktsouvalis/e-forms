$(document).ready(function () {
    
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
                if (selectedValue == 1) {//Όταν αλλάξει η κατάσταση σε "Αναμονή Διορθώσεων" δείξε τη φόρμα για τα Σχόλια
                    $('#' + elementId).removeClass('btn-success').addClass('btn-info');
                    $('.hideAndAppearOnTheFly' + timetableFileId).removeClass('d-none');
                    console.log("Μπήκε στην Αναμονή Διορθώσεων");
                }
                else if (selectedValue == 3) {
                    $('#' + elementId).removeClass('btn-info').addClass('btn-success');
                    $('.hideAndAppearOnTheFly' + timetableFileId).addClass('d-none');
                    console.log("Μπήκε στην Επικυρωμένο");
                }
                else{
                    
                }
            },
            error: function (error) {
                // Handle errors
                console.log("An error occurred: " + error);
            }
        });
    });
});

