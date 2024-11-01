<?php

if (!defined('ABSPATH')) {
    exit;
}

class Sfsc_Side_Cart
{

    public function __construct()
    {
        $global_enable = get_option('sp_cart_global_cart', 'no');

        if ($global_enable === 'yes') {
            add_action('wp_ajax_woocommerce_ajax_add_to_cart', array($this, 'sp_woocommerce_ajax_add_to_cart'));
            add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', array($this, 'sp_woocommerce_ajax_add_to_cart'));
            add_action('woocommerce_add_to_cart_fragments', array($this, 'SP_cart_fragments'));
            add_action('wp_ajax_scart_change_qty', array($this, 'scart_change_qty'));
            add_action('wp_ajax_nopriv_scart_change_qty', array($this, 'scart_change_qty'));
            add_action('wp_ajax_scart_product_remove', array($this, 'scart_product_remove'));
            add_action('wp_ajax_nopriv_scart_product_remove', array($this, 'scart_product_remove'));
            add_action('wp_head', array($this, 'Sp_side_cart_create'));
            add_action('wp_ajax_sp_get_refresh_fragments', array($this, 'SP_cart_get_refreshed_fragments'));
            add_action('wp_ajax_nopriv_sp_get_refresh_fragments', array($this, 'SP_cart_get_refreshed_fragments'));
            add_action('wp_is_mobile', array($this, 'sp_exclude_ipad_and_tablets'));
            add_action('admin_enqueue_scripts', array($this, 'sp_load_wp_media_files'));
            add_action('wp_ajax_sp_myprefix_get_image', array($this, 'sp_myprefix_get_image'));
            // do_action('delete_attachment', array($this, 'sp_delete_image'));
            add_action('wp_ajax_coupon_ajax_call', array($this, 'sp_coupon_ajax_call_func'));
            add_action('wp_ajax_nopriv_coupon_ajax_call', array($this, 'sp_coupon_ajax_call_func'));
            add_action('wp_ajax_remove_applied_coupon_ajax_call', array($this, 'sp_remove_applied_coupon_ajax_call_func'));
            add_action('wp_ajax_nopriv_remove_applied_coupon_ajax_call', array($this, 'sp_remove_applied_coupon_ajax_call_func'));
        }
    }


    function sp_remove_applied_coupon_ajax_call_func()
    {
        $code = $_REQUEST['remove_code'];

        $sp_coupon_removed_suc_txt = 'Coupon Removed Successfully.';

        if (WC()->cart->remove_coupon($code)) {
            echo $sp_coupon_removed_suc_txt;
        }
        WC()->cart->calculate_totals();
        WC()->cart->maybe_set_cart_cookies();
        WC()->cart->set_session();
        exit();
    }

    function sp_coupon_ajax_call_func()
    {

        $code = $_REQUEST['coupon_code'];
        $code = strtolower($code);

        // Check coupon code to make sure is not empty
        if (empty($code) || !isset($code)) {

            $sp_cpnfield_empty_txt = 'Coupon Code Field is Empty!';
            // Build our response
            $response = array(
                'result'    => 'empty',
                'message'   => $sp_cpnfield_empty_txt
            );

            header('Content-Type: application/json');
            echo json_encode($response);

            // Always exit when doing ajax
            WC()->cart->calculate_totals();
            WC()->cart->maybe_set_cart_cookies();
            WC()->cart->set_session();
            exit();
        }

        // Create an instance of WC_Coupon with our code
        $coupon = new WC_Coupon($code);

        if (in_array($code, WC()->cart->get_applied_coupons())) {

            $sp_cpn_alapplied_txt = 'Coupon Code Already Applied.';

            $response = array(
                'result'    => 'already applied',
                'message'   => $sp_cpn_alapplied_txt
            );

            header('Content-Type: application/json');
            echo json_encode($response);

            // Always exit when doing ajax
            WC()->cart->calculate_totals();
            WC()->cart->maybe_set_cart_cookies();
            WC()->cart->set_session();
            exit();
        } elseif (!$coupon->is_valid()) {

            $sp_invalid_coupon_txt = 'Invalid code entered. Please try again.';
            // Build our response
            $response = array(
                'result'    => 'not valid',
                'message'   => $sp_invalid_coupon_txt
            );

            header('Content-Type: application/json');
            echo json_encode($response);

            // Always exit when doing ajax
            WC()->cart->calculate_totals();
            WC()->cart->maybe_set_cart_cookies();
            WC()->cart->set_session();
            exit();
        } else {

            WC()->cart->apply_coupon($code);

            $sp_coupon_applied_suc_txt = 'Coupon Applied Successfully.';
            // Build our response
            $response = array(
                'result'    => 'success',
                'message'      => $sp_coupon_applied_suc_txt
            );

            header('Content-Type: application/json');
            echo json_encode($response);

            // Always exit when doing ajax
            WC()->cart->calculate_totals();
            WC()->cart->maybe_set_cart_cookies();
            WC()->cart->set_session();
            wc_clear_notices();
            exit();
        }
    }


