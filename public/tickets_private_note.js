$(document).ready(function () {
    $('body').on('blur', '#private_note', function () {

        const textarea = document.getElementById('private_note');
        const note = textarea.value;
        const ticketId = $(this).data('ticket-id');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const savePrivateNoteURL = $(this).data('private-note-url'); 

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        $.ajax({
            url: savePrivateNoteURL,
            type: 'POST',
            data: {
                private_note: note
            },
            success: function (response) {
                console.log(response);
            },
            error: function (error) {
                console.log("An error occurred: " + error);
            }
        });
    });
});