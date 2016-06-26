var visibleForm;

$(document).ready(function(){

  nav();
  validateForm();
  filter();

  $(':checkbox').uniform();

  visibleForm = $('form:visible');

  centerForm( visibleForm );

  $(window).resize(function(){
    if (visibleForm.length) {
      centerForm(visibleForm);
    }
  })

  $('a[data-form]').click(function(){
    var formID = $(this).attr('data-form');
    var h1, role = $(this).attr('data-role');

    $(this).closest('form').fadeOut(250, function(){
      centerForm($('#'+formID));
      $('#'+formID).fadeIn(250)
    });
      return false;
  })

    $('#adminloginok').click(function(){
        checkAuth();
        return false;
    })

    $('#forget-password-btn').click(function(){
        sendEmail();
        return false;
    })

    $('input').keydown(function(event) {
        $('.form-error').fadeOut();
        $(this).parent().removeClass('has-error');
    });


    $('#recoveryPass').click(function(){
      recoveryPass();
      return false;
    })

    $('#recovery-form input').change(function(){
      $('.form-error').fadeOut();
    })

})

function filter() {
  $('input[name="fphone"]').on('keyup keypress', function(e) {
     if (!(e.keyCode == 8 || e.keyCode == 46)) {
         var letters=' 1234567890()-+';
              return (letters.indexOf(String.fromCharCode(e.which))!=-1);
       }
  })
}

function nav(){

  $(document).on('click', '.mainlevel', function(e){
    e.preventDefault();
    $('.mainlevel').removeAttr("id");
    $(this).attr("id", "active_menu");

    var role = $(this).data('role');
    $('#frole').val(role);
    if (role == "advertiser") {
      $('#fphone')
        .attr("placeholder", "Телефон *")
        .attr("required", true)
    } else {
      $('#fphone')
        .attr("placeholder", "Телефон")
        .attr("required", false)
    }
    $(this).tab('show');
    validateForm();

  })

    if($("#active_menu").length>0){
        var menuWidth = $("#active_menu").outerWidth();
        var menuLeft = $("#active_menu").position().left + 10;
        $("#activeMenu").stop().css({
          left: menuLeft+'px',
          width: menuWidth+'px'
        }).fadeIn();
    }

    $("#tabs a.mainlevel").mouseover(function(){
            var menuWidth = $(this).outerWidth();
            var menuLeft = $(this).position().left + 10;
            $("#activeMenu").stop().animate({
                left: menuLeft+'px',
                width: menuWidth+'px'
            }, 300, 'linear');
    });

    $("#tabs").mouseleave(function(){
        if($("#active_menu").length<=0){
            $("#activeMenu").stop().animate({
                left: '-999px',
                width: '0px'
            }, 500, 'linear');
        }
        else{
            var menuWidth = $("#active_menu").outerWidth();
            var menuLeft = $("#active_menu").position().left + 10;
            $("#activeMenu").stop().animate({
                left: menuLeft+'px',
                width: menuWidth+'px'
            }, 500, 'linear');
        }
    });
}

function validateForm(){


  if (!$('#register-form').length) {
    return false;
  }

  var role = $('#frole').val();

  var rules = {
    fname : {
      required : true,
    },
    fpass : {
      required : false,
    },
    fpass2 : {
      required : true,
      equalTo: "#fpass"
    },
    femail : {
      required : true,
      email : true
    },
    captcha : {
      equalTo : "1"
    },
    fskype : {
      required : true
    }
  }

  if (role == "webmaster") {
    rules.frules = {
      required : true
    }
  }


  if (role == "advertiser") {
    rules.fphone = {
      required : true
    }
  }

  $('#register-form').validate({
    rules: rules,
    messages : {
      fname : {
        required : "Введите имя"
      },
      femail : {
        required : "Введите email",
        email : "Неправильный формат"
      },
      fpass : {
        required : "Введите пароль"
      },
      fpass2 : {
        required : "Введите подтверждение пароля",
        equalTo : "Пароли не совпадают"
      },
      fskype : {
        required : "Введите логин Skype"
      },
      fphone : {
        required : "Введите телефон"
      },
      captcha : {
        equalTo : "Captcha - обязательное поле"
      }
    },
    submitHandler: function(form) {
      var fields = {}, errors = [], val;

      $('#register-form input').each(function(){
        if ($(this).is(':checkbox')) {
          val = $(this).prop('checked');
        } else {
          val = $(this).val()
        }
        fields[$(this).attr('name')] = val;
      })

      if (fields.frole == "webmaster" && fields.frules == false) {
        errors.push("Необходимо принять правила системы");
      }

      if (fields.captcha == "0") {
        errors.push("reCaptcha error");
      }

      if (errors.length) {
        $('#register-form .alert-danger').html(errors.join("<br />")).fadeIn();
        return false;
      }

      $('#register-form .alert-danger').fadeOut();

      $.ajax({
        url: '/ajax/register/',
        type: 'POST',
        dataType: 'json',
        data: $.param(fields),
        success: function(r) {
          if (r.errors.length) {
            for (var i=0; i<r.errors.length; i++) {
              $('input[name="'+r.errors[i].name+'"]').after("<label class='error' id='"+r.errors[i].name+"-error'>"+r.errors[i].text+"</label>");
              $("#"+r.errors[i].name+"-error").fadeIn();
            }
          } else {
            setTimeout(function(){ window.location = "/admin" }, 8000);
              visibleForm.fadeOut(300, function(){
              showMessage2({ elem:$(".succesful"), delay:3000});
            })
          }
        }
      });

      return false;
    }
  });

  $('#register-form input').keydown(function(){
       var id = $(this).attr('id');
       $(this).parent().removeClass('has-error');
       $('#'+id+'-error').fadeOut();
  });

}

