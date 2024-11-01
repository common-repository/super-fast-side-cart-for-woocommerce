(function (t) {
        t(document).on("click", ".single_add_to_cart_button", function (a) {
            if (t(this).hasClass("disabled")) return !1;
            var e = t(this),
                r = e.closest("form.cart"),
                n = e.val(),
                s = r.find("input[name=quantity]").val() || 1,
                c = { action: "woocommerce_ajax_add_to_cart", product_id: r.find("input[name=product_id]").val() || n, product_sku: "", quantity: s, variation_id: r.find("input[name=variation_id]").val() || 0 };
            return (
                t(document.body).trigger("adding_to_cart", [e, c]),
                t.ajax({
                    type: "post",
                    url: wc_add_to_cart_params.ajax_url,
                    data: c,
                    beforeSend: function (t) {
                        e.removeClass("added").addClass("loading");
                    },
                    complete: function (t) {
                        e.addClass("added").removeClass("loading");
                    },
                    success: function (a) {
                        a.error && a.product_url ? (window.location = a.product_url) : t(document.body).trigger("added_to_cart", [a.fragments, a.cart_hash, e]);
                    },
                }),
                !1
            );
        });
	
    })(jQuery);