var datatables_defaults = {
  'lang' : { // language settings
      // metronic spesific
      "metronicGroupActions": "",
      "metronicAjaxRequestGeneralError": "Невозможно выполнить запрос. Пожалуйста, проверьте подключение к интернету.",

      // data tables spesific
      "lengthMenu": "<span class='seperator'>|</span>Просмотр _MENU_ записей",
      "info": "<span class='seperator'>|</span> Найдено всего _TOTAL_ записей",
      "infoEmpty": "Записи не найдены",
      "emptyTable": "Нет записей в таблице",
      "zeroRecords": "Не найдено записей",
      "paginate": {
          "previous": "Назад",
          "next": "Вперед",
          "last": "В конец",
          "first": "В начало",
          "page": "Страница",
          "pageOf": "из"
      }
  },
}

jQuery(document).ready(function(){
    $ = jQuery;

    getBages();

    $('[data-toggle="tooltip"]').tooltip();

    $('body').on('keyup keypress', 'input.numbers-only', function(e) {
      if (e.keyCode == 8 || e.keyCode == 46) {}
      else {
           var letters='1234567890';
           return (letters.indexOf(String.fromCharCode(e.which))!=-1);
        }
    })

    if ($('.money').length) {
      $('.money').mask('# ### ### ### ###', {reverse: true});
    }

    $('.page-header.navbar').on('click', '.logout', function(){
      var data = {
        action: "logout"
      }
      $.ajax({
        url: '/ajax/manage-users/',
        type: 'POST',
        dataType: "json",
        data: $.param( data ),
        success: function( response ){
          window.location.reload();
        }
      })
    })

    $('.login-as-panel').on('click', '.login-as', function(){
      var data = {
        action: "login-as",
        role: $(this).data('role')
      }

      $.ajax({
        url: '/ajax/manage-users/',
        type: 'POST',
        dataType: "json",
        data: $.param( data ),
        success: function( response ){
          window.location = response.url;
        }
      })
    })

    $('body').on('click', 'a[data-toggle="tab"]', function(){
      $('.nav-tabs a[href='+$(this).attr('href')+']').tab('show');
    })

    // Включение / отключение оффера на странице оффера
    $('#page-offer-view').on('click', '.remove-user-good', function(){
        if (confirm("Вы действительно хотите отключить оффер?")) {
          var btn = $(this);
          handleUserOffers( btn );
        }
    })

    // Включение / отключение оффера на странице оффера
    $('#page-offer-view').on('click', '.add-user-good', function(){

      if ($(this).attr('data-rules') == "0") {
        var data = {
          "action" : "add",
          "g_id" : $(this).attr('data-g_id')
        }

        $.ajax({
          url: '/ajax/add-user-good/',
          dataType: 'json',
          type: 'POST',
          data: $.param(data),
          success: function( result ){
            location.reload();
          }
        })

        return false;
      }

      var data = {
        action : "get-rules",
        id : $(this).attr('data-g_id')
      };

       $.ajax({
        url: '/ajax/manage-offer-rules/',
        type: 'POST',
        dataType: 'json',
        data: $.param(data),
        success: function(response){
          $('#rulesModal #add-offer').attr('data-g_id', data.id);
          $('#rulesModal #rules-wrap').html(response.text);
          $('#rulesModal').modal('show');
        }
      })
    })

    $('#page-offer-view').on('click', '#rulesModal #add-offer', function(){
      var data = {
        "action" : "add",
        "g_id" : $(this).attr('data-g_id')
      }

      $.ajax({
        url: '/ajax/add-user-good/',
        dataType: 'json',
        type: 'POST',
        data: $.param(data),
        success: function( result ){
          location.reload();
        }
      })

    })

    if ( $(".fancybox").length ) {
      $(".fancybox").fancybox({
        prevEffect  : 'none',
        nextEffect  : 'none',
        helpers : {
          title : {
            type: 'outside'
          },
          thumbs  : {
            width : 50,
            height  : 50
          }
        }
      });
    }

    $('body').on('click', '#success-message .close', function(){
      $('#success-message').stop().fadeOut();
    })

    var url = document.location.toString();

    if (url.match('#')) {
        $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
    } else {
      $('.nav-tabs a:first').tab('show');
    }

})


function getBages(){
  $.ajax({
      url: '/ajax/get-bages/',
      dataType: 'json',
      success: function(result){
        for (var i = 0; i < result.length; i++) {
          $('.page-sidebar-menu a[href="' + result[i].url + '"]').append('<span class="badge badge-awesome">'+result[i].num+'</span>');
        };
      }
    })
}

function ShowPreload(message, time){
    if (typeof time === 'undefined') {
      time = 1500;
    }

    if ( !message ) message = "<i class='glyphicon glyphicon-ok'></i>&nbsp;Сохранено";
    jQuery('#success-message .message').html(message);
    jQuery('#success-message').fadeIn().delay(time).fadeOut();
}

// Включение / отключение офферов
function handleUserOffers( btn ){
  var g_id = btn.data('g_id');
  var action = '', message = '';

  if ( btn.data('action') == "connect" ) {
    action = "add";
    message = "Оффер успешно подключен"
  } else {
    action = "remove";
    message = "Оффер успешно отключен"
  }

  $.ajax({
      url: '/ajax/add-user-good/',
      dataType: 'json',
      type: 'POST',
      data: 'g_id='+g_id+'&action='+action,
      success: function( result ){
        if ( btn.data('action') == "reload" ) location.reload();
      }
    })
}

handleGroupSelect = function(){

    $.ajax({
      url: '/ajax/get-group-content/',
      type: 'POST',
      dataType: 'json',
      data: $.param( { action: "load-groups" } ),
      success: function( result ){
		 if ($('#content_group').hasClass('select2me')) {
				$('#content_group').select2("destroy")
		 }

		 if ($('#content_landing').hasClass('select2me')) {
			$('#content_landing').select2("destroy")
		 }
        $('#content_group').html( "<option value=''></option>" + result.rows.groups ).select2({width: '100%'});
        $('#content_landing').html( "<option value=''></option>" + result.rows.landings ).select2({width: '100%'});
      }
    })

    $('body').on("change", "#content_group", function(){
      var data = {
        g_id : $(this).val(),
        action : "load"
      }

      $.ajax({
        url: '/ajax/get-group-content/',
        type: 'POST',
        dataType: 'json',
        data: $.param(data),
        success: function( result ){
          $('#content_landing').select2("destroy").html( "<option value=''></option>" + result.rows.landings ).select2();
        }
      })
    })

    $('#content_landing').change(function(){
       $('#add-landing').prop("disabled", false);
    })

    $('#content_group').change(function(){
       $('#add-landing').prop("disabled", true);
    })
}