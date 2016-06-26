jQuery(document).ready(function(){
  Banners.init();
});

var Banners = function () {

  var reloadBanners = function(){
    var data = {
      size: $('#banner-size-filter option:selected').val(),
      type: $('#banner-type-filter option:selected').val(),
      subject : "-1",
      action : "get-filtered"
    }

    if (data.type == "1") {
      data.subject = $('#banner-category-filter option:selected').val();
    }

    if (data.type == "2") {
      data.subject = $('#banner-product-filter option:selected').val();
    }

    handleAjax(data);
  }

  var handleFilters = function(){
    $(document)
      // Применение фильтров
      .on('click', '.submit-filters', function(){
        var data = {
          size: $('#banner-size-filter option:selected').val(),
          type: $('#banner-type-filter option:selected').val(),
          subject : "-1",
          action : "get-filtered"
        }

        if (data.type == "1") {
          data.subject = $('#banner-category-filter option:selected').val();
        }

        if (data.type == "2") {
          data.subject = $('#banner-product-filter option:selected').val();
        }

        handleAjax(data);
      })
      // Отмена фильтров
      .on('click', '.reset-filters', function(){
        $('#banner-size-filter option:first-child').prop('selected', true).select2();
        $('#banner-type-filter option:first-child').prop('selected', true).select2();
        $('#banner-product-filter option:first-child').prop('selected', true).select2();
        $('#banner-category-filter option:first-child').prop('selected', true).select2();
        $('#banner-category-filter-wrap').fadeOut();
        $('#banner-product-filter-wrap').fadeOut();

        reloadBanners();
      })
      .on('change', '.filter', function(){
        var id = $(this).attr('id'),
            type = $('#banner-type-filter option:selected').val();

        if (id == "banner-type-filter") {
          if (type == 1) {
            $('#banner-product-filter-wrap option[value="-1"]').prop('selected', true);
            $('#banner-product-filter-wrap').fadeOut(150, function(){
              $('#banner-category-filter-wrap').fadeIn(150);
            });
          } else if (type == 2) {
            $('#banner-category-filter-wrap option[value="-1"]').prop('selected', true);
            $('#banner-category-filter-wrap').fadeOut(150, function(){
              $('#banner-product-filter-wrap').fadeIn(150);
            });
          } else {
            $('#banner-category-filter-wrap option[value="-1"], #banner-product-filter-wrap option[value="-1"]').prop('selected', true);
            $('#banner-category-filter-wrap, #banner-product-filter-wrap').fadeOut(150);
          }
        }
      })
      // Удаление баннера
      .on('click', '.banners-wrap .actions .btn-remove', function(){
        if (confirm("Вы действительно хотите удалить баннер?")) {
          var data = {
            "action" : "remove-banner",
            "id" : $(this).data("id")
          }

          handleAjax(data);
        }

      })
      // Редактирование баннера
      .on('click', '.banners-wrap .actions .btn-edit', function(){
        var data = {
          "action" : "get-form",
          "id" : $(this).data("id")
        }

        handleAjax(data);
      })
      // Отмена редактирования баннера
      .on('click', '#cancel-edit', function(){
        var data = {
          action : "get-form",
          id : 0
        }
        handleAjax(data);
        return false;
      })
  }

  var handleAjax = function(data){
    $.ajax({
      url: '/ajax/manage-banners/',
      type: 'POST',
      dataType: 'json',
      data: $.param(data),
      success: function(response){
        if (data.action == "get-cats" || data.action == "get-products") {
          $('#banner-subject').html(response.rows);
          $('#banner-subject-wrap').fadeIn();
          $('#banner-subject').select2().select2("val", response.selected);
          $('input[name="banner[link]"]').val(response.link);
        }

        if (data.action == "get-link") {
          $('input[name="banner[link]"]').val(response.link);
        }

        if (data.action == "get-filtered") {
          $('.banners-wrap').html(response.rows);
          $('html,body').animate({
            scrollTop: $('.banners-wrap').offset().top - 100
          }, 100);
        }

        if (data.action == "remove-banner") {
          $('.thumb[data-id="'+data.id+'"]').fadeOut(150).remove();
        }

        if (data.action == "get-form") {
          $('#edit-banner-form').html(response.form);
          handleImgUpload('misc/images/banner/', $('#banner'), 'banner');
          if (response.data.type == 1 || response.data.type == 2) {
            $('#banner-subject').html(response.rows);
            $('#banner-subject option[value="'+response.data.subject+'"]').prop('selected', true)
          }
        }
      }
    })
  }

  var handleType = function(){
    $(document)
      // Изменение типа баннера
      .on('change', 'select[name="banner[type]"]', function(){
        var val = $(this).find("option:selected").val();

        if (val == 1 || val == 2) {
          var action = (val == 1) ? "get-cats" : "get-products";
          var data = {
            "action" :  action
          }
          handleAjax(data);
          $('input[name="banner[link]"]').prop('readonly', true);
        } else {
          $('#banner-subject-wrap').fadeOut();
          $('input[name="banner[link]"]').prop('readonly', false);
        }
      })
      // Изменение товара или категории
      .on('change', '#banner-subject', function(){
        var data = {
          type : $('select[name="banner[type]"] option:selected').val(),
          id : $(this).find("option:selected").val(),
          action : "get-link"
        }

        handleAjax(data);
      })
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
              $('#upload-banner-btn').fadeOut();
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
                picBox.innerHTML = '<img src="'+response.path+response.file + '" alt="" class="img-responsive">';
                picBox.style.display = "block";
                thumb.val(response.file);
                $('#upload-banner-btn').fadeIn();
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
        handleFilters();
        handleType();
        handleImgUpload('misc/images/banner/', $('#banner'), 'banner');
      }
  };
}();

function validateForm() {
  if ( $('input[name="banner[name]"]').val() == "") {
    $('.alert').text("Необходимо выбрать изображение").fadeIn();
    return false;
  }
  return true;
}
