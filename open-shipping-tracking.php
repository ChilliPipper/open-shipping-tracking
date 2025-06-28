<?php
/**
 * Plugin Name: Open Shipping Tracking
 * Description: Adds shipping carrier, tracking code, and tracking URL to WooCommerce orders and sends tracking information via email when order is completed.
 * Version: 1.0
 * Author: @ChilliPipper - Marco Revilla
 * License: GPL2
 * Text Domain: open-shipping-tracking
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Load plugin text domain for translations
add_action( 'plugins_loaded', 'ost_load_textdomain' );

function ost_load_textdomain() {
    load_plugin_textdomain( 'open-shipping-tracking', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

// Hook to add tracking fields to the order edit page
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'ost_add_tracking_fields_to_order' );

function ost_add_tracking_fields_to_order( $order ) {
    // Get existing meta data
    $shipping_carrier = get_post_meta( $order->get_id(), '_ost_shipping_carrier', true );
    $tracking_code    = get_post_meta( $order->get_id(), '_ost_tracking_code', true );
    $tracking_url     = get_post_meta( $order->get_id(), '_ost_tracking_url', true );
    ?>
    <div class="shipping_tracking">
        <h3><?php esc_html_e( 'Shipping Tracking Information', 'open-shipping-tracking' ); ?></h3>
		<?php wp_nonce_field( 'ost_save_tracking_fields', 'ost_tracking_nonce' ); ?>
        <p class="form-field form-field-wide">
            <label for="ost_shipping_carrier"><?php esc_html_e( 'Shipping Carrier', 'open-shipping-tracking' ); ?>:</label>
            <input type="text" name="ost_shipping_carrier" id="ost_shipping_carrier" value="<?php echo esc_attr( $shipping_carrier ); ?>" />
        </p>
        <p class="form-field form-field-wide">
            <label for="ost_tracking_code"><?php esc_html_e( 'Tracking Code', 'open-shipping-tracking' ); ?>:</label>
            <input type="text" name="ost_tracking_code" id="ost_tracking_code" value="<?php echo esc_attr( $tracking_code ); ?>" />
        </p>
        <p class="form-field form-field-wide">
            <label for="ost_tracking_url"><?php esc_html_e( 'Tracking URL', 'open-shipping-tracking' ); ?>:</label>
            <input type="url" name="ost_tracking_url" id="ost_tracking_url" value="<?php echo esc_attr( $tracking_url ); ?>" />
        </p>
    </div>
    <?php
}

// Hook to save the tracking fields when the order is updated
add_action( 'woocommerce_process_shop_order_meta', 'ost_save_tracking_fields' );

function ost_save_tracking_fields( $order_id ) {
	if ( ! isset( $_POST['ost_tracking_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ost_tracking_nonce'] ) ), 'ost_save_tracking_fields' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_shop_order', $order_id ) ) {
		return;
	}

    if ( isset( $_POST['ost_shipping_carrier'] ) ) {
        update_post_meta( $order_id, '_ost_shipping_carrier', sanitize_text_field( wp_unslash( $_POST['ost_shipping_carrier'] ) ) );
    }
    if ( isset( $_POST['ost_tracking_code'] ) ) {
        update_post_meta( $order_id, '_ost_tracking_code', sanitize_text_field( wp_unslash( $_POST['ost_tracking_code'] ) ) );
    }
    if ( isset( $_POST['ost_tracking_url'] ) ) {
        update_post_meta( $order_id, '_ost_tracking_url', esc_url_raw( wp_unslash( $_POST['ost_tracking_url'] ) ) );
    }
}

if ( ! class_exists( 'WC_Email_Shipping_Tracking' ) ) {
    class WC_Email_Shipping_Tracking extends WC_Email {

        public function __construct() {
            $this->id             = 'shipping_tracking';
            $this->customer_email = true;
            $this->title          = __( 'Shipping Tracking', 'open-shipping-tracking' );
            $this->description    = __( 'This email is sent to customers when their order is marked as shipped and tracking information is added.', 'open-shipping-tracking' );
            $this->heading        = __( 'Your order has been shipped', 'open-shipping-tracking' );
            $this->subject        = __( 'Your {site_title} order has been shipped', 'open-shipping-tracking' );

            $this->template_html  = 'emails/customer-shipping-tracking.php';
            $this->template_plain = 'emails/plain/customer-shipping-tracking.php';
            $this->template_base  = plugin_dir_path( __FILE__ ) . 'templates/';
            
            // Triggers for this email.
			add_action( 'woocommerce_order_status_completed_notification', array( $this, 'trigger' ), 10, 2 );

            // Call parent constructor
            parent::__construct();
        }

        public function trigger( $order_id, $order = false ) {
            if ( ! $order ) {
				$order = wc_get_order( $order_id );
			}

            if ( $order ) {
				$this->object    = $order;
				$this->recipient = $this->object->get_billing_email();
                
                $this->shipping_carrier = get_post_meta( $order_id, '_ost_shipping_carrier', true );
                $this->tracking_code    = get_post_meta( $order_id, '_ost_tracking_code', true );
                $this->tracking_url     = get_post_meta( $order_id, '_ost_tracking_url', true );

                // Only send if we have tracking info.
                if ( ! $this->is_enabled() || ! $this->recipient || ! $this->shipping_carrier || ! $this->tracking_code || ! $this->tracking_url ) {
                    return;
                }

                $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
            }
        }

        public function get_content_html() {
            return wc_get_template_html(
                $this->template_html,
                array(
                    'order'              => $this->object,
                    'email_heading'      => $this->get_heading(),
                    'shipping_carrier'   => $this->shipping_carrier,
                    'tracking_code'      => $this->tracking_code,
                    'tracking_url'       => $this->tracking_url,
                    'sent_to_admin'      => false,
                    'plain_text'         => false,
                    'email'              => $this,
                ),
                '',
                $this->template_base
            );
        }

        public function get_content_plain() {
            return wc_get_template_html(
                $this->template_plain,
                array(
                    'order'              => $this->object,
                    'email_heading'      => $this->get_heading(),
                    'shipping_carrier'   => $this->shipping_carrier,
                    'tracking_code'      => $this->tracking_code,
                    'tracking_url'       => $this->tracking_url,
                    'sent_to_admin'      => false,
                    'plain_text'         => true,
                    'email'              => $this,
                ),
                '',
                $this->template_base
            );
        }
        
        public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'woocommerce' ),
					'default' => 'yes',
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'    => array(
					'title'       => __( 'Email Heading', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'woocommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
    }
}

function add_shipping_tracking_email( $email_classes ) {
    $email_classes['WC_Email_Shipping_Tracking'] = new WC_Email_Shipping_Tracking();
    return $email_classes;
}
add_filter( 'woocommerce_email_classes', 'add_shipping_tracking_email' );
