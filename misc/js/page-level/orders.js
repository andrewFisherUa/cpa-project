jQuery(document).ready(function(){
  Orders.init();
});

var Orders = function () {

    var handleOrders = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#datatable_orders"),
            loadingMessage: 'Загрузка...',
            dataTable: {
                "language": datatables_defaults.lang,
                "lengthMenu": [
                    [10, 25, 50, 100, 150, -1],
                    [10, 25, 50, 100, 150, "Все"] // change per page values here
                ],
                "pageLength": 25, // default record count per page
                "ajax": {
                    "url": "/ajax/get-orders/", // ajax source
                },
                fnDrawCallback: function( oSettings ) {
                    $("[data-toggle='tooltip']").tooltip();
                    $(":checkbox").uniform();
                    $('.date-picker').datepicker();
                },
            }
        });

        grid.getDataTable().on('page.dt', function () {
            $('.table-group-action-input').each(function(){
                var actionName = $(this).attr('name');
                if ($(this).is('select')) {
                    var actionValue = $(this).find('option:selected').val();
                    if ( actionValue != '-1' ) {
                      grid.setAjaxParam(actionName, actionValue);
                    }
                } else {
                    grid.setAjaxParam(actionName, $(this).val());
                }
            })
        });
    }

    return {

        //main function to initiate the module
        init: function () {
            handleOrders();
        }

    };
}();