jQuery(document).ready(function(){
  Users.init();
});

var Users = function () {

  var handleDatatable = function(){
    grid = new Datatable();

    grid.init({
      src: $("#datatable_users"),
      loadingMessage: 'Загрузка...',
      dataTable: {
        "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
        "language": datatables_defaults.lang,
        "pageLength": 25, // default record count per page
        "ajax": {
          "url": '/ajax/get-users/', // ajax source
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
        drawCallback: function(oSettings) { // run some code on table redraw
          $(':checkbox').uniform();
          $('.date-picker').datepicker();
          handleStatus();
        },
        "bSort" : false
      },
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

  var handleStatus = function(){
    $('.status').each(function(){

      var source, id = $(this).data('subject');

      if ( $(this).attr('data-value') == 0 ) {
        source = [ {value: 0, text: 'Не подтвержден'}];
      } else {
        source = [ {value: 1, text: 'На модерации'},
                   {value: 2, text: 'Активирован'},
                   {value: 3, text: 'Заблокирован'}];
      }

      $('#status'+id).editable({
        inputclass: 'form-control',
        source: source,
        autotext: 'always',
        params: { 'id': id },
        url: '/ajax/change-partner-status/',
        success: function(result){
          $('#status'+id).parent().removeAttr('class').addClass('label label-sm label-'+result);
        }
    });

    })
  }

  var handleLogin = function() {
    $('body').on('click', '.login-as', function(){
       var data = {
        "user_id" : $(this).data('user'),
        "action" : "login-as"
        }

        $.ajax({
          url: '/ajax/manage-users/',
          type: 'POST',
          data: $.param( data ),
          dataType: "json",
          success: function( response ){
            window.location = response.url;
          }
        })
      })
  }

  var handleProfile = function() {
    $('#datatable_users').on('click', '.show-profile', function(){
      var data = {
        action: "get-profile",
        user_id : $(this).data('user')
      }

      $.ajax({
        url: '/ajax/manage-users/',
        type: 'POST',
        data: $.param( data ),
        dataType: "json",
        success: function( response ){
          $('#profile-modal .modal-body').html( response.rows );
          $(':checkbox').uniform();
          $('#profile-modal').modal('show');
        }
      })
    })

    $('body').on('click', "#save-profile", function(){
      var errors = [];
      var data = {
        action: "save-profile",
        id : $('input[name=user_id]').val(),
        name : $('input[name=first_name]').val(),
        last_name : $('input[name=last_name]').val(),
        email : $('input[name=email]').val(),
        phone : $('input[name=phone]').val(),
        options : {}
      }

      var passVal = $('input[name=pass]').val();

      if ( passVal != '' ) {
        if ( passVal != $('input[name=passr]').val() ) {
        errors.push("Пароли не совпадают");
        } else {
          data.password = passVal;
        }
      }

      $('input[data-option]').each(function(){
        data.options[ $(this).data("option") ] = +$(this).prop("checked");
      })

      if ( data.email == "" ) {
        errors.push("Введите email");
      }

      if ( errors.length ) {
        $('#profile-modal .alert').html( errors.join("<br />") ).fadeIn();
        return false;
      }      

      $.ajax({
        url: '/ajax/manage-users/',
        type: 'POST',
        data: $.param( data ),
        dataType: "json",
        success: function( response ){
          console.log( response );
          if ( response.errors.length ) {
            $('#profile-modal .alert').html( response.errors.join("<br />") ).fadeIn();
          } else {
            $('#profile-modal').modal('hide');
            ShowPreload();
          }
        }
      })

      return false;
    });
  }

  var handleEmails = function(){
    $('#emails-modal').on('show.bs.modal', function (e) {

      var data = {
        action: "get-emails"
      }

      $.ajax({
        url: '/ajax/manage-users/',
        type: 'POST',
        data: $.param( data ),
        dataType: "json",
        success: function( response ){
          $('#emails-modal #list').html(response.list.join("\n"));
        }
      })

    })
  }

  return {
      init: function () {
        handleDatatable();
        handleLogin();
        handleProfile();
        handleEmails();
      }
  };
}();