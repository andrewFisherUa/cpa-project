toastr.options = {
  "closeButton": true,
  "debug": false,
  "positionClass": "toast-top-right",
  "onclick": null,
  "showDuration": "500",
  "hideDuration": "500",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}

jQuery(document).ready(function(){
  StatIndex.init();
});

var StatIndex = function () {

    var grid;

    var handleTable = function () {

        grid = new Datatable();

        grid.init({
            src: $("#statindex_table"),
            loadingMessage: 'Загрузка...',
            dataTable: {
                "language": datatables_defaults.lang,
                "lengthMenu": [
                    [50, 100, 150, -1],
                    [50, 100, 150, "Все"] // change per page values here
                ],
                "pageLength": 50, // default record count per page
                "ajax": {
                    "url": "/ajax/get-offer-stat-index/", // ajax source
                    data : function (d) {
                        var o = {};

                        o.id = $('select[name="offer"] option:selected').val()

                        d.params = o;
                    }
                },
                "bSort" : false,
                fnDrawCallback: function(oSettings) {
                   //$("#statindex_table :checkbox").uniform();
                   handleMode();
                   $('input[name="epc_mode"][value!="no_data"]:checked, input[name="cr_mode"][value!="no_data"]:checked').closest("td").addClass("td-mark");
                },
            }
        });

        $(document).on('change', 'select[name=offer]', function(){
            grid.getDataTable().ajax.reload();
        })
    }

    var handleMode = function() {
        $(document).on('change', 'input[name="epc_mode"], input[name="cr_mode"]', function(){
            var row = $(this).closest("tr");
            var name = $(this).attr("name");
            var val = $(this).val();

            if ($(this).prop("checked") == true) {
                row.find('input[name='+name+'][value!='+val+']').prop("checked", false);            
            } else {
                row.find('input[name='+name+'][value=no_data]').prop("checked", true);  
            }
            
        })
    }

    var handleSave = function(){
        $(document).on('click', "#statindex_table .btn-save", function(){
            var row = $(this).closest('tr');

            var data = {
                epc_mode: row.find('input[name=epc_mode]:checked').val(),
                cr_mode: row.find('input[name=cr_mode]:checked').val(),
                epc: row.find('input[name=specific_epc]').val(),
                cr: row.find('input[name=specific_cr]').val(),
                id: $(this).data('id'),
            }

            $.ajax({
              url: '/ajax/save-offer-stat-index/',
              type: 'POST',
              data: $.param(data),
              dataType: "json",
              success: function(response){
                if (response.success) {
                    toastr.success("Сохранено!");
                    grid.getDataTable().ajax.reload();
                } else {
                    toastr.error("Ошибка при сохранении");
                }
              }
            })

            return false;
        })
    }

    return {

        //main function to initiate the module
        init: function () {
            handleTable();
            handleSave();
        }

    };
}();