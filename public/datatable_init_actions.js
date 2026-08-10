$(document).ready(function () {

    // Μικρή βοηθητική συνάρτηση που μετατρέπει τα #search header cells
    // ενός συγκεκριμένου table σε include/exclude search inputs
    // και επιστρέφει το αρχικοποιημένο DataTable object.
    function initActionsTable(tableSelector) {
        $(tableSelector + ' thead tr #search').each(function () {
            var title = $(this).text();
            $(this).html(`
                <div class="vstack gap-1">
                    <input type="text" class="include-search" style="font-size:small;" placeholder="${title} +" />
                    <input type="text" class="exclude-search" style="font-size:small;" placeholder="${title} - " />
                </div>
            `);
        });

        var table = $(tableSelector).DataTable({
            "order": [],
            lengthMenu: [10, 25, 50, 100, -1],
            pageLength: 50,
            initComplete: function () {
                this.api().columns().every(function () {
                    var that = this;
                    var includeColumn = $('input.include-search', this.header());
                    var excludeColumn = $('input.exclude-search', this.header());

                    includeColumn.on('keyup change clear', function () {
                        var includeValue = this.value;
                        var excludeValue = excludeColumn.val();
                        var regex;

                        if (includeValue) {
                            regex = excludeValue
                                ? `^(?=.*${includeValue})(?!.*${excludeValue})`
                                : `.*${includeValue}`;
                        } else {
                            regex = excludeValue ? `^(?!.*${excludeValue}).*` : '';
                        }

                        that.search(regex, true, false).draw();
                    });

                    excludeColumn.on('keyup change clear', function () {
                        var excludeValue = this.value;
                        var includeValue = includeColumn.val();
                        var regex;

                        if (excludeValue) {
                            regex = includeValue
                                ? `^(?=.*${includeValue})(?!.*${excludeValue})`
                                : `^(?!.*${excludeValue}).*`;
                        } else {
                            regex = includeValue ? `.*${includeValue}` : '';
                        }

                        that.search(regex, true, false).draw();
                    });
                });
            },
        });

        return table;
    }

    // Αρχικοποίηση του πίνακα "Όλες οι Δράσεις" (πάντα ορατός εξ αρχής)
    var dataTable = initActionsTable('#dataTable');

    // Αν υπάρχει και δεύτερος πίνακας ("Τα Σχολεία μου", μόνο για Επόπτες)
    var dataTableMyActions = null;
    if ($('#dataTableMyActions').length) {
        dataTableMyActions = initActionsTable('#dataTableMyActions');

        // ΣΗΜΑΝΤΙΚΟ: Όταν ο πίνακας αρχικοποιείται μέσα σε tab-pane που είναι
        // κρυμμένο (display:none), το DataTables υπολογίζει λάθος πλάτη
        // στηλών και ο header row "καταρρέει". Λύση: κάθε φορά που το tab
        // γίνεται ορατό, ζητάμε από το DataTables να ξαναϋπολογίσει τα
        // πλάτη των στηλών.
        $('#mine-tab').on('shown.bs.tab', function () {
            dataTableMyActions.columns.adjust().draw(false);
        });
    }
});
