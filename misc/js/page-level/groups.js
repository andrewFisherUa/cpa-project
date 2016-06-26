$(document).ready(function(){
  Groups.init();
});

// Content groups
var Groups = function () {

  var grid;

  var handleAjax = function(data){
    $.ajax({
        url: '/ajax/manage-content/',
        type: 'POST',
        dataType: 'json',
        data: $.param(data),
        success: function(response){
          var tblReload = false;

          // Удаление группы
          if ( data.action == 'remove-group' ) {
            tblReload = true;
          }
          // Загрузка формы редактирования группы

          if ( data.action == 'get-groups-form' ) {
            var title;
            if ( data.id == 0 ) {
              title = "Создание группы"
            } else {
              title = "Редактирование группы"
            }

            $('#edit-group .modal-title').text(title);
            $('#edit-group .modal-body').html(response.form);
            $('#edit-group').modal('show');
          }

          // Сохранение группы
          if ( data.action == "save-group" ) {
            if ( response.error ) {
              $('#edit-group .alert').text( response.error ).fadeIn();
              return false;
            }
            $('#edit-group').modal('hide');
            tblReload = true;
          }

          if ( tblReload ) {
            grid.getDataTable().ajax.reload();
            grid.clearAjaxParams();
          }
        }
      })
  }

  var handleDataTable = function(){
    grid = new Datatable();

    grid.init({
        src: $("#datatable_groups"),
        loadingMessage: 'Загрузка...',
        dataTable: {
            "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
            "language": datatables_defaults.lang,
            "pageLength": 25, // default record count per page
            "ajax": {
                "url": '/ajax/get-content-groups', // ajax source
            },
            "bSort" : false
        },
    });
  }

  var handleEdit = function(){
    $('body').on('click', '.btn-edit', function(){
      var data = {
        id : $(this).data('group'),
        action : 'get-groups-form'
      }
      handleAjax( data );
    })
  }

  var handleSave = function(){
    $('body').on('click', '#save-group', function(){
      var data = {
        id: $('#group-id').val(),
        name: $('#group-name').val(),
        action: "save-group"
      }

      if ( data.name == "" ) {
        $('#edit-group .alert').text("Введите название группы").fadeIn();
        return false;
      }
      handleAjax( data );
    })
  }

  // Remove group
  var handleGroups = function(){
    $('body').on('click', '.remove-group', function(){
      if ( confirm("Вы действительно хотите удалить группу?")) {
        var data = {
          id : $(this).data('id'),
          action : 'remove-group'
        }
        handleAjax(data);
      }
      return false;
    })
  }

  return {
      //main function to initiate the module
      init: function () {
        handleDataTable();
        handleGroups();
        handleEdit();
        handleSave();
      }
  }
}()
