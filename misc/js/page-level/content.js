jQuery(document).ready(function(){
  Content.init();
});

// Content
var Content = function () {
  var grid;

  var handleAjax = function( data ){
    $.ajax({
      url: '/ajax/manage-content/',
      type: 'POST',
      dataType: 'json',
      data: $.param(data),
      success: function( result ){
        if ( data.action == "add-group" ) {
          $('#content-modal #content_group').html( result.groups );
        }
        if ( data.action == "remove" ) {
          grid.getDataTable().ajax.reload();
        }

      }
    })
  }

  var handleAddContent = function() {
    // Open modal to edit content
    $('.add-item').click(function(){
      var data = {
        type : $(this).data("content-type"),
        action: 'add'
      }

      handleAjax(data);
    })
  }

  var handleDataTable = function() {

    if (!$("#datatable_content").length) {
      return false;
    }
    
    var contentType = $("#datatable_content").data("content-type");
    grid = new Datatable();

    grid.init({
        src: $("#datatable_content"),
        loadingMessage: 'Загрузка...',
        dataTable: {
            "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
            "language": datatables_defaults.lang,
            "pageLength": 10, // default record count per page
            "ajax": {
                "url": '/ajax/get-content-' + contentType, // ajax source
                 data : function (d) {
                  var v, data = {};

                  $('select.form-filter option:selected').each(function(){
                    v = $(this).val();
                    if (v != '-1') {
                      data[$(this).parent().attr('name')] = v;
                    }
                  })

                  $('input.form-filter[type="text"]').each(function(){
                    v = $(this).val();
                    if (v.length) {
                      data[$(this).attr('name')] = v;
                    }
                  })

                  d.params = data;
              }
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
      .on('click', '.filter-cancel', function(){
        $('select.form-filter').each(function(){
          $(this).val("-1").trigger("change");
        })
      
        $('.form-filter:text').val("");

      })

      // Редактирование, Удаление
      .on('click', '.remove-item', function(){

        if (confirm("Вы действительно хотите удалить контент? Все файлы шаблонов, стили и js код, связанный с этим контентом будет удален!")) {
          var data = {
            id : $(this).data('id'),
            type: $(this).data('content-type'),
            action: 'remove'
          }
          handleAjax( data );
        }
      })
  }

  return {
      //main function to initiate the module
      init: function () {
        handleAddContent();
        handleDataTable();
      }
  };
}();


// Save Blog Content
var SaveContent = function () {

  var handleRemoveLanding = function(){
    $('#selected_landings').on('click', '.remove', function(){
    var data = {
      c_id : $(this).data("id"),
      action : "remove"
    }

    $.ajax({
      url: '/ajax/get-group-content/',
      type: 'POST',
      dataType: 'json',
      data: $.param(data),
      success: function( result ){
        $('#content_group').select2("destroy").html( "<option value=''></option>" + result.rows.groups ).select2();
        $('#content_landing').select2("destroy").html( "<option value=''></option>" + result.rows.landings ).select2();
      }
    })

     $(this).parent().fadeOut(300, function(){
        $(this).remove();
        if ( !$('#selected_landings li').length ) $('#selected_landings').hide();
     });

      return false;
    })
  }

  var handleFormSubmit = function (){
    $('#edit-content-form').submit(function(){

      var data = {
        action : "save",
        type : $('#content_type').val(),
        id : $('#content_id').val(),
        name : $('#content_name').val(),
        link : $('#content_link').val(),
        group : $('#content_group').val(),
        groups : [],
        landings : []
      }

      if ( data.type == "blog" ) {
        $('#selected_landings option').each(function(){
          data.landings.push( $(this).val() );
        })
      }

      $('.groups:checked').each(function(){
        data.groups.push( $(this).val() );
      })

      //handle errors
      var error = [];
      if ( data.name == "" ) {
        error.push( "Введите название контента." );
      }

      if ( data.link == "" ) {
        error.push( "Введите ссылку." );
      }

      if ( ( data.type == "landing" && !data.groups.length ) ) {
        error.push( "Необходимо выбрать группу." );
      }

      if ( data.type == "blog" && data.landings.length == 0 ) {
        error.push( "Необходимо выбрать лендинг." );
      }

      if ( error.length ) {
        $('.alert').html( error.join("<br />") ).fadeIn();
      } else {
        $.ajax({
          url: '/ajax/manage-content/',
          type: 'POST',
          dataType: 'json',
          data: $.param(data),
          success: function( result ){
            if ( result.error ) {
              $('.alert').html( result.error ).fadeIn();
            } else {
              window.location = "/admin/" + data.type + 's/';
            }
          }
        })
      }

      return false;
    })

    $(document).on('click', '#add-landing', function(){
      var val = $('#content_landing option:selected').val();

       if (val > 0) {
        if ($("#selected_landings option[value="+val+"]").length) {
          return false;
        }

        var name = $('#content_landing option:selected').text();
        $("#selected_landings").append($('<option>', {value:val, text: name}));
       }
    })
  }

  return {
      //main function to initiate the module
      init: function () {
        handleGroupSelect();
        handleRemoveLanding();
        handleFormSubmit();
      }
  }
}()