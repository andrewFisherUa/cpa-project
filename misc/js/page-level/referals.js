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
  Referals.init();
});

var Referals = function () {

  // Управление таблицей потоков
  var handleDatatable = function(){
    grid = new Datatable();

    grid.init({
      src: $("#referals_datatable"),
      loadingMessage: 'Загрузка...',
      dataTable: {
          "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
          "language": datatables_defaults.lang,
          "pageLength": 25, // default record count per page
          "ajax": {
              "url": '/ajax/get-referals/', // ajax source
          },
          "bSort" : false
      },
    });
  }

  var handleLinkCopy = function(){

    $('#referal-link1-btn').zclip({
      path: "/misc/plugins/jquery-zclip-master/ZeroClipboard.swf",
      copy: $('#referal-link1').val(),
      afterCopy: function(){
        toastr.success("Ссылка скопирована в буфер обмена");
      }
    })

    $('#referal-link2-btn').zclip({
      path: "/misc/plugins/jquery-zclip-master/ZeroClipboard.swf",
      copy: $('#referal-link2').val(),
      afterCopy: function(){
        toastr.success("Ссылка скопирована в буфер обмена");
      }
    })
  }

  return {
      init: function () {
        handleDatatable();
        handleLinkCopy();
      }
  };
}();