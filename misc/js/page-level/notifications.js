jQuery(document).ready(function(){
  Notifications.init();
});

var Notifications = function () {

    var grid;

    var handleTable = function () {

        grid = new Datatable();

        grid.init({
            src: $("#datatable_notifications"),
            loadingMessage: 'Загрузка...',
            dataTable: {
                "language": datatables_defaults.lang,
                "lengthMenu": [
                    [50, 100, 150, -1],
                    [50, 100, 150, "Все"] // change per page values here
                ],
                "pageLength": 50, // default record count per page
                "ajax": {
                    "url": "/ajax/get-notifications/", // ajax source
                },
                "bSort" : false,
            }
        });
    }

    return {
        init: function () {
            handleTable();
        }

    };
}();