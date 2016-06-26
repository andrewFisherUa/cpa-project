jQuery(document).ready(function(){
  Hold.init();
});

var Hold = function () {

  var handleAjax = function(data) {
    $.ajax({
      url: '/ajax/manage-hold/',
      type: "POST",
      dataType: 'json',
      data: $.param(data),
      success: function(result){
        if (data.action == "save-defaults") {
          $('#defaults-table-wrap').html(result.table);
        }

        if (data.action == "save-to-all") {
          $('#defaults-table-wrap').html(result.table);
          if ($('#select_webmaster option:selected').val() == "-1") {
            $('#webmaster-table-wrap').html(result.table);
          }
        }

        if (data.action == "get-webmaster-hold-values") {
          $('#webmaster-table-wrap').html(result.table);
        }

        if (data.action == "save-webmaster-hold") {
          $('#webmaster-table-wrap').html(result.table);
        }
      }
    })
  }

  var handleDefaults = function(){
    $(document)
      .on('click', '#save_default', function(){
        handleAjax({
          country_code : $('#select_country_1 option:selected').val(),
          target_id : $('#select_target_1 option:selected').val(),
          value : $('#hold_1').val(),
          action : "save-defaults"
        });
        return false;
      })
      .on('click', '#save_to_all', function(){
        if (confirm('Применить изменения ко всем пользователям?')){
          handleAjax({
            country_code : $('#select_country_1 option:selected').val(),
            target_id : $('#select_target_1 option:selected').val(),
            value : $('#hold_1').val(),
            action : "save-to-all"
          });

          if ($('#select_webmaster option:selected').val() != "-1") {
            handleAjax({
              user_id :$('#select_webmaster option:selected').val(),
              action : 'get-webmaster-hold-values'
            });
          }

        }
        return false;
      })
  }

  var handleWebmasters = function(){
    $(document)
      .on('change', '#select_webmaster', function(){
        handleAjax({
          user_id : $(this).find('option:selected').val(),
          action : 'get-webmaster-hold-values'
        });
      })

      .on('click', '#save_webmaster_hold', function(){
        if ($('#select_webmaster option:selected').val() == -1) {
          $('#webmaster-hold-form .alert').text("Необходимо выбрать вебмастера").fadeIn();
          return false;
        }

        handleAjax({
          user_id : $('#select_webmaster option:selected').val(),
          country_code : $('#select_country_2 option:selected').val(),
          target_id : $('#select_target_2 option:selected').val(),
          value : $('#hold_2').val(),
          action : "save-webmaster-hold"
        });

        return false;
      })
  }

  return {
      init: function() {
        handleDefaults();
        handleWebmasters();
      }
  };
}();