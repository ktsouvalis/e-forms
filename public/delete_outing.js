$(document).ready(function () {
    $('body').on('click', '.outing-delbox', function () {

        const outingId = $(this).data('outing-id');
        // Get the CSRF token from the meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        $.ajax({
            url: deleteOutingUrl.replace("mpla", outingId),
            type: 'POST',
            data: {
                _method: 'DELETE', // Laravel uses PATCH for updates
            },
            success: function (response) {
                // Handle the response here, update the page as needed
                $('#outing-' + outingId).remove();
            },
            error: function (error) {
                // Handle errors
                console.log("An error occurred: " + error);
            }
        });
    });
});