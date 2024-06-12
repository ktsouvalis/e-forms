$(document).ready(function () {
    $('.summernote').summernote();

    $('.edit-button').click(function () {
        $(this).siblings('.card').find('.post-text').hide();
        $(this).hide();
        $(this).siblings('.card').find('.post-editor').show();
    });

    $('.save-button').click(function () {
        var markup = $(this).siblings('.summernote').summernote('code');
        var postId = $(this).data('id');
        const editPostUrl = $(this).data('edit-post-url');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        $.ajax({
            url: editPostUrl,
            type: 'POST',
            data: {
                id: postId,
                text: markup
            },
            success: (function () {
                return function () {
                    location.reload();
                }
            })($(this).parent().siblings('.post-text'))
        });
    });
    $('.cancel-button').click(function () {
        $(this).parent().hide();
        $(this).parent().siblings('.post-text').show();
        $('.edit-button').show();
    });
});