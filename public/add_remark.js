$(document).ready(function () {
    let currentId = null;

    $('body').on('click', '.outing-remark-btn', function () {
        currentId = $(this).data('outing-id');
        $('#remarkModalTitle').text('Παρατηρήσεις εκδρομής ' + currentId);
        $('#remarkHistory').text($('#remarks_' + currentId).text() || 'Δεν υπάρχουν παρατηρήσεις');
        $('#remarkText').val('');
    });

    $('#remarkForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: addRemarkUrl.replace('mpla', currentId),
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { remark: $('#remarkText').val() },
            success: function (r) {
                $('#remarks_' + currentId).text(r.remarks);
                $('#remarkHistory').text(r.remarks);
                $('#remark_badge_' + currentId).removeClass('d-none');
                $('#remarkText').val('');
                if (!r.mail_sent) alert('Η παρατήρηση αποθηκεύτηκε, αλλά το email στο σχολείο δεν στάλθηκε');
            },
            error: function () { alert('Η παρατήρηση δεν αποθηκεύτηκε'); }
        });
    });
});