$(document).ready(function(){
    $('#adminloginok').click(function(){
        checkAuth();
        return false;
    })

    $('#forgot-passEmail').click(function(){
        sendEmail();
        return false;
    })

    $('input').keydown(function(event) {
        $('.form-error').fadeOut();
        $(this).parent().removeClass('has-error');
    });

    $("a[href='#forgot-pass']").click(function(){
      $('#auth').fadeOut(500, function(){ $('#forgot-pass').fadeIn(500) });
      return false;
    })

    $("a[href='#auth']").click(function(){
      $('#forgot-pass').fadeOut(500, function(){ $('#auth').fadeIn(500) });
      return false;
    })

    $('#recoveryPass').click(function(){
      recoveryPass();
      return false;
    })

    $('#recovery-form input').change(function(){
      $('.form-error').fadeOut();
    })

})

function recoveryPass(){
  var data = {
    key : $("#key").val(),
    email : $("#email").val(),
    password : $("#password").val(),
    confirmpassword : $("#confirmpassword").val()
  }
  
  jQuery.ajax({
    url: '/ajax/pass-recovery/',
    type: 'POST',
    dataType: 'json',
    data: data,
    success: function( response ) {
      if ( response.success ) {
        $('#wrapper').fadeOut();
        $('#success').fadeIn();
      }

      if ( response.error ) {
        $('.form-error').html(response.error).fadeIn();
      }
    }
  });
}

function checkAuth(){
    var login = $('#login').val(),
        pass = $('#pass').val(),
        err = '';

    if ( login=='' ) {
      $('#login').parent().addClass('has-error');
      err = "Введите логин.";
    }
    if ( pass=='' ) {
      $('#pass').parent().addClass('has-error');
      err += "<br /> Введите пароль.";
    }

    if ( err!='' ) {
      $('#auth .form-error').html(err).fadeIn();
    } else {
      jQuery.ajax({
        url: '/ajax/login/',
        type: 'POST',
        data:'alogin='+login+'&apassword='+pass,
        success: function(result) {
          if (result == '') {
              $('#form1').submit();
          } else {
              $('#auth .form-error').text(result).fadeIn();
          }
          
        }
      });
    }
}

function sendEmail() {
  var err = '', 
      email = $('#email').val();
  if (email == '') {
    err = 'Введите Ваш электронный адрес.';
  } else if (!isEmail(email)) {
    err = 'Неправильный формат электронной почты.'
  }

  if (err!='') {
    $('#forgot-pass .form-error').text(err).fadeIn();
  } else {
    jQuery.ajax({
      url: '/ajax/forgot-pass/',
      type: 'POST',
      data: 'email='+$('#email').val(),
      success: function(result) {
        console.log(result);
        if ( result != '' ) {
          $('#forgot-pass .form-error').text(result).fadeIn();
        } else {
          $('#wrapper').fadeOut(300, function(){ $('#success').fadeIn(300) })
        }
      }
    });
  }
}

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}