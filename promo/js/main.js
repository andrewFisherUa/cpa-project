$(document).ready(function(){

  filter();

    $('.carousel').carousel();
    circularLayout();
    window.setTimeout(function(){ $('#mockup').addClass('animated') }, 2000);
    window.onscroll = winScroll;
    smoothScroll();
    backToTop();
  $(document).resize(function(){
    var cWidth = window.innerWidth, cHeight = window.innerHeight;
    if(cWidth < 1000){
      var fHeight = $('.frame iframe').attr('height');
      $('.frame iframe').attr('width', cWidth*0.8).attr('height', (cWidth*0.8)*0.5625);
    }
  }).resize();

    $('#q-submit').click(function(){
        $('#q-form').hide();
        $('#q-send').show();
        return false;
    });

    $('#questions').on('hidden.bs.modal', function (e) {
      $('#q-send').hide();
      $('#q-form').show();
    });

    $('#nav li').hover(
        function(){
            var index = $(this).index()+1;
            $('#nav li:nth-child('+index+')').addClass('hover');
        },
        function(){
            $('#nav li').removeClass('hover');
        }
    );
  $('.mobile-menu a.mmenu').click(function(){
    $(this).closest('.mobile-menu').find('ul').fadeIn();
  });
  $(document).bind('click touchend', function (event) {
    if ($(event.target).closest('.mobile-menu').length) return;
    $('.mobile-menu ul').fadeOut();
  });
});

var circle = false;
var features = false;

function filter() {
  $('input[name="fphone"]').on('keyup keypress', function(e) {
     if (!(e.keyCode == 8 || e.keyCode == 46)) {
         var letters=' 1234567890()-+';
              return (letters.indexOf(String.fromCharCode(e.which))!=-1);
       }
  })
}


function backToTop(){
  var offset = $(window).height();

  $(window).scroll(function() {
      if ($(this).scrollTop() > offset) {
          $('#back-to-top').fadeIn();
      } else {
          $('#back-to-top').fadeOut();
      }
  });

  $('#back-to-top').click(function(event) {
      event.preventDefault();
      $('html, body').animate({scrollTop: 0}, 500);
      return false;
  })
}

function smoothScroll(){
    $('a[data-scroll], #nav a').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
          var target = $(this.hash);
          target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
          if (target.length) {
            $('html,body').animate({
              scrollTop: target.offset().top
            }, 500);
            return false;
          }
        }
    });
}

function winScroll(){
    var delay, scrollY =  $(window).scrollTop(),
        point1 = $('#easy-work').offset().top - scrollY;
        point2 = $('#features').offset().top - scrollY;

    if ( !circle && (point1 <= 30) ) {
        makeCircle();
        circle = true;
    }

    if ( !features && (point2 <= 70) ) {
        for (var i=1; i<=$('.feature').length; i++) {
            //$('.feature-'+i).addClass('animate').clearQueue();
        }

        features = true;
    }

}



function circularLayout(element) {
    var elems = $('#pics span'),
        increase = Math.PI * 2 / elems.length,
        x = 0, y = 0, angle = 0;

    elems.each(function(index){
        elem = $(this);
        x = 140 * Math.cos(angle) + 250;
        y = 140 * Math.sin(angle) + 170;
        elem.css({left: x + 'px', top: y + 'px' });
        elem.addClass('pic-'+index);
        angle += increase;
    })

    elems.hover(
        function(){
            $(this).animate({width: '112px', height: '112px', 'line-height': '112px', margin: '-22px 0 0 -22px'}, 200);
        },
        function(){
            $(this).animate({width: '68px', height: '68px', 'line-height': '68px', margin: '0'}, 200);
        }
    )
}

function makeCircle() {
    var elems = $('#pics span');
    elems.each(function(i){
        $(this).delay(400*i).animate(
            {width: '112px', height: '112px', 'line-height': '112px', margin: '-22px 0 0 -22px'},
            200,
            function(){
                $(this).animate({width: '68px', height: '68px', 'line-height': '68px', margin: '0'}, 200);
            });
    })
}

$(function(){

   var rules = {
    fname : {
      required : true,
    },
    fpass : {
      required : true
    },
    fpass2 : {
      required : true,
      equalTo: "#fpass"
    },
    femail : {
      required : true,
      email : true
    },
    fskype : {
      required: true
    }
  }

  $('#regform').validate({
    rules: rules,
    messages : {
      fname : {
        required : "Введите имя"
      },
      fskype : {
        required : "Введите логин skype"
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
      }
    },
    submitHandler: function(form) {      

      var fields = {
        frole : "webmaster",
        captcha : 1
      },

      errors = [], val;

      $('#regform input').each(function(){
        if ($(this).is(':checkbox')) {
          val = $(this).prop('checked');
        } else {
          val = $(this).val()
        }
        fields[$(this).attr('name')] = val;
      })

      if ($('input[name="frules"]').prop('checked') == false) {
        errors.push("Необходимо принять правила");
      }

      if (errors.length) {
        $('#regform .alert-danger').html(errors.join("<br />")).fadeIn();
        return false;
      }

      $('#regform .alert-danger').fadeOut();

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
            showMessage2({ elem:$(".succesful"), delay:3000});
          }
        }
      });

      return false;
    }
  });

  $('#regform input').keydown(function(){
       var id = $(this).attr('id');
       $(this).parent().removeClass('has-error');
       $('#'+id+'-error').fadeOut();
  });
});

function showMessage2 (options) {
  var elem      = options.elem,
      delay     = options.delay,
      resetTime = delay + 1000;

  elem.toggleClass('appear');
};
