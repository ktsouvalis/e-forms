$(document).ready(function () {
    $('body').on('click', '.criteria-calcbox', function () {
        const acId = $(this).data('acid');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        $.ajax({
            url: countCriteriaTeachersUrl.replace("mpla", acId),
            type: 'POST',
            success: function (response) {
                const count = response.count;
                
                $('#calc_acid').show();
                $('#calc_acid').html(count + ' εκπαιδευτικοί');
            },
            error: function (error) {
                console.log("An error occurred: " + error);
            }
        });
    });
});