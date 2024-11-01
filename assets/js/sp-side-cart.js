function spUpdateRefreshFragments(t) {
    t.fragments &&
        (jQuery.each(t.fragments, function (t, a) {
            jQuery(t).replaceWith(a);
        }),
        "sessionStorage" in window &&
            null !== window.sessionStorage &&
            (sessionStorage.setItem(wc_cart_fragments_params.fragment_name, JSON.stringify(t.fragments)),
            localStorage.setItem(wc_cart_fragments_params.cart_hash_key, t.cart_hash),
            sessionStorage.setItem(wc_cart_fragments_params.cart_hash_key, t.cart_hash),
            t.cart_hash && sessionStorage.setItem("wc_cart_created", new Date().getTime())),
        jQuery(document.body).trigger("wc_fragments_refreshed"));
}
function spGetRefreshedFragments() {
    jQuery.ajax({
        url: ajax_postajax.ajaxurl,
        type: "POST",
        data: { action: "sp_get_refresh_fragments" },
        success: function (t) {
            spUpdateRefreshFragments(t);
        },
    });
}
jQuery(document).ready(function () { 
    jQuery(document.body).trigger("wc_fragment_refresh"),
        /* jQuery("body").on("added_to_cart", function () {
            jQuery(".sp_cart_container").css("display", "block"), jQuery(".sp_cart_container").animate({ width: "650px", right: "0px" });
        }), */ 
        jQuery(".sp_cart_close_cart").click(function () {
            jQuery(".sp_cart_container").width(), jQuery(".sp_cart_container").animate({ right: "-650px" });
        }),
        jQuery(".sp_cart_basket_cart").click(function () {
            jQuery(".sp_cart_container").css("display", "block"), jQuery(".sp_cart_container").animate({ width: "650px", right: "0px" }), spGetRefreshedFragments();
        }),
        jQuery("body").on("click", ".sp_cart_qnty .sp_cart_minus", function () {
            var t = jQuery(this).parent().find("input.sp_cart_qnty_field"),
                a = parseInt(t.val()) - 1;
            return !(a < 1 || (t.val(a), t.change(), 1));
        }),
        jQuery("body").on("click", ".sp_cart_qnty .sp_cart_plus", function () {
            var t = jQuery(this).parent().find("input.sp_cart_qnty_field");
            return t.val(parseInt(t.val()) + 1), t.change(), !1;
        }),
        jQuery("body").on("change", '.sp_cart_qnty input[name="update_qty"]', function () {
            jQuery(this).closest("tr").attr("product_id");
            var t = jQuery(this).val(),
                a = jQuery(this).closest("tr").attr("c_key"),
                e = jQuery(this);
            e.prop("disabled", !0);
            var r = jQuery(this).parents(".sp_cart_container");
            r.block({ message: null, overlayCSS: { cursor: "none" } }),
                jQuery.ajax({
                    url: ajax_postajax.ajaxurl,
                    type: "POST",
                    data: "action=scart_change_qty&c_key=" + a + "&qty=" + t,
                    success: function (t) {
                        e.prop("disabled", !1),
                            spGetRefreshedFragments(),
                            setTimeout(function () {
                                r.unblock();
                            }, 1e3);
                    },
                });
        });


         //apply coupon code ajax
    

         
    jQuery('body').on('click', '.sp_coupon_submit', function() { 

        var couponCode = jQuery("#sp_coupon_code").val();

        jQuery.ajax({
            url:ajax_postajax.ajaxurl,
            type:'POST',
            data:'action=coupon_ajax_call&coupon_code='+couponCode,
            success : function(response) {
                jQuery("#sp_cpn_resp").html(response.message);
                if(response.result == 'not valid' || response.result == 'already applied') {
                	jQuery("#sp_cpn_resp").css('background-color', '#e2401c');
                } else {
                	jQuery("#sp_cpn_resp").css('background-color', '#0f834d');
                }
                jQuery(".sp_coupon_response").fadeIn().delay(2000).fadeOut();
                document.getElementById("sp_coupon_code").value = '';
                jQuery( document.body ).trigger( 'wc_fragment_refresh' );
            }
        });
    });
    
    jQuery('body').on('click', '.sp_remove_cpn', function() {

        var removeCoupon = jQuery(this).attr('cpcode');

        jQuery.ajax({
            url:ajax_postajax.ajaxurl,
            type:'POST',
            data:'action=remove_applied_coupon_ajax_call&remove_code='+removeCoupon,
            success : function(response) {
                jQuery("#sp_cpn_resp").html(response);
                jQuery(".sp_coupon_response").fadeIn().delay(2000).fadeOut();
                jQuery( document.body ).trigger( 'wc_fragment_refresh' );
            }
        });

    });





}),
    jQuery(document).on("click", ".sp_cart_body a.sp_cart_remove", function (t) {
        t.preventDefault();
        var a = jQuery(this).attr("data-product_id"),
            e = jQuery(this).attr("data-cart_item_key"),
            r = jQuery(this).parents(".sp_cart_container");
        r.block({ message: null, overlayCSS: { cursor: "none" } }),
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: ajax_postajax.ajaxurl,
                data: { action: "scart_product_remove", product_id: a, cart_item_key: e },
                success: function (t) {
                    if (t && !t.error) {
                        var a = t.fragments;
                        a &&
                            (jQuery.each(a, function (t, a) {
                                jQuery(t).replaceWith(a);
                            }),
                            r.unblock());
                    }
                },
            });
    });

//code to collapse side cart on click 'continue shopping button' and redirect to shop page
jQuery(document).ready(function () { 
    jQuery(".sp_cart_continueshop_btn").click(function(){
        jQuery(".sp_cart_container").width(), jQuery(".sp_cart_container").animate({
          right: "-650px"
        });
        var origin   = window.location.origin;
        var origin   = origin + "/shop";
        jQuery(location).prop('href', origin)
      });
    });




   