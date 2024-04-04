$(document).ready(function () {
    $('body').on('change', '.outing-checkbox', function () {

        const outingId = $(this).data('outing-id');
        const isChecked = $(this).is(':checked');
        // Get the CSRF token from the meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const myurl =
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

        $.ajax({
            url: checkOutingUrl.replace("mpla", outingId),
            type: 'POST',
            data: {
                // _method: 'PATCH', // Laravel uses PATCH for updates
                checked: isChecked
            },
            success: function (response) {
                // Handle the response here, update the page as needed
                $('#successMessage').text(response.message).show();
                if (isChecked) {
                    $('.check_td_' + outingId).html('Ελέγχθηκε')
                }
                else {
                    $('.check_td_' + outingId).html('Προς έλεγχο')
                }

            },
            error: function (error) {
                // Handle errors
                console.log("An error occurred: " + error);
            }
        });
    });
});