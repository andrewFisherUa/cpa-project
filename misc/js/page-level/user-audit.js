jQuery(document).ready(function(){
  Audit.init();
});

var Audit = function () {

    var grid;

    var handleTable = function () {

        grid = new Datatable();

        grid.init({
            src: $("#user-audit-table"),
            loadingMessage: 'Загрузка...',
            dataTable: {
                "language": datatables_defaults.lang,
                "lengthMenu": [
                    [50, 100, 150, -1],
                    [50, 100, 150, "Все"] // change per page values here
                ],
                "pageLength": 50, // default record count per page
                "ajax": {
                    "url": "/ajax/get-user-audit/", // ajax source
                    data : function (d) {

                        d.params = {
                            uid : $("#user-audit-table").data("user")
                        }

                        $('.form-filter').each(function(){
                            d.params[$(this).attr('name')] = $(this).val()
                        })

                        
                    }
                },
                "bSort" : false,
                fnDrawCallback: function(oSettings) {
                    var c = {
                        "suspicious" : "warning",
                        "checked" : "success",
                        "malicious" : "danger"
                    }

                    $("#user-audit-table span[data-status]").each(function(){
                        $(this).closest("tr").addClass(c[$(this).data("status")]);
                    })
                    //$('.date-picker').datepicker();
                },
            }
        });
        
        $(document).on('click', '#apply-filters', function(){
            grid.getDataTable().ajax.reload();
        })
    }   

    return {

        //main function to initiate the module
        init: function () {
            handleTable();
        }

    };
}();