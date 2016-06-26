jQuery(document).ready(function(){
  Rules.init();
});

var Rules = function () {

  var handleOfferSelect = function(){
    $(document).on('change', '#rules-frm #offer', function(){

      $('#rules-frm #save-rules').attr('data-action', 'save');

      if ($('#rules-frm .alert').is(':visible')) {
        $('#rules-frm .alert').fadeOut();
      }

      handleAjax({
        id : $(this).find('option:selected').val(),
        action : "get-rules"
      });
    })

  }

  var handleSave = function(){
    $(document)
      // Сохранение правил
      .on('click', '#save-rules', function(){

        handleAjax({
          id : $('#rules-frm #offer option:selected').val(),
          text : CKEDITOR.instances["rules"].getData(),
          show_rules: +$('#rules-frm input[name="switch"]').prop("checked"),
          action : $(this).attr('data-action')
        });

        return false;
      })

      // Сохранение правил для всех
      .on('click', '#reset', function(){
        handleAjax({
          text : CKEDITOR.instances["rules"].getData(),
          action : "reset"
        });
        return false;
      })

      // Восстановление текста по умолчанию
      .on('click', '#recovery-rules', function(){
         handleAjax({
          action : "recovery-text"
        });
        return false;
      })
  }

  var handleAjax = function(data){
    $.ajax({
      url: '/ajax/manage-offer-rules/',
      type: 'POST',
      dataType: 'json',
      data: $.param(data),
      success: function(response){

        if (data.action == "get-rules") {
          CKEDITOR.instances["rules"].setData(response.text);
          if (response.special) {
            $('#rules-frm #recovery-rules').fadeIn();
          } else {
            $('#rules-frm #recovery-rules').fadeOut();
          }

          if (data.id == 0) {
            $('#rules-frm #reset').fadeIn();
            $('#rules-frm #switch-wrap').fadeOut();
          } else {

            var checked = response.show_rules == 1;
            $('#rules-frm input[name="switch"]').prop("checked", checked).uniform();
            $('#rules-frm #reset').fadeOut()
            $('#rules-frm #switch-wrap').fadeIn();
          }
        }

        if (data.action == "recovery-text") {
          CKEDITOR.instances["rules"].setData(response.text);
          $('#rules-frm #recovery-rules').fadeOut();
          $('#rules-frm #save-rules').attr('data-action', 'recovery');
        }

        if (data.action == "save" || data.action == "recovery" || data.action == "reset") {
          if (response.success) {
            $('#rules-frm .alert')
              .removeClass('alert-danger')
              .addClass('alert-success')
              .text("Сохранено")
              .fadeIn();
          } else {
            $('#rules-frm .alert')
              .removeClass('alert-success')
              .addClass('alert-danger')
              .text("Ошибка при сохранении")
              .fadeIn();
          }
        }

        if (data.action == "recovery") {
          $('#rules-frm #recovery-rules').fadeIn();
          $('#rules-frm #save-rules').attr('data-action', 'save');
        }
      }
    })
  }

  return {
      //main function to initiate the module
      init: function () {
        handleOfferSelect();
        handleSave();
      }
  };
}();