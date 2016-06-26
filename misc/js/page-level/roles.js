jQuery(document).ready(function(){
  Roles.init();
});


// Roles
var Roles = function () {

  var grid;

  var handleAjax = function(data){
    $.ajax({
        url: '/ajax/manage-roles/',
        type: 'POST',
        dataType: 'json',
        data: $.param(data),
        success: function(response){
          var tblReload = false;

          // Удаление роли
          if ( data.action == 'remove' ) {
            tblReload = true;
          }
          // Загрузка формы редактирования роли

          if ( data.action == 'edit' ) {
            var title;
            if ( data.id == 0 ) {
              title = "Создание роли"
            } else {
              title = "Редактирование роли"
            }

            $('#edit-role .modal-title').text(title);
            $('#edit-role .modal-body').html(response.form);
            $('#edit-role').modal('show');
          }

          // Сохранение роли
          if ( data.action == "save" ) {
            if ( response.error ) {
              $('#edit-role .alert').text( response.error ).fadeIn();
              return false;
            }
            $('#edit-role').modal('hide');
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
        src: $("#datatable_roles"),
        loadingMessage: 'Загрузка...',
        dataTable: {
            "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
            "language": datatables_defaults.lang,
            "pageLength": 10, // default record count per page
            "ajax": {
                "url": '/ajax/get-roles', // ajax source
            }
        },
    });
  }

  var handleRemove = function(){

    $('body').on('click', '.remove-item', function(){
      if ( confirm("Вы действительно хотите удалить роль?")) {
        var data = {
          id : $(this).data('role'),
          action: "remove"
        }
       handleAjax(data);
      }
      return false;
    })

  }

  var handleEdit = function(){
    $('body').on('click', '.btn-edit', function(){
      var data = {
        id : $(this).data('role'),
        action : 'edit'
      }
      handleAjax( data );
    })
  }

  var handleSave = function(){
    $('body').on('click', '#save-role', function(){
      var data = {
        id: $('#role-id').val(),
        name: $('#role-name').val(),
        action: "save"
      }

      if ( data.name == "" ) {
        $('#edit-role .alert').text("Введите название роли").fadeIn();
        return false;
      }
      handleAjax( data );
    })
  }

  return {
      //main function to initiate the module
      init: function () {
        handleDataTable();
        handleEdit();
        handleSave();
        handleRemove();
      }
  }
}()