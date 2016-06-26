jQuery(document).ready(function(){
  Targets.init();
});

var Targets = function () {

  var handleFilters = function(){
    $(document)
      // Обработка изменения значения фильтров
      .on('change', '.filter', function(){
        var data = {
          action : "get-selection",
          filter : $(this).data('filter'),
          ids : []
        }

        $('.filter[data-filter="'+data.filter+'"]:checked').each(function(){
          data.ids.push($(this).val())
        })

        handleAjax(data);
      })

      //
      .on('click', '.check-all', function(){
        var checked = $(this).prop("checked"),
            target = $(this).data("target");
        $('#'+target+' :checkbox').prop("checked", checked).uniform();

        if ($('#'+target+' .filter').length) {
          var data = {
            action : "get-selection",
            filter : $('#'+target+' .filter').data('filter'),
            ids : []
          }

          $('#'+target+' .filter[data-filter="'+data.filter+'"]:checked').each(function(){
            data.ids.push($(this).val())
          })

          handleAjax(data);
        }
      })
      //
      .on('change', '.search', function(){
        var val = $(this).find('option:selected').val(),
            target = $(this).data("target"),
            offset = $('#'+target+' :checkbox[value="'+val+'"]').offset().top - $('#'+target).offset().top;

        var  x = $('#'+target).scrollTop() + offset;
        $('#'+target).slimScroll({ scrollTo: x, animate: true });
      })
  }

  var handleSave = function(){
    $(document).on('click', '#save', function(){
      var data = {
        action : "save",
        filter : $(this).data('filter'),
        ids : [],
        result : []
      }

      $('.filter[data-filter="'+data.filter+'"]:checked').each(function(){
        data.ids.push($(this).val())
      })

      $('.result[data-filter="'+data.filter+'"]:checked').each(function(){
        data.result.push($(this).val())
      })

      handleAjax(data);
    })
  }

  var handleAjax = function(data){
    $.ajax({
      url: '/ajax/manage-user-targets/',
      type: 'POST',
      dataType: 'json',
      data: $.param(data),
      success: function(response){
        if (data.action == "save"){
          ShowPreload("Сохранено");
        }
        if (data.action == "get-selection") {
          $('.result[data-filter="'+data.filter+'"]').prop("checked", false).uniform();
          if (response.ids.length) {
            for (var i=0; i<response.ids.length; i++) {
              $('.result[data-filter="'+data.filter+'"][value="'+response.ids[i]+'"]').prop("checked", true).uniform();
            };
          }
        }
      }
    })
  }

  return {
      init: function () {
        handleFilters();
        handleSave();
      }
  };
}();