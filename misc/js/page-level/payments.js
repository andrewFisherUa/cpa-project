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
  Payments.init();
});

var Payments = function () {

  var grid;

  // Добавление кошелька
  var handleAddWallet = function(){
    if (!$('#add-wallet-form').length) {
      return false;
    }

    $('#add-wallet-form input[name=wallet]').mask('R 9999 9999 9999');

    $(document).on('click', '#add-wallet-btn', function(){
      var data = {
        action : "add-wallet",
        wallet : $('#add-wallet-form input[name=wallet]').val()
      }

      $('#add-wallet-form .alert').removeClass('alert-danger').removeClass('alert-success');
      handleAjax(data);
      return false;
    })
  }

  // Запрос на выплату
  var handleAskPayment = function(){
    $(document)
      .on('click', '#ask-payment-btn', function(){
        var form = $('#ask-payment-form');

        var data = {
          "wallet" : form.find('select[name="p_wallet_id"] option:selected').val(),
          "amount" : form.find('input[name="p_amount"]').val(),
          "type" : form.find('select[name="p_type"] option:selected').val(),
          "action" : "ask-payment"
        }

        handleAjax(data);

        return false;

      })
  }

  var handleAjax = function(data){
    $.ajax({
        url: '/ajax/manage-payments/',
        type: 'POST',
        dataType: 'json',
        data: $.param(data),
        success: function(response){

          if ( data.subaction == "approve" ) {
            if (response.success.length) {
              toastr.success(response.success);
            } else {
              toastr.error(response.errors);
            }
          }

          if ( data.action == "add-wallet") {
            if (response.errors.length) {
              $('#add-wallet-form .alert').html(response.errors.join("<br/>")).addClass("alert-danger").fadeIn();
            } else {
              $('#add-wallet-form .alert').html("Кошелек добавлен").addClass("alert-success").fadeIn();

              $('#wallets-list').append("<li><label><input type='radio' name='wallet' value='"+response.wallet.wid+"'/>"+response.wallet.wid+"</label></li>");

              $('#wallets-list input').uniform();

              if ($('#wallets-list-wrap').is(':hidden')) {
                $('#wallets-list-wrap').fadeIn();
              }
            }
          }

          if ( data.action == "ask-payment") {
            if (response.errors.length) {
              $('#ask-payment-form .alert').html(response.errors.join("<br/>")).attr('class', 'alert alert-danger').fadeIn();
            } else {
              $('#ask-payment-form .alert').html("Запрос отправлен на модерацию").attr('class', 'alert alert-success').fadeIn();
            }
          }

          if ( data.action == "reload-table" ) {
            grid.getDataTable().ajax.reload();
          }

          if ( data.action == "show-modal" ) {
            $('#ph-modal .modal-title').html(response.title);
            $('#ph-modal .modal-body').html(response.content);
            $('#ph-modal').modal('show')
          }

          if ( data.action == "add-comment" ) {
            $('#ph-modal').modal('hide');
            grid.getDataTable().ajax.reload();
          }
        }
      })
  }

  var handleComment = function(){
    $(document).on('click', '#add-ph-comment', function(){
      var data = {
        id : $('#ph-comment').attr('data-payment_id'),
        comment: $('#ph-comment').val(),
        action : "add-comment"
      }

      handleAjax(data);
      return false;
    })
  }

  var handleDatatable = function(){
    if (!$("#datatable_payments-history").length) {
      return false;
    }

    grid = new Datatable();

    grid.init({
      src: $("#datatable_payments-history"),
      loadingMessage: 'Загрузка...',
      dataTable: {
        "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
        "language": datatables_defaults.lang,
        "pageLength": 25, // default record count per page
        "ajax": {
            "url": '/ajax/get-payments-history/', // ajax source
            data : function(d){
              if ($('input[data-status]:checked').length) {
                d.status = [];

                $('input[data-status]:checked').each(function(){
                  d.status.push($(this).data("status"));
                })

              }
            }
        },
        fnDrawCallback: function( oSettings ) {
          if ($('.money').length) {
            $('.money').mask('# ### ### ###', {reverse: true});
          } 

          if ($('.colorme').length) {
            var index = $('.colorme').index() + 1;
            $('#datatable_payments-history td:nth-child('+index+')').addClass('colorme');
          }

          if ($('.was_changed').length) {
            $('.was_changed').closest("tr").addClass("danger");
          }
        },
        "bSort" : false
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
      .on('click', '.table-action', function(){

        var data = {
          'subaction' : $(this).data('action'),
          'id' : $(this).data('id')
        }

        if (data.subaction == "approve" && !confirm("Одобрить выплату?")) {
          return false;
        }

        data.action = (data.subaction == "info" || data.subaction == "add-comment") ? "show-modal" : "reload-table";
        handleAjax(data);
      })


    $(document).on('change', 'input[data-status]', function(){
      grid.getDataTable().ajax.reload();
    })
  }

  return {
      init: function () {
        handleAddWallet();
        handleAskPayment();
        handleDatatable();
        handleComment();
      }
  };
}();