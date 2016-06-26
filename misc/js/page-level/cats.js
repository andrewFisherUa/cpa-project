jQuery(document).ready(function(){
  Cats.init();
});

var Cats = function () {
  var grid;

  // Управление таблицей потоков
  var handleDatatable = function(){
    if (!$("#datatable_categories").length) return false;
    grid = new Datatable();

    grid.init({
        src: $("#datatable_categories"),
        loadingMessage: 'Загрузка...',
        dataTable: {
            "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
            "language": datatables_defaults.lang,
            "pageLength": 25, // default record count per page
            "ajax": {
                "url": '/ajax/get-cats/'
            },
            fnDrawCallback: function( oSettings ) {
              handleEditable();
            },
        },
    });

    // handle group actionsubmit button click
    grid.getTableWrapper()
      // Обработка изменения значения фильтров
      .on('click', '.submit-filters', function () {
        $('.table-group-action-input').each(function(){
          var actionValue = $(this).find('option:selected').val();
          if ( actionValue != '-1' ) {
            var actionName = $(this).attr('name');
            grid.setAjaxParam(actionName, actionValue);
          }

        })
        grid.getDataTable().ajax.reload();
        grid.clearAjaxParams();
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

  var handleEditable = function() {
    $('.editable').each(function(){

      $(this).editable({
        params: { 'id': $(this).data('id') },
        url: '/ajax/save-cat-weight/',
        success: function(){
          grid.getDataTable().ajax.reload();
          grid.clearAjaxParams();
        }
      });
    })
  }

  var handleAjax = function(data){
    $.ajax({
        url: '/ajax/manage-cats/',
        type: 'POST',
        dataType: 'json',
        data: $.param(data),
        success: function(response){
          if ( data.action == "remove" || data.action == "show" || data.action == "hide") {
            grid.getDataTable().ajax.reload();
            grid.clearAjaxParams();
          }
          if (data.action == "check-form" ) {
            if (response.errors.length) {
              $('.alert').html(response.errors.join("<br />")).fadeIn();
            } else {
              $('#cat-form').submit();
            }
          }
        }
      })
  }

  var handleActions = function(){
    // Удаление
    $(document).on('click', '.action-btn', function(){

      var data = {
        id : $(this).data('id'),
        action : $(this).data('action')
      }

      if (data.action=="remove") {
        if (!confirm("Вы действительно хотите удалить категорию?")) {
          return false;
        }
      }

      handleAjax(data);
    })
  }

  // Сохранение категории
  var handleSaveCat = function(){
  }

  var handleForm = function(){
    $(document).on('click', '#save-cat', function(){
      var data = {
        action : 'check-form',
        name : $('input[name="name"]').val(),
        link : $('input[name="link"]').val(),
        id : $('input[name="id"]').val()
      }

      handleAjax(data);
    });
  }

  var handleImgUpload = function(path, thumb, id){
    if (thumb.length == 0) return false;
    var btn = document.getElementById('upload-'+id+'-btn'),
    wrap = document.getElementById(id+'-progress-wrap'),
    picBox = document.getElementById(id+'box'),
    errBox = document.getElementById(id+'-errormsg');
    progressBar = document.getElementById(id+'-progressBar');
    progressOuter = document.getElementById(id+'-progressOuter');

    var uploader = new ss.SimpleUpload({
          button: btn,
          url: '/ajax/file-upload/',
          name: 'uploadfile',
          multiple: true,
          maxUploads: 2,
          maxSize: 1024,
          queue: false,
          allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
          accept: 'image/*',
          debug: true,
          hoverClass: 'btn-hover',
          focusClass: 'active',
          disabledClass: 'disabled',
          responseType: 'json',
          data: {'path' : path},
          startXHR: function() {
              progressOuter.style.display = 'block'; // make progress bar visible
              this.setProgressBar( progressBar );
          },
          onSubmit: function(filename, ext) {
             var prog = document.createElement('div'),
                 outer = document.createElement('div'),
                 bar = document.createElement('div'),
                 size = document.createElement('div'),
                 self = this;

              prog.className = 'prog';
              size.className = 'size';
              outer.className = 'progress';
              bar.className = 'bar';

              outer.appendChild(bar);
              prog.appendChild(size);
              prog.appendChild(outer);
              wrap.appendChild(prog); // 'wrap' is an element on the page

              self.setProgressBar(bar);
              self.setProgressContainer(prog);
              self.setFileSizeBox(size);

              errBox.innerHTML = '';
              btn.value = 'Выбрать другой файл';
            },
            onSizeError: function() {
                  errBox.innerHTML = 'Размер файла не должен превышать 1024мб.';
            },
            onExtError: function() {
                errBox.innerHTML = 'Неверный тип файлы. Пожалуйста выберите изображение типа PNG, JPG, GIF.';
            },
          onError: function() {
              progressOuter.style.display = 'none';
              errBox.innerHTML = 'Невозможно загрузить файл';
            },
          onComplete: function(file, response) {
              if (!response) {
                errBox.innerHTML = 'Невозможно загрузить файл';
              }
              if (response.success === true) {
                $(wrap).fadeIn();
                picBox.innerHTML = '<img src="'+response.path+response.file + '">';
                picBox.style.display = "block";
                thumb.val(response.file);
                progressOuter.style.display = 'none'; // hide progress bar when upload is completed
              } else {
                if (response.msg)  {
                  errBox.innerHTML = response.msg;
                } else {
                  errBox.innerHTML = 'Невозможно загрузить файл';
                }
              }

            }
    });
  }

  return {
      init: function () {
        handleDatatable();
        handleActions();
        handleSaveCat();
        handleForm();
        handleImgUpload('misc/images/cats/', $('#mainimg'), 'mainimg');
        handleImgUpload('misc/images/cats/', $('#topimg'), 'topimg');
      }
  };
}();
