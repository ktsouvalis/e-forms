$(document).ready(function () {
    $('body').on('click', '.secondment-extra-files-checkbox', function () {
        const secondmentId = $(this).data('secondment-id');
        // Get the CSRF token from the meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        // alert("secondmentId: " + secondmentId + " csrfToken: " + csrfToken);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        $.ajax({
            url: '/secondments/allow_extra_files/' + secondmentId,
            type: 'POST',
            data: {
                _method: 'POST', // Laravel uses PATCH for updates
            },
            success: function (response) {
                // Handle the response here, update the page as needed
                alert("Success: " + response);
            },
            error: function (error) {
                // Handle errors
                console.log("An error occurred: " + error);
            }
        });
    });
});