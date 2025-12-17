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
        add_action('admin_init', array ($this, 'timed_featured_settings')); // We create settings sections, register settings, and create fields.
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

    /**
     * We create settings sections, register settings, and create fields.
     */
    public function timed_featured_settings() {

        $page_slug = 'mkp-timed-featured-options';
	    $option_group = 'timedfeatured_settings_group';

        // 1 - We create the section
        add_settings_section(
	        'timedfeatured-section', // Section ID
	        esc_html__( 'Options', 'timed-featured-products-for-woocommerce' ), // title (optional)
	        '', // callback to paint the section (optional)
	        $page_slug
	    );

        // 2 - We register the fields
        register_setting($option_group, 'timedfeatured_time', array ($this, 'validate_time'));

        // 3 - We add the fields
        add_settings_field(
            'timedfeatured_time',
            esc_html__( 'Time (in days)', 'timed-featured-products-for-woocommerce' ),
            array ($this, 'paint_time'), // function that paints the field
            $page_slug,
            'timedfeatured-section' // Section ID
        );
    }

    // 4 - We paint the fields
    public function paint_time () {
        $time = get_option('timedfeatured_time', 0);
        echo "<input id='mkp-timedfeatured-time' name='timedfeatured_time' type='number' min='0' value='". esc_attr( $time ) ."' />";
        echo '<p class="description">' . esc_html__( 'A time of 0 means that the products will remain featured until you manually remove them.', 'timed-featured-products-for-woocommerce' ) . '</p>';
        echo '<p class="description">' . esc_html__( 'This is a global option. You can override it at product level.', 'timed-featured-products-for-woocommerce' ) . '</p>';
    }

    // Fields validation
    public function validate_time ($input) {
        if (!is_numeric($input) || $input < 0) { // Time must be a number greater than zero.
            add_settings_error(
		    'timedfeatured_settings_error',
		    'no-float', // part of the error message ID id="setting-error-no-float"
		    esc_html__('Time is invalid', 'timed-featured-products-for-woocommerce'), // Error message
		    'error' // error type
	    );
        $input = get_option('timedfeatured_time', 0); // If there is an error, the previous value remains.
    }

    $sanitized_input = floatval($input);
    return $sanitized_input;
    }
}