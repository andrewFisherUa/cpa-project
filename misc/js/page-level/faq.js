var articles_grid, rubrics_grid;

jQuery(document).ready(function(){
  Articles.init();
  Rubrics.init();
});

var Articles = function () {

  var handleAjax = function( data ) {

    $.ajax({
      url: '/ajax/manage-articles/',
      type: "POST",
      dataType: 'json',
      data: $.param(data),
      success: function(result){

        if ( data.action == "edit" ) {
          $('#edit-articles .modal-body').html( result.form );
          $('.summernote').summernote({height: '200px'});
          $('.note-editable').css('font-size','14px');
          $('.select2me').select2({width : '100%'});
          $('#edit-articles').modal('show');
        }

        if ( data.action == "save" ) {
          $('#edit-articles').modal('hide');
          articles_grid.getDataTable().ajax.reload();
          articles_grid.clearAjaxParams();
        }

        if ( data.action == "remove" ) {
          articles_grid.getDataTable().ajax.reload();
        }
      }
    });
  }

  var handleModal = function(){
    $('body').on('click', 'a[href="#edit-articles"]', function(){
      var data = {
        article_id : $(this).data('article'),
        action : "edit"
      }

      handleAjax( data );
    })
  }

  var handleSave = function(){
    $('#edit-articles').on('click', ".btn-save", function(){
      var errors = [];
      var data = {
        article_id: $('#article-id').val(),
        title: $('#article-title').val(),
        content: $('#edit-articles .summernote').code(),
        status: $('#article-status option:selected').val(),
        rubric_id: $('#article-rubric option:selected').val(),
        weight: $('#article-weight option:selected').val(),
        action: "save"
      }

      if ( data.title == "" ) {
        errors.push("Введите название статьи");
      }

      if ( errors.length ) {
        $('#edit-articles .alert').html( errors.join("<br />") ).fadeIn();
      } else {
        handleAjax( data );
      }
    })
  }

  var handleDatatable = function(){
    articles_grid = new Datatable();

    articles_grid.init({
        src: $("#datatable_articles"),
        loadingMessage: 'Загрузка...',
        dataTable: {
            "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
            "language": datatables_defaults.lang,
            "pageLength": 25, // default record count per page
            "ajax": {
                url: '/ajax/get-articles/', // ajax source
            }
        },
    });

    // handle group actionsubmit button click
    articles_grid.getTableWrapper()
      // Удаление
      .on('click', '.btn-remove', function(){
        var data = {
          article_id : $(this).data('article'),
          action: 'remove'
        }
        if ( confirm("Вы действительно хотите удалить статью?") ) {
           handleAjax( data );
        }
      })
  }

  return {
      init: function () {
        handleDatatable();
        handleModal();
        handleSave();
      }
  };
}();

var Rubrics = function () {

  var handleAjax = function( data ) {

    $.ajax({
      url: '/ajax/manage-rubrics/',
      type: "POST",
      dataType: 'json',
      data: $.param(data),
      success: function(result){

        if ( data.action == "edit" ) {
          $('#edit-rubrics .modal-body').html( result.form );
          $('#edit-rubrics').modal('show');
        }

        if ( data.action == "save" ) {
          $('#edit-rubrics').modal('hide');
          rubrics_grid.getDataTable().ajax.reload();
          rubrics_grid.clearAjaxParams();

          articles_grid.getDataTable().ajax.reload();
          articles_grid.clearAjaxParams();
        }

        if ( data.action == "remove" ) {
          rubrics_grid.getDataTable().ajax.reload();
        }
      }
    });
  }

  var handleModal = function(){
    $('body').on('click', 'a[href="#edit-rubrics"]', function(){
      var data = {
        id : $(this).data('rubric'),
        action : "edit"
      }

      handleAjax( data );
    })
  }

  var handleSave = function(){
    $('#edit-rubrics').on('click', ".btn-save", function(){
      var errors = [];
      var data = {
        id: $('#rubric-id').val(),
        name: $('#rubric-name').val(),
        weight: $('#rubric-weight option:selected').val(),
        css: $('#rubric-css').val(),
        action: "save"
      }

      if ( data.name == "" ) {
        errors.push("Введите название рубрики");
      }

      if ( errors.length ) {
        $('#edit-rubrics .alert').html( errors.join("<br />") ).fadeIn();
      } else {
        handleAjax( data );
      }
    })
  }

  var handleDatatable = function(){
    rubrics_grid = new Datatable();

    rubrics_grid.init({
        src: $("#datatable_rubrics"),
        loadingMessage: 'Загрузка...',
        dataTable: {
            "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
            "language": datatables_defaults.lang,
            "pageLength": 25, // default record count per page
            "ajax": {
                url: '/ajax/get-rubrics/', // ajax source
            }
        },
    });

    // handle group actionsubmit button click
    rubrics_grid.getTableWrapper()
      // Удаление
      .on('click', '.btn-remove', function(){
        var data = {
          id : $(this).data('rubric'),
          action: 'remove'
        }
        if ( confirm("Вы действительно хотите удалить рубрику?") ) {
           handleAjax( data );
        }
      })
  }

  return {
      init: function () {
        handleDatatable();
        handleModal();
        handleSave();
      }
  };
}();