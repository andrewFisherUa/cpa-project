jQuery(document).ready(function(){
  Pages.init();
});

var Pages = function () {

  var handleFormFields = function(){

    $('input[name=geo]').click(function(){
      var geo = $(this).prop('checked');
      if (geo == true) {
        $('.item-content, .item-content .country_code').fadeIn();
      } else {
        $('.item-content:not(.item-content-first), .item-content .country_code').fadeOut();
      }
    })
  }

  var handleForm = function(){
    $(document).on('click', '#save[data-validation="true"]', function(e){
      e.preventDefault();
      var title = $('input[name="title"]').val(),
          link = $('input[name="link"]').val(),
          errors = [],
          errorBox = $('.alert');

      if (title == '') {
        errors.push("Введите заголовок страницы");
      }

      if (link == '') {
        errors.push("Введите ссылку на страницу");
      } else {
        var pattern = new RegExp('^[a-zA-Z0-9-_]+$',"i");
        if (!pattern.test(link)) {
          errors.push("Неправильный формат ссылки. Допустимые символы: 0-9, a-Z, _, -");
        } else {

          if (errors.length == 0) {
            var data = {
              action : "check-link",
              link : link,
              id : $('input[name="id"]').val()
            }
            $.ajax({
              url: '/ajax/manage-pages/',
              type: 'POST',
              data: $.param(data),
              dataType: "json",
              success: function( response ){
                if (response.linkIsAvailable == false) {
                  if (errorBox.html() == "") {
                    errorBox.html("Ссылка занята<br/>").fadeIn();
                  } else {
                    errorBox.append("<br/>Ссылка занята");
                  }
                } else {
                  $('#edit-page-form').submit();
                }
              }
            })
          }
        }
      }

      if (errors.length) {
        errorBox.html(errors.join("<br />")).fadeIn();
      }

    })
  }

  var handleDataTable = function(){
    if ($("#datatable_pages").length == 0) return;

    var grid = new Datatable();

    grid.init({
      src: $("#datatable_pages"),
      loadingMessage: 'Загрузка...',
      dataTable: {
          "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
          "language": datatables_defaults.lang,
          "pageLength": 10, // default record count per page
          "ajax": {
              "url": "/ajax/get-shop-pages/", // ajax source
          }
      },
    });
  }

  return {
      //main function to initiate the module
      init: function () {
        handleFormFields();
        handleForm();
        handleDataTable();
      }
  };
}();