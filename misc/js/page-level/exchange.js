jQuery(document).ready(function(){
  Exchange.init();
});

var Exchange = function () {

  var grid;

  var handlePickers = function(){
    $(".datetime").datetimepicker({
      autoclose: !0,
      isRTL: App.isRTL(),
      format: "dd MM yyyy - hh:ii",
      pickerPosition: App.isRTL() ? "bottom-right" : "bottom-left"
    })
  }

  var getMonthNumber = function(monthname){
    var number = ["january",
                  "february",
                  "march",
                  "april",
                  "may",
                  "june",
                  "july",
                  "august",
                  "september",
                  "october",
                  "november",
                  "december"].indexOf(monthname.toLowerCase());
    return number;
  }

  var getTimestamp = function(str){
    if (typeof str === 'undefined' || str === ""){
      return 0
    }

    var date = str.split(" ");
    var time = date[4].split(":");

    var d = {
      y : parseInt(date[2]),
      m : getMonthNumber(date[1]),
      d : parseInt(date[0])
    }

    var t = {
      h : parseInt(time[0]),
      m : parseInt(time[1])
    }

    var timestamp = new Date(d.y, d.m, d.d, t.h, t.m).getTime();
    return timestamp/1000;
  }

  var handleAjax = function(data){
    $.ajax({
        url: '/ajax/manage-exchange-rates/',
        type: 'POST',
        dataType: 'json',
        data: $.param(data),
        success: function(response){

          if (data.action == "add-special") {
            if (response.errors) {
              $('#add_rate .alert')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .html(response.errors.join("<br />"))
                .fadeIn();
            } else {
              $('#add_rate .alert')
                .removeClass('alert-danger')
                .addClass('alert-success')
                .html("Значение курса сохранено")
                .fadeIn();

              grid.getDataTable().ajax.reload();
            }
          }

          if (data.action == "get-rate") {
            if (response.success) {
              $('#bid').val(response.rate.bid);
              $('#ask').val(response.rate.ask);
            }
          }

          if (data.action == "approve") {
            if (response.error.length) {
              for (var i=0; i<response.error.length; i++) {
                $('#datatable_rates :checkbox[name="id[]"][value="'+response.error[i]+'"]').closest("tr").addClass('has-error');
              }
              if (!$('#datatable_rates_wrapper .alert-danger').length) {
                $("#datatable_rates").before("<div class='alert alert-danger'>Найдены пересечения дат в ID - " + response.error.join(",") + "</div>");
              } else {
                $('#datatable_rates_wrapper .alert-danger').text("Найдены пересечения дат в ID - " + response.error.join(",")).fadeIn();
              }
            } else {
              grid.getDataTable().ajax.reload();
            }
          }

          if (data.action == "cancel") {
            grid.getDataTable().ajax.reload();
          }

          if (data.action == "cancel" || data.action == "approve") {
            for (var key in response.widgets) {
              $('#exchange-widget-'+key).html(response.widgets[key]);
            }
          }

          if (data.action == "test-conflict") {
            if (response.success) {
              handleAjax({
                ids : data.ids,
                action : "approve"
              })
            } else {
              var message = ""; arr = response.conflict;
              for (i=0; i<arr.length; i++) {
                 message += "Если будет одобрен ID:" + arr[i].shift() + ", будут отклонены - " + arr[i].join(", ") + " \r\n";
              }
              message += "Применить изменения? \r\n";
              if (confirm(message)) {
                handleAjax({
                  ids : data.ids,
                  action : "approve"
                })
              }
            }
          }
        }
      })
  }

  var handleForm = function(){

    $(document)
      // Сохранение значений
      .on('click', '#add_rate button[name="submit"]', function(){
      var errors = [];
      var data = {
        "from" : $('#add_rate #from_currency option:selected').val(),
        "to" : $('#add_rate #to_currency option:selected').val(),
        "bid" : $('#add_rate #bid').val(),
        "ask" : $('#add_rate #ask').val(),
        "start" : getTimestamp($('#add_rate #start').val()),
        "end" : getTimestamp($('#add_rate #end').val()),
      }

      // end date
      var end = $('#add_rate #end').val();
      if (end) {
        data.end = getTimestamp(end);
      }

      if (data.from == "-1" || data.to == "-1") {
        errors.push("Необходимо выбрать валюту");
      }

      if (data.bid <= 0 || data.ask <= 0 ){
        errors.push("Значения Bid и Ask должны быть больше 0");
      }

      if (data.start == 0 || data.end == 0) {
        errors.push("Значения начала и конца изменения курса обязательны");
      }

      if (data.start > data.end) {
        errors.push("Неверно указан временной период");
      }

      if (errors.length) {
        $('#add_rate .alert')
          .removeClass('alert-success')
          .addClass('alert-danger')
          .html(errors.join("<br />"))
          .fadeIn();

        return false;
      }

      data.action = "add-special";
      handleAjax(data);

      return false;
      })
      // Подгрузка текущего курса при смене валюты
      .on('change', '#to_currency', function(){
        handleAjax({
          action : "get-rate",
          from : $('#from_currency option:selected').val(),
          to : $('#to_currency option:selected').val()
        })
      })
  }

  var handleDatatable = function(){
    grid = new Datatable();

    grid.init({
      src: $("#datatable_rates"),
      loadingMessage: 'Загрузка...',
      dataTable: {
          "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
          "language": datatables_defaults.lang,
          "pageLength": 10, // default record count per page
          "ajax": {
              "url": '/ajax/get-special-exchange-rates/', // ajax source
          },
          "columnDefs": [{ // define columns sorting options(by default all columns are sortable extept the first checkbox column)
                        'orderable': false,
                        'targets': [0,1,2,3,4,5,6,7,8,9]
                    }],
          drawCallback: function(oSettings) { // run some code on table redraw
            $("#datatable_rates .label-active").closest("tr").addClass("active-row");
            $("#datatable_rates td:nth-child(4)").addClass("bid-column");
            $("#datatable_rates td:nth-child(5)").addClass("ask-column");
            $(":checkbox").uniform();
        }
      },
    });

    grid.getDataTable()
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


    $(document)
      // Одобрение или отклонение курса
      .on('click', '.btn-cancel, .btn-approve', function(){

        $('#datatable_rates tr').removeClass('has-error');

        if ($('#datatable_rates_wrapper .alert-danger').length) {
          $('#datatable_rates_wrapper .alert-danger').fadeOut();
        }

        var data = {
          ids : []
        };

        if ($(this).hasClass('btn-cancel')) {
          data.action = "cancel";
        } else if ($(this).hasClass('btn-approve')) {
          data.action = "approve";
        }

        $('#datatable_rates :checkbox[name="id[]"]:checked').each(function(){
          data.ids.push($(this).val());
        })

        if (data.action == "approve") {
          handleAjax({
            ids : data.ids,
            action : "test-conflict"
          })
        } else {
          handleAjax(data);
        }

        return false;
      })

      // Применение фильтров к таблице
      .on('change', '.tbl-filter', function(){
        var filters = {};
        $('.tbl-filter').each(function(){
          filters[$(this).attr('name')] = $(this).find('option:selected').val();
        })

        grid.setAjaxParam("filters", filters);
        grid.getDataTable().ajax.reload();
      })

      // Очистка фильтров
      .on('click', '.reset-filters', function(){
        $('.tbl-filter').val("-1");
        grid.setAjaxParam("filters", []);
        grid.getDataTable().ajax.reload();
      })
  }

  return {
      init: function () {
        handleDatatable();
        handlePickers();
        handleForm();
      }
  };
}();