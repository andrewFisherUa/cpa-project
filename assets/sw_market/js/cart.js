jQuery(document).ready(function(){
  Cart.init();

});

var Cart = function () {

	var $ = jQuery;

	// Записываем значения в cookie. Список по странам cart-[shopID]-ua, cart-[shopID]-kz ...
	var cartName = 'cart-'+$('body').data('shop')+"-"+$('body').data('country-code');
	var shopId = $('body').data('shop');
	var countryCode = $('body').data('country-code');

	var handleAddToCart = function(){
		// Добавление товара в корзину
		$(document).on('click', '.add_to_cart_button, .single_add_to_cart_button', function(){

			var product = $(this).data('product');
			var qty = ($(this).data('qty')) ? parseInt($(this).data('qty')) : 1;

			if (!product) return false;

			cart = getCookie(cartName);

			if (cart) {
				deleteCookie(cartName);
				var patt = new RegExp("("+product+"):([1-9]+);");
				var res = cart.match(patt);
				if (res) {
					var patt2 = new RegExp(product+":[1-9]+");
					res[2] = parseInt(res[2]) + qty;
					cart = cart.replace(patt2, res[1]+":"+res[2]);
				} else {
					cart = cart + product + ":"+qty+";";
				}
			} else {
				cart = product + ":"+qty+";";
			}

			setCookie(cartName, cart, {path : '/'});
			updateMiniCart();

			$(this).text("В корзине").addClass('added');
			$('#productAddedModal #addedProductName').text('"'+$(this).attr("data-product_name")+'"');
			$('#productAddedModal').modal("show")
		})
	}

	// Обновление корзины
	var updateCart = function(){
		var cart = '';

		// Обновление корзины
		$('#cart-content-table .cart_item').each(function(){
			var row = $(this),
				id = row.data('product'),
				qty = row.find('.qty').val();

			cart = cart+id+":"+qty+";";
		})

		deleteCookie(cartName);
		setCookie(cartName, cart, {path : '/'});

		var data = {
			'shop_id' : shopId,
			'countryCode' : countryCode
		}

		$.ajax({
	        url: '/ajax/get-cart-content/',
	        type: 'POST',
	        dataType: "json",
	        data: $.param(data),
	        success: function( response ){
	          $('#contents .entry-content').html(response.content);
	        }
	    })
	}

	var handleQuantity = function(){
		$(document).on('change', 'form.cart input[name="quantity"]', function(){
			$(this).parent().siblings(".single_add_to_cart_button").attr("data-qty", $(this).val());
		})
	}

	var updateMiniCart = function(){
		var data = {
			'shop_id' : shopId,
			'countryCode' : countryCode
		}

		$.ajax({
	        url: '/ajax/get-minicart-content/',
	        type: 'POST',
	        dataType: "json",
	        data: $.param(data),
	        success: function( response ){
	          $('#minicart-widget .widget-inner').html(response.content);
	        }
	    })
	}

	var handleUpdateCart = function(){
		$(document)
			// Обновление корзины при нажа11тии на кнопку "Обновить корзину"
			.on('click', '#update-cart', function(){
				updateCart();
				updateMiniCart()
			})
			// Обновление корзины при изменении количества товара
			.on('change', '#cart-content-table .qty', function(){
				updateCart();
				updateMiniCart();
			})
	}

	var handleRemoveItem = function(){
		// Удаление товара из корзины
		$(document).on('click', '#cart-content-table .cart_item .remove', function(){
			$(this).closest('.cart_item').fadeOut(300, function(){
				$(this).remove();
				updateCart();
				updateMiniCart();
			});
		})

		// Удаление товара из mini-корзины
		$(document).on('click', '#mini-cart .btn-remove', function(){
			var product = $(this).data('product'),
				newCart = '',
			    cart = getCookie(cartName),
			    expr = new RegExp(product+':[1-9]+;?', 'g');

			newCart = cart.replace(expr, '');

			deleteCookie(cartName);
			setCookie(cartName, newCart, {path : '/'});
			//$('.add_to_cart_button[data-product="'+product+'"]').removeClass("added").text("В корзину");
			location.reload();
		})
	}

	var handleCheckout = function(){
		$('#checkout-form').on("submit", function(){

			var errors = [];

			if ($('input[name="first_name"]').val() == "") {
				errors.push("<li><strong>Имя</strong> обязательное поля для заполнения.</li>");
			}

			if ($('input[name="last_name"]').val() == "") {
				errors.push("<li><strong>Фамилия</strong> обязательное поля для заполнения.</li>");
			}

			if ($('input[name="phone"]').val() == "") {
				errors.push("<li><strong>Телефон</strong> обязательное поля для заполнения.</li>");
			}

			if (errors.length) {
				$('#checkout-form .woocommerce-error').html(errors.join("")).fadeIn();
				return false;
			}

		})
	}

  return {
      //main function to initiate the module
      init: function () {
        handleAddToCart();
        handleUpdateCart();
        handleRemoveItem();
        handleCheckout();
        handleQuantity();
      }
  };
}();

