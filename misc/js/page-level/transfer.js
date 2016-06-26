jQuery(document).ready(function(){
  Transfer.init();
});

var Transfer = function () {

  // Управление таблицей потоков
  var handleDatatable = function(){
    grid = new Datatable();

    grid.init({
      src: $("#datatable_transfer"),
      loadingMessage: 'Загрузка...',
      dataTable: {
          "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
          "language": datatables_defaults.lang,
          "pageLength": 15, // default record count per page
          "ajax": {
              "url": '/ajax/get-transfers/', // ajax source
          },
          drawCallback: function(oSettings) { // run some code on table redraw
            $(':checkbox').uniform();
            $('.date-picker').datepicker();
            $('#datatable_transfer .money').mask('# ### ### ### ###.00', {reverse: true});
            handleStatus();
        }
      },
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

  var handleStatus = function(){
    $('.status').each(function(){
      var source = [
            {value: "processing", text: "processing"},
            {value: "approved", text: "approved"},
            {value: "canceled", text: "canceled"},
          ],

          id = $(this).data('subject');

      $('#status'+id).editable({
        inputclass: 'form-control',
        source: source,
        autotext: 'always',
        params: { 'id': id },
        url: '/ajax/change-transaction-status/',
        success: function(result){
          $('#status'+id).parent().removeAttr('class').addClass('label label-sm label-'+result);
        }
    });

    })
  }

  return {
      init: function () {
        handleDatatable();
      }
  };
}();