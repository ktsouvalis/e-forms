$(document).ready(function () {
    $('body').on('change', '.internal-rule-checkbox', function () {
        const internalRuleId = $(this).data('internal-rule-id');
        const isChecked = $(this).is(':checked');
        var who
        if (isChecked == true) {
            who = 'consultantYes';
        } else {
            who = 'consultantNo';
        }

        //const buttonValue = $(this).data('set');
        // Get the CSRF token from the meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            }
        });

        $.ajax({
            url: internalRuleCheckUrl.replace("mpla", internalRuleId),
            type: 'POST',
            data: {
                // _method: 'PATCH', // Laravel uses PATCH for updates
                checked: who,
            },
            success: function (response) {
                // Handle the response here, update the page as needed
                if (isChecked) {
                    $('.check_td_' + internalRuleId).html('Εγκρίθηκε')
                }
                else {
                    $('.check_td_' + internalRuleId).html('Έγκριση')
                }
            },
            error: function (error) {
                // Handle errors
                console.log("An error occurred: " + error);
            }
        });
    });
});