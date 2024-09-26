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
                console.log(selectedValue);
                if (selectedValue == 1) {//Όταν αλλάξει η κατάσταση σε "Αναμονή Διορθώσεων" δείξε τη φόρμα για τα Σχόλια
                    console.log("Μπήκε στην Αναμονή Διορθώσεων");
                    $('#' + elementId).removeClass('btn-success').addClass('btn-info');
                    $('.hideAndAppearOnTheFly' + timetableFileId).removeClass('d-none');
                    
                }
                else if (selectedValue == 3) {
                    console.log("Μπήκε στην Επικυρωμένο");
                    $('#' + elementId).removeClass('btn-info').addClass('btn-success');
                    $('.hideAndAppearOnTheFly' + timetableFileId).addClass('d-none');
                    
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

