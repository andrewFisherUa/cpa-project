jQuery(document).ready(function(){
  NEW_News.init();
});


var NEW_News = function(){
  var grid;

  var handleModal = function(){
   $('body').on('click', '.action[data-action="edit"]', function(){

     var data = {
       id: $(this).attr('data-news'),
       action: 'get-form'
     };

     $.ajax({
       url: '/ajax/manage-news/',
       type: 'POST',
       data: jQuery.param(data),
       dataType: "json",
       success: function(result){

        if ( data.id == 0 ) {
          $('#form-news .modal-title').text("Создание новости");
        } else {
          $('#form-news .modal-title').text("Редактирование новости");
        }

         $('#form-news .modal-body').html(result.form);
         $('#form-news .summernote').summernote({height: 200});

         if ($('#news-form select#type-news option:selected').val() >= 1 && $('#news-form select#type-news option:selected').val() <= 4) {
           $('#news-form #goods-news').closest('.form-group').show();
         }
         $('#news-form select#type-news').on('change', function () {
           if (this.value >= 1 && this.value <= 4) {
             $('#news-form #goods-news').closest('.form-group').show();
           } else {
             $('#news-form #goods-news').closest('.form-group').hide();
           }
         });
         $('.select2me').select2({ width: '100%' });

         $(".datetime").datetimepicker({
            autoclose: !0,
            isRTL: App.isRTL(),
            format: "dd MM yyyy - hh:ii",
            pickerPosition: "top-left"
          })

         $('#form-news').modal('show');

       }
     })
   });
 };

 var getMonthNumber = function(monthname){
    var number = ["january",
                  "february",
                  "march",
                  "april",
                  "may",
                  "june",
                  "july",
                  "august",
                  "september",
                  "october",
                  "november",
                  "december"].indexOf(monthname.toLowerCase());
    return number;
  }

  var getTimestamp = function(str){
    if (typeof str === 'undefined' || str === ""){
      return 0
    }

    var date = str.split(" ");
    var time = date[4].split(":");

    var d = {
      y : parseInt(date[2]),
      m : getMonthNumber(date[1]),
      d : parseInt(date[0])
    }

    var t = {
      h : parseInt(time[0]),
      m : parseInt(time[1])
    }

    var timestamp = new Date(d.y, d.m, d.d, t.h, t.m).getTime();
    return timestamp/1000;
  }

 var SaveNews = function(){

   $('body').on('click', 'a.savenews', function(){

    var errors = [];


    if( $('#form-news input[name=title]').val() == "" ){
      errors.push('Не заполнено название новости');
    }

    if( $('#news-form .note-editable').text() == "" ){
      errors.push('Не заполнено описание новости');
    }

    if (errors.length) {
      $('.error-news .alert-danger').show().text('Не заполнено название новости');
      return false;
    }

   var data = {
     id: $(this).data('news'),
     title: $('#form-news input[name=title]').val(),
     content: $('#form-news .summernote').code(),
     type: $('#form-news select[name=type] option:selected').val(),
     good_id: $('#form-news select[name=goods] option:selected').val(),
     status: $('#form-news #status-news option:selected').val(),
     activate_time: getTimestamp($('#activate_time').val()),
     action: 'save'
   }

   $('#form-news').modal('hide');

   $.ajax({
     url: '/ajax/manage-news/',
     type: 'POST',
     data: jQuery.param(data),
     success: function (result) {
       grid.getDataTable().ajax.reload();
     }
   });
   });

 }

 var DeleteNews = function(){
   $('body').on('click', '.action[data-action="delete"]', function(){

     if (confirm("Вы действительно хотите удалить новость?")) {
        var data = {
         id: $(this).data('news'),
         action: "delete"
       }

       $.ajax({
         url: '/ajax/manage-news/',
         type: 'POST',
         data: jQuery.param(data),
         success: function (result) {
           grid.getDataTable().ajax.reload();
         }
       });
     }
   });
 }

 var handleDatatable = function(){
   grid = new Datatable();
   grid.init({
     src: $("#news_datatable"),
     loadingMessage: 'Загрузка...',
     dataTable: {
       "language": datatables_defaults.lang,
       "pageLength": 25, // default record count per page
       "ajax": {
         "url": '/ajax/get-new-news/' // ajax source
       },
      fnDrawCallback: function( oSettings ) {
        $(":checkbox").uniform();
        $('.date-picker').datepicker()
      },
     }
   });

   grid.getDataTable().on('page.dt', function () {
      $('.table-group-action-input').each(function(){
        var actionName = $(this).attr('name');
        if ($(this).is('select')) {
          var actionValue = $(this).find('option:selected').val();
          if ( actionValue != '-1' ) {
            grid.setAjaxParam(actionName, actionValue);
          }
        } else {
          grid.setAjaxParam(actionName, $(this).val());
        }
    })
   });

   }

  var getTimestamp = function(str){
    if (typeof str === 'undefined' || str === ""){
      return 0
    }

    var date = str.split(" ");
    var time = date[4].split(":");

    var d = {
      y : parseInt(date[2]),
      m : getMonthNumber(date[1]),
      d : parseInt(date[0])
    }

    var t = {
      h : parseInt(time[0]),
      m : parseInt(time[1])
    }

    var timestamp = new Date(d.y, d.m, d.d, t.h, t.m).getTime();
    return timestamp/1000;
  }


 return {
   init : function () {
     handleDatatable();
     handleModal();
     SaveNews();
     DeleteNews();
   }
 };

}();
