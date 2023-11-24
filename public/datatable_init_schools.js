$(document).ready(function () {
    // Setup - add a text input for inclusion and exclusion to each header cell
    $('#dataTable thead tr #search').each(function () {
        var cellIndex = $(this)[0].cellIndex;
        var width='';
        if (cellIndex==2 || cellIndex==4 || cellIndex==5 || cellIndex==6 || cellIndex==7 || cellIndex==11){
            width="width:60px;";
        }
        var title = $(this).text();
        $(this).html(`
            <div class="vstack gap-1">
                <input type="text" class="include-search" style=" ${width} font-size:small;" placeholder="${title} +" />
                <input type="text" class="exclude-search" style="  ${width} font-size:small;" placeholder="${title} - " />
            </div>
        `);
    });

    // DataTable
    var table = $('#dataTable').DataTable({
        // "columnDefs": [
        //     { "width": "60px", "targets": 0 }
        // ],
        "order": [],
        lengthMenu: [10, 25, 50, 100, -1], // Add -1 for "All"
        pageLength: 25, // Set the initial page length
        initComplete: function () {
            // Apply the search
            this.api().columns().every(function () {
                var that = this;
                var includeColumn = $('input.include-search', this.header());
                var excludeColumn = $('input.exclude-search', this.header());

                includeColumn.on('keyup change clear', function () {
                    var includeValue = this.value;
                    var excludeValue = excludeColumn.val();
                    var regex;

                    if (includeValue) {
                        if (excludeValue) {
                            regex = `^(?=.*${includeValue})(?!.*${excludeValue})`;
                        } else {
                            regex = `.*${includeValue}`;
                        }
                    } else {
                        regex = excludeValue ? `^(?!.*${excludeValue}).*` : '';
                    }

                    that.search(regex, true, false).draw();
                }).on('click', function (e) {
                    e.stopPropagation();
                    column.search($(this).val()).draw();
                });

                excludeColumn.on('keyup change clear', function () {
                    var excludeValue = this.value;
                    var includeValue = includeColumn.val();
                    var regex;

                    if (excludeValue) {
                        if (includeValue) {
                            regex = `^(?=.*${includeValue})(?!.*${excludeValue})`;
                        } else {
                            regex = `^(?!.*${excludeValue}).*`;
                        }
                    } else {
                        regex = includeValue ? `.*${includeValue}` : '';
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
