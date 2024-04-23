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
                $('.mark-' + notificationId).append('<i class="text-secondary fa-regular fa-envelope-open" data-toggle="tooltip" title="Αναγνωσμένο"></i>');//add the read icon
                $('.mark-' + notificationId).find('#icon'+notificationId).remove(); //remove the unread icon
                $('#actions' + notificationId).find('#mark' + notificationId).remove(); //remove the mark as read button 
                $('#notification-' + notificationId).removeClass('table-secondary'); //remove the unread background color
            },
            error: function (error) {
                // Handle errors
                console.log("An error occurred: " + error);
            }
        });
    });
});