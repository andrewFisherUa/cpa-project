jQuery(document).ready(function(){
  Wishlist.init();

});

// Content
var Wishlist = function () {

	var $ = jQuery;

	// Записываем значения в cookie. Список по странам wishlist-ua, wishlist-kz ...
	var listName = 'wishlist-'+$('body').data('shop')+"-"+$('body').data('country-code');

	var handleAddToWishList = function(){
		// Добавление товара в список желаний
		$(document).on('click', '.add_to_wishlist', function(){

			// Меняем кнопку
			var block = $(this).closest('.yith-wcwl-add-to-wishlist');
			block.find('.yith-wcwl-add-button').fadeOut(150);
			block.find('.yith-wcwl-wishlistaddedbrowse').fadeIn(150);

			// Выводим сообщение об успешном добавлении товара в список желаний
			messageWrap = $('#yith-wcwl-popup-message');

			$('#yith-wcwl-message').text("Товар добавлен в Ваш список желаний!");
			messageWrap
				.css('margin-left', -(16+messageWrap.width()/2))
				.fadeIn(300).delay(1500).fadeOut();

			var list = getCookie(listName);
			if (list) {
				deleteCookie(listName);
				setCookie(listName, list + ';' + $(this).data('product'), {path : '/'});
			} else {
				setCookie(listName, $(this).data('product'), {path : '/'});
			}
		})
	}

	var handleRemoveFromWishList = function(){
		// Удаление товара из списка желаний
		$(document).on('click', '.remove_from_wishlist', function(){
			var product = $(this).data('product');

			$('#yith-wcwl-row-'+product).fadeOut().remove();

			if ($('.wishlist_table tbody tr').length == 1) {
				$('#empty-wishlist').fadeIn();
			}

			var list = getCookie(listName);
			var newList = list.replace(new RegExp(product+';*','g'), "");

			deleteCookie(listName);
			setCookie(listName, newList, {path : '/'});
		})
	}

  return {
      //main function to initiate the module
      init: function () {
        handleAddToWishList();
        handleRemoveFromWishList();
      }
  };
}();

