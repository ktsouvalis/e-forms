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

    var criteriaFinalButton = document.getElementById('criteriaFinalSubmit');
    if(criteriaFinalButton) {
        criteriaFinalButton.addEventListener('click', function() {
            $('#protocolContactCriteriaOverlay').show();
            $('#protocolContactCriteriaSpinner').css('display', 'flex');
        });
    }
    
    var preferencesFinalButton = document.getElementById('preferencesFinalSubmit');
    if(preferencesFinalButton) {
        preferencesFinalButton.addEventListener('click', function() {
            $('#protocolContactPreferencesOverlay').show();
            $('#protocolContactPreferencesSpinner').css('display', 'flex');
        });
    }
    
    // Hide spinner when page is fully loaded
    $(window).on('load', function () {
        $('#loadingOverlay').hide();
        $('#loadingSpinner').hide();
    });

    $(window).on('pageshow', function () {
        $('#loadingOverlay').hide();
        $('#loadingSpinner').hide();
    });

    $(window).on('load', function () {
        $('#protocolContactCriteriaOverlay').hide();
        $('#protocolContactCriteriaSpinner').hide();
    });

    $(window).on('pageshow', function () {
        $('#protocolContactCriteriaOverlay').hide();
        $('#protocolContactCriteriaSpinner').hide();
    });

    $(window).on('load', function () {
        $('#protocolContactPreferencesOverlay').hide();
        $('#protocolContactPreferencesSpinner').hide();
    });

    $(window).on('pageshow', function () {
        $('#protocolContactPreferencesOverlay').hide();
        $('#protocolContactPreferencesSpinner').hide();
    });
});