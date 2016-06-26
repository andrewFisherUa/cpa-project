jQuery(document).ready(function(){
  singleProduct.init();
});

var singleProduct = function () {

    function handleMultiUpload(path){

      var btn = document.getElementById('m-upload-btn'),
      wrap = document.getElementById('m-pic-progress-wrap'),
      picBox = document.getElementById('m-picbox'),
      errBox = document.getElementById('m-errormsg');
      progressBar = document.getElementById('m-progressBar');
      progressOuter = document.getElementById('m-progressOuter');

      var uploader = new ss.SimpleUpload({
            button: btn,
            url: '/ajax/file-upload/',
            name: 'uploadfile',
            multiple: true,
            maxUploads: 10,
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
              },
              onSizeError: function() {
                    errBox.innerHTML = 'Размер файла не должен превышать 1мб.';
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

                   $.ajax({
                    url: '/ajax/mod-offer-img/',
                    type: 'POST',
                    data: $.param({name: response.file, action: 'add'}),
                    success: function( result ){
                      $('#images-table tbody').html(result);
                    }
                  })

                  progressOuter.style.display = 'none'; // hide progress bar when upload is completed
                } else {
                  if (response.msg)  {
                    errBox.innerHTML = response.msg;
                  } else {
                    errBox.innerHTML = 'Невозможно загрузить файл';
                  }
                }

              }
            })
    }

    var handleImages = function(){
      $('#images-table').on('click', '.btn-remove', function(){
        var imageID = $(this).attr('data-image');
        if ( $('.image'+imageID).prop('checked')  ) {
          $('#mainImg').attr('data-image', false);
          $('#mainImg').attr('src', '/misc/images/images/placeholder.jpg');
        }
        $.ajax({
          url: '/ajax/mod-offer-img/',
          type: 'POST',
          data: $.param({'id': imageID, action: 'remove'}),
          success: function( result ){
            $('#images-table tbody').html(result);
            var replace = '', replaceOpt = $('#images-table input[type="radio"]:first-child');
            if ( replaceOpt.length ) {
              replaceOpt.prop("checked", true);
              replace = replaceOpt.val();
            }
              changeMainImg(replace);

          }
        })
        $('#add-offer-form').attr("data-changed", true);
      })
    }

    return {
        init: function () {
          handleMultiUpload('misc/images/goods/');
          handleImages();
        }
    };
}();