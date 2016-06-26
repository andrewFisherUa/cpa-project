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
  if (location.hash == "#streams") {
    $('#offer-tabs a[href="#flows"]').tab('show');
  }

  $(document)
    .on('change', '#postback-check', function(){
      if ($(this).prop('checked')) {
        $('#postback-link').attr('disabled', true);
        $('#postback-triggers-wrap').hide();
      } else {
        $('#postback-link').attr('disabled', false);
        $('#postback-triggers-wrap').show();
      }
    })

  Flows.init();
});


// Функции для создания, редактирования и просмотра потоков
var Flows = function () {

  var grid, wrap;
  // Задание переменных
  var initVars = function(){
    if ( $('#create-flow').length ) {
      view = "tabs";
      wrap = $('#create-flow');
    } else if ( $('#flowModal').length ) {
      view = "modal";
      wrap = $('#flowModal');
    } else {
      //single-page
      view = "single-page";
      wrap = $('#single-stream');
    }
  }

  // Обработка ajax запросов
  var handleAjax = function( data ) {
    $.ajax({
      url: '/ajax/manage-flows/',
      type: "POST",
      dataType: 'json',
      data: $.param(data),
      success: function(result){

        if (result.success.length && view == "single-page") {
          window.location = "/admin/flows";
          return false;
        }

        if (result.errors.length) {
          wrap.find('.alert-danger').html( result.errors.join("<br/>") ).fadeIn();
          $('html,body').animate({
            scrollTop: wrap.find('.alert-danger').offset().top - 80
          }, 300);
        }

        if (result.success.length) {
          wrap.find('.alert-success').html( result.success ).fadeIn();
          $('html,body').animate({
            scrollTop: wrap.find('.alert-success').offset().top - 80
          }, 300);
        }

        if ( data.action == "add-account" ) {
          if ( result.errors.length == 0 ) {
            $('#subaccount_name').val('');
            $('#subaccount_id').html( result.rows );
          }
        }

        if ( data.action == "edit" ) {
          wrap.find('.alert').fadeOut();
          wrap.find('.form-container').html( result.rows );
          wrap.find('select').select2({ width: '100%' });
          handleTargets();
          handleTouchSpin();
          $(":checkbox").uniform();
          if ( view == "tabs" ) {
            $('#offer-tabs a[href="#create-flow"]').text('Редактирование потока').tab('show');
          } else {
            if ( data.flow_id == 0 ) {
              wrap.find('.modal-title').text("Создание потока")
            } else {
              wrap.find('.modal-title').text("Редактирование потока")
            }
            wrap.modal('show');
            $("[data-toggle='tooltip']").tooltip();
          }

          handleLinkCopy();
        }

        if ( data.action == "save" ) {

          if (result.errors.length == 0) {
            if (view == "tabs" || view == "modal") {
              grid.getDataTable().ajax.reload();
            }

            if (view == "tabs") {
              $('#create-flow .alert, #flows .alert').fadeOut();
              $('#offer-tabs a[href="#create-flow"]').text('Создание потока');
              $('#create-flow .form-container').html( result.rows );
              $(':checkbox').uniform();

              if ( data.id != 0 ) {
                $('#offer-tabs a[href="#flows"]').tab('show');
              } else {
                $('#linkModal #flow_full_link').val(result.flow_link);
                handleLinkCopy();
                $('#linkModal').modal('show');
              }
            }

            if (view == "modal" || view == "single-page") {
              $('#flow_link_wrap').fadeIn();
              $('#flow_full_link').val(result.flow_link);
              handleLinkCopy();
            }
          }
        }

		if (data.action == "get-preview") {
			$('a[data-action="get-preview"][data-content="'+data.content_id+'"]').attr('href', result.link).fadeIn();
		}
	  }
    })
  }

  var handleTargets = function(){
    $(document).on('change', '#table_prices .target', function(){
      var code = $(this).data('code'),
          option = $(this).find('option:selected'),
          price = parseInt(option.data("price")),
          commission = parseInt(option.attr("data-webmaster_commission")),
          max_profit = option.data('max_profit');

      if (option.val() == 1) {
        disabled = option.data("editable") != 1
      } else {
        disabled = false;
      }

      $('#table_prices').find('.recommended[data-code="'+code+'"], .price[data-code="'+code+'"]').text(price);
      $('#table_prices')
        .find('.profit[data-code="'+code+'"]')
        .val(commission)
        .prop("disabled", disabled)
        .trigger("touchspin.updatesettings", {max: max_profit});
    })
  }

  // Потоки
  var handleFields = function(){
    // Подгрузка лендингов подключенных к офферу
    $('body')
    .on('change', '#flow_oid', function(){
      var data = {
        offer_id: $(this).val(),
        action: "get-landings"
      }

      $.ajax({
        url: '/ajax/manage-flows/',
        type: "POST",
        dataType: 'json',
        data: $.param(data),
        success: function(result){
          $('#prices-wrap').html( result.prices );
          $('#landings-wrap table>tbody').html(result.rows);
          $('#landings-wrap').fadeIn();
          $('#blogs-wrap').fadeOut();
          $('#blogs-wrap table>tbody').html("");
          $('input[name="flow_landing[]"]').uniform();
          handleTargets();
          handleTouchSpin();
        }
      })
      })
    // Очистка полей
    .on('click', 'a[data-action="clear"]', function(){
      var target = $(this).data("target");
      $('#'+target).val("");
      return false;
    })
    // Подгрузка блогов подключенных к лендингу
    .on('change', 'input[name="flow_landing[]"]', function(){
      var data = {
        landing_id: $(this).val(),
        action: "get-blogs"
      }

    $('#flow_blog_link, #comebacker-wrap').fadeOut();

	  $.ajax({
        url: '/ajax/manage-flows/',
        type: "POST",
        dataType: 'json',
        data: $.param(data),
        success: function(result){
          $('#blogs-wrap table>tbody').html(result.rows);
          $('input[name="flow_blog[]"]').uniform();
          $('#blogs-wrap').fadeIn();
        }
      })
    })
  }

  // Создание нового потока
  var handleCreateFlow = function(){
    // Добавление нового потока
    $('body').on('click', 'a.add-item[href="#flowModal"]', function(){
      var data = {
        action: "edit",
        flow_id: 0
      }
      handleAjax( data );
    })
  }

  var applyFilters = function(){
    $('.table-group-action-input').each(function(){
      var actionValue = $(this).find('option:selected').val();
      if ( actionValue != '-1' ) {
        var actionName = $(this).attr('name');
        grid.setAjaxParam(actionName, actionValue);
      }
    })
  }

  var handleAdminDatatable = function(){
    if (!$("#datatable_streams").length) {
      return false;
    }

    grid = new Datatable();
    grid.init({
        src: $("#datatable_streams"),
        loadingMessage: 'Загрузка...',
        dataTable: {
            "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
            "language": datatables_defaults.lang,
            "pageLength": 25, // default record count per page
            "ajax": {
                "url": '/ajax/get-streams/',
            },
            drawCallback: function(oSettings) { // run some code on table redraw
              $('select.table-group-action-input').select2({width: '300px'});
              $('#datatable_streams .copy-link').each(function(){
                var link = $('#datatable_streams .link[data-id="'+$(this).data('id')+'"]').attr('href');
                var id = $(this).attr('id');
                $('#'+id).zclip({
                  path: "/misc/plugins/jquery-zclip-master/ZeroClipboard.swf",
                  copy: link,
                  afterCopy: function(){
                    toastr.success("Ссылка скопирована в буфер обмена");
                  }
                });
              })
          },
        },
    });

    grid.getDataTable().on('page.dt', function () {
        applyFilters()
    });

     // handle group actionsubmit button click
    grid.getTableWrapper()
      // Обработка изменения значения фильтров
      .on('change', '.table-group-action-input', function () {
        applyFilters();
        grid.getDataTable().ajax.reload();
        grid.clearAjaxParams();
      })
      // Сброс фильтров
      .on('click', '.reset-filters', function () {
        $('.table-group-action-input').each(function(){
          $(this).find('option:first-child').prop('selected', true);
        })
        grid.getDataTable().ajax.reload();
        grid.clearAjaxParams();
      })

      // Редактирование
      .on('click', '.edit-item', function(){
        var data = {
          flow_id : $(this).data('id'),
          offer_id : $(this).data('oid'),
          action: 'edit'
        }

        handleAjax( data );
      })
  }

  // Управление таблицей потоков
  var handleDatatable = function(){
    if (!$("#datatable_flows").length) {
      return false
    }
    grid = new Datatable();
    grid.init({
        src: $("#datatable_flows"),
        loadingMessage: 'Загрузка...',
        dataTable: {
            "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
            "language": datatables_defaults.lang,
            "pageLength": 10, // default record count per page
            "ajax": {
                "url": '/ajax/get-user-flows/', // ajax source
                 data: function ( d ) {
                  d.user_id = $('#datatable_flows').data('uid');
                  d.oid = $('#datatable_flows').data('oid');
                  $('.table-filter').each(function(){
                    var actionValue = $(this).val();
                    var actionName = $(this).attr('name');
                    if ( actionValue ) {
                      d[actionName] = actionValue;
                    }
                  })
                },
            },
            drawCallback: function(oSettings) { // run some code on table redraw
              $('#datatable_flows .copy-link').each(function(){
                var link = $('#datatable_flows .link[data-id="'+$(this).data('id')+'"]').attr('href');
                var id = $(this).attr('id');
                $('#'+id).zclip({
                  path: "/misc/plugins/jquery-zclip-master/ZeroClipboard.swf",
                  copy: link,
                  afterCopy: function(){
                    toastr.success("Ссылка скопирована в буфер обмена");
                  }
                });
              })
          },
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

    // handle group actionsubmit button click
    grid.getTableWrapper()
      // Удаление
      .on('click', '.remove-item', function(){
        var data = {
          id : $(this).data('id'),
          action: 'remove'
        }
        if (confirm("Вы действительно хотите удалить поток?")) {
           handleAjax( data );
           grid.getDataTable().ajax.reload( function(){
              if ( $('#datatable_flows .dataTables_empty').length ) {
                $('#flows .alert').fadeIn();
              }
           });
        }
      })

      // Редактирование
      .on('click', '.edit-item', function(){
        var data = {
          flow_id : $(this).data('id'),
          offer_id : $(this).data('oid'),
          action: 'edit'
        }

        handleAjax( data );
      })
      // Обработка изменения значения фильтров
      .on('change', '.table-filter', function () {
        grid.getDataTable().ajax.reload();
        grid.clearAjaxParams();
      })
      // Сброс фильтров
      .on('click', '.reset-filters', function () {
        $('.table-filter').each(function(){
          if ($(this).hasClass('select2me')){
            $(this).select2("val", "");
          } else {
            $(this).val('0');
          }
        })
        grid.getDataTable().ajax.reload();
        grid.clearAjaxParams();
      })
  }

  // Сохранение потока
  var handleSaveFlow = function(){
    $('body').on('click', '#save-flow', function(){
      var errors = [],
          data = {
            id : $('#flow_id').val(),
            name : $('#flow_name').val(),
            key : $('#flow_key').val(),
            space : $('#flow_space').val(),
            user_id : $('#flow_uid').val(),
            landing_id : $('input[name="flow_landing[]"]:checked').val(),
            subaccount_id : $('#subaccount_id').val(),
            comebacker : $('#comebacker').is(":checked"),
            subid1 : $('#subid1').val(),
            subid2 : $('#subid2').val(),
            subid3 : $('#subid3').val(),
            subid4 : $('#subid4').val(),
            subid5 : $('#subid5').val(),
            yandex_id : $('#yandex_id').val(),
            google_id : $('#google_id').val(),
            mail_id : $('#mail_id').val(),
            landing_alias : $('#landing_alias').val(),
            blog_alias : $('#blog_alias').val(),
            redirect_traffic : $('#redirect_traffic').val(),
            trafficback : $('#trafficback').val(),
            postback: {},
            use_global_postback: +$('#postback-check').prop('checked'),
            prices: [],
            action: 'save'
          }

        if ($('#postback-check').prop('checked') == false) {
          data.postback = {
            use_global_postback: 0,
            url : $('#postback-link').val(),
            send_on_create : +$('#postback-send-on-create').prop('checked'),
            send_on_confirm : +$('#postback-send-on-confirm').prop('checked'),
            send_on_cancel : +$('#postback-send-on-cancel').prop('checked')
          }

          if (data.postback.url != "" &&
              data.postback.send_on_create == 0 &&
              data.postback.send_on_confirm == 0 &&
              data.postback.send_on_cancel == 0) {
            errors.push("Необходимо выбрать вариант отправки постбек");
          }
        }

      if ($('input[name="flow_blog[]"]:checked').length) {
        data.blog_id = $('input[name="flow_blog[]"]:checked').val();
      }

      data.offer_id = $('#flow_oid').is('select') ? $('#flow_oid option:selected').val() : $('#flow_oid').val();

      $('#table_prices tbody tr').each(function(index){
        data.prices[index] = {
          country_code : $(this).find('.profit').data('code'),
          profit : $(this).find('.profit').val(),
          target_id : $(this).find('.target option:selected').val()
        }
      })

      if ( !data.space ) {
        errors.push("Необходимо выбрать источник трафика");
      }

      if ( data.name == "" ) {
        errors.push("Введите название потока");
        console.log(data);
      }

      if ( data.landing_id === undefined ) {
        errors.push("Необходимо выбрать лендинг");
      }

      if (data.landing_alias != "" && (data.landing_alias == data.blog_alias)) {
        errors.push("Псевдоними лендинга и блога не могут быть одинаковыми");
        $('#blog_alias_group, #landing_alias_group').addClass('has-error');
      }

      if (data.landing_alias != "") {
        reg = new RegExp('^[a-zA-Z0-9_\.\-]+$',"i");
        if (!reg.test(data.landing_alias)) {
           errors.push("Недопустимые символы в псевдониме лендинга");
           $('#landing_alias_group').addClass('has-error');
        }
      }

      if (data.blog_alias != "") {
        reg = new RegExp('^[a-zA-Z0-9_\.\-]+$',"i");
        if (!reg.test(data.blog_alias)) {
           errors.push("Недопустимые символы в псевдониме блога");
           $('#blog_alias_group').addClass('has-error');
        }
      }

      if ( errors.length ) {
        wrap.find('.alert-danger').html(errors.join("<br/>")).fadeIn();
        $('html,body').animate({
          scrollTop: wrap.find('.alert-danger').offset().top - 80
        }, 300);
        return false;

      } else {
        wrap.find('.alert-danger').fadeOut();
        wrap.find('.has-error').removeClass('has-error');
      }

      handleAjax( data );
    })

    $('#linkModal').on('hidden.bs.modal', function (e) {
      $('#offer-tabs a[href="#flows"]').tab('show');
    })
  }

  // Субаккаунты и субID
  var handleSubs = function(){
    $('body')
      // Добавление субаккаунта
      .on('click', '#add-subaccount', function(){
        var data = {
          user_id : $('#flow_uid').val(),
          subaccount: $('#subaccount_name').val(),
          action: "add-account"
        }
        handleAjax( data );
      })
      // Сделать кнопку добавления субаккаунта доступной если поле #subaccount_name не пустое
      .on('keyup', '#subaccount_name', function(){
        $('#add-subaccount').attr("disabled", $(this).val() == '' );
      })
      // Показать или скрыть поля субаккаунта и субID
      .on('change', 'input[data-toggle="show-block"]', function(){
        var blockID = $(this).data('block');
        if ( $(this).is(":checked") ) {
          $('#'+blockID).fadeIn();
        } else {
          $('#'+blockID).fadeOut();
        }
      })
  }

  // Копирование ссылки в буфер
  var handleLinkCopy = function(){

    $('#copy-link-btn').zclip({
      path: "/misc/plugins/jquery-zclip-master/ZeroClipboard.swf",
      copy: $('#flow_full_link').val(),
      afterCopy: function(){
        toastr.success("Ссылка скопирована в буфер обмена");
      }
    })

    $('#flowModal').on('shown.bs.modal', function () {
      if ($('#flowModal #copy-link-btn').length) {
        $('#copy-link-btn').zclip({
          path: "/misc/plugins/jquery-zclip-master/ZeroClipboard.swf",
          copy: $('#flow_full_link').val(),
          afterCopy: function(){
            $('#flowModal #flow_link_wrap .alert-info').fadeIn();
          }
        });
      }
    })

    $('#linkModal').on('shown.bs.modal', function () {
      if ($('#linkModal #copy-link-btn').length) {
        $('#linkModal #copy-link-btn').zclip({
          path: "/misc/plugins/jquery-zclip-master/ZeroClipboard.swf",
          copy: $('#linkModal #flow_full_link').val(),
          afterCopy: function(){
            $('#linkModal #flow_link_wrap .alert-info').fadeIn();
          }
        });
      }
    })

  }

  var handleResetRadiobuttons = function(){
    $(document).on('click', '.btn-reset', function(){
      var target = $(this).attr('data-target');
      $('input[name="'+target+'"]').prop('checked', false).uniform();
      if (target == "flow_landing[]") {
        $('#blogs-wrap').fadeOut();
        $('#blogs-wrap table>tbody').html("");
      }
    })
  }

  var handleTouchSpin = function() {
    $('.profit').each(function(){
      var min = 1, id = $(this).attr('id'),
          currency = $(this).data('currency'),
          code = $(this).data('code'),
          max = $('#table_prices .target[data-code="'+code+'"] option:selected').data("max_profit");

      if ( code == 'by' ) {
        min = 100;
      }

      $('#'+id).TouchSpin({
          buttondown_class: 'btn green',
          buttonup_class: 'btn green',
          maxboostedstep: 10000000,
          min: min,
          max: max,
          stepinterval: 1,
          postfix: currency
      });
    })

    $('body').on('change', '.profit', function(){
      var code = $(this).data('code');
      var target = $('#table_prices .target[data-code="'+code+'"] option:selected');
      var price = parseInt(target.data("price")) - parseInt(target.attr("data-webmaster_commission")) + parseInt($(this).val());
      $('#table_prices .price[data-code="'+code+'"]').text(price);
    })
  }

  var handlePostbackForm = function(){
    $(document)
      .on('click', '#save-postback', function(){
        var form = $('#postback-form');

        var data = {
          "action" : "save",
          "url" : form.find('input[name=postback_url]').val(),
          "user_id" : +form.find('input[name=user_id]').val(),
          "send_on_create" : +form.find('input[name=send_on_create]').is(':checked'),
          "send_on_confirm" : +form.find('input[name=send_on_confirm]').is(':checked'),
          "send_on_cancel" : +form.find('input[name=send_on_cancel]').is(':checked'),
        }

        console.log(data);

        $.ajax({
          url: '/ajax/manage-postback/',
          type: "POST",
          dataType: 'json',
          data: $.param(data),
          success: function(result){
              if (result.errors.length) {
                $('#postback-form .alert').removeClass('alert-success').addClass('alert-danger').html(result.errors.join("<br />")).fadeIn();
              } else {
                $('#postback-form .alert').removeClass('alert-danger').addClass('alert-success').html("Ссылка сохранена").fadeIn();
              }
          }
        })

        return false;
      })
      .on('click', '#reset-postback-form', function(){
        var form = $('#postback-form');

        form.find('input[name=postback_url]').val("");
        form.find('input[name=send_on_create]').prop('checked', false).uniform();
        form.find('input[name=send_on_confirm]').prop('checked', false).uniform();
        form.find('input[name=send_on_cancel]').prop('checked', false).uniform();

        return false;
      })
  }

  return {
      init: function () {
        initVars();
        handleFields();
        handleSaveFlow();
        handleSubs();
        handleDatatable();
        handleAdminDatatable();
        handleCreateFlow();
        handleTouchSpin();
        handleTargets();
        handleLinkCopy();
        handleResetRadiobuttons();
        handlePostbackForm();
      }
  };
}();