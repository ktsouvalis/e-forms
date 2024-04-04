$(document).ready(function () {
    $('#dataTable thead tr #search').each(function () {
        var cellIndex = $(this)[0].cellIndex;
        var width='';
        if (cellIndex==1 || cellIndex==3 || cellIndex==4){
            width="width:80px;";
        }
        if ( cellIndex == 6) {
            width = "width:200px;";
        }
        var title = $(this).text();
        $(this).html(`
            <div class="vstack gap-1">
                <input type="text" class="include-search" style=" ${width} font-size:small;" placeholder="${title} +" />
                <input type="text" class="exclude-search" style="  ${width} font-size:small;" placeholder="${title} - " />
            </div>
        `);
    });

    var table = $('#dataTable').DataTable({
        "order": [],
        lengthMenu: [10, 25, 50, 100, -1], // Add -1 for "All"
        pageLength: 25,
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
