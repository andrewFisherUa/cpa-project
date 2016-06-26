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
  Spaces.init();

  if ($('.context-platform').length) {
    $('input[name="space[source]"]').val($('.context-platform > li[data-checked=1]').attr("data-id"));
  }

  $(document).on('click', '.context-platform > li', function(){
    $('.context-platform > li').attr('data-checked', 0);
    $(this).attr('data-checked', 1);
    $('input[name="space[source]"]').val($(this).attr("data-id"));
  })
});

var Spaces = function () {

  var handleUserSpacesDatatable = function(){
    if (!$("#datatable_user_spaces").length) {
      return false;
    }

    grid = new Datatable();

    grid.init({
      src: $("#datatable_user_spaces"),
      loadingMessage: 'Загрузка...',
      dataTable: {
        "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
        "language": datatables_defaults.lang,
        "pageLength": 25, // default record count per page
        "ajax": {
            "url": '/ajax/get-user-spaces/', // ajax source
        },
        "bSort" : false
      },
    });

    grid.getDataTable()
      .on('click', '.btn-remove', function(){
        if (!confirm("Удалить источник трафика?")) {
          return false;
        }

        var data = {
          id: $(this).data('id'),
          action: "remove"
        }

        $.ajax({
          url: '/ajax/manage-spaces/',
          type: 'POST',
          dataType: 'json',
          data: $.param(data),
          success: function(result){
            if (result.error == "") {
              grid.getDataTable().ajax.reload();
            } else {
              toastr.error(result.error)
            }
          }
        })
      })

      .on('page.dt', function () {
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
      })
  }

  var applyFilters = function(grid){
    var filters = {}, val;
    $('.tbl-filter').each(function(){
      if ($(this).is('select')) {
        val = $(this).find('option:selected').val();
      } else {
        val = $(this).val();
      }

      if (val != '-1' && val != "") {
        filters[$(this).attr('name')] = val;
      }
    })

    $('.date-picker > input').each(function(){
      val = $(this).val();
      if (val != "") {
        filters[$(this).attr('name')] = val;
      }
    })

    console.log(filters)

    grid.setAjaxParam("filters", filters);
    grid.getDataTable().ajax.reload();
  }

  var handleSpacesDatatable = function(){
    if (!$("#datatable_spaces").length) {
      return false;
    }

    $('.date-picker').datepicker();

    $('select.tbl-filter').select2({
      width: '200px'
    })

    grid = new Datatable();

    grid.init({
      src: $("#datatable_spaces"),
      loadingMessage: 'Загрузка...',
      dataTable: {
        "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
        "language": datatables_defaults.lang,
        "pageLength": 25, // default record count per page
        "ajax": {
            "url": '/ajax/get-spaces/', // ajax source
        },
       "bSort" : false,
        drawCallback: function(oSettings) { // run some code on table redraw
          handleStatus();
          $("[data-toggle='tooltip']").tooltip();
        }
      },
    });

    grid.getDataTable()
      .on('page.dt', function () {
        applyFilters(grid);
      })

    $(document)
      // Применение фильтров к таблице
      .on('click', '.submit-filters', function(){
        applyFilters(grid);
      })

      // Очистка фильтров
      .on('click', '.reset-filters', function(){
        $('select.tbl-filter').each(function(){
          $(this).select2('val', "-1");
        })
        $('input.tbl-filter').each(function(){
          $(this).val("");
        })
        $('.date-picker').each(function(){
          $(this).datepicker('setDate', null);
        })
        grid.setAjaxParam("filters", []);
        grid.getDataTable().ajax.reload();
      })

      .on('click', '.btn-view', function(){
        var data = {
          id : $(this).data('id'),
          action : "get-view"
        }

        $.ajax({
          url: '/ajax/manage-spaces/',
          type: 'POST',
          dataType: 'json',
          data: $.param(data),
          success: function(result){
            $('#space-modal .modal-title').text(result.name);
            $('#space-modal .modal-body').html(result.rows);
            $('#space-modal').modal("show");
          }
        })

      })
  }

  var handleNote = function(){
    $(document)
      .on('keydown', '#note', function(){
        if ($('#save-note').text() != 'Сохранить') {
          $('#save-note').text('Сохранить');
        }
      })

      .on('click', '#save-note', function(){
        var data = {
          'id' : $(this).data('id'),
          'text' : $('#note').val(),
          'action' : 'save-note'
        }

        $.ajax({
            url: "/ajax/manage-spaces/",
            dataType: "json",
            type: "POST",
            data: $.param(data),
            success: function(r) {
              if (r.success) {
                $('#save-note').html('<i class="fa fa-check"></i>&nbsp;Сохранено');
              }
            }
        })

        return false;
      })
  }

  var handleValidate = function(){
    if (!$("#space-form").length) {
      return false;
    }

    $.validator.addMethod("regx", function(value, element, regexpr) {
        return regexpr.test(value);
    }, "Пожалуйста введите правильный url");

    var rules = {
      /*
      "space[desc]" : {
          required: true,
          minlength: 70
        },*/
        "space[name]" : {
          required: true,
          maxlength: 30
        },
    }

    if ($('input[name="space[url]"]').length) {
      rules["space[url]"] = {
        required: true,
        regx: /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/
      }
    }

    /*
    if ($('input[name="space[meta][lang][]"]').length) {
      rules["space[meta][lang][]"] = {
        required: true,
        minlength: 1
      }
    }
    */


    $("#space-form").validate({
      "rules" : rules,
      "messages": {
        "space[meta][lang][]": {
          required: "Необходимо выбрать язык",
          minlength: "Необходимо выбрать язык"
        }
      },
      invalidHandler: function(event, validator) {
        $('#space-form .form-group').removeClass('has-error');
        var f;
        for (v in validator.invalid) {
          f = $('*[name="'+v+'"]').closest(".form-group");
          f.addClass('has-error');
          /*
          if (v == "space[meta][lang][]") {
            f.find("label + div").append("<label id='"+v+"-error' class='error' for='"+v+"'>Необходимо выбрать язык</label>")
          }
          */
        }

        $('html,body').animate({
          scrollTop: $('.form-group.has-error:first').offset().top - 80
        }, 300);
      }
    });
  }

  var handleStatus = function(){
    $('.status').each(function(){

      var id = $(this).data('subject'), val = $(this).attr('data-value');

      var source = [
        {value: 'processing', text: 'Не подтверждено'},
        {value: 'moderation', text: 'На модерации'},
        {value: 'canceled', text: 'Отклонен'},
        {value: 'approved', text: 'Активный'}
      ];

      var temp = source[0];
      for (var i=0; i<source.length; i++){
        if (source[i].value == val) {
          source[0] = source[i];
          source[i] = temp;
          break;
        }
      }

      $('#status'+id).editable({
        inputclass: 'form-control',
        source: source,
        autotext: 'always',
        params: { 'id': id },
        url: '/ajax/change-space-status/',
        success: function(result){
          $('#status'+id).parent().removeAttr('class').addClass('label label-sm label-'+result);
        }
    });

    })
  }

  var handleConfirm = function(){
    $(document).on('click', '#confirm-space', function(){
       var data = {
        id: $('#confirm-space').data("id"),
        action: "confirm"
      }

      var name = $('#space-name').text();

      $.ajax({
        url: "/ajax/manage-spaces/",
        dataType: "json",
        type: "POST",
        data: $.param(data),
        success: function(r) {
          if (r.success) {
            toastr.success("Источник трафика '"+name+"' успешно подтвержден и отправлен на модерацию.", "Подтверждение источника").css("width", "450px");
          } else {
            toastr.error("Источник трафика '"+name+"' не подтвержден. Проверьте правильно ли вы выполнили все условия добавления источника и нажмите 'Подтвердить'", "Подтверждение источника").css("width", "450px");
          }
        }
      })
    })
  }

  return {
      init: function () {
        handleUserSpacesDatatable();
        handleSpacesDatatable();
        handleValidate();
        handleConfirm();
        handleNote();
      }
  };
}();