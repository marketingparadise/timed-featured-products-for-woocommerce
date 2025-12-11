<?php
/*
 * Plugin Name: Timed featured products for Woocommerce
 * Description: Feature woocommerce products for a limited time.
 * Version: 1.0.0
 * Requires Plugins: woocommerce
 * Author: Marketing Paradise
 * Author URI: https://mkparadise.com/
 * License: GPLv2 or later
 * Text Domain: timed-featured-products-for-woocommerce
 * Domain Path: /languages

 * @link      https://mkparadise.com/
 * @package   Timed_Featured_Products_for_Woocommerce
 */

if (!defined('ABSPATH')) {
    exit;
}


/**
 * When activating the plugin, we check whether WooCommerce is active.
 */
function timedfeatured_activate_plugin() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die(
            esc_html__( 'We are sorry, but the "minimum order" plugin requires WooCommerce to be installed and active. Please activate WooCommerce and try again.', 'timed-featured-products-for-woocommerce' ),
            esc_html__( 'Activation error', 'timed-featured-products-for-woocommerce' ),
            array( 'back_link' => true )
        );
    }
}
register_activation_hook( __FILE__, 'timedfeatured_activate_plugin' );

final class TimedFeatured_Principal {

    public function __construct() {
        $this->load_dependencies();
        new TimedFeatured_Admin();
        new TimedFeatured_Public();
    }

    private function load_dependencies() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-timed-featured-admin.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-timed-featured-public.php';
    }
}

/**
 * Display message if we deactivate WooCommerce
 */
function timedfeatured_init_plugin() {
    
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'timedfeatured_warning_woocommerce' );
        return;
    }

    new TimedFeatured_Principal();
}
add_action( 'plugins_loaded', 'timedfeatured_init_plugin' );

// If WooCommerce is not active, we will launch a warning.
function timedfeatured_warning_woocommerce() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p>
            <strong><?php esc_html_e( 'Minimum order for WooCommerce:', 'timed-featured-products-for-woocommerce' ); ?></strong>
            <?php esc_html_e( 'This plugin requires WooCommerce to be installed and active in order to work.', 'timed-featured-products-for-woocommerce' ); ?>
        </p>
    </div>
    <?php
}
