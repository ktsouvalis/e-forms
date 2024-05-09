$(document).ready(function () {
    // Show spinner on clicks of a hrefs which do not have a no-spinner class (eg modals)
    $('a:not(.no-spinner)').on('click', function () {
        $('#loadingOverlay').show();
        $('#loadingSpinner').css('display', 'flex'); // or 'block'
    });

    // Show spinner on submissions of post forms which do not have a data-export attribute
    $('form[method="post"]:not([data-export])').on('submit', function () {
        $('#loadingOverlay').show();
        $('#loadingSpinner').css('display', 'flex'); // or 'block'
    });

    // Hide spinner when page is fully loaded
    $(window).on('load', function () {
        $('#loadingOverlay').hide();
        $('#loadingSpinner').hide();
    });
});