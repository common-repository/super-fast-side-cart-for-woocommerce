jQuery(document).ready(function ($) {
        jQuery("body").on("added_to_cart", function () {
            jQuery(".sp_cart_container").css("display", "block"), 
            jQuery(".sp_cart_container").animate({ width: "650px", right: "0px" });
        });
        
        if ($(".woocommerce-message .button.wc-forward")[0]){
            spGetRefreshedFragments();
            jQuery(".sp_cart_container").css("display", "block"), 
            jQuery(".sp_cart_container").animate({ width: "650px", right: "0px" });
        }
        
        
});

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