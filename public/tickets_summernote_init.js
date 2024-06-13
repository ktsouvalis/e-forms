$(document).ready(function () {
    $('.summernote').each(function () {
        $(this).summernote({
            width: "100%",
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['list', ['ul', 'ol']],
                ['link', ['link']],
            ],
            lang: 'el-GR',
        });
    });
});