$(document).ready(function(){

    nav();


    $('#partners-slider').bxSlider({
        minSlides: 3,
        maxSlides: 3,
        slideWidth: 250,
        slideMargin: 10,
        pager: false
    });

    var w = 0;

    $('#path').delay(1000).fadeIn(1000);

    $(' #da-thumbs .thumb a').each(function(){
        if ( $(this).width() > w ) w = $(this).width();
    })

    $(' #da-thumbs .thumb ').each( function() {
        $(this).find('a').css('height', w-30);
        $(this).hoverdir();
    });

    $(window).resize(function(){
        $(' #da-thumbs .thumb ').each( function() {
            $(this).find('a').css('height', w-30);
        });
    })

    $(".scroll").each(function() {
        var block = $(this);
        $(window).scroll(function() {
            var top = block.offset().top;
            var bottom = block.height()+top;
            top = top - $(window).height();
            var scroll_top = $(this).scrollTop();
            var block_center = block.offset().top + (block.height() / 2);
            var screen_center = scroll_top + ($(window).height() / 2);
            if(block.height() < $(window).height()) {
                if ((scroll_top > (top-(block.height()/2))) && ((scroll_top < bottom+(block.height()/2))) && (scroll_top + $(window).height() > (bottom-(block.height()/2))) && (scroll_top < (block.offset().top+(block.height()/2)))) {
                    if (!block.hasClass("animated")) {
                        block.addClass("animated"); block.trigger('animateIn');
                    }
                } else {
                    if((block.offset().top + block.height() < scroll_top) || (block.offset().top > (scroll_top + $(window).height()))) {
                        block.removeClass("animated"); block.trigger('animateOut');
                    }
                }
            } else {
                if ((scroll_top > top) && (scroll_top < bottom) && (Math.abs(screen_center - block_center) < (block.height() / 4))) {
                    if (!block.hasClass("animated")) {
                        block.addClass("animated"); block.trigger('animateIn');
                    }
                } else {
                    if((block.offset().top + block.height() < scroll_top) || (block.offset().top > (scroll_top + $(window).height()))) {
                        block.removeClass("animated"); block.trigger('animateOut');
                    }
                }
            }
        });
    });

})

function nav(){

    if($("#active_menu").length>0){ // если есть активный пункт меню, то позиционируем двигающуюся плашку на нем
        var menuWidth = $("#active_menu").outerWidth(); // определяем ширину активного пункта меню
        var menuLeft = $("#active_menu").position().left; // определяем смещение активного пункта меню слева
        $("#activeMenu").stop().animate({ // анимируем движущуюся плашку
            left: menuLeft+'px',
            width: menuWidth+'px'
        }, 500, 'linear');
    } else {
        $("#topMenu li:first-child a.mainlevel").attr('id', 'active_menu');
    }
    $("#topMenu a.mainlevel").mouseover(function(){ // поведение движущейся плашки при наведении на любой пункт меню. Все тоже самое, что и при наличии активного пункта, только позиция плашки определяется относительно пункта, на который произошло наведение курсора мыши
            var menuWidth = $(this).outerWidth();
            var menuLeft = $(this).position().left;
            $("#activeMenu").stop().animate({
                left: menuLeft+'px',
                width: menuWidth+'px'
            }, 300, 'linear');
    });
    $("#topMenu").mouseleave(function(){ // поведение плашки при окончании события наведения мыши на пункт меню (выход курсора мыши на пределы блока, в котором содержится меню)
        if($("#active_menu").length<=0){ // если активного пункта нет, то перемещаем плашку за границу экрана
            $("#activeMenu").stop().animate({
                left: '-999px',
                width: '0px'
            }, 500, 'linear');
        }
        else{ // иначе, если есть активный пункт меню – возвращаем плашку на него
            var menuWidth = $("#active_menu").outerWidth();
            var menuLeft = $("#active_menu").position().left;
            $("#activeMenu").stop().animate({
                left: menuLeft+'px',
                width: menuWidth+'px'
            }, 500, 'linear');
        }
    });
}