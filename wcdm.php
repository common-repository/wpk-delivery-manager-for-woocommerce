<?php
/**
 * Plugin Name: Order Delivery Date & Time For WooCommerce
 * Description: <code><strong>Don't worry about your website speed it is fully optimized</strong></code>A flexible and optimized WordPress plugin that allows you to add date & time field on checkout page so user can select the specific delivery date also allow flexibility to add additional charge if specific delivery date chosen.
 * Author: SpiderWares
 * Text Domain: order-delivery-date-and-time-for-woocommerce
 * Version: 1.0.5
 * Author URI: https://spiderwares.com/
 */

if( ! defined( 'WCDM_PLUGIN_FILE' ) ) :
	define( 'WCDM_PLUGIN_FILE', __FILE__ );
endif;

if( ! defined( 'WCDM_PLUGIN_BASENAME' ) ) :
	define( 'WCDM_PLUGIN_BASENAME', plugin_basename( WCDM_PLUGIN_FILE ) );
endif;

if ( ! defined( 'WCDM_VERSION' ) ) :
	define( 'WCDM_VERSION', '1.0.2' );
endif;

if ( ! defined( 'WCDM_PATH' ) ) :
	define( 'WCDM_PATH', plugin_dir_path( __FILE__ ) );
endif;

if ( ! defined( 'WCDM_URL' ) ) :
	define( 'WCDM_URL', plugin_dir_url( __FILE__ ) );
endif;



if ( ! function_exists( 'wcdm_constructor' ) ) :

	function wcdm_constructor(){
		if( is_admin() ) :
			require_once "include/wcdm-glboal.php";
			require_once "admin/class-wcdm-admin.php";
		else :
			require_once "public/class-wcdm-frontend.php";
		endif;
	}
	add_action( 'wcdm_init', 'wcdm_constructor' );

endif;


if ( ! function_exists( 'wcdm_woocommerce_admin_notice' ) ) :
	
	function wcdm_woocommerce_admin_notice() { ?>
		<div class="error">
			<p><?php echo esc_html( 'Order Delivery Date & Time For WooCommerce' . __( 'is enabled but not effective. It requires WooCommerce to work.', 'order-delivery-date-and-time-for-woocommerce' ) ); ?></p>
		</div>
		<?php
	}

endif;


if ( ! function_exists( 'wcdm_install' ) ) :
	
	function wcdm_install() {
		if ( ! function_exists( 'WC' ) ) :
			add_action( 'admin_notices', 'wcdm_woocommerce_admin_notice' );
		else :
			do_action( 'wcdm_init' );
		endif;
	}
	add_action( 'plugins_loaded', 'wcdm_install', 11 );

endif;