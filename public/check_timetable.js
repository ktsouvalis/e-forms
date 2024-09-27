$(document).ready(function () {
    
    $('body').on('change', '.changeTimetableStatus', function () { //Όταν αλλάξει η κατάσταση του προγράμματος ανανέωσε την κατάσταση στη βάση
        var selectedValue = $(this).val();
        var timetableFileId = $(this).attr('name');
        var fileCount = $(this).attr('id');

        // Get the CSRF token from the meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const myurl =
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

        $.ajax({
            url: checkTimetableStatusUrl.replace("mpla", timetableFileId),
            type: 'POST',
            data: {
                // _method: 'PATCH', // Laravel uses PATCH for updates
                status: selectedValue,
            },
            success: function (response) {
                //update page  
                var elementId = timetableFileId + '_' + fileCount;
                if (selectedValue == 1) {//Όταν αλλάξει η κατάσταση σε "Αναμονή Διορθώσεων" δείξε τη φόρμα για τα Σχόλια
                    if ($('#' + elementId).hasClass('btn-success')) {
                        $('#' + elementId).removeClass('btn-success');
                    }
                    $('#' + elementId).removeClass('btn-success').addClass('btn-info');
                    $('.hideAndAppearOnTheFly' + timetableFileId).removeClass('d-none');
                }
                else if (selectedValue == 3) {
                    if ($('#' + elementId).hasClass('btn-info')) {
                        $('#' + elementId).removeClass('btn-info');
                    }
                    $('#' + elementId).removeClass('btn-info').addClass('btn-success');
                    $('.hideAndAppearOnTheFly' + timetableFileId).addClass('d-none'); 
                }
                else{ //if value = 0
                    if ($('#' + elementId).hasClass('btn-sucess')) {
                        $('#' + elementId).removeClass('btn-success');
                    }
                    $('.hideAndAppearOnTheFly' + timetableFileId).addClass('d-none'); 
                    $('#' + elementId).addClass('btn-info');
                }
            },
            error: function (error) {
                // Handle errors
                console.log("An error occurred: " + error);
            }
        });
    });

    //ADD FILTERS ON TOP OF THE DATATABLE
    var table = $('#dataTable').DataTable(); //get datatable
    // Function to filter table rows based on selected checkboxes
    function filterTable() {
        var selectedSchoolKind = [];
        var displaySchoolKind = [];
        // Retrieve and log the stored preferences
        var storedPreferences = Cookies.get('timetablesPreference');
        console.log('Stored preferences out of if: ', storedPreferences);
        if (storedPreferences) {
            console.log('Stored preferences inside if: ', JSON.parse(storedPreferences)); // Log the parsed array
            selectedSchoolKind = storedPreferences; //βάλε τα φίλτρα σε ένα πίνακα
            $('.filter-checkbox').each(function() {
                if(selectedSchoolKind.includes($(this).val())) {
                    $(this).prop('checked', true);
                }
            });
        } else { // Αν μπαίνει από refresh του checkbox μηδενίζονται τα cookies. Πάρε τα επιλεγμένα
            $('.filter-checkbox:checked').each(function() {
                selectedSchoolKind.push($(this).val()); //βάλε τα φίλτρα σε ένα πίνακα
                displaySchoolKind.push($(this).val() == 'primary'?'Δημοτικά':'Νηπιαγωγεία'); //βάλε τα φίλτρα σε ένα πίνακα
            });
             //SET COOKIES FOR THE SELECTED FILTERS AND RETRIEVE THEM
            Cookies.set('timetablesPreference', JSON.stringify(selectedSchoolKind), { expires: 14, sameSite: 'Lax' });
        }
        
        //DISPLAY THE SELECTED FILTERS ON THE PAGE
        $('.selected-filters').text(displaySchoolKind.join(', ')); // Display selected filters on the page
       
        //FILTER THE TABLE ROWS BASED ON THE SELECTED CHECKBOXES
        table.rows().every(function() {
            var row = $(this.node());
            var rowSchoolKind = row.data('school-kind');
            rowSchoolKind = rowSchoolKind.trim();
            if (selectedSchoolKind.length === 0 || selectedSchoolKind.includes(rowSchoolKind)) { // If no filter is selected or the filter is in the array
                row.show();
            } else {
                row.hide();
            }
        });
    }

    // Attach change event to checkboxes
    $('.filter-checkbox').on('change', function() {
        // REMOVE THE COOKIE
        Cookies.remove('timetablesPreference');
        filterTable();
    });

    // Initial filter
    filterTable();
});

