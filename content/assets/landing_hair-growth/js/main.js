$(function() {
	$('.fancybox').fancybox();

	$(function() {
	    $('a', $('li', $('.delivery'))).hover(function(){
	        $(this).siblings('p').slideToggle('fast');
	    });
	});
	$('.scrollto').on('click', function(e){
		$('body,html').animate({scrollTop: ($($(this).attr('href')).offset().top)-170}, 1000);
		e.preventDefault();
	});
	$('.close').click(function(){
		$('.plank').slideUp(250, function(){$('.plank').fadeOut(250)});		
		return false;
	});
	
	$('.goorder').click(function(){
		$('.plank').slideUp(250, function(){$('.plank').fadeOut(250)});		
	});

	$('.main-table-list td').each(function(){
		$(this).listSlide();
	});
});
$.fn.listSlide = function(){
	var show = 2;
	$(this).find('li').slice(show).hide();
	$(this).find('.header-info-table-listslide').on('click',function(){
		if (!$(this).hasClass('active')) {
			$(this).parent('td').find('li').slideDown(300);
			$(this).addClass('active');
		}else{
			$(this).parent('td').find('li').slice(show).slideUp(300);
			$(this).removeClass('active');
		};
	});
};