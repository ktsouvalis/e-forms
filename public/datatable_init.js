$(document).ready(function () {
    // Setup - add a text input for inclusion and exclusion to each header cell
    $('#dataTable thead tr #search').each(function () {
        var title = $(this).text();
        $(this).html(`
      <div class="vstack gap-1">
        <input type="text" class="include-search" style=" font-size:small;" placeholder="${title} +" />
        <input type="text" class="exclude-search" style=" font-size:small;" placeholder="${title} - " />
      </div>
    `);
    });

    // DataTable
    var table = $('#dataTable').DataTable({
        lengthMenu: [10, 25, 50, 100, -1], // Add -1 for "All"
        pageLength: 10, // Set the initial page length
        initComplete: function () {

            // Apply the search
            this.api()
                .columns()
                .every(function () {
                    var that = this;
                    var includeColumn = $('input.include-search', this.header());
                    var excludeColumn = $('input.exclude-search', this.header());

                    includeColumn.on('keyup change clear', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    }).on('click', function (e) {
                        e.stopPropagation();
                        column.search($(this).val()).draw();
                    });

                    excludeColumn.on('keyup change clear', function () {
                        var excludeValue = this.value;
                        var includeValue = includeColumn.val();
                        var regex;

                        if (excludeValue) {
                            regex = `^(?!.*${excludeValue}).*${includeValue}`;
                        } else {
                            regex = includeValue;
                        }

                        that.search(regex, true, false).draw();
                    }).on('click', function (e) {
                        e.stopPropagation();
                        column.search($(this).val()).draw();
                    });
                });
        },
    });
});