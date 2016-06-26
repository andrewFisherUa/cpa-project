$(document).ready(function(){

    if ( $('#datatable_offers').length ) {
        Offers.init();
    }

    preview();

    $('#cats .items').each(function(){
      //collapseItems($(this));
      expandItems($(this));
    });

    var h = $(window).height()/2 - 273;
    if ( h > 20 ) {
      $('.anchors.fixed').css('top', h);
    }

    countDown();
    backToTop();
    window.onscroll = showAnchors;

    $('.anchors li').click(function(e) {
      var target = $(this);
      goToAnchor(target.data('anchor'));
    });

    $('.item-container .buy').hover(
      function(){
        $(this).parent().find('.new-price').addClass('active');
      },
      function(){
        $('.new-price').removeClass('active');
      }
    )

    $('#cats a[data-expanded]').click(function(){
      var cat = $(this).attr('data-cat'),
          expanded = $(this).attr('data-expanded'),
          href = $(this).attr('href'),
          target = $("#cats ."+href+" .items");

      if (expanded == "false") {
          getGoods(target, cat, -1);
          $(this).attr('data-expanded', 'true');
          $(this).html('Свернуть <span></span>');
      } else {
          getGoods(target, cat, 10);
          $(this).attr('data-expanded', 'false');
          $(this).html('Смотреть все товары <span></span>');
      }

      return false;
    })

    if ( $('.b-slider').length ) {
        $('.b-slider').bxSlider({
            mode: 'fade',
            onSliderLoad: function(){
              $
            },
        });
    }

    if ( $('#popular .items').length ) {
        $('#popular .items').bxSlider({
            maxSlides: 5,
            slideWidth: 170,
            slideMargin: 10,
            pager: false,
            controls: true
        });
    }

    if ( $('#recommended .items').length ) {
        $('#recommended .items').bxSlider({
            maxSlides: 5,
            slideWidth: 170,
            slideMargin: 10,
            pager: false,
            controls: true
        });
    }

    if ($('#top-sell .items').length) {
        $('#top-sell .items').bxSlider({
            maxSlides: 4,
            minSlides: 4,
            slideWidth: 182,
            slideMargin: 10,
            pager: false,
            controls: true
        });
    }
})

function getGoods(target, catID, limit) {
  jQuery.ajax({
    url: '/main-ajax/get-cat-goods',
    type: 'POST',
    data:'catID='+catID+'&limit='+limit,
    success: function(result) {
      target.html(result);
      if ( limit<0 ) {
        expandItems(target);
      } else {
        collapseItems(target);
      }
      preview();
    }
  });
}

function backToTop(){
  var offset = $(window).height();
  var duration = 300;

  $(window).scroll(function() {
      if ($(this).scrollTop() > offset) {
          $('#back-to-top').fadeIn(duration);
      } else {
          $('#back-to-top').fadeOut(duration);
      }
  });

  $('#back-to-top').click(function(event) {
      event.preventDefault();
      $('html, body').animate({scrollTop: 0}, duration);
      return false;
  })
}

function collapseItems(container){
  var col, total = 9, n = 5, mTop, i = 0, row = -1,
      blocks = container.find('.item-container');

  if (blocks.length > 9) {
    container.parent().find('.expand-link').show();
  } else total = blocks.length;

  for ( i=0; i<blocks.length; i++, col++ ) {

    if ( i > total ) {
      blocks[i].style.display = 'none';
      continue;
    } else {
      if ( i % n == 0 ) {
        row++; col = 1;
      }

      $(blocks[i]).removeClass('sm');
      mTop = ( row > 0 && col%2==0 ) ? row*356-98 : row*356;
      blocks[i].style.marginTop = mTop + 'px';
      blocks[i].style.marginLeft = (i - row*n)*228 + 4 + 'px';

      if (total > 5) {
        if ( (row == 0 && col%2==0) || (row == 1 && col%2!=0) ) {
          $(blocks[i]).addClass('sm');
        }
      }
    }

  }

  if ( row > 0 ) {
    container.css('height', '618px');
  }
}

function countDown() {
  var timeLeft, date = new Date();
  date.setDate(date.getDate() + 1);
  timeLeft = 86400 - date.getHours()*60*60 + date.getMinutes()*60 + date.getSeconds();

  if ($(".countdown").length) {
    // countdown
    $(".time").each(function() {
      $(this).countdown({
        until: timeLeft,
        padZeroes: true,
        regional: 'ru',
        layout: '<ul>' +
                '<li><strong>{dnn}</strong><span>{dl}</span></li>' +
                '<li><strong>{hnn}</strong><span>{hl}</span></li>' +
                '<li><strong>{mnn}</strong><span>{ml}</span></li>' +
                '<li><strong>{snn}</strong><span>{sl}</span></li>' +
                '</ul>'
      });
    });
  };

}

function expandItems(container){
  var col, n = 5, mTop, i = 0, row = -1,
      blocks = container.find('.item-container'),
      total = Math.ceil(blocks.length/n);

  for ( i=0; i<blocks.length; i++, col++ ) {

      if ( i % n == 0 ) {
        row++; col = 1;
      }

      mTop = ( row > 0 && col%2==0 ) ? row*358-98 : row*358;
      $(blocks[i]).removeClass('sm');
      blocks[i].style.display = "block";
      blocks[i].style.marginTop = mTop + 'px';
      blocks[i].style.marginLeft = (i - row*n)*230 + 'px';

      if ( (row == total-1 && col%2!=0) || (row == 0 && col%2==0) ) {
        $(blocks[i]).addClass('sm');
      }

  }

  container.css('height', (row+1)*358 - 98);
}

function goToAnchor (anchor) {
  var offset = 0;

  if  ($('.'+anchor).length) target = $('.'+anchor);
  if  ($('#'+anchor).length) target = $('#'+anchor);

  offset = target.offset().top;

  $('body,html').stop().animate({
    scrollTop: offset-10
  }, 700);
};

function showAnchors(){
  var scrollTop = $(this).scrollTop(),
    winH = $(window).height();

  scrollTop >= winH ? $('.anchors.fixed').addClass('up') : $('.anchors.fixed').removeClass('up');
}


function preview(){
  var goodID;

  /* show preview */
  $('a[href="#preview"]').click(function(){
    goodID = $(this).attr('data-good');

    jQuery.ajax({
      url: '/main-ajax/good-preview',
      type: 'POST',
      data:'goodID='+goodID,
      success: function(result) {
        $('#preview .modal-body').html(result);
        $('#preview').modal('show');
      }
    });

    return false;
  })

  $('#preview').on('shown.bs.modal', function() {

    if ( $('.preview-bx').length ) {
      $('.bx-pages').bxSlider({
        minSlides: 4,
        maxSlides: 4,
        slideWidth: 51,
        slideMargin: 3,
        pager: false,
        controls: true,
        onSliderLoad: function(){
          $('#preview-slider .thumbs a').css('visibility', 'visible')
        }
      });

      previewSlider = $('.preview-bx').bxSlider({
        mode: 'fade',
        pagerCustom: '.bx-pages',
        controls: false,
      });
    }

    $('.bx-pages a').click(function(){
      $('.bx-pages li').removeClass('active');
      $(this).parent().addClass('active');
    })


  })




  /* qty minus 1 */
  $('body').on('click','.minus', function() {
      var qty = parseInt($('#preview .qty').val());
      if (qty > 1) {
        $('#preview .qty').val(--qty);
      }
  });

  /* qty plus 1 */
  $('body').on('click','.plus', function() {
      var qty = parseInt($('#preview .qty').val());
      $('#preview .qty').val(++qty);
  });

}


function addItemToCart(goodID){
  console.log($('#preview .message').length);
  $('#preview .message').fadeIn(300);
  $('.qty-holder').hide();
  $('#add-to-cart').hide();
  $('#go-to-cart').show();
  AddCart(goodID, parseInt($('#preview .qty').val()));
}


var previewSlider = null;

var Offers = function () {

    var handleOffers = function () {

      var grid = new Datatable();

      grid.init({
          src: $("#datatable_offers"),
          loadingMessage: 'Загрузка...',
          dataTable: {
              "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
              "language": { // language settings
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
              "pageLength": 10, // default record count per page
              "ajax": {
                  "url": '/ajax/get-frontend-offers/', // ajax source
              },
          },
      });

      // handle group actionsubmit button click
      grid.getTableWrapper()
        // Обработка изменения значения фильтров
        .on('change', '.table-group-action-input', function () {

          $('.table-group-action-input').each(function(){
            var actionValue = $(this).find('option:selected').val();
            if ( actionValue != '-1' ) {
              var actionName = $(this).attr('name');
              grid.setAjaxParam(actionName, actionValue);
            }

          })

          grid.getDataTable().ajax.reload();
          grid.clearAjaxParams();
        })
        // Сброс фильтров
        .on('click', '.reset-filters', function () {
          $('.table-group-action-input').each(function(){
            $(this).find('option:first-child').prop('selected', true);
          })
          grid.getDataTable().ajax.reload();
          grid.clearAjaxParams();
        })
      }


    return {
        //main function to initiate the module
        init: function () {
            handleOffers();
        }
    };

};
