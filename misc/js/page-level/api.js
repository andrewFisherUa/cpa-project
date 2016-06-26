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
  Api.init();
});

var Api = function () {

  var handleApiKeysTable = function(){

    if (!$("#hash_table").length) {
      return false;
    }

    hashTable = new Datatable();

    hashTable.init({
      src: $("#hash_table"),
      loadingMessage: 'Загрузка...',
      dataTable: {
        "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
        "language": datatables_defaults.lang,
        "pageLength": 25, // default record count per page
        "ajax": {
          "url": '/ajax/get-api-hash/', // ajax source
          data : function (d) {
            var data = {};

            $('#hash_table .form-filter').each(function(){
              var n = $(this).attr('name');
              if ($(this).is('select')) {
                data[n] = $(this).find('option:selected').val();
              } else {
                data[n] = $(this).val();
              }
            })

            d.params = data;
          }
        },
        drawCallback: function(oSettings) { // run some code on table redraw
          handleStatus();
        },
        "bSort" : false
      },
    });

    // handle group actionsubmit button click
    $("#hash_table")
      // Фильтры
      .on('change', 'select.form-filter, .date-picker input', function(){
          hashTable.getDataTable().ajax.reload();
      })
      .on('keyup keypress', '.form-filter:text', function(){
          hashTable.getDataTable().ajax.reload();
      })
  }

  var handleStatus = function(){
    $('.status').each(function(){

      var source, id = $(this).data('subject');

      $('#status'+id).editable({
        inputclass: 'form-control',
        source: [
          {value: "moderation", text: 'Модерация'},
          {value: "accepted", text: 'Одобрен'},
          {value: "refused", text: 'Отклонен'}
        ],
        autotext: 'always',
        params: { 
          uid: id,
        },        
        url: '/ajax/change-api-request-status/',
        success: function(result, newValue){

          if (result.length) {
            toastr.error(result);
            return false;
          } else {

            switch (newValue) {
              case "moderation" : status_class = "info"; break;
              case "accepted" : status_class = "success"; break;
              case "refused" : status_class = "danger"; break;
            }            

            $('#status'+id).parent().removeAttr('class').addClass('label label-sm label-' + status_class);
          }
        }
    });

    })
  }

  var handleFormMessages = function(form, type, messages){

    var box = form.find(".alert");

    box.attr('class', 'alert alert-' + type)
       .html(messages.join("<br/>"))
       .fadeIn();

    $('html, body').animate({
        scrollTop: box.offset().top - $('.page-top').height() - 20
    }, 300);
  }

  var handleEditStream = function(){
    $(document)
      // Загрузка цен оффера
      .on('change', 'select[name=stream_oid]', function(){

        var data = {
          action : "get-prices",
          oid: $(this).find('option:selected').val()
        }

        $.ajax({
          url: '/ajax/manage-api-streams/',
          type: 'POST',
          data: $.param(data),
          dataType: "json",
          success: function(response){
            $('#prices-wrap').html(response.prices);
            handleTouchSpin();
          }
        })
      })

      // Подгрузка цен при изменении цели
      .on('change', '#table_prices .target', function(){
        var row = $(this).closest("tr"),
            code = row.data('code'),
            option = $(this).find('option:selected'),
            price = +option.data("price"),
            profit = +option.data("profit"),
            max_profit = option.data('max');

        if (option.val() == 1) {
          disabled = option.data("editable") != 1
        } else {
          disabled = false;
        }

        row.find('.recommended, .price').text(price);

        row.find('.profit')
           .val(profit)
           .prop("disabled", disabled)
           .trigger("touchspin.updatesettings", {max: max_profit});
      })

      // Сохранение потока
      .on('click', '#save-stream', function(){

        // Проверяем поля
        var errors = [],
            form = $('#edit-stream-form');

        var data = {
          id: form.find('input[name=stream_id]').val(),
          oid: form.find('select[name=stream_oid] option:selected').val(),
          name: form.find('input[name=stream_name]').val(),
          prices : {}
        }

        if (data.name == "") {
          errors.push("Введите название потока");
        }        

        if (data.oid < 1) {
          errors.push("Необходимо выбрать оффер");
        } 

        if (errors.length) {
          handleFormMessages(form, "danger", errors);
          return false;
        } else {

          data.action = "save";

          $('#table_prices > tbody > tr').each(function(){
            var code = $(this).data('code');

            data.prices[code] = {
              target_id: $(this).find('select[name=target] option:selected').val(),
              profit: $(this).find('input[name=profit]').val()
            }

          })

          $.ajax({
            url: '/ajax/manage-api-streams/',
            type: 'POST',
            data: $.param(data),
            dataType: "json",
            success: function(response){
              if (response.errors.length) {
                handleFormMessages(form, "danger", response.errors);
              } else {

                document.location = "/admin/api";
                
                handleFormMessages(form, "success", ["Поток сохранен!"]);

                if (data.id == 0) {
                  var key_wrap = $('#key_wrap');
                  key_wrap.find('input').val(response.key);
                  key_wrap.fadeIn();
                  handleKeyCopy();
                }
              }
            }
          })
        }

      })
  }

  var handleTouchSpin = function() {
    if (!$('.profit').length) {
      return false;
    }

    $('.profit').each(function(){
      var min = 1, id = $(this).attr('id'),
          row = $(this).closest("tr");
          code = row.data('code'),
          currency = row.data('currency'),
          max = row.find('.target option:selected').data("max");

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
      var row = $(this).closest("tr"),
          target = row.find('.target option:selected'),
          price = parseInt(target.data("price")) - parseInt(target.data("profit")) + parseInt($(this).val());

      row.find('.price').text(price);
    })
  }

  // Копирование ссылки в буфер
  var handleKeyCopy = function(){

    if (!$('#copy_key_btn').length) {
      return false;
    }

    $('#copy_key_btn').zclip({
      path: "/misc/plugins/jquery-zclip-master/ZeroClipboard.swf",
      copy: $('#stream_key').val(),
      afterCopy: function(){
        toastr.success("Ключ скопирован в буфер обмена");
      }
    })

  }

  var handleStreamsTable = function(){

    if (!$("#api_streams_table").length) {
      return false;
    }

    streamsTable = new Datatable();

    streamsTable.init({
      src: $("#api_streams_table"),
      loadingMessage: 'Загрузка...',
      dataTable: {
        "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
        "language": datatables_defaults.lang,
        "pageLength": 25, // default record count per page
        "ajax": {
          "url": '/ajax/get-api-streams/', // ajax source
           data : function (d) {
            var data = {};

            $('#api_streams_table .form-filter').each(function(){
              var n = $(this).attr('name');
              if ($(this).is('select')) {
                data[n] = $(this).find('option:selected').val();
              } else {
                data[n] = $(this).val();
              }
            })

            d.params = data;
          }
        },
        drawCallback: function(oSettings) { // run some code on table redraw
          $('#api_streams_table .select2me').select2();
          $('#api_streams_table .date-picker').datepicker();

          $('#api_streams_table .copy-key').each(function(){
            $(this).zclip({
              path: "/misc/plugins/jquery-zclip-master/ZeroClipboard.swf",
              copy: $(this).data("key"),
              afterCopy: function(){
                toastr.success("Ключ скопирован в буфер обмена");
              }
            })
          })
        },
        "bSort" : false
      },
    });

    // handle group actionsubmit button click
    $('#api_streams_table')
      // Фильтры
      .on('change', 'select.form-filter, .date-picker input', function(){
        streamsTable.getDataTable().ajax.reload();
      })
      .on('keyup keypress', '.form-filter:text', function(){
        streamsTable.getDataTable().ajax.reload();
      })
      // Удаление потока
      .on('click', '.remove-item', function(){

        if (confirm("Удалить поток?")) {
          var data = {
            action : "delete",
            id: $(this).data("id")
          }

          $.ajax({
            url: '/ajax/manage-api-streams/',
            type: 'POST',
            data: $.param(data),
            dataType: "json",
            success: function(response){
              streamsTable.getDataTable().ajax.reload();
              toastr.success("Поток удален.");
            }
          })
        }
      })
  }

  return {
      init: function () {
        handleApiKeysTable();
        handleStreamsTable();
        handleEditStream();
        handleTouchSpin();
        handleKeyCopy();
      }
  };
}();