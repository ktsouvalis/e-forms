$(document).ready(function () {
    $('body').on('click', '.delete-notification', function () {

        const notificationId = $(this).data('notification-id');
        // Get the CSRF token from the meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        $.ajax({
            url: deleteNotificationUrl.replace("mpla", notificationId),
            type: 'POST',
            data: {
                _method: 'DELETE', // Laravel uses PATCH for updates
            },
            success: function (response) {
                // Handle the response here, update the page as needed
                $('#notification-' + notificationId).remove();
            },
            error: function (error) {
                // Handle errors
                console.log("An error occurred: " + error);
            }
        });
    });
});