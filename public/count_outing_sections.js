$(document).ready(function () {
    $('body').on('click', '.outing-calcbox', function () {
        const outingId = $(this).data('outing-id');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        $.ajax({
            url: countSectionsUrl.replace("mpla", outingId),
            type: 'POST',
            success: function (response) {
                const sections = JSON.parse(response.sections);
                let output = '';
                for (let section in sections) {
                    output += section + ': ' + sections[section] + '<br>';
                }
                $('#calc_td_' + outingId).show();
                $('#calc_td_' + outingId).html(output);
                $('#hide_button-' + outingId).removeClass('hide-button');
                $('#calc_button-' + outingId).addClass('hide-button');
            },
            error: function (error) {
                console.log("An error occurred: " + error);
            }
        });
    });

    $('body').on('click', '.outing-hidebox', function () {
        const outingId = $(this).data('outing-id');
        $('#calc_td_' + outingId).hide();
        $('#calc_button-' + outingId).removeClass('hide-button');
        $(this).addClass('hide-button');
    });
});