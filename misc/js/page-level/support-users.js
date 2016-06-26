jQuery(document).ready(function(){
  Users.init();
});

var Users = function () {

  var handleDatatable = function(){
    grid = new Datatable();

    grid.init({
      src: $("#datatable_users"),
      loadingMessage: 'Загрузка...',
      dataTable: {
        "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
        "language": datatables_defaults.lang,
        "pageLength": 25, // default record count per page
        "ajax": {
          "url": '/ajax/get-support-users/', // ajax source
          data : function (d) {
            var data = {};

            $('.form-filter').each(function(){
              var n = $(this).attr('name');
              if ($(this).is('select')) {
                data[n] = $(this).find('option:selected').val();
              } else {
                data[n] = $(this).val();
              }
            })

            d.f = data;
          }
        },
        drawCallback: function(oSettings) { // run some code on table redraw
          $(':checkbox').uniform();
          $('.date-picker').datepicker();
          handleStatus();
        },
        "bSort" : false
      },
    });

    // handle group actionsubmit button click
      grid.getTableWrapper()
        .on('change', 'select.form-filter, .date-picker input', function(){
            grid.getDataTable().ajax.reload();
        })
        .on('keyup keypress', '.form-filter:text', function(){
            grid.getDataTable().ajax.reload();
        })
  }

  var handleStatus = function(){
    $('.status').each(function(){

      var id = $(this).data('subject');


      var source = [{value: 0, text: 'На модерации'},
                   {value: 1, text: 'Не найден'},
                   {value: 2, text: 'В ожидании'},
                   {value: 3, text: 'Добавлен'}];

      $('#status'+id).editable({
        inputclass: 'form-control',
        source: source,
        autotext: 'always',
        params: { 'id': id },
        url: '/ajax/change-skype-status/',
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