function centerForm( form ) {
  var winH = $(document).height();
  var formH = form.height() + 120;
  var margin = 20;

  if ( formH < winH ) {
    margin =  (winH - formH) / 2;
  }

  form.css({'margin-top': margin + 'px'});
}

function showMessage2 (options) {
  var elem      = options.elem,
      delay     = options.delay,
      resetTime = delay + 1000;

  elem.toggleClass('appear');

  $('#enter').attr('href', '/admin/');
  $('#promoenter').attr('href', '/promo/');

};

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
    var login = $('#alogin').val(),
        pass = $('#apassword').val(),
        err = '';

    if ( login=='' ) {
      $('#alogin').parent().addClass('has-error');
      err = "Введите логин.";
    }
    if ( pass=='' ) {
      $('#apassword').parent().addClass('has-error');
      err += "<br /> Введите пароль.";
    }

    if ( err!='' ) {
      $('#login-form .form-error').html(err).fadeIn();
    } else {

      jQuery.ajax({
        url: '/ajax/auth/',
        type: 'POST',
        data:'alogin='+login+'&apassword='+pass,
        success: function(result) {
          if (result == '') {
              $('#login-form').submit();
          } else {
              $('#login-form .form-error').text(result).fadeIn();
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
  }

  if (err!='') {
    $('#forget-password .form-error').text(err).fadeIn();
  } else {
    jQuery.ajax({
      url: '/ajax/forgot-pass/',
      type: 'POST',
      data: 'email='+email,
      success: function(result) {
        if ( result != '' ) {
          $('#forget-password .form-error').text(result).fadeIn();
        } else {
          $('#forget-password .form-wrap').fadeOut(300, function(){ $('#forget-password .form-success').fadeIn(300) })
        }
      }
    });
  }
}


//processing the registration password

$.fn.passwordStrength = function( options ){
  return this.each(function(){
    var that = this;that.opts = {};
    that.opts = $.extend({}, $.fn.passwordStrength.defaults, options);

    that.div = $(that.opts.targetDiv);
    that.defaultClass = that.div.attr('class');

    that.percents = (that.opts.classes.length) ? 100 / that.opts.classes.length : 100;

     v = $(this).keyup(function(){
      if( typeof el == "undefined" )
        this.el = $(this);
      var s = getPasswordStrength (this.value);
      var p = this.percents;
      var t = Math.floor( s / p );

      if( 100 <= s )
        t = this.opts.classes.length - 1;

      this.div
        .removeAttr('class')
        .addClass( this.defaultClass )
        .addClass( this.opts.classes[ t ] );

    }).after('<span class="input-group-btn gulpy"><button type="button" class="btn btn-success custom_but">Сгенерировать</button></span>').next().click(function(){

      $(this).prev().val( randomPassword() ).trigger('keyup');
      $('.info_block').css('display', 'none');

      var numTime = 1;
      //clone password
      var a,L,epl=$("#fpass");
      function epl3(){a=epl.val();$("#fpass2").val(a)};epl3();

      return false;
    });
  });

  function getPasswordStrength(H){

    var D=(H.length);
    if(D>=8){
      D=20;
    }

    var F = H.replace(/[0-9]/g,"");
    var G = (H.length-F.length);
    if(G>=1){
      G=25;
    }

    var B=H.replace(/[A-Z]/g,"");
    var I=(H.length-B.length);
    if(I>=1){
      I=25;
    }

    var K = H.replace(/[a-z]/g,"");
    var L = (H.length-K.length);

    if(L>=1){
      L=25;
    }

    var E = D+G+L+I;

    if(E<0){E=0}
    if(E>100){E=100}

    return E;
  }

  function randomPassword() {

    var num     = "0,1,2,3,4,5,6,7,8,9".split(',');
    var chars   = "a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z".split(',');
    var upChars = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z".split(',');

    var size  = 3;
    var i     = 1;
    var ret   = "";

    while ( i <= size ) {

      var rand         = Math.floor(Math.random() * num.length);
      var randChars    = Math.floor(Math.random() * chars.length);
      var randUpChars  = Math.floor(Math.random() * upChars.length);

      ret += num[rand];
      ret += chars[randChars];
      ret += upChars[randUpChars];

      retResult = ret.substr(1);
      i++;
    }


    return retResult;
  }
};

$.fn.passwordStrength.defaults = {
  classes : Array('is10','is20','is30','is40','is50','is60','is70','is80','is90','is100'),
  targetDiv : '#passwordStrengthDiv',
  cache : {}
}
$(document)
.ready(function(){
  $('input[name="fpass"]').passwordStrength();
  $('input[name="password2"]').passwordStrength({targetDiv: '#passwordStrengthDiv2',classes : Array('is10','is20','is30','is40')});

});

//function restriction character input

function validatePass(){

  $('div').on('focusin', '#fpass', function(){
      $('.info_block').css('display', 'none');
      $('.info_block_error').css('display', 'none');
  });


   var passLength = 8;
   var pass       = $('input[name="fpass"]').val();
   var rule       = /^(?!.*admin)(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9]{8,}$/;

   if (!rule.test(pass) || pass.length < passLength){

       if(pass == '')
        $('.info_block_error').css('display', 'block');

   }else{
     console.log("Пароль " + pass + " соответствует требованиям безопасности");
   }
}

$('input.btn.btn-join').on('click', function(){

    var passLength    = 8;
    var rule          = /^(?!.*admin)(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9]{8,}$/;
    var passwordHero  = $('input[name="fpass"]').val();

    if(passwordHero == ''){
      $('.info_block_error').css('display', 'block');
    }

    if (!rule.test(passwordHero) || passwordHero.length < passLength){
       $('.info_block').css('display', 'block');
      //console.log(1);
    }
});
