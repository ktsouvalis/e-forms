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
    var criteriaForm = document.getElementById('criteriaForm');
    var hiddenButton = document.getElementById('hiddenButton');
    if(criteriaFinalButton) {
        criteriaFinalButton.addEventListener('click', function(event) {
            event.preventDefault();
            if(confirm('1) Βεβαιωθείτε ότι έχετε υποβάλλει όλα τα απαραίτητα δικαιολογητικά. 2) Με την οριστική υποβολή θα αποσταλεί η αίτηση στο Πρωτόκολλο του ΠΥΣΠΕ και θα πρωτοκολληθεί. Είστε βέβαιοι;')){
                console.log('criteriaFinalButton clicked if statement');
                if(hiddenButton) {
                    hiddenButton.click();
                }
                $('#protocolContactCriteriaOverlay').show();
                $('#protocolContactCriteriaSpinner').css('display', 'flex');
            } else {
                console.log('criteriaFinalButton clicked else statement');
                return false;
            }
        });
    }
    
    var preferencesFinalButton = document.getElementById('preferencesFinalSubmit');
    var preferencesForm = document.getElementById('preferencesForm');
    var hiddenButton = document.getElementById('hiddenButton');
    if(preferencesFinalButton) {
        preferencesFinalButton.addEventListener('click', function(event) {
            event.preventDefault();
            if(confirm('1) Με την οριστική υποβολή θα αποσταλούν οι προτιμήσεις σας στο Πρωτόκολλο του ΠΥΣΠΕ και θα ενημερωθεί η αίτησή σας. Είστε βέβαιοι;')){
                console.log('preferencesFinalButton: clicked if statement');
                if(hiddenButton) {
                    hiddenButton.click();
                }
                $('#protocolContactPreferencesOverlay').show();
                $('#protocolContactPreferencesSpinner').css('display', 'flex');
            } else {
                console.log('preferencesFinalButton: clicked else statement');
                return false;
            }
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