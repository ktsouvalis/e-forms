$(document).ready(function () {
    $('body').on('click', '.mark-notification', function () {

        const notificationId = $(this).data('notification-id');
        // Get the CSRF token from the meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        $.ajax({
            url: markNotificationAsReadUrl.replace("mpla", notificationId),
            type: 'POST',
            success: function (response) {
                // Handle the response here, update the page as needed
                $('.mark-'+notificationId).find('.mark-notification').remove();
                $('.mark-'+notificationId).append('<button class="btn btn-secondary bi bi-check2" disabled></button>');
                $('#notification-' + notificationId).removeClass('table-warning');
            },
            error: function (error) {
                // Handle errors
                console.log("An error occurred: " + error);
            }
        });
    });
});