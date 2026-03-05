<?php
/**
 * The plugin's administration functionality.
 *
 * @package     Timed_Featured_Products_for_Woocommerce
 * @subpackage  Timed_Featured_Products_for_Woocommerce/admin
 * @author      Marketing Paradise <rafael@mkparadise.com>
 */

if (!defined('ABSPATH')) {
    exit;
}

class TimedFeatured_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'timed_featured_menu')); // Add page to WooCommerce submenu
        add_action('admin_init', array ($this, 'timed_featured_settings')); // We create settings sections, register settings, and create fields.
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'paint_product_field' ) );// Add product field in general tab
        add_action( 'woocommerce_admin_process_product_object', array( $this, 'save_product_field' ) ); // Save product field in general tab
        add_action( 'woocommerce_before_product_object_save', array( $this, 'save_product_star_toggle' ), 10, 2 ); // Save product field in product list administration
        add_filter( 'manage_edit-product_columns', array( $this, 'paint_days_column' ) ); // Add featured days column
        add_action( 'manage_product_posts_custom_column', array( $this, 'render_days_column_content' ), 10, 2 ); // Render value in featured days column
        add_action( 'admin_notices', array( $this, 'display_featured_notice' ) ); // Notifications in backend
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
                    settings_errors(); // Errors. We do not set "timedfeatured_settings_error" so that all errors are displayed, not just those we configure.
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

    public function paint_product_field() {
        global $post;
        $value = get_post_meta( $post->ID, '_featured_days', true );
        $args = array(
            'id'                => '_featured_days', // Meta key
            'label'             => __( 'Featured days', 'timed-featured-products-for-woocommerce' ),
            'placeholder'       => '',
            'desc_tip'          => true,
            'description'       => __( 'Priority rules:<br>1. <b>Empty - </b> The product is no longer featured.<br>2. <b>Zero - </b> Override global settings: never expires (infinite).<br>3. <b>Number - </b>Override global settings: specific days for this product.', 'timed-featured-products-for-woocommerce' ),
            'type'              => 'number',
            'value'             => $value, 
            'custom_attributes' => array(
                'step' => '1',
                'min'  => '0'
            )
        );
        echo '<div class="options_group">';
        woocommerce_wp_text_input( $args ); // Use woocommerce_wp_text_input to maintain native styles.
        echo '</div>';
    }

    public function paint_days_column( $columns ) {
        $new_columns = array();

        foreach ( $columns as $key => $column_label ) {
            $new_columns[ $key ] = $column_label;
            if ( 'featured' === $key ) {
                $new_columns['timedfeatured_featured_days'] = esc_html__( 'Featured Days', 'timed-featured-products-for-woocommerce' );
            }
        }

        return $new_columns;
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

    // Save product field in general tab
    public function save_product_field( $product ) {

        // Values post form
        $days_post  = isset( $_POST['_featured_days'] ) ? $_POST['_featured_days'] : ''; 
        $is_checked = isset( $_POST['_featured'] );

        // Values in DDBB
        $current_meta_days = $product->get_meta( '_featured_days' );
        $had_days_assigned = ( '' !== $current_meta_days );
        
        // Variables for notifications
        $notice_days = 0;
        $is_default  = false;
        $set_notice  = false; // True = featured notification | False = unfeatured notification

        // 1 - If days field IS NOT empty
        if ( '' !== $days_post ) {
            $new_days = absint( $days_post );

            if ( ! $is_checked && $had_days_assigned && $new_days === absint( $current_meta_days ) ) {
                $product->delete_meta_data( '_featured_days' );
                $product->set_featured( false );
                $set_notice  = false;
            } else {
                $product->update_meta_data( '_featured_days', $new_days );
                $product->set_featured( true );
                $notice_days = $new_days;
                $set_notice  = true;
            }
        } 
        // 2 - If days field IS empty
        else {
            if ( $is_checked && ! $had_days_assigned ) {
                $global_default = get_option( 'timedfeatured_time', 0 );
                $product->update_meta_data( '_featured_days', absint( $global_default ) );
                $product->set_featured( true );
                $notice_days = absint( $global_default );
                $is_default  = true;
                $set_notice  = true;

            } else {
                $product->delete_meta_data( '_featured_days' );
                $product->set_featured( false );
                $set_notice  = false;
            }
        }

        // Transient for notifications
        if ( $set_notice ) {
            $this->set_featured_transient( $product->get_name(), $notice_days, $is_default );
        } else {
            $this->set_unfeatured_transient( $product->get_name() );
        }

    }

    // Save product field in product list administration
    public function save_product_star_toggle( $product, $data_store ) {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] !== 'woocommerce_feature_product' ) {
            return;
        }

        if ( $product->get_featured() ) {
            $global_default = get_option( 'timedfeatured_time', 0 );
            $days = absint( $global_default );
            $product->update_meta_data( '_featured_days', $days );
        
            $this->set_featured_transient( $product->get_name(), $days, true ); // Transient for ajax notifications
        } else {
            $product->delete_meta_data( '_featured_days' );
            $this->set_unfeatured_transient( $product->get_name() );
        }
    }

    // Featured notifications
    private function set_featured_transient( $product_name, $days, $is_default ) {
        $days_text = ( 0 === $days ) ? __( 'indefinitely', 'timed-featured-products-for-woocommerce' ) : $days . ' ' . _n( 'day', 'days', $days, 'timed-featured-products-for-woocommerce' );

        if ( $is_default && 0 !== $days ) {
            $days_text .= ' ' . __( '(by default)', 'timed-featured-products-for-woocommerce' );
        }

        $message = sprintf(
            __( 'The product <strong>%1$s</strong> has been featured %2$s.', 'timed-featured-products-for-woocommerce' ),
            esc_html( $product_name ),
            $days_text
        );

        set_transient( 'timedfeatured_notice_' . get_current_user_id(), $message, 45 );
    }

    // Unfeatured notifications
    private function set_unfeatured_transient( $product_name ) {
        $message = sprintf(
            __( 'The product <strong>%1$s</strong> is no longer featured.', 'timed-featured-products-for-woocommerce' ),
            esc_html( $product_name )
        );

        set_transient( 'timedfeatured_notice_' . get_current_user_id(), $message, 45 );
    }

    // Display featured notifications
    public function display_featured_notice() {
        $transient_name = 'timedfeatured_notice_' . get_current_user_id();
        $message = get_transient( $transient_name );

        if ( $message ) {
            ?>
            <div class="notice notice-info is-dismissible">
                <p><?php echo wp_kses_post( $message ); ?></p>
            </div>
            <?php
            delete_transient( $transient_name );
        }
    }

    public function render_days_column_content( $column, $post_id ) {
        if ( 'timedfeatured_featured_days' === $column ) {
            $days = get_post_meta( $post_id, '_featured_days', true );

            if ( '' === $days ) {
                echo '<span class="na">&ndash;</span>';
            } else {
                echo esc_html( $days );
            }
        }
    }
}