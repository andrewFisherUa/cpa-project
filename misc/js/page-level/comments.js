jQuery(document).ready(function(){
  Comments.init();
});

var Comments = function () {
  var grid;

  // Управление таблицей потоков
  var handleDatatable = function(){
    if (!$("#datatable_comments").length) return false;



    grid = new Datatable();

    grid.init({
        src: $("#datatable_comments"),
        loadingMessage: 'Загрузка...',
        dataTable: {
           "dom": "<'row'<'col-md-7 col-sm-12'pli><'col-md-5 col-sm-12'<'table-group-actions pull-right'>>r><'table-scrollable't><'row'<'col-md-7 col-sm-12'pli><'col-md-5 col-sm-12'>>",
            "language": {
                // metronic spesific
                "metronicGroupActions": "",
                "metronicAjaxRequestGeneralError": "Невозможно выполнить запрос. Пожалуйста, проверьте подключение к интернету.",

                // data tables spesific
                "lengthMenu": "<span class='seperator'>|</span>Просмотр _MENU_ записей",
                "info": "<span class='seperator'>|</span> Всего _TOTAL_ записей",
                "infoEmpty": "Записи не найдены",
                "emptyTable": "Нет записей в таблице",
                "zeroRecords": "Не найдено записей",
                "paginate": {
                    "previous": "Назад",
                    "next": "Вперед",
                    "last": "В конец",
                    "first": "В начало",
                    "page": "",
                    "pageOf": "из"
                }
              },
            "pageLength": 25, // default record count per page
            "ajax": {
                "url": '/ajax/get-comments/'
            },
            fnDrawCallback: function( oSettings ) {
              handleEditable();
              $(':checkbox').uniform();
              $('#datatable_comments .check[data-viewed="0"]').each(function(){
                $(this).closest("tr").addClass('bold');
              })
            },
        },
    });

     // handle group actionsubmit button click
    grid.getTableWrapper()
      .on('click', '.table-group-action-submit', function (e) {
          e.preventDefault();
          var action = $(".table-group-action-input", grid.getTableWrapper());
          if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
              if (action.val() == "delete" && !confirm("Вы действительно хотите удалить эти записи?"))  {
                return false
              }
              grid.setAjaxParam("customActionType", "group_action");
              grid.setAjaxParam("customActionName", action.val());
              grid.setAjaxParam("id", grid.getSelectedRows());
              grid.getDataTable().ajax.reload();
              grid.clearAjaxParams();
          } else if (action.val() == "") {
              Metronic.alert({
                  type: 'danger',
                  icon: 'warning',
                  message: 'Действие не выбрано',
                  container: grid.getTableWrapper(),
                  place: 'prepend'
              });
          } else if (grid.getSelectedRowsCount() === 0) {
              Metronic.alert({
                  type: 'danger',
                  icon: 'warning',
                  message: 'Для выполнения действия необходимо выбрать хотя бы одну строку таблицы',
                  container: grid.getTableWrapper(),
                  place: 'prepend'
              });
          }
      })

      .on('click', '.filter-reset', function(){
        $('.form-filter, .table-group-action-input').each(function(){
          elem = $(this);
          if (elem.is('select')) {
            elem.find("option:first-child").prop("selected", true);
            elem.select2();
          } else {
            elem.val("");
          }
        })

        grid.clearAjaxParams();
        grid.getDataTable().ajax.reload();
      })

      // Обработка изменения значения фильтров
      .on('change', '.form-filter', function () {
        $('select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function() {
            grid.setAjaxParam($(this).attr("name"), $(this).val());
        });

        grid.getDataTable().ajax.reload();
      })

      // Обработка изменения значения фильтров
      .on('keyup', 'input[type="text"].form-filter', function () {
        $('select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function() {
            grid.setAjaxParam($(this).attr("name"), $(this).val());
        });

        grid.getDataTable().ajax.reload();
      });

      $('#commentModal').on('hidden.bs.modal', function (e) {
        grid.getDataTable().ajax.reload();
      })
  }

  var handleEditable = function() {
    source = [ {value: 1, text: 'На модерации'},
               {value: 2, text: 'Опубликован'},
               {value: 3, text: 'Архив'},
               {value: 4, text: 'Отклонен'} ];

    $('.editable').each(function(){
      $(this).editable({
        source: source,
        autotext: 'always',
        params: { 'id': $(this).data('id'), 'action' : 'change-status' },
        url: '/ajax/manage-comments/',
        success: function(){
          grid.getDataTable().ajax.reload();
          grid.clearAjaxParams();
        }
      });
    })
  }

  var handleAjax = function(data){


    $.ajax({
        url: '/ajax/manage-comments/',
        type: 'POST',
        dataType: 'json',
        data: $.param(data),
        success: function(response){

          if (data.action == "get-form") {
            $('#commentModal .modal-body').html(response.form);
            $('#commentModal').modal('show');
            $(':checkbox').uniform();
            if (data.id == 0) {
              $('#comment-good').select2();
            }
          }

          if (data.action == "save-comment") {
            $('#commentModal .modal-body').html("");
            $('#commentModal').modal("hide");
          }

          if (data.action == "delete-comment") {
            $('#commentModal').modal("hide");
            ShowPreload("Отзыв удален");
          }


        }
      })
  }

  var handleForm = function(){

    $(document)
      // Открытие окна редактирования комментария
      .on('click', 'a[href="#commentModal"]', function(){
        var data = {
          action : 'get-form',
          id : $(this).data('comment')
        }

        handleAjax(data);
        return false;
      })
      // Сохранение комментария
      .on('click', '#commentModal .btn-save', function(){
        var data = {
          id : $('#comment-id').val(),
          shop_id : $('#comment-shop option:selected').val(),
          good_id : $('#comment-good option:selected').val(),
          name : $('#comment-name').val(),
          content : $('#comment-content').val(),
          reply : $('#comment-reply').val(),
          status : $('#comment-status option:selected').val(),
          score : $('#comment-score option:selected').val(),
          country_code : []
        }

        $('.shop-country_code:checked').each(function(){
          data.country_code.push($(this).val())
        })

        var errors = [];

        if (data.name == "") {
          errors.push("Добавьте имя автора отзыва");
        }

        if (data.content == "") {
          errors.push("Введите текст отзыва");
        }

        if (data.country_code.length == 0) {
          errors.push("Необходимо выбрать хотя бы одну страну");
        }

        if (errors.length) {
          $('#commentModal .alert').html(errors.join("<br />")).fadeIn();
          return false;
        }

        data.action = "save-comment";
        handleAjax(data);

        return false;
      })
      // Удаление комментария
      .on('click', '#commentModal .btn-delete', function(){
        if (confirm('Вы действительно хотите удалить этот отзыв?')) {
          var data = {
            id : $('#comment-id').val(),
            action: "delete-comment"
          }

          handleAjax(data);
        }

      })
  }

  /*
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
  */


  return {
      init: function () {
        handleDatatable();
        handleForm();
        //handleActions();
      }
  };
}();
