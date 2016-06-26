jQuery(document).ready(function(b) {
    function l() {
        b('a[data-rel="prettyPhoto[ask_an_estimate]"]').prettyPhoto({
            hook: "data-rel",
            social_tools: !1,
            theme: "pp_woocommerce",
            horizontal_padding: 20,
            opacity: .8,
            deeplinking: !1
        });
        f.off("change");
        f = b('.wishlist_table tbody input[type="checkbox"]');
        b("select.selectBox").selectBox();
        g()
    }

    function n(a) {
        var c = a.data("product-id"),
            d = b(".add-to-wishlist-" + c),
            c = {
                add_to_wishlist: c,
                product_type: a.data("product-type"),
                action: yith_wcwl_l10n.actions.add_to_wishlist_action
            };
        if (yith_wcwl_l10n.multi_wishlist &&
            yith_wcwl_l10n.is_user_logged_in) {
            var e = a.parents(".yith-wcwl-popup-footer").prev(".yith-wcwl-popup-content"),
                f = e.find(".wishlist-select"),
                m = e.find(".wishlist-name"),
                e = e.find(".wishlist-visibility");
            c.wishlist_id = f.val();
            c.wishlist_name = m.val();
            c.wishlist_visibility = e.val()
        }
        q() ? b.ajax({
            type: "POST",
            url: yith_wcwl_l10n.ajax_url,
            data: c,
            dataType: "json",
            beforeSend: function() {
                a.siblings(".ajax-loading").css("visibility", "visible")
            },
            complete: function() {
                a.siblings(".ajax-loading").css("visibility", "hidden")
            },
            success: function(a) {
                var c = b("#yith-wcwl-popup-message"),
                    e = a.result,
                    f = a.message;
                if (yith_wcwl_l10n.multi_wishlist && yith_wcwl_l10n.is_user_logged_in) {
                    var p = b("select.wishlist-select");
                    b.prettyPhoto.close();
                    p.each(function(c) {
                        c = b(this);
                        var d = c.find("option"),
                            d = d.slice(1, d.length - 1);
                        d.remove();
                        if ("undefined" != typeof a.user_wishlists)
                            for (d in d = 0, a.user_wishlists) "1" != a.user_wishlists[d].is_default && b("<option>").val(a.user_wishlists[d].ID).html(a.user_wishlists[d].wishlist_name).insertBefore(c.find("option:last-child"))
                    })
                }
                b("#yith-wcwl-message").html(f);
                c.css("margin-left", "-" + b(c).width() + "px").fadeIn();
                window.setTimeout(function() {
                    c.fadeOut()
                }, 2E3);
                "true" == e ? ((!yith_wcwl_l10n.multi_wishlist || !yith_wcwl_l10n.is_user_logged_in || yith_wcwl_l10n.multi_wishlist && yith_wcwl_l10n.is_user_logged_in && yith_wcwl_l10n.hide_add_button) && d.find(".yith-wcwl-add-button").hide().removeClass("show").addClass("hide"), d.find(".yith-wcwl-wishlistexistsbrowse").hide().removeClass("show").addClass("hide").find("a").attr("href", a.wishlist_url), d.find(".yith-wcwl-wishlistaddedbrowse").show().removeClass("hide").addClass("show").find("a").attr("href",
                    a.wishlist_url)) : "exists" == e ? ((!yith_wcwl_l10n.multi_wishlist || !yith_wcwl_l10n.is_user_logged_in || yith_wcwl_l10n.multi_wishlist && yith_wcwl_l10n.is_user_logged_in && yith_wcwl_l10n.hide_add_button) && d.find(".yith-wcwl-add-button").hide().removeClass("show").addClass("hide"), d.find(".yith-wcwl-wishlistexistsbrowse").show().removeClass("hide").addClass("show").find("a").attr("href", a.wishlist_url), d.find(".yith-wcwl-wishlistaddedbrowse").hide().removeClass("show").addClass("hide").find("a").attr("href",
                    a.wishlist_url)) : (d.find(".yith-wcwl-add-button").show().removeClass("hide").addClass("show"), d.find(".yith-wcwl-wishlistexistsbrowse").hide().removeClass("show").addClass("hide"), d.find(".yith-wcwl-wishlistaddedbrowse").hide().removeClass("show").addClass("hide"));
                b("body").trigger("added_to_wishlist")
            }
        }) : alert(yith_wcwl_l10n.labels.cookie_disabled)
    }

    function r(a) {
        var c = a.parents(".cart.wishlist_table"),
            d = c.data("pagination"),
            e = c.data("per-page"),
            f = c.data("page");
        a = a.parents("tr");
        c.find(".pagination-row");
        a = a.data("row-id");
        var m = c.data("id"),
            g = c.data("token"),
            d = {
                action: yith_wcwl_l10n.actions.remove_from_wishlist_action,
                remove_from_wishlist: a,
                pagination: d,
                per_page: e,
                current_page: f,
                wishlist_id: m,
                wishlist_token: g
            };
        b("#yith-wcwl-message").html("&nbsp;");
        c.fadeTo("400", "0.6").block({
            message: null,
            overlayCSS: {
                background: "transparent url(" + yith_wcwl_l10n.ajax_loader_url + ") no-repeat center",
                backgroundSize: "16px 16px",
                opacity: .6
            }
        });
        b("#yith-wcwl-form").load(yith_wcwl_l10n.ajax_url + " #yith-wcwl-form", d, function() {
            c.stop(!0).css("opacity",
                "1").unblock();
            l();
            b("body").trigger("removed_from_wishlist")
        })
    }

    function t(a) {
        var c = a.parents(".cart.wishlist_table"),
            d = c.data("token"),
            e = c.data("id"),
            f = a.parents("tr").data("row-id");
        a = a.val();
        var g = c.data("pagination"),
            h = c.data("per-page"),
            k = c.data("page"),
            d = {
                action: yith_wcwl_l10n.actions.move_to_another_wishlist_action,
                wishlist_token: d,
                wishlist_id: e,
                destination_wishlist_token: a,
                item_id: f,
                pagination: g,
                per_page: h,
                current_page: k
            };
        "" != a && (c.fadeTo("400", "0.6").block({
            message: null,
            overlayCSS: {
                background: "transparent url(" +
                    yith_wcwl_l10n.ajax_loader_url + ") no-repeat center",
                backgroundSize: "16px 16px",
                opacity: .6
            }
        }), b("#yith-wcwl-form").load(yith_wcwl_l10n.ajax_url + " #yith-wcwl-form", d, function() {
            c.stop(!0).css("opacity", "1").unblock();
            l();
            b("body").trigger("moved_to_another_wishlist")
        }))
    }

    function k(a) {
        var c = b(this);
        a.preventDefault();
        c.parents(".wishlist-title").next().show();
        c.parents(".wishlist-title").hide()
    }

    function q() {
        if (navigator.cookieEnabled) return !0;
        document.cookie = "cookietest=1";
        var a = -1 != document.cookie.indexOf("cookietest=");
        document.cookie = "cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT";
        return a
    }

    function g() {
        f.on("change", function() {
            var a = "",
                c = b(this).parents(".cart.wishlist_table"),
                d = c.data("id"),
                c = c.data("token"),
                e = document.URL;
            f.filter(":checked").each(function() {
                var c = b(this);
                a += 0 != a.length ? "," : "";
                a += c.parents("tr").data("row-id")
            });
            e = h(e, "wishlist_products_to_add_to_cart", a);
            e = h(e, "wishlist_token", c);
            e = h(e, "wishlist_id", d);
            b("#custom_add_to_cart").attr("href", e)
        })
    }

    function h(a, c, b) {
        b = c + "=" + b;
        a = a.replace(new RegExp("(&|\\?)" +
            c + "=[^&]*"), "$1" + b); - 1 < a.indexOf(c + "=") || (a = -1 < a.indexOf("?") ? a + ("&" + b) : a + ("?" + b));
        return a
    }
    var u = "undefined" !== typeof wc_add_to_cart_params ? wc_add_to_cart_params.cart_redirect_after_add : "",
        f = b('.wishlist_table tbody input[type="checkbox"]:not(:disabled)');
    b(document).on("click", ".add_to_wishlist", function(a) {
        var c = b(this);
        a.preventDefault();
        n(c);
        return !1
    });
    b(document).on("click", ".remove_from_wishlist", function(a) {
        var c = b(this);
        a.preventDefault();
        r(c);
        return !1
    });
    b(document).on("adding_to_cart", "body",
        function(a, b, d) {
            0 != b.closest(".wishlist_table").length && (d.remove_from_wishlist_after_add_to_cart = b.closest("tr").data("row-id"), d.wishlist_id = b.closest("table").data("id"), wc_add_to_cart_params.cart_redirect_after_add = yith_wcwl_l10n.redirect_to_cart)
        });
    b(document).on("added_to_cart", "body", function(a) {
        wc_add_to_cart_params.cart_redirect_after_add = u;
        a = b(".wishlist_table");
        a.find(".added").removeClass("added");
        a.find(".added_to_cart").remove()
    });
    b(document).on("added_to_cart", "body", function() {
        var a =
            b(".woocommerce-message");
        0 == a.length ? b("#yith-wcwl-form").prepend(yith_wcwl_l10n.labels.added_to_cart_message) : a.fadeOut(300, function() {
            b(this).replaceWith(yith_wcwl_l10n.labels.added_to_cart_message).fadeIn()
        })
    });
    b(document).on("cart_page_refreshed", "body", l);
    b(document).on("click", ".show-title-form", k);
    b(document).on("click", ".wishlist-title-with-form h2", k);
    b(document).on("click", ".hide-title-form", function(a) {
        var c = b(this);
        a.preventDefault();
        c.parents(".hidden-title-form").hide();
        c.parents(".hidden-title-form").prev().show()
    });
    b(document).on("change", ".change-wishlist", function(a) {
        a = b(this);
        t(a);
        return !1
    });
    b(document).on("change", ".yith-wcwl-popup-content .wishlist-select", function(a) {
        a = b(this);
        "new" == a.val() ? a.parents(".yith-wcwl-first-row").next(".yith-wcwl-second-row").css("display", "table-row") : a.parents(".yith-wcwl-first-row").next(".yith-wcwl-second-row").hide()
    });
    b(document).on("change", "#bulk_add_to_cart", function() {
        b(this).is(":checked") ? f.attr("checked", "checked").change() : f.removeAttr("checked").change()
    });
    b(document).on("click",
        "#custom_add_to_cart",
        function(a) {
            var c = b(this),
                d = c.parents(".cart.wishlist_table");
            yith_wcwl_l10n.ajax_add_to_cart_enabled && (a.preventDefault(), d.fadeTo("400", "0.6").block({
                message: null,
                overlayCSS: {
                    background: "transparent url(" + yith_wcwl_l10n.ajax_loader_url + ") no-repeat center",
                    backgroundSize: "16px 16px",
                    opacity: .6
                }
            }), b("#yith-wcwl-form").load(yith_wcwl_l10n.ajax_url + c.attr("href") + " #yith-wcwl-form", {
                action: yith_wcwl_l10n.actions.bulk_add_to_cart_action
            }, function() {
                d.stop(!0).css("opacity", "1").unblock();
                b('a[data-rel="prettyPhoto[ask_an_estimate]"]').prettyPhoto({
                    hook: "data-rel",
                    social_tools: !1,
                    theme: "pp_woocommerce",
                    horizontal_padding: 20,
                    opacity: .8,
                    deeplinking: !1
                });
                f.off("change");
                f = b('.wishlist_table tbody input[type="checkbox"]');
                b("select.selectBox").selectBox();
                g()
            }))
        });
    (function() {
        if (0 != b(".yith-wcwl-add-to-wishlist").length && 0 == b("#yith-wcwl-popup-message").length) {
            var a = b("<div>").attr("id", "yith-wcwl-message"),
                a = b("<div>").attr("id", "yith-wcwl-popup-message").html(a).hide();
            b("body").prepend(a)
        }
    })();
    g();
    b("select.selectBox").selectBox()
});