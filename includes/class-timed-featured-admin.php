<?php
/**
 * The plugin's administration functionality.
 *
 * @package    Timed_Featured_Products_for_Woocommerce
 * @subpackage Timed_Featured_Products_for_Woocommerce/admin
 * @author     Marketing Paradise <rafael@mkparadise.com>
 */

if (!defined('ABSPATH')) {
    exit;
}

class TimedFeatured_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'timed_featured_menu')); // Add page to WooCommerce submenu
    }

    /**
     * Add page to WooCommerce submenu
     */
    public function timed_featured_menu() {
        add_submenu_page(
            'woocommerce',
            esc_html__( 'Timed featured products', 'timed-featured-products-for-woocommerce' ),
            esc_html__( 'Timed featured products', 'timed-featured-products-for-woocommerce' ),
            'manage_woocommerce',
            'mkp-timed-featured-options',
            array ($this, 'timedfeatured_options'),
            7
        );
    }

        public function timedfeatured_options() {
        ?>
		    <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			    <form method="post" action="options.php">
				    <?php
                    settings_errors(); // Errors. We do not set “timedfeatured_settings_error” so that all errors are displayed, not just those we configure.
					settings_fields( 'timedfeatured_settings_group' ); // Name of the settings group
					do_settings_sections( 'mkp-timed-featured-options' ); // page slug of the options page
					submit_button(); // Button that saves the options
				    ?>
			    </form>
		    </div>
	    <?php
    }

}