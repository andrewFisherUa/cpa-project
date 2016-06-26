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

function changeSupportEmail(){
  var data = {
    email : $('#bemail').val()
  }

  if ( data.email != '' ) {
    $.ajax({
      url: '/ajax/set-partners-email/',
      type: 'POST',
      data: $.param( data ),
      dataType: "json",
      success: function( response ) {
        console.log(response);
        if ( response.success ) {
          toastr.success(response.success);
        }
      }
    });
  } else {
    toastr.error("Необходимо указать email");
  }
}

jQuery(document).ready(function(){
  Tickets.init();
});

var Tickets = function () {

  var grid;

  var handleTickets = function () {

      if (!$("#datatable_tickets").length) {
          return false;
      }

      grid = new Datatable();

      grid.init({
          src: $("#datatable_tickets"),
          loadingMessage: 'Загрузка...',
          dataTable: {
              "language": datatables_defaults.lang,
              "pageLength": 25,
              "ajax": {
                  "url": "/ajax/get-tickets/",
                  data : function (d) {
                      var data = {};

                      $('.form-filter').each(function(){
                        var n = $(this).attr('name');
                        if ($(this).is('select')) {
                          data[n] = $(this).find('option:selected').val();
                        } else {
                          data[n] = $(this).val();
                        }
                      })

                      d.f = data;
                  }
              },
              fnDrawCallback: function( oSettings ) {
                $('.date-picker').datepicker();
                $('#datatable_tickets tbody span.unread').each(function(){
                  $(this).closest('tr').addClass('unread');
                })
              },
          }
      });

      // handle group actionsubmit button click
      grid.getTableWrapper()
          .on('change', 'select.form-filter, .date-picker input', function(){
              grid.getDataTable().ajax.reload();
          })
          .on('keyup keypress', '.form-filter:text', function(){
              grid.getDataTable().ajax.reload();
          })
  }

  var handleAlert = function(container, message, mclass){
    $(container).attr('class', 'alert alert-' + mclass).html(message).fadeIn();
  }

  var handleForm = function(){
      $(document)
        // Добавление сообщения к тикету
        .on('click', '#reply-ticket-form button[name="add_message"]', function(){
          var form = $('#reply-ticket-form');
          var fields = {
            message: form.find('textarea[name="ticket[message]"]').val(),
            ticket_id: form.find('input[name="ticket[ticket_id]"]').val(),
            from_uid: form.find('input[name="ticket[from_uid]"]').val(),
            to_uid: form.find('input[name="ticket[to_uid]"]').val(),
            action: "add-message"
          }

          if (fields.message == "") {
            handleAlert(form.find('.form-alert'), "Введите текст сообщения", "danger");
            return false;
          }

          handleAjax(fields);

          return false;
      })

      // Создание тикета
      .on('click', '#new-ticket-form button[name="submit"]', function(){
        var errors = [];
        var form = $('#new-ticket-form');
        var fields = {
          subject: form.find('input[name="ticket[subject]"]').val(),
          message: form.find('textarea[name="ticket[message]"]').val(),
          urgent: +form.find('textarea[name="ticket[urgent]"]').prop('checked'),
          action: "add-ticket"
        };
        
        if (fields.subject == "") {
            errors.push("Введите тему тикета");
        }

        if (fields.message == "") {
            errors.push("Введите текст сообщения");
        }

        if (errors.length) {
          handleAlert(form.find('.alert'), errors.join("<br />"), "danger");
          return false;
        }          

        handleAjax(fields);

        return false;
      })

      // Закрытие тикета
      .on('click', '#reply-ticket-form button[name="close_ticket_btn"]', function(){
        var data = {
          ticket_id: $('#reply-ticket-form input[name="ticket[ticket_id]"]').val(),
          action: "close-ticket"
        }

        handleAjax(data);
        return false;
      })

      // Открытие тикета
      .on('click', '#reply-ticket-form button[name="open_ticket_btn"]', function(){
        var data = {
          ticket_id: $('#reply-ticket-form input[name="ticket_id"]').val(),
          action: "open-ticket"
        }

        handleAjax(data);
        return false;
      });

      
  }

     // Обработка ajax запросов
  var handleAjax = function( data ) {
    $.ajax({
      url: '/ajax/manage-tickets/',
      type: "POST",
      dataType: 'json',
      data: $.param(data),
      success: function(response){        

        var form_alert;

        if (data.action == "add-ticket") {
          form_alert = $('#new-ticket-form .alert');
        } else {
          form_alert = $('#reply-ticket-form .alert');
        }

        if (response.errors.length) {
          handleAlert(form_alert, response.errors.join("<br/>"), "danger");
        } else {
          handleAlert(form_alert, response.success, "success");

          if (data.action == "add-message") {
            $('.tickets-messages').prepend(response.message);
            $('#reply-ticket-form textarea[name="ticket[message]"]').val("");
          }

          if (data.action == "add-ticket") {
            grid.getDataTable().ajax.reload();
            $('#new-ticket-form input[name="ticket[subject]"]').val("");
            $('#new-ticket-form textarea[name="ticket[message]"]').val("");
            $('#new-ticket-form input[name="ticket[urgent]"]').prop("checked", false).uniform();
          }

          if (data.action == "close-ticket" || data.action == "open-ticket") {
            document.location.reload();
          }
        }
      }
    })
  }

    return {

        //main function to initiate the module
        init: function () {
            handleTickets();
            handleForm();
        }

    };
}();