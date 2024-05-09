$(document).ready(function () {
    // $('#loadingSpinner').hide();
    // Show spinner on link click
    $('a').on('click', function () {
        $('#loadingOverlay').show();
        $('#loadingSpinner').css('display', 'flex'); // or 'block'
    });

    // Show spinner on form submit
    $('form[method="post"]').on('submit', function () {
        $('#loadingOverlay').show();
        $('#loadingSpinner').css('display', 'flex'); // or 'block'
    });

    // Hide spinner when page is fully loaded
    $(window).on('load', function () {
        $('#loadingOverlay').hide();
        $('#loadingSpinner').hide();
    });
});