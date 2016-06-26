jQuery(document).ready(function(){
  Balance.init();
});

var Balance = function () {

  var applyFilters = function(){
    $('.table-group-action-input').each(function(){
      var actionValue = $(this).find('option:selected').val();
      if ( actionValue != '-1' ) {
        var actionName = $(this).attr('name');
        grid.setAjaxParam(actionName, actionValue);
      }
    })
  }

  var handleAjax = function(data){
    $.ajax({
        url: '/ajax/manage-user-balance/',
        type: 'POST',
        dataType: 'json',
        data: $.param(data),
        success: function(response){
          var form;

          if ( data.action == "change-account-balance") {
            form = $('#change_default_account_frm');
          }

          if ( data.action == "make_transfer") {
            form = $('#make_transfer_frm');
          }

          if ( data.action == "change-account-balance" || data.action == "make_transfer") {
            if (response.success) {
              form.find('.alert')
                .removeClass('alert-danger')
                .addClass('alert-success')
                .text("Ваш запрос отправлен в обработку")
                .fadeIn();
            } else {
              form.find('.alert')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .html(response.errors.join("<br/>"))
                .fadeIn();
            }
          }
        }
      })
  }

  var handleCheckBalance = function(){
    if (!$("#datatable_check_balance").length) {
      return false;
    }

    grid = new Datatable();

    grid.init({
      src: $("#datatable_check_balance"),
      loadingMessage: 'Загрузка...',
      dataTable: {
        "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
        "language": datatables_defaults.lang,
        "pageLength": 25, // default record count per page
        "ajax": {
          "url": '/ajax/get-check-balance/', // ajax source,
        },
        "bSort" : false,
        fnDrawCallback: function( oSettings ) {
          $('.money').mask('# ### ### ### ###', {reverse: true});

          $('#datatable_check_balance tbody span.diff').each(function(){
            if ($(this).text() != "0") {
              $(this).closest('tr').addClass('danger');  
            }
          })
        },
      },
    });

  }

  var handleBalanceDatatable = function(){
    if (!$("#datatable_balance").length) {
      return false;
    }

    grid = new Datatable();

    grid.init({
      src: $("#datatable_balance"),
      loadingMessage: 'Загрузка...',
      dataTable: {
        "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
        "language": datatables_defaults.lang,
        "pageLength": 25, // default record count per page
        "ajax": {
            "url": '/ajax/get-users-balance/', // ajax source,
            data: function ( d ) {
              d.country_code = $('.table-group-action-input[name=country_code] option:selected').val();
              d.role = $('.table-group-action-input[name=role] option:selected').val();
            },
          },
          drawCallback: function(oSettings) { // run some code on table redraw
            $('#datatable_balance .money').mask('# ### ### ### ###', {reverse: true});
          }
      },
    });

    grid.getDataTable().on('page.dt', function () {
      applyFilters()
    });

    // handle group actionsubmit button click
    grid.getTableWrapper()
      // Обработка изменения значения фильтров
      .on('change', '.table-group-action-input', function () {
        $('.table-group-action-input').each(function(){
          applyFilters();
          grid.getDataTable().ajax.reload();
          grid.clearAjaxParams();
        })
      })
      // Сброс фильтров
      .on('click', '.reset-filters', function () {
        $('.table-group-action-input').each(function(){
          $(this).find('option:first-child').prop('selected', true);
        })
        grid.getDataTable().ajax.reload();
        grid.clearAjaxParams();
      })
  }

  var grid2;

  var handleAccountRequests = function(){
    if (!$("#datatable_account_requests").length) {
      return false;
    }

    grid2 = new Datatable();

    grid2.init({
      src: $("#datatable_account_requests"),
      loadingMessage: 'Загрузка...',
      dataTable: {
        "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
        "language": datatables_defaults.lang,
        "pageLength": 25, // default record count per page
        "ajax": {
            "url": '/ajax/get-account-requests/', // ajax source,
        },
        drawCallback: function(oSettings) { // run some code on table redraw
          $(':checkbox').uniform();
          handleStatus();
        }
      },
    });
  }

  var handleStatus = function(){
    $('#datatable_account_requests .status').each(function(){

      var id = $(this).data('subject');

      $('#datatable_account_requests #status'+id).editable({
        inputclass: 'form-control',
        source: [{value: "processing", text: "processing"},
                 {value: "approved", text: "approved"},
                 {value: "canceled", text: "canceled"}],
        autotext: 'always',
        params: { 'id': id },
        url: '/ajax/change-account-currency/',
        success: function(result){
          $('#datatable_account_requests #status'+id).parent().removeAttr('class').addClass('label label-sm label-'+result);
          grid2.getDataTable().ajax.reload();
        }
    });

    })
  }

  var handleForms = function(){
    $(document)
      .on('click', '#make_replenishment_frm input[name="submit"]', function(){
        var form = $('#make_replenishment_frm'),
          userIdField = form.find('input[name="user_id"]'),
          currencyField = form.find('select[name="country_code"]'),
          amountField = form.find('input[name="amount"]'),
          errors = [];

        form.find('.alert-danger').fadeOut();
        form.find('.form-group').removeClass('has-error');

        if (currencyField.find("option:selected").val() == -1) {
          errors.push("Необходимо выбрать валюту");
          currencyField.closest('.form-group').addClass('has-error');
        }

        if (parseInt(amountField.val()) <= 0) {
          errors.push("Сумма пополнения должна быть больше 0");
          amountField.closest('.form-group').addClass('has-error');
        }

        if (errors.length){
          form.find('.alert-danger').html(errors.join("<br />")).fadeIn();
          return false;
        }
      })

      // Форма переводов
      .on('change', '#account_from', function(){
        var opt = $(this).find("option:selected"),
            val = opt.val(),
            currency = val == "-1" ? "<i class='fa fa-money'></i>" : opt.text(),
            flag = val == "-1" ? "<i class='glyphicon glyphicon-flag'></i>" : "<i class='flag flag-"+opt.data('code')+"'>",
            amount = opt.data('amount');

        $('#account_to option').prop('disabled', false);
        $('#account_to').find('option[value='+val+']').prop('disabled', true);
        $('#account_from_flag').html(flag);
        $('#transfer_from_currency').html(currency);
        $('#transfer_amount_from').val(amount);

        if (val == $('#account_to option:selected').val() || val == "-1") {
          $('#account_to').val("-1");
          $('#account_to_flag').html("<i class='glyphicon glyphicon-flag'></i>");
          $('#transfer_to_currency').html("<i class='fa fa-money'></i>");
          $('#convert-rate-wrap').fadeOut();
          $('#transfer_amount_to').val("");
        }

        if (val != "-1" && $('#account_to option:selected').val() != "-1") {
          makeConvert();
        }
      })

      // Форма переводов
      .on('change', '#account_to', function(){
        var opt = $(this).find("option:selected"),
            val = opt.val(),
            flag = val == "-1" ? "<i class='glyphicon glyphicon-flag'></i>" : "<i class='flag flag-"+opt.data('code')+"'>",
            currency = val == "-1" ? "<i class='fa fa-money'></i>" : opt.text();
        $('#transfer_to_currency').html(currency);
        $('#account_to_flag').html(flag);
        makeConvert();
      })

      .on('input', '#transfer_amount_from', function(e){
        var max = $('#account_from option:selected').data('amount');
        var val = $(this).val();

        if (val > max) {
          $(this).val(max)
        }

        if (val < 0) {
          $(this).val(0)
        }

        makeConvert();
      })

      // Обработка формы изменения валюты по умолчанию
      .on('click', '#change_default_account', function(){
        var form =  $('#change_default_account_frm');

        handleAjax({
          user_id : parseInt(form.find('input[name=user_id]').val()),
          currency : form.find('select[name=account_currency] option:selected').val(),
          action : 'change-account-balance'
        });

        return false;
      })

      // Обработка формы перевода средств между счетами
      .on('click', '#make_transfer', function(){
        var data = {
          "from_currency" : $('#account_from option:selected').val(),
          "to_currency" : $('#account_to option:selected').val(),
          "from_amount" : $('#transfer_amount_from').val(),
          "action" : "make_transfer"
        }

        var errors = [];

        if (data.from_currency == "-1") {
          errors.push("Необходимо выбрать баланс, с которого осуществляется перевод");
        }

        if (data.to_currency == "-1") {
          errors.push("Необходимо выбрать баланс, на который осуществляется перевод")
        }

        if (data.from_amount <= 0) {
          errors.push("Сумма перевода должна быть больше 0");
        }

        if (errors.length) {
          $('#make_transfer_frm .alert')
            .removeClass('alert-success')
            .addClass('alert-danger')
            .html(errors.join("<br/>"))
            .fadeIn();
        } else {
          handleAjax(data);
        }

        return false;
      })
  }

  var makeConvert = function(){
    var data = {
      from : $('#account_from').find('option:selected').text(),
      to : $('#account_to').find('option:selected').text(),
      amount : $('#transfer_amount_from').val()
    }

    $.ajax({
      url: '/ajax/make_convert/',
      type: 'POST',
      data: $.param(data),
      dataType: "json",
      success: function(response){
        if (response == false) {
          $('#convert-rate-wrap').fadeOut();
        } else {
          $('#transfer_amount_to').val(response.amount);
          $('#convert-rate').text(response.rate);
          $('#convert-rate-wrap').fadeIn();
        }
      }
    })
  }

  return {
      init: function () {
        handleBalanceDatatable();
        handleCheckBalance();
        handleAccountRequests();
        handleForms();
      }
  };
}();