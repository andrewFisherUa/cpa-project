jQuery(document).ready(function(){
  Menus.init();
});

// Menus
var Menus = function () {
    var handleOperations = function() {
      $('#menu-links-table-wrap').on('click', '.edit-item, .remove-item, .add-item', function(){
        var link = $(this);
        if (link.data('action') == "remove" && !confirm("Удалить страницу")) {
          return false;
        }

        var row = link.closest('tr');
        var data = {
          m_id : link.data('id'),
          action : link.data('action'),
          link : row.find('.item-link').attr('href')
        }

        $.ajax({
          url: '/ajax/save-menu/',
          type: 'POST',
          dataType: 'json',
          data: $.param( data ),
          success: function( result ){

            if ( result.menu ) {
              $("#menu-links-table tbody").html( result.menu );
              $(':checkbox').uniform();
            }

            if ( data.action == "remove") {
              row.fadeOut();
            }
            if ( data.action == "edit" || data.action == "add") {
              $('#menu-modal .modal-title').text( result.title );
              $('#menu-modal .modal-body').html( result.form );
              $('#menu-modal').modal('show');
              $('select').select2({width: "100%"});
            }
          }
        })
      })
    }

    return {
        //main function to initiate the module
        init: function () {
            handleOperations();
        }
    };
}();

function addCategory(){
  var data = {
    m_id: $('#menu-id').val(),
    title: $('#menu-title').val(),
    link: $('#menu-link').val(),
    description: $('#menu-desc').val(),
    weight: $('#menu-weight').val(),
    parent: $('#menu-parent').val(),
    css: $('#menu-css').val(),
    action: 'save'
  }

  $.ajax({
      url: '/ajax/save-menu/',
      type: 'POST',
      dataType: 'json',
      data: $.param( data ),
      success: function( result ){
        $('#menu-modal').modal('hide');
        $("#menu-links-table tbody").html( result.menu );
        $(":checkbox").uniform();
      }
    })

  return false;
}