jQuery(document).ready(function(){jQuery(".sp_cart_container ul.sp_cart_tab li").click(function(){var r=jQuery(this).attr("data-tab");jQuery(".sp_cart_container ul.sp_cart_tab li").removeClass("current"),jQuery(".sp_cart_container .sp_cart_content_tab").removeClass("current"),jQuery(this).addClass("current"),jQuery("#"+r).addClass("current")})});