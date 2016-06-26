jQuery(document).ready(function(){
  SingleOrder.init();
});

var SingleOrder = function () {

    var handleSaveExtra = function() {
        $('body').on('click', '#save-comment', function(){
            var data = {
                id: $(this).data('order'),
                comment: $('#comment').val(),
            }

            $.ajax({
              url: '/ajax/save-order-comment/',
              type: 'POST',
              data: $.param(data),
              success: function() {
                $('#comment-success').text("Примечание сохранено").fadeIn();
              }
            });
        })
    }

    var handleAddOrder = function(){
       if ( !$('#cat').length ) return false;

       var catID, cat = $("#cat"),
       product = $('#product-select');

       // Подгрузка категорий при выборе страны
       $('body').on('click', '#select-country', function(){
         var data = {
          code : $('#country option:selected').val(),
          action : 'get-country-cats'
         }

        $.ajax({
          url: '/ajax/add-order-handler/',
          type: 'POST',
          data: $.param(data),
          dataType: "json",
          success: function( response ) {
            $('#cat').html( response.rows ).attr("disabled", false);
            $('#select-country, #country').attr("readonly", true);
          }
        });

       })

       // Подгрузка товаров при изменении категории
       $('body').on('change', '#cat', function(){
          var data = {
              с_id : $(this).find('option:selected').val(),
              code : $('#country option:selected').val(),
              action : "get-cat-products"
          }

          $.ajax({
            url: '/ajax/add-order-handler/',
            type: 'POST',
            data: $.param(data),
            dataType: "json",
            success: function( response ) {
              $('#product-select').html( response.rows );
              $('#add-product-submit, #product-select').attr("disabled", false);
            }
          });
       })

       // Добавление товара к заказу
       $('body').on('click', '#add-product-submit', function(){
        var table = $('#productList tbody');
        var data = {
          id : product.find('option:selected').val(),
          code : $('#country option:selected').val(),
          num : table.find('tr').length + 1,
          action : "add-product-to-order"
        }

        $.ajax({
          url: '/ajax/add-order-handler/',
          type: 'POST',
          data: $.param(data),
          dataType: "json",
          success: function( response ) {
            if (!table.is(':visible')) table.parent().show();
              table.append(response.rows);
              calcTotal();
              table.find('tr:last-child').show(300);
            }
        });

       })

       // Сохранение заказа
       $('body').on('click', '#add-order-submit', function(){

            var errors = [];

            if ( $('#productList tbody tr').length == 0 ) {
                errors.push("Выберите товары для заказа");
            }

            if ( $('#last-name').val() == "" ) {
                errors.push("Введите фамилию");
            }

            if ( $('#first-name').val() == "" ) {
                errors.push("Введите имя");
            }

            if ( $('#phone').val() == "" ) {
                errors.push("Введите телефон");
            }

            if ( errors.length ) {
                $('.alert-danger').html( errors.join("<br />") ).fadeIn();
                return false;
            }

            var pID, qty, cart='';
            $('#productList .item').each(function(){
              pID = $(this).attr('id').replace('item-','');
              qty = $(this).find('.qty').val();
              cart += pID+'-'+qty+';';
            });

            deleteCookie('cart');
            setCookie('cart', cart, {path : '/'});
            $('form[name="add-order"]').submit();
       });

      $('body').on('change', '.qty', function(){
        calcSum( $(this).val(), $(this).data('good') );
      })

      // Удаление товара из заказа
      $('body').on('click', '.remove-item', function(){
        var i=1, productID = $(this).data('good');
        $('#item-'+productID).hide(300, function(){
          $('#item-'+productID).remove();
          if ( $('#productList .num').length ) {
            $('#productList .num').each(function(){
              $(this).text(i);
              i++;
            })
            calcTotal();
          } else {
             $('#productList').hide();
          }
        });
      })
    }

    // Считает общую сумму заказа
    var calcTotal = function() {
      var total = 0;
      $('#productList .sum>span').each(function(){
        total += +$(this).text();
      })

      $('#productList #total>span').text(total);
    }

    // Считает сумму по товару при изменении количества
    var calcSum = function(qty, productID) {
      var sum, product = $('#item-'+productID);
      sum = parseInt(product.find('.price>span').text()) * qty;
      product.find('.sum>span').text(sum);
      calcTotal();
    }

    return {

        //main function to initiate the module
        init: function () {
           handleSaveExtra();
           handleAddOrder();
        }

    };
}();

function deleteCookie(name) {
  setCookie(name, "", {
    expires: -1
  })
}

function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, options) {
  options = options || {};

  var expires = options.expires;

  if (typeof expires == "number" && expires) {
    var d = new Date();
    d.setTime(d.getTime() + expires * 1000);
    expires = options.expires = d;
  }
  if (expires && expires.toUTCString) {
    options.expires = expires.toUTCString();
  }

  value = encodeURIComponent(value);

  var updatedCookie = name + "=" + value;

  for (var propName in options) {
    updatedCookie += "; " + propName;
    var propValue = options[propName];
    if (propValue !== true) {
      updatedCookie += "=" + propValue;
    }
  }

  document.cookie = updatedCookie;
}