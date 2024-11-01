<?php
/**
 
 * Plugin Name:       Super Fast Side Cart for WooCommerce
 * Plugin URI:        https://storepro.io/
 * Description:       Built for speed! A fast (Ajax) slide in Side Cart / Floating Cart for WooCommerce. Fully flexible, with options to control functionality.
 * Version:           1.0.7
 * Author:            StorePro
 * Author URI:        https://storepro.io/
 */

if (!defined('ABSPATH')) {
  die('-1');
}
if (!defined('SFSC_PLUGIN_NAME')) {
  define('SFSC_PLUGIN_NAME', 'Super Fast Side Cart for WooCommerce');
}
if (!defined('SFSC_PLUGIN_VERSION')) {
  define('SFSC_PLUGIN_VERSION', '1.0.3');
}
if (!defined('SFSC_PLUGIN_FILE')) {
  define('SFSC_PLUGIN_FILE', __FILE__);
}
if (!defined('SFSC_THM_DIR')) {
  define('SFSC_THM_DIR',plugins_url('', __FILE__));
}



if (!class_exists('Sfsc_Cart')) {

  	class Sfsc_Cart {

    	protected static $Sfsc_Cart_instance;

    	public static function Sfsc_Cart_instance() {
	      	if (!isset(self::$Sfsc_Cart_instance)) {
	        	self::$Sfsc_Cart_instance = new self();
	        	self::$Sfsc_Cart_instance->init();
	        	self::$Sfsc_Cart_instance->includes();
	      	}
	      	return self::$Sfsc_Cart_instance;
	    }

      	function __construct() {
        	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        	// Checking the plugin activted or not
        	add_action('admin_init', array($this, 'Sfsc_Cart_check_plugin_state'));
      	}

      	function init() {
	      	add_action( 'admin_notices', array($this, 'sfsc_cart_show_notice'));
	        add_action( 'admin_enqueue_scripts', array($this, 'sfsc_cart_load_admin_script_style')); 
	      	add_action( 'wp_enqueue_scripts',  array($this, 'sfsc_side_cart_script'));
	      	add_action( 'wp_enqueue_scripts',  array($this, 'sfsc_side_cart_single_script'));
	      	add_action( 'wp_enqueue_scripts',  array($this, 'sfsc_side_cart_auto_open_script'));
	      		
	    }

	    //Main Files
	    function includes() {
	      	include_once('includes/cart_sp_backend.php');
	      	include_once('includes/cart_sp_front.php');
	    }

	    //Js and Css For Backend
	   function sfsc_cart_load_admin_script_style() {
	      	wp_enqueue_style( 'sp-side-cart-admin-css', SFSC_THM_DIR . '/assets/css/sp-side-cart-admin-style.css', false, '1.0.3' );
			wp_enqueue_script( 'sp-side-cart-admin-script', SFSC_THM_DIR . '/assets/js/sp-side-cart-admin-script.js', array( 'jquery', 'select2') );
	    } 

		//Js and Css For Frontend
	    function sfsc_side_cart_script() {
			$global_enable = get_option('sp_cart_global_cart', 'no');
			if ($global_enable === 'yes'){
			wp_enqueue_style( 'sp-side-cart-css', SFSC_THM_DIR . '/assets/css/sp-side-cart.css', false, '1.0.3' );
	      	wp_enqueue_script( 'sp-side-cart-js', SFSC_THM_DIR . '/assets/js/sp-side-cart.js', false, '1.0.3' );
			wp_enqueue_style( 'Font_Awesome', 'https://use.fontawesome.com/releases/v5.6.1/css/all.css' );
			wp_localize_script( 'sp-side-cart-js', 'ajax_postajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	    }
		}
		function sfsc_side_cart_single_script() {
			$enable_ajax = get_option('sp_cart_ajax_cart', 'no');
			if ($enable_ajax === 'yes'){
			wp_enqueue_script( 'sp-side-single-cart', SFSC_THM_DIR . '/assets/js/sp-side-single-cart.js', false, '1.0.3' );
			}
		}
		function sfsc_side_cart_auto_open_script() {
			$enable_auto_open = get_option('sp_cart_auto_cart', 'no');
			if ($enable_auto_open === 'yes'){
			wp_enqueue_script( 'sp-side-auto-open', SFSC_THM_DIR . '/assets/js/sp-side-auto-open.js', false, '1.0.3' );
			}
		}

    	function sfsc_cart_show_notice() {
        	if ( get_transient( get_current_user_id() . 'spcarterror' ) ) {

          		deactivate_plugins( plugin_basename( __FILE__ ) );

          		delete_transient( get_current_user_id() . 'spcarterror' );

          		echo '<div class="error"><p> The plugin is deactivated, because it require WooCommerce plugin installed and activated.</p></div>';
        	}
    	}


    	function Sfsc_Cart_check_plugin_state(){
      		if ( ! ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) ) {
        		set_transient( get_current_user_id() . 'spcarterror', 'message' );
      		}
    	}
	    
  	}

  	add_action('plugins_loaded', array('Sfsc_Cart', 'Sfsc_Cart_instance'));
}