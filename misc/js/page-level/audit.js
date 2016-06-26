jQuery(document).ready(function(){
  Audit.init();
});

var Audit = function () {

    var grid;

    var handleTable = function () {

        grid = new Datatable();

        grid.init({
            src: $("#datatable_audit"),
            loadingMessage: 'Загрузка...',
            dataTable: {
                "language": datatables_defaults.lang,
                "lengthMenu": [
                    [50, 100, 150, -1],
                    [50, 100, 150, "Все"] // change per page values here
                ],
                "pageLength": 50, // default record count per page
                "ajax": {
                    "url": "/ajax/get-audit/", // ajax source
                    data : function (d) {
                        var o = {};

                        $('.form-filter').each(function(){
                            o[$(this).attr('name')] = $(this).val()
                        })

                        o["show_important"] = +$('input[name=show_important]').is(':checked');

                        o["pages"] = {
                            "action" : $('input[name=include]:checked').val(),
                            "list" : $('textarea[name=pages]').val()
                        }

                        console.log(o);

                        d.params = o;
                    }
                },
                "bSort" : false,
                fnDrawCallback: function(oSettings) {
                    $('.date-picker').datepicker();
                    $('span.medium-priority').closest('tr').addClass('active');
                    $('span.high-priority').closest('tr').addClass('danger');
                },
            }
        });

        $(document).on('click', '#apply-filters', function(){
            grid.getDataTable().ajax.reload();
        })
    }

    var handleDetails = function(){
        $(document).on('click', '.show-details', function(){
            
            var data = {
                action: "get-details",
                aid: $(this).data("aid")
            }

            $.ajax({
              url: '/ajax/manage-audit/',
              type: 'POST',
              data: $.param( data ),
              dataType: "json",
              success: function( response ){
                $('#audit-details .modal-body').html(response.details);
                $('#audit-details').modal("show");
              }
            })

            return false
        })
    }

    var handleConfig = function(){
        $(document)
            .on('click', '#show-settings', function(){
                $("#audit-settings").slideToggle('slow');
            })

            .on('click', '#apply-settings', function(){
                grid.getDataTable().ajax.reload();
            })        
    }


    return {

        //main function to initiate the module
        init: function () {
            handleTable();
            handleDetails();
            handleConfig();
        }

    };
}();