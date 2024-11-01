<?php

if (!defined('ABSPATH'))
  exit;
  if (!class_exists('Sfsc_Cart_admin_menu')) {

    class Sfsc_Cart_admin_menu {


        protected static $Sfsc_Cart_instance;

        function Sp_Cart_submenu_page() {
		add_menu_page('Side Cart', 'Side Cart', 'manage_options', 'side_cart', array($this, 'Sp_Cart_callback'),'dashicons-cart', 61);
        }

        function Sp_Cart_callback() {
            ?>    
                <div class="wrap">
				
                    <h2>Side Cart</h2>
              
                <div class="sp_cart_container">
				
                    <form method="post" >
                        <?php wp_nonce_field( 'sp_cart_nonce_action', 'sp_cart_nonce_field' ); ?>
                       
						<ul class="sp_cart_tab">
                            <li class="tab-link current" data-tab="sp-cart-general-tab"><?php echo __( 'General Settings' );?></li>
                            <li class="tab-link" data-tab="sp-cart-cart-tab"><?php echo __( 'Cart Settings' );?></li>
                            <li class="tab-link" data-tab="sp-cart-texts"><?php echo __( 'Cart Texts ' );?></li>
                        </ul>
                        <div id="sp-cart-general-tab" class="sp_cart_content_tab current">
                            <div class="sp_cart_cover">
                                <table class="sp_cart_data_table">
                                    <tr>
                                        <th>Enable Side Cart</th>
                                        <td>
                                            <input type="checkbox" name="sp_cart_global_cart" value="yes" <?php if (get_option( 'sp_cart_global_cart' ) == "yes" ) { echo 'checked="checked"'; } ?>>                         
                                        </td>
                                    </tr>
									<tr>
                                        <th>Auto Open Cart </th>
                                        <td>
                                            <input type="checkbox" name="sp_cart_auto_cart" value="yes" <?php if (get_option( 'sp_cart_auto_cart' ) == "yes" ) { echo 'checked="checked"'; } ?>>
                                        </td>
                                    </tr>
									<tr>
                                        <th>Ajax Add To Cart</th>
                                        <td>
                                            <input type="checkbox" name="sp_cart_ajax_cart" value="yes" <?php if (get_option( 'sp_cart_ajax_cart' ) == "yes" ) { echo 'checked="checked"'; } ?>>
                                        </td>
                                    </tr>
									<tr>
                                        <th>Hide Cart Icon On Mobile</th>
                                        <td>
                                            <input type="checkbox" name="sp_hide_cart_mob" value="yes" <?php if (get_option( 'sp_hide_cart_mob' ) == "yes" ) { echo 'checked="checked"'; } ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Select your icon (Not greater than 1MB)</th>
                                        <td class = "sp_select_icon" style="align-items: center; display: flex; position: relative;">
                                        
                                         <?php 
                                         $image_id = get_option( 'sp_myprefix_image_id' );
                                         if( intval( $image_id ) > 0 ) {
                                             
                                             $image_fetch = wp_get_attachment_url(  $image_id ); 
                                             $image = ' <img id="sp_myprefix-preview-image" src="'. $image_fetch.'" style = "height:50px; "/> ';

                                             $image_id = get_option( 'sp_delete_file' );?>
                                           
                                           <input type="hidden" value="<?php echo esc_attr( $delete_img ); ?>" name="sp_delete_file" id="delete_file" />
                                           
                                           <input type="submit" onclick="return confirm('Are you sure! Do you want to remove this icon?')" name="sp_delete_file" value ="<?php esc_attr_e('x') ?>" style="border: 1px solid; position: absolute; top: -16px; right: -23px; color: #595e62; background: transparent; font-weight: 700; font-size: 13px;border-radius: 50%;padding: 0 5px 4px;line-height: 0.9;"/>
                                           
                                         <?php } 
                                         
                                         else {
                                             // Some default image
                                             $image_url =  SFSC_THM_DIR . '/assets/images/float-scart-icon.png';
                                             $image = '<img id="sp_myprefix-preview-image" src="'.$image_url.'" style="height:50px;" value="'.get_option('default_image').'"/>';
                                         }
                                         ?>
                                          <input type='button' class="button-primary" value="<?php esc_attr_e( 'Select an image', 'mytextdomain' ); ?>" id="sp_myprefix_media_manager" style="margin-right: 10px;"/>

                                           <?php
                                           
                                            echo $image; 
                                           
                                           ?>

                                                                                      
                                          <input type="hidden" name="sp_myprefix_image_id" id="sp_myprefix_image_id" value="<?php echo esc_attr( $image_id ); ?>" class="regular-text" />
                                         
                                          
                                        </td>
                                    </tr>
                                    
								</table>
							</div>
						</div>
						
						<div id="sp-cart-cart-tab" class="sp_cart_content_tab">
                            <div class="sp_cart_cover">
                                    <table class="sp_cart_data_table">
									
                                    <tr>
                                        <th>Show Product Image</th>
                                        <td>
                                            <input type="checkbox" name="sp_cart_hide_prdct_img" value="yes" <?php if (get_option( 'sp_cart_hide_prdct_img', 'yes' ) == "yes" ) { echo 'checked="checked"'; } ?>>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Show Product Name</th>
                                        <td>
                                            <input type="checkbox" name="sp_cart_hide_prdct_name" value="yes" <?php if (get_option( 'sp_cart_hide_prdct_name', 'yes' ) == "yes" ) { echo 'checked="checked"'; } ?>>
                                            
                                        </td>
                                    </tr>
									<tr>
                                        <th>Show Product Variations</th>
                                        <td>
                                            <input type="checkbox" name="sp_cart_hide_variation_name" value="yes" <?php if (get_option( 'sp_cart_hide_variation_name', 'yes' ) == "yes" ) { echo 'checked="checked"'; } ?>>
                                            
                                        </td>
                                    </tr>
									<tr>
                                        <th>Show Product Price</th>
                                        <td>
                                            <input type="checkbox" name="sp_cart_hide_price" value="yes" <?php if (get_option( 'sp_cart_hide_price', 'yes' ) == "yes" ) { echo 'checked="checked"'; } ?>>
                                            
                                        </td>
                                    </tr>
									
									<tr>
                                        <th>Show Cart Total</th>
                                        <td>
                                            <input type="checkbox" name="sp_cart_hide_cart_total" value="yes" <?php if (get_option( 'sp_cart_hide_cart_total', 'yes' ) == "yes" ) { echo 'checked="checked"'; } ?>>
                                            
                                        </td>
                                    </tr>
									<tr>
                                        <th>Show Product Link</th>
                                        <td>
                                            <input type="checkbox" name="sp_cart_hide_prdct_link" value="yes" <?php if (get_option( 'sp_cart_hide_prdct_link', 'yes' ) == "yes" ) { echo 'checked="checked"'; } ?>>
                                            
                                        </td>
                                    </tr>
                                
                                </table>
                            </div>
                            
                            
                        </div>
                        <div id="sp-cart-texts" class="sp_cart_content_tab">
                            <div class="sp_cart_cover">
                                <table class="sp_cart_data_table"> 
                                    
									<tr>
                                        <th>Cart Heading</th>
                                        <td>
                                            <input type="text" name="sp_cart_cart_heading" value="<?php echo get_option( 'sp_cart_cart_heading', 'Your Cart' ); ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>View Cart Button Text</th>
                                        <td>
                                            <input type="text" name="sp_cart_viewcart_btn" value="<?php echo get_option( 'sp_cart_viewcart_btn', 'View Cart' ); ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Checkout Button Text</th>
                                        <td>
                                            <input type="text" name="sp_cart_checkout_btn" value="<?php echo get_option( 'sp_cart_checkout_btn', 'Checkout' ); ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Continue Shopping Button Text</th>
                                        <td>
                                            <input type="text" name="sp_cart_contshipping_btn" value="<?php echo get_option( 'sp_cart_contshipping_btn', 'Continue Shopping' ); ?>">
                                        </td>
                                    </tr>
									<tr>
                                        <th>Empty Cart Text</th>
                                        <td>
                                            <input type="text" name="sp_cart_empty_cart_txt" value="<?php echo get_option( 'sp_cart_empty_cart_txt', 'Cart is empty' ); ?>">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <input type="hidden" name="action" value="sp_cart_save_option">
                        <input type="submit" value="Save changes" name="submit" class="button-primary" id="sp-cart-btn-space">
						<div style="float:right"><p style="margin-top: 3px;">Powered by: <a href="https://storepro.io/" style="color: #ee6443;text-decoration: none;font-weight: 500;">StorePro</a></p></div>
                    </form>  
                </div>
            <?php
        }
        

       
        
        

        function sp_cart_recursive_sanitize_text_field($array) {  
            if(!empty($array)) {
                foreach ( $array as $key => $value ) {
                    if ( is_array( $value ) ) {
                        $value = $this->sp_cart_recursive_sanitize_text_field($value);
                    }else{
                        $value = sanitize_text_field( $value );
                    }
                }
            }
            return $array;
        }

        function sp_cart_save_options() {
            if( current_user_can('administrator') ) {
                if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'sp_cart_save_option') {
                    if(!isset( $_POST['sp_cart_nonce_field'] ) || !wp_verify_nonce( $_POST['sp_cart_nonce_field'], 'sp_cart_nonce_action' ) ){
                        print 'Sorry, your nonce did not verify.';
                        exit;
                    } else {

                        if(isset($_REQUEST['sp_cart_global_cart']) && !empty($_REQUEST['sp_cart_global_cart'])) {
                            $sp_cart_global_side_cart = sanitize_text_field( $_REQUEST['sp_cart_global_cart'] );
                        } else {
                            $sp_cart_global_side_cart = 'no';
                        }

                        update_option('sp_cart_global_cart', $sp_cart_global_side_cart, 'yes');
						
						if(isset($_REQUEST['sp_cart_ajax_cart']) && !empty($_REQUEST['sp_cart_ajax_cart'])) {
                            $sp_side_cart_ajax_cart = sanitize_text_field( $_REQUEST['sp_cart_ajax_cart'] );
                        } else {
                            $sp_side_cart_ajax_cart = 'no';
                        }

                        update_option('sp_cart_ajax_cart', $sp_side_cart_ajax_cart, 'yes');
						
						if(isset($_REQUEST['sp_cart_auto_cart']) && !empty($_REQUEST['sp_cart_auto_cart'])) {
                            $sp_side_cart_auto_cart = sanitize_text_field( $_REQUEST['sp_cart_auto_cart'] );
                        } else {
                            $sp_side_cart_auto_cart = 'no';
                        }

                        update_option('sp_cart_auto_cart', $sp_side_cart_auto_cart, 'yes');
						
						if(isset($_REQUEST['sp_hide_cart_mob']) && !empty($_REQUEST['sp_hide_cart_mob'])) {
                            $sp_hide_cart_mob = sanitize_text_field( $_REQUEST['sp_hide_cart_mob'] );
                        } else {
                            $sp_hide_cart_mob = 'no';
                        }

                        update_option('sp_hide_cart_mob', $sp_hide_cart_mob, 'yes');


                        if(isset($_REQUEST['sp_myprefix_image_id']) && !empty($_REQUEST['sp_myprefix_image_id'])) {
                            $myprefix_image_id = sanitize_text_field( $_REQUEST['sp_myprefix_image_id'] );
                            update_option('sp_myprefix_image_id', $myprefix_image_id, 'yes');
                        } 
                        // else {
                        //     $myprefix_image_id= 'no';
                        // }

                       // update_option('sp_myprefix_image_id', $myprefix_image_id, 'yes');

                        // if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) ) :
                            if ( isset( $_POST['submit'] ) && isset( $_POST['image_attachment_id'] ) ) :
                            update_option( 'media_selector_attachment_id', absint( $_POST['image_attachment_id'] ) );
                        endif;
//To Remove uploaded cart

                        if(isset($_REQUEST['sp_delete_file']) && !empty($_REQUEST['sp_delete_file'])) {
                            if ($_REQUEST['sp_delete_file']=="x"){ 
                                                  
                                $myprefix_image_id= 'no';
                                update_option('sp_myprefix_image_id', $myprefix_image_id, 'yes');
                        } }
//


                        if(isset($_REQUEST['sp_cart_hide_prdct_img']) && !empty($_REQUEST['sp_cart_hide_prdct_img'])) {
                            $sp_cart_side_hide_prdct_img = sanitize_text_field( $_REQUEST['sp_cart_hide_prdct_img'] );
                        } else {
                            $sp_cart_side_hide_prdct_img = 'no';
                        }

                        update_option('sp_cart_hide_prdct_img', $sp_cart_side_hide_prdct_img, 'yes');
						
						if(isset($_REQUEST['sp_cart_hide_prdct_link']) && !empty($_REQUEST['sp_cart_hide_prdct_link'])) {
                            $sp_cart_side_hide_prdct_link = sanitize_text_field( $_REQUEST['sp_cart_hide_prdct_link'] );
                        } else {
                            $sp_cart_side_hide_prdct_link = 'no';
                        }

                        update_option('sp_cart_hide_prdct_link', $sp_cart_side_hide_prdct_link, 'yes');
						
						if(isset($_REQUEST['sp_cart_hide_cart_total']) && !empty($_REQUEST['sp_cart_hide_cart_total'])) {
                            $sp_cart_side_hide_cart_total = sanitize_text_field( $_REQUEST['sp_cart_hide_cart_total'] );
                        } else {
                            $sp_cart_side_hide_cart_total = 'no';
                        }

                        update_option('sp_cart_hide_cart_total', $sp_cart_side_hide_cart_total, 'yes');

                        if(isset($_REQUEST['sp_cart_hide_prdct_name']) && !empty($_REQUEST['sp_cart_hide_prdct_name'])) {
                            $sp_cart_side_hide_prdct_name = sanitize_text_field( $_REQUEST['sp_cart_hide_prdct_name'] );
                        } else {
                            $sp_cart_side_hide_prdct_name = 'no';
                        }

                        update_option('sp_cart_hide_prdct_name', $sp_cart_side_hide_prdct_name, 'yes');
						
						if(isset($_REQUEST['sp_cart_hide_variation_name']) && !empty($_REQUEST['sp_cart_hide_variation_name'])) {
                            $sp_cart_side_hide_variation_name = sanitize_text_field( $_REQUEST['sp_cart_hide_variation_name'] );
                        } else {
                            $sp_cart_side_hide_variation_name = 'no';
                        }

                        update_option('sp_cart_hide_variation_name', $sp_cart_side_hide_variation_name, 'yes');

                        if(isset($_REQUEST['sp_cart_hide_price']) && !empty($_REQUEST['sp_cart_hide_price'])) {
                            $sp_cart_side_hide_price = sanitize_text_field( $_REQUEST['sp_cart_hide_price'] );
                        } else {
                            $sp_cart_side_hide_price = 'no';
                        }
						update_option('sp_cart_cart_heading', sanitize_text_field( $_REQUEST['sp_cart_cart_heading'] ), 'yes');
						update_option('sp_cart_empty_cart_txt', sanitize_text_field( $_REQUEST['sp_cart_empty_cart_txt'] ), 'yes');
                        update_option('sp_cart_hide_price', $sp_cart_side_hide_price, 'yes');
                        update_option('sp_cart_viewcart_btn', sanitize_text_field( $_REQUEST['sp_cart_viewcart_btn'] ), 'yes');
						update_option('sp_cart_checkout_btn', sanitize_text_field( $_REQUEST['sp_cart_checkout_btn'] ), 'yes');
						update_option('sp_cart_contshipping_btn', sanitize_text_field( $_REQUEST['sp_cart_contshipping_btn'] ), 'yes');
						update_option('woocommerce_enable_ajax_add_to_cart', $sp_side_cart_ajax_cart, 'yes');
						
					
						function sp_cart_update_notice() {
									?>
									<div class="updated notice is-dismissible">
										<p><strong><?php _e( 'Your settings have been saved.' ); ?></strong></p>
									</div>
									<?php
								}
								add_action( 'admin_notices', 'sp_cart_update_notice' );
                    }
                }
            }
        }

	
        function init() {
            add_action( 'admin_menu',  array($this, 'Sp_Cart_submenu_page'));
            add_action( 'init',  array($this, 'sp_cart_save_options'));
            
        }

        public static function Sfsc_Cart_instance() {
            if (!isset(self::$Sfsc_Cart_instance)) {
                self::$Sfsc_Cart_instance = new self();
                self::$Sfsc_Cart_instance->init();
            }
            return self::$Sfsc_Cart_instance;
        }
    }
    Sfsc_Cart_admin_menu::Sfsc_Cart_instance();
}