    //function to remove the icon and set the default icon for the side cart

    function sp_delete_image()
    {
        if (array_key_exists('sp_delete_file', $_)) {
            $filename = $_POST['sp_delete_file'];
            if (file_exists($filename)) {
                unlink($filename);
                echo 'File ' . $filename . ' has been deleted';
            }
        }
    }


    // function to add side cart icon

    function sp_load_wp_media_files()
    {

        // Enqueue WordPress media scripts
        wp_enqueue_media();
        // Enqueue custom script that will interact with wp.media
        wp_enqueue_script('sp_myprefix_script', plugins_url('../assets/js/sp-cart-icon.js', __FILE__), array('jquery'), '0.1');
    }


    function sp_myprefix_get_image()
    {
        if (isset($_GET['id'])) {
            $image = wp_get_attachment_image(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT), 'medium', false, array('id' => 'sp_myprefix-preview-image'));
            $data = array(
                'image'    => $image,
            );
            wp_send_json_success($data);
        } else {
            wp_send_json_error();
        }
    }

    function SP_cart_get_refreshed_fragments()
    {
        WC_AJAX::get_refreshed_fragments();
    }


    /**
     * AJAX ADD TO CART - SINGLE PRODUCT PAGE
     */
    function sp_woocommerce_ajax_add_to_cart()
    {
        $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
        $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
        $variation_id = absint($_POST['variation_id']);
        $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
        $product_status = get_post_status($product_id);

        if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {

            do_action('woocommerce_ajax_added_to_cart', $product_id);

            if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
                wc_add_to_cart_message(array($product_id => $quantity), true);
            }

            WC_AJAX::get_refreshed_fragments();
        } else {

            $data = array(
                'error' => true,
                'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
            );

            echo wp_send_json($data);
        }

        wp_die();
    }


    /**
     * Create cart html in wp footer
     */

    function SP_cart_create()
    {
?>
        <div class="sp_cart_container">
            <div class="sp_cart_header">
                <div class="sp_coupon_response">
                    <div class="sp_inner_div">
                        <span id="sp_cpn_resp"></span>
                    </div>
                </div>
                <h3 class="sp_cart_header_title"><?php echo get_option('sp_cart_cart_heading', 'Your Cart'); ?></h3>
                <span class="sp_cart_close_cart"><img src="<?php echo SFSC_THM_DIR . '/assets/images/close-scart.png'; ?>"></span>
            </div>
            <div class="sp_cart_body">
            </div>

            <div class="sp_cart_footer">
                <div class='sp_coupon'>

                    <div class="sp_coupon_field" id="sp_coupon_field">
                        <input type="text" id="sp_coupon_code" placeholder="Enter coupon code">
                        <span class="sp_coupon_submit">Apply Coupon</span>
                    </div>

                    <div class="sp_coupon_response">
                        <div class="sp_inner_div">
                            <span id="sp_cpn_resp"></span>
                        </div>
                    </div>

                    <?php $applied_coupons = WC()->cart->get_applied_coupons();

                    ?>
                </div>

                <a class="sp_cart_cart_btn" href="<?php echo wc_get_cart_url(); ?>" style="margin-bottom: 4px;">
                    <?php echo get_option('sp_cart_viewcart_btn', 'View Cart'); ?>
                </a>
                <a class="sp_cart_checkout_btn" href="<?php echo wc_get_checkout_url(); ?>" style="margin-bottom: 4px;">
                    <?php echo get_option('sp_cart_checkout_btn', 'Checkout'); ?>
                </a>
                
                <a class="sp_cart_continueshop_btn" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
                    <?php echo esc_html(get_option('sp_cart_contshipping_btn', 'Continue Shopping')); ?>
                </a>




            </div>

        </div>

        <div id="sp-cart-footer-basket" class="sp_cart_basket_cart" style="height: 60px;width: 60px;background-color: #cccccc;position: sticky;">
            <div class="sp_cart_box">
                <?php
                $myprefix_image_id =  get_option('sp_myprefix_image_id');
                $image = wp_get_attachment_image($myprefix_image_id, 'medium', false, array('id' => 'sp_myprefix-preview-image'));
                if (!empty($myprefix_image_id && !empty($image))) {
                    echo $image;
                } else { ?>
                    <img src="<?php echo SFSC_THM_DIR . '/assets/images/float-scart-icon.png'; ?>">
                <?php } ?>
            </div>
            <div class="sp_cart_item_count" style="background-color: #8ec545;color: #ffffff;font-size: 15px;">
                <span class="sp_cart_float_count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
            </div>
        </div>
<?php
    }

    function Sp_side_cart_create()
    {

        if (wp_is_mobile()) {
            if (get_option('sp_hide_cart_mob', 'no') == "no") {
                if (is_checkout() || is_cart()) {
                } else {
                    add_action('wp_footer', array($this, 'SP_cart_create'));
                }
            }
        } else {
            if (is_checkout() || is_cart()) {
            } else {
                add_action('wp_footer', array($this, 'SP_cart_create'));
            }
        }
    }

    function sp_exclude_ipad_and_tablets()
    {
        static $is_mobile;

        if (isset($is_mobile))
            return $is_mobile;

        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            $is_mobile = false;
        } elseif (
            strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        ) {
            $is_mobile = true;
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false) {
            $is_mobile = true;
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) {
            $is_mobile = false;
        } else {
            $is_mobile = false;
        }

        return $is_mobile;
    }
    /**
     * Ajax trigger for side-cart 
     */
    function SP_cart_fragments($fragments)
    {
        WC()->cart->calculate_totals();
        WC()->cart->maybe_set_cart_cookies();
        $cart_product_ids = $cart_variation_ids = array();
        $html = '<div class="sp_cart_body">';
        if (!WC()->cart->is_empty()) :
            $html .= "<table class='sp_cart_mini_cart_custom'>";
            $cart_items = WC()->cart->get_cart();
            foreach ($cart_items as $cart_item_key => $cart_item) {
                $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                if (get_option('sp_cart_hide_prdct_link', 'yes') == "yes") {
                    $product_permalink     = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                    $sp_cart_prdt_link = "sp_cart_link";
                }
                if (get_option('sp_cart_hide_prdct_name', 'yes') == "yes") {
                    if (get_option('sp_cart_hide_variation_name', 'yes') == "yes") {
                        $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                    } else {
                        $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key);
                    }
                }
                if (get_option('sp_cart_hide_prdct_img', 'yes') == "yes") {
                    $thumbnail     = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                    $sp_cart_thumb_img = "sp_cart_prdct_image";
                }
                if (get_option('sp_cart_hide_price', 'yes') == "yes") {
                    $product_subtotal = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);

                    /* 								$sp_cart_price_sub="Price :"; */

                    $sp_cart_price_sub = "";
                    $sp_cart_price = "sp_prdct_total";
                }
                if (get_option('sp_cart_hide_cart_total', 'yes') == "yes") {
                    $cart_total = WC()->cart->get_subtotal();
                    $coupon_price = WC()->cart->get_discount_total();
                    $total_cart = $cart_total - $coupon_price;
                    $currency = get_woocommerce_currency_symbol() . number_format($total_cart, 2);
                    $sp_cart_total = "sp_cart_total_price";
                    $sp_cart_total_txt = "TOTAL";
                }
                $cart_product_ids[] = $product_id;

                if ($_product->get_type() == 'variation') {
                    $cart_variation_ids[] = $cart_item['variation_id'];
                }


                $html .= "<tr product_id='" . $product_id . "' c_key='" . $cart_item_key . "'>";
                $html .= "<td class='" . $sp_cart_thumb_img . "'><a class=" . $sp_cart_prdt_link . " href='" . $product_permalink . "'>" . $thumbnail . "</a></td>";
                $html .= "<td class='sp-cart-item-content'><div class='sp_cart_description'>";
                $html .= "<span class='sp_cart_product_name'><b><a class=" . $sp_cart_prdt_link . " href='" . $product_permalink . "'>" . $product_name . '</a></b></span>  <br> ';

                /* Before Qty: Removed
                                $html .= '<div class="sp_cart_number sp_cart_qnty"><span class="sp_cart_qnty_title">Qty: </span><span class="sp_cart_minus"><i class="fa fa-minus" aria-hidden="true"></i></span><input type="number" name="update_qty" class="sp_cart_qnty_field" min="1" value="'.$cart_item['quantity'].'"><span class="sp_cart_plus"><i class="fa fa-plus" aria-hidden="true"></i></span></div>';
*/


                $html .= '<div class="sp_cart_number sp_cart_qnty"><span class="sp_cart_qnty_title"></span><span class="sp_cart_minus"><i class="fa fa-minus" aria-hidden="true"></i></span><input type="number" name="update_qty" class="sp_cart_qnty_field" min="1" value="' . $cart_item['quantity'] . '"><span class="sp_cart_plus"><i class="fa fa-plus" aria-hidden="true"></i></span></div>';
                $html .= "<span class='" . $sp_cart_price . "' > " . $sp_cart_price_sub . " " . $product_subtotal . '</span>';
                $html .= "</div></td>";
                $html .= "<td class='sp_cart_item_close'>";
                $html .= apply_filters('woocommerce_cart_item_remove_link', sprintf(
                    '<a href="%s" class="sp_cart_remove"  aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart_item_key="%s">&times;</a>',
                    esc_url(wc_get_cart_remove_url($cart_item_key)),
                    esc_html__('Remove this item', 'woocommerce'),
                    esc_attr($product_id),
                    esc_attr($_product->get_sku()),
                    esc_attr($cart_item_key)
                ), $cart_item_key);
                $html .= "</td>";
                $html .= "</tr>";
            }


            $html .= "</table>";

            $html .= "<div class='sp_cart_total_price' class='" . $sp_cart_total . "'>";

            $applied_coupons = WC()->cart->get_applied_coupons();

            if (!empty($applied_coupons)) {
                $html .= "<ul class='sp_applied_cpns'>";

                foreach ($applied_coupons as $cpns) {
                    $html .= "<li class='sp_remove_cpn' cpcode='" . $cpns . "'>Coupon: " . $cpns . " 
                               
                                <span class='sp_cpn_value'>-$" . WC()->cart->get_coupon_discount_amount($cpns) . ".00</span>
                                <span class='sp_rmv_cpn'>[Remove]</span></li>";
                }

                $html .= "</ul>";
            }
            $html .= "<div class='sp_total_txt' style='width: 60%;text-align: left;'><span>" . $sp_cart_total_txt . "</span></div>";
            $html .= "<div style='text-align: right; width: 40%;padding-right: 25px;'><span class='sp_cart_total'>" . $currency . "</span></div>";
            $html .= "</div>";


        else :
            $html .= "<h3 class='sp_cart_empty_cart_text'>" . get_option('sp_cart_empty_cart_txt', 'Cart is empty') . "</h3>";
        endif;
        $html .= '</div>';
        //-------  Ajax - assign side cart body and cart count -------
        $fragments['div.sp_cart_body'] = $html;
        $html = '<span class="sp_cart_float_count">' . WC()->cart->get_cart_contents_count() . '</span>';
        $fragments['span.sp_cart_float_count'] = $html;




        $sp_coupon_html = "<div class='sp_coupon'>";
        $sp_coupon_html .= '<div class="sp_coupon_field">';
        $sp_coupon_html .= '<input type="text" id="sp_coupon_code" placeholder="Enter your coupon code">';
        $sp_coupon_html .= '<span class="sp_coupon_submit">Apply Coupon</span>';
        $sp_coupon_html .= '</div>';


        $sp_coupon_html .= "</div>";

        $fragments['div.sp_coupon'] = $sp_coupon_html;


        return $fragments;
    }

    /**
     * Ajax remove from side cart
     */

    function scart_product_remove()
    {
        ob_start();
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $_POST['product_id'] && $cart_item_key == $_POST['cart_item_key']) {
                WC()->cart->remove_cart_item($cart_item_key);
            }
        }

        WC()->cart->calculate_totals();
        WC()->cart->maybe_set_cart_cookies();

        woocommerce_mini_cart();

        $mini_cart = ob_get_clean();

        // Fragments and mini cart are returned
        $data = array(
            'fragments' => apply_filters(
                'woocommerce_add_to_cart_fragments',
                array(
                    'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
                )
            ),
            'cart_hash' => apply_filters('woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5(json_encode(WC()->cart->get_cart_for_session())) : '', WC()->cart->get_cart_for_session())
        );

        wp_send_json($data);

        die();
    }

    /**
     * Ajax - change quantity in side cart
     */

    function scart_change_qty()
    {
        $c_key = sanitize_text_field($_REQUEST['c_key']);
        $qty = sanitize_text_field($_REQUEST['qty']);
        WC()->cart->set_quantity($c_key, $qty, true);
        WC()->cart->set_session();
        exit();
    }
}
new Sfsc_Side_Cart();
