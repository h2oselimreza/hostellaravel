$(function () {
    $('.jq-option-datatable').DataTable({
        responsive: true,
        lengthMenu: [ [ 10, 25, 50, 100, -1], [10, 25, 50,100, "All"] ],
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                    );

                            column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                        });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>')
                });
            });
        }
    });

    $('.jq-no-sort-datatable').DataTable({
        responsive: true,
         "columnDefs": [{
            "targets": 'no-sort',
            "orderable": false,
            "order": []
        }],
        lengthMenu: [ [ 10, 25, 50, 100, -1], [10, 25, 50,100, "All"] ],
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                    );

                            column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                        });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>')
                });
            });
        }
    });

    //Exportable table
    $('.js-exportable').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
         bPaginate: false
    });
    
     $('.js-exportable-no-search').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
         bPaginate: false,
         searching: false
    });

    $('.vehicle-requisition-js-exportable').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [1, 2,3, 4,5,6,7,8,9,10,11 ]
                }
            },
            // 'csv', 'excel',
        ],
    });
});