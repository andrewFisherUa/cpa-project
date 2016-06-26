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

  var handleDatatable = function(){
    if (!$("#user-payments-table").length) {
      return false;
    }

    grid = new Datatable();

    grid.init({
      src: $("#user-payments-table"),
      loadingMessage: 'Загрузка...',
      dataTable: {
        "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
        "language": datatables_defaults.lang,
        "pageLength": 25, // default record count per page
        "ajax": {
            "url": '/ajax/get-user-payments/', // ajax source
            data : function(d){
              d.user_id = $("#user-payments-table").data("user");
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
  }

  var handleWalletCopy = function(){
    $('#copy-wallet').zclip({
      path: "/misc/plugins/jquery-zclip-master/ZeroClipboard.swf",
      copy: $('#copy-wallet').data('wallet'),
      afterCopy: function(){
        toastr.success("Ссылка скопирована в буфер обмена");
      }
    });
  }

  var handleActions = function(){
    
    $(document)

      // Одобрение выплаты
      .on("click", "#approve-btn", function(){

        var data = {
          action : "check-changed",
          id : $('input[name=payment_id]').val()
        }

        $.ajax({
          url: '/ajax/manage-payments/',
          type: 'POST',
          dataType: 'json',
          data: $.param(data),
          success: function(response){
            if (response.changed) {
              $("#confirmModal .modal-body").html(response.message);
              $("#confirmModal").modal("show");
            } else {
              $('input[name="approve"]').trigger("click");
            }
          }
        })

        return false;
      })

      .on("click", "#confirm-approve-btn", function(){
        $('input[name="approve"]').trigger("click");
      })

      .on("click", "#toggle-edit-payment", function(){
        if ( $("#edit-payment-block").is( ":hidden" ) ) {
          $("#edit-payment-block").slideDown(200);
        } else {
          $("#edit-payment-block").hide();
        }
      })
  }

  return {
      init: function () {
        handleDatatable();
        handleWalletCopy();
        handleActions();
      }
  };
}();