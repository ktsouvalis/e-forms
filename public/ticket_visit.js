$(document).ready(function () {
    $('body').on('change', '.ticket-checkbox', function () {

        const ticketId = $(this).data('ticket-id');
        const isChecked = $(this).is(':checked');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        $.ajax({
            url: ticketNeededVisitUrl.replace("mpla", ticketId),
            type: 'POST',
            data: {
                checked: isChecked
            },
            success: function (response) {
                console.log("success");
            },
            error: function (error) {
                console.log("An error occurred: " + error);
            }
        });
    });
});