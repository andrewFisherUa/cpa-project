$(function () {
  KMA.init();
  $(window).resize(function () {
    KMA.modalRefresh();
  });
  KMA.modalRefresh();
  $(document).on("click", "a[modal]", function () {
    var modalWindow = $("div#" + $(this).attr("modal"));
    if (modalWindow.length) {
      KMA.modalShow(modalWindow);
      return false;
    }
  }).on("click", ".icon-close, .modal, .button-close", function (event) {
    if (event.target != this) {
      return false;
    } else {
      KMA.modalHide($(this).closest(".modal"));
    }
  }).on("keydown", function (key) {
    if (key.keyCode == 27) {
      KMA.modalHide($(".modal:visible:last"));
    }
  }).on("click", ".modal > *", function (event) {
    event.stopPropagation();
    return true;
  });
});
var KMA = (function ($, $n) {
  return $.extend($n, {
    init: function () {
      //this.validateAndSendForm();
      //this.setTimezone();
    }, modalHide: function ($modal) {
      $modal.fadeOut("fast", function () {
        if (!$(".modal:visible").length) {
          $("body").removeClass("modal-show");
        }
      });
    }, modalRefresh: function () {
      if ($(".modal:visible:last").length) {
        var modalBlock = $(".modal:visible:last .modal-block"), width = parseInt(modalBlock.width()), height = parseInt(modalBlock.height());
        if ($(window).height() > height + 20) {
          modalBlock.addClass("modal-top").removeClass("margin-t-b").css("margin-top", -1 * (height / 2));
        } else {
          modalBlock.addClass("margin-t-b").removeClass("modal-top");
        }
        if ($(window).width() > width) {
          modalBlock.addClass("modal-left").removeClass("margin-l").css("margin-left", -1 * (width / 2));
        } else {
          modalBlock.addClass("margin-l").removeClass("modal-left");
        }
      }
    }, modalShow: function ($modal) {
      $modal.fadeIn("fast");
      $("body").addClass("modal-show");
      this.modalRefresh();
    }, initCallback: function (timeout) {
      try {
        setTimeout(function start_kmacb() {
          $("#kmacb").show();
        }, timeout);
      } catch (e) {
      }
    }, initComebacker: function (timeout) {
      try {
        setTimeout(function start_kmacb() {
          var comebacker = true;
          $(window).on("mouseout", function (event) {
            if (event.pageY - $(window).scrollTop() < 1 && comebacker) {
              comebacker = false;
              $("a[modal=kmacb-form]").trigger("click");
            }
          });
        }, timeout);
      } catch (e) {
      }
    }
  });
})(jQuery, KMA || {});