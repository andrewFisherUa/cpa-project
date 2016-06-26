jQuery(document).ready(function(){
  singleOffer.init();
});

var singleOffer = function () {

    var handleAjax = function(data){
      $.ajax({
        url: '/ajax/manage-offer-prices/',
        type: 'POST',
        dataType: 'json',
        data: $.param(data),
        success: function(response){
          if (data.action == "save-target") {
            $('#offers-table-wrapper').html(response.rows);
            $('#edit-offer-modal').modal('hide');
          }

          if (data.action == "edit-target") {
            $('#edit-offer-modal .modal-body').html(response.rows);
            $('#offer-price-table :checkbox').uniform();
            $('#edit-offer-modal').modal('show');
            $('#add-webmaster').select2();
          }

          if (data.action == "save-price") {
            $("#geo-list").html(response.list);
            $('#offers-table-wrapper').html(response.rows);
            $('#edit-geo').modal('hide');
            $('#edit-geo .alert').fadeOut();
            $('#add-offer-form').attr("data-changed", true);
          }

          if (data.action == "remove-price") {
            $("#geo-list").html(response.list);
            $('#offers-table-wrapper').html(response.rows);
          }

        }
      })
    }

    var handleExit = function(){
      window.onbeforeunload = function(e) {
        if ( $('#add-offer-form').attr("data-changed") == "true" ) {
          return "Изменения не были сохранены!";
        }
      };
    }

    var handleCKEditor = function () {
        $('.ckeditor').each(function(){
          CKEDITOR.replace( $(this).attr('id') );
        })
    }

    var handleForm = function(){
      $('#add-offer-form').submit(function(){
        for (var i in CKEDITOR.instances) {
          var id = CKEDITOR.instances[i].name;
          var textID = $('#'+id).data('text');
          $('#' + textID).val( CKEDITOR.instances[i].getData() );
        }

        var ids = $('#tree_2').jstree('get_checked');
        var arr = []; var rows = "";

        for (var i=0; i < ids.length; i++) {
		  arr = ids[i].split("-");
          if (arr[0] == "landing") {
			if (arr[2] == "blog") {
				rows += "<input type='hidden' name='contents[blogs]["+arr[1]+"][]' value='"+arr[3]+"'>";
			} else {
				rows += "<input type='hidden' name='contents[landings][]' value='"+arr[1]+"'>";
			}
		  }
        };

        $('#add-offer-form').append( rows );
        $('#add-offer-form').unbind('submit').submit();
      })

      $('body').on('change', '#offer-type', function(){
        var value = $(this).find('option:selected').val();

        if ( value == "0" ) {
          $('input[name="options[available_in_shop]"]').prop("checked", true).uniform();
          $('input[name="options[available_in_offers]"]').prop("checked", true).uniform();
        }

        if ( value == "2" ) {
          $('#available_webmaster').fadeIn();
        }

        if ( value != "2" && $('#available_webmaster').is(':visible') ) {
          $('#available_webmaster :checkbox').attr("checked", false).uniform();
          $('#available_webmaster').fadeOut();
        }

        if (value == "3"){
          $('#webmaster-list').fadeIn();
        } else {
          $('#webmaster-list').fadeOut();
        }

      })
    }

    var handleTargets = function () {
      $(document)
        // Сохранение цели
        .on('click', '#save-offer-target', function(){
          var params = {
            action: "save-target",
            target: $('#edit-offer-modal input[name="target"]').val(),
            offer_id : $('input[name="offer[id]"]').val(),
            webmasters: [],
            targets: []
          };

          $('#offer-price-table .select-row:checked').each(function(){
            var row = $(this).closest("tr");
            var data = {
              code: row.data('code'),
              max_price : row.find("input[name='max_price']").val(),
              commission : row.find("input[name='commission']").val(),
              webmaster_commission : row.find("input[name='webmaster_commission']").val()
            }
            params.targets.push(data);
          })

          $('#selected-webmasters .tag').each(function(){
            params.webmasters.push({id: $(this).data("id"), login: $(this).data("login")});
          });

          handleAjax(params);
        })
        // Выбор страны
        .on('change', '#offer-price-table :checkbox', function(){
          if ($(this).prop("checked") == true) {
            $(this).closest("tr").css("opacity", "1");
          } else {
            $(this).closest("tr").css("opacity", "0.5");
          }
        })
        //Редактирование или добавление цели
        .on('click', '#targets .btn-edit', function(){
          var btn = $(this),
              params = {
                target: $(this).data('target'),
                offer_id : $('input[name="offer[id]"]').val(),
                action: "edit-target"
              };
          handleAjax(params);
        })
        // Добавление вебмастера к цели "подтвержденный заказ"
        .on('click', '#add-webmaster-btn', function(){
          var id = $("#add-webmaster option:selected").val(),
              login = $("#add-webmaster option:selected").data("login");
          if (!$('#selected-webmasters span[data-id="'+id+'"]').length) {
            $('#selected-webmasters').append('<span class="tag label label-info" data-id="'+id+'" data-login="'+login+'">'+id+': <span class="login">'+login+'</span><span data-role="remove"></span></span>');
          }
        })
        //Удаление тега
        .on('click', '#selected-webmasters .tag span[data-role="remove"]', function(){
          var data = {
            id : $(this).parent().data("id"),
            action : "remove-webmaster-from-target"
          }
          handleAjax(data);
          $(this).parent().remove();
        })
    }

    // Обновление списка стран
    var updGeoList = function(){
      $('#edit-geo #country-name option').each(function(){
        code = $(this).val();
        var disabled = $('#countries-list .btn-edit[data-code="'+ code +'"]').length;
        $(this).prop('disabled', disabled);
      })
    }

    var handleGeo = function(){
      var priceModal = $('#edit-geo'),
          fields = {
            'country_code' : priceModal.find('#country-name'),
            'qty' : priceModal.find('#country-qty'),
            'price' : priceModal.find('#country-price'),
            'price_id' : priceModal.find('#country-price_id'),
            'btn' : priceModal.find('.btn-save')
          }

      fields.country_code.change(function(){
        fields.btn.prop('disabled', $(this).val() == -1 );
      })

      $('#countries-list')
        // Редактирование страны
        .on('click', '.btn-edit', function(){
          var data = {
            code: $(this).data('code'),
            offer_id : $('input[name="offer[id]"]').val(),
            action : 'edit-price'
          }

          $.ajax({
            url: '/ajax/manage-offer-prices/',
            type: 'POST',
            dataType: 'json',
            data: $.param(data),
            success: function(response){
              fields.country_code.prop('disabled', true);
              fields.country_code.find('option[value='+ data.code +']').prop('selected', true);
              fields.qty.val(response.qty);
              fields.price.val(response.price);
              fields.price_id.val(response.price_id);
              priceModal.modal('show');
            }
          })
        })
        // Добавление страны
        .on('click', '.btn-new', function(){
          fields.btn.prop('disabled', true);
          fields.country_code.prop('disabled', false);
          fields.country_code.find('option[value="-1"]').prop('selected', true);
          fields.price.val("");
          fields.price_id.val("");
          fields.qty.val("");
          updGeoList();
          priceModal.modal('show');
        })
        // Удаление страны
        .on('click', '.btn-remove', function(){
          var data = {
            country_code : $(this).data("code"),
            offer_id : $('input[name="offer[id]"]').val(),
            action: "remove-price"
          }

          handleAjax(data);
          $('#add-offer-form').attr("data-changed", true);
        })

      // Сохранение страны
      priceModal.on('click', '.btn-save', function(){
        var errors = [];
        var data = {
          country_code : fields.country_code.find("option:selected").val(),
          price : fields.price.val(),
          price_id : fields.price_id.val(),
          qty : fields.qty.val(),
          offer_id : $('input[name="offer[id]"]').val(),
          action : "save-price"
        }

        if ( data.price == '' ) {
          errors.push("Введите цену");
        }

        if ( data.price_id == '' ) {
          errors.push("Введите код цены");
        }

        if ( errors.length ) {
          $('#edit-geo .alert').html(errors.join("<br />")).fadeIn();
          return false;
        }

        handleAjax(data);
      })
    }

    var handleLogoUpload = function(path, thumb){
      var btn = document.getElementById('upload-btn'),
      wrap = document.getElementById('pic-progress-wrap'),
      picBox = document.getElementById('picbox'),
      errBox = document.getElementById('errormsg');
      progressBar = document.getElementById('progressBar');
      progressOuter = document.getElementById('progressOuter');

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

                  if ( $('#mainImg').attr('data-image') == "false" ) {
                    $('#mainImg').attr('src', response.path+response.file);

                    $.ajax({
                      url: '/ajax/mod-offer-img/',
                      type: 'POST',
                      data: $.param({name: response.file, action: 'add'}),
                      success: function( result ){
                        $('#images-table tbody').html(result);
                        $('#images-table tbody input[type="radio"]:last-child').prop("checked", true);
                      }
                    })
                  }

                  $('#add-offer-form').attr("data-changed", true);

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

    var detectFormChanges = function(){
      $('#add-offer-form')
        .on('click', 'button[name="save-offer"]', function(){
          $('#add-offer-form').attr('data-changed', false);
          $('fieldset').attr('disabled', false);
          $('#add-offer-form').submit();
        })
        .on('change', 'select, input, textarea, :checkbox, :radio', function(){
          $('#add-offer-form').attr('data-changed', true);
        })
        .on('keyup', '.note-editable', function() {
          $('#add-offer-form').attr('data-changed', true);
        })
    }

    var handleContentTree = function( ) {
      var data = {};
      if ( $('input[name="offer[id]"]').length ) {
        data.offer_id = $('input[name="offer[id]"]').val();
      }

      $.ajax({
        url: '/ajax/get-content-tree/',
        type: "POST",
        dataType: 'json',
        data: $.param(data),
        success: function(result){
          $('#tree_2').jstree({
            'plugins': ["wholerow", "checkbox", "sort", "search", "json_data", "crrm"],
            'core': {
                "themes" : {
                    "responsive": false
                },
                'check_callback': true,
                'data': result.tree,
            }
          });
        }
      })

      $(document).on('click', '#tree_2 .remove-node', function(){
        $("#tree_2").jstree(true).delete_node($(this).data('node'));
      })
    }

    var handleAddLanding = function(){
      $('#add-landing').click(function(){
  		if ($(this).prop('disabled') == true) {
  			return false;
  		}

        var data = {
          l_id: $('#content_landing').val(),
          l_name : $('#content_landing option:selected').text(),
          num: $('#tree_2 .jstree-container-ul > li').length
        }

        $.ajax({
          url: '/ajax/get-content-tree/',
          type: "POST",
          dataType: 'json',
          data: $.param(data),
          success: function(result){
      			if ($("#tree_2 #landing-"+data.l_id).length == 0) {
      				var node_text = data.l_name + " <em class='remove-node' data-node='landing-"+data.l_id+"'>Удалить</em>";
      				$("#tree_2").jstree('create_node', '#', { text : node_text, id : 'landing-'+data.l_id } , 'last');
      			}
      			var blogs = result.node.blogs;
      			for ( var i=0; i < blogs.length; i++ ) {
      				if ($("#tree_2 #landing-"+data.l_id+"-blog-"+blogs[i].id).length == 0) {
      					var node_text = blogs[i].name + " <em class='remove-node' data-node='landing-"+data.l_id+"-blog-"+blogs[i].id+"'>Удалить</em>";
      					$("#tree_2").jstree('create_node', $('#landing-'+data.l_id), { text : node_text, id : "landing-"+data.l_id+"-blog-"+blogs[i].id } , 'last');
      				}
      			}
          }
        });

        handleGroupSelect();
        $('#content_landing').select2("destroy").html("<option value=''></option>" ).select2();
        $(this).prop("disabled", true);
      })
    }

    var handleOptions = function(){
      $('a[href=#tab-options]').click(function (e) {
        e.preventDefault();
        $('#countries-tabs a:first').tab('show');
      })

      // Восстановить значения параметров по умолчанию
      $('body').on('click', '#reset-options', function(){
        var data = {
          action : "get-defaults"
        }

        $.ajax({
          url: '/ajax/manage-options/',
          type: "POST",
          dataType: 'json',
          data: $.param(data),
          success: function(result){
            $('#tab-options .alert-info > p').text("Восстановлены настройки контента по умолчанию");
            $('#tab-options .alert-info').fadeIn();
            $('#options-wrap').html( result.rows );
            $('#countries-tabs a:first').tab('show');
          }
        });
        return false;
      })
    }

    var handleWebmasterList = function(){
      $(document)
        // Добавление вебмастера к списку
        .on("change", '#webmaster-list', function(){
          var option = $(this).find('option:selected');
          var id = option.val();

          if ($('#webmaster-table .remove[data-id="'+id+'"').length) {
            return false;
          }

          var name = option.data("login");
          $('#webmaster-table > tbody').append("<tr><td><input type='hidden' name='webmaster_list[]' value='"+id+"'>"+id+"</td><td>"+name+"</td><td><span class='remove' data-id='"+id+"'><i class='fa fa-close'></i></a></td></tr>")
        })
        // Удаление вебмастера из списка
        .on("click", "#webmaster-table .remove", function(){
          var id = $(this).data("id");
          $(this).closest("tr").remove();
        })
    }

    return {
        //main function to initiate the module
        init: function () {
          detectFormChanges();
          handleCKEditor();
          handleForm();
          handleGeo();
          handleTargets();
          handleLogoUpload('misc/images/goods/', $('#offer-logo'));
          handleExit();
          handleContentTree();
          handleGroupSelect();
          handleAddLanding();
          handleOptions();
          handleWebmasterList();
        }
    };
}();