

$(document).ready(function(){


   


    var arrMonth = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'],
        today = new Date(),
        date = today.getDate()+' '+arrMonth[today.getMonth()],
        timeFrom = (today.getHours()==0) ? 23 : today.getHours() - 1,
        timeTo = (today.getHours()==23) ? 0 : today.getHours() + 1;

    $('.seedate').attr('datetime', date).html(date+' c '+timeFrom+' до '+timeTo);

    // smooth scroll
    $.localScroll({
        duration: 1000,
        hash: true,
        offset: -30
    });

    // таймер обратного отсчета, до 00:00:00
    var now = new Date(),
        secPassed = now.getHours() * 60 * 60 + now.getMinutes() * 60 + now.getSeconds();
    $('.countdown').countdown({
        until: (24 * 60 * 60 - secPassed),
        labels: ['Годы', 'Месяцы', 'Недели', 'Дня', 'Часа', 'Минут', 'Секунд'],
        labels1: ['Годы', 'Месяцы', 'Недели', 'День', 'Час', 'Минута', 'Секуна'],
        format: 'HMS',
        layout: '<div class="timebox"><div class="n">{h10}</div><div class="n">{h1}</div><div class="l">{hl}</div></div><div class="timebox"><div class="n">{m10}</div><div class="n">{m1}</div><div class="l">{ml}</div></div><div class="timebox"><div class="n">{s10}</div><div class="n">{s1}</div><div class="l">{sl}</div></div>'
    });

    $('.fixblock .close').click(function(){
        $(this).parent().slideUp(300);
    });

    // remaining
    function lastpack(last){
        if (last>5){
            last--;
            $('.lastpack').html(last);
            setTimeout(lastpack, 360000, last);
        }
    }
    lastpack(10);

    $('.fixblock .hide .btn').click(function(){
        $(this).parent().hide().next().show();
    });




});