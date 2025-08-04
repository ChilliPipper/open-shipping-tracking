<?php
/**
 * Plugin Name: Open Shipping Tracking
 * Description: Adds shipping carrier, tracking code, and tracking URL to WooCommerce orders and sends tracking information via email when order is completed.
 * Version: 1.2.0
 * Author: @ChilliPipper - Marco Revilla
 * License: GPL2
 * Text Domain: open-shipping-tracking
 * Domain Path: /languages
 * WC requires at least: 6.0
 * WC tested up to: 9.5
 * Requires PHP: 7.4
 * Features: high-performance-order-storage
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Load plugin text domain for translations
add_action( 'init', 'ost_load_textdomain' );

function ost_load_textdomain() {
    load_plugin_textdomain( 'open-shipping-tracking', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

// Hook to add tracking fields to the order edit page
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'ost_add_tracking_fields_to_order' );

function ost_add_tracking_fields_to_order( $order ) {
    if ( ! $order instanceof WC_Order ) {
        return;
    }
    // Get existing meta data
    $shipping_carrier = $order->get_meta( '_ost_shipping_carrier', true );
    $tracking_code    = $order->get_meta( '_ost_tracking_code', true );
    $tracking_url     = $order->get_meta( '_ost_tracking_url', true );
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
        <?php if ( $shipping_carrier && $tracking_code && $tracking_url ) : ?>
            <p class="form-field form-field-wide">
                <button type="submit" class="button" name="ost_send_tracking_now" value="1"><?php esc_html_e( 'Resend Shipping Tracking Email', 'open-shipping-tracking' ); ?></button>
            </p>
        <?php endif; ?>
    </div>
    <?php
}

// Hook to save the tracking fields when the order is updated
add_action( 'woocommerce_process_shop_order_meta', 'ost_save_tracking_fields' );

function ost_save_tracking_fields( $order_id ) {
	// First, verify the nonce and user capabilities. This is for both saving and manual sending.
	if ( ! isset( $_POST['ost_tracking_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ost_tracking_nonce'] ) ), 'ost_save_tracking_fields' ) ) {
		return;
	}
	
	// Additional security check for referrer
	if ( ! check_admin_referer( 'ost_save_tracking_fields', 'ost_tracking_nonce' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_shop_order', $order_id ) ) {
		return;
	}

	// Handle manual email sending if the button was clicked
	if ( isset( $_POST['ost_send_tracking_now'] ) ) {
		// Trigger the email with error handling
		try {
			$emails = WC()->mailer()->get_emails();
			if ( isset( $emails['WC_Email_Shipping_Tracking'] ) ) {
				$emails['WC_Email_Shipping_Tracking']->trigger( $order_id );
				// Set a transient to show success notice on the next page load
				set_transient( 'ost_manual_email_sent_notice_' . get_current_user_id(), 'success', 5 );
			} else {
				// Set error notice if email class not found
				set_transient( 'ost_manual_email_sent_notice_' . get_current_user_id(), 'error', 5 );
			}
		} catch ( Exception $e ) {
			// Set error notice if email sending fails
			set_transient( 'ost_manual_email_sent_notice_' . get_current_user_id(), 'error', 5 );
			error_log( 'Open Shipping Tracking: Failed to send email - ' . $e->getMessage() );
		}
	}

	// Now, handle saving the meta fields. This will run regardless of whether the email was sent.
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

    if ( isset( $_POST['ost_shipping_carrier'] ) ) {
        $order->update_meta_data( '_ost_shipping_carrier', sanitize_text_field( wp_unslash( $_POST['ost_shipping_carrier'] ) ) );
    }
    if ( isset( $_POST['ost_tracking_code'] ) ) {
        $order->update_meta_data( '_ost_tracking_code', sanitize_text_field( wp_unslash( $_POST['ost_tracking_code'] ) ) );
    }
    if ( isset( $_POST['ost_tracking_url'] ) ) {
        $order->update_meta_data( '_ost_tracking_url', esc_url_raw( wp_unslash( $_POST['ost_tracking_url'] ) ) );
    }

	$order->save();
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
			// Additional trigger for direct status changes
			add_action( 'woocommerce_order_status_changed', array( $this, 'status_changed_trigger' ), 10, 3 );

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
                
                $this->shipping_carrier = $this->object->get_meta( '_ost_shipping_carrier', true );
                $this->tracking_code    = $this->object->get_meta( '_ost_tracking_code', true );
                $this->tracking_url     = $this->object->get_meta( '_ost_tracking_url', true );

                // Only send if we have tracking info.
                if ( ! $this->is_enabled() || ! $this->recipient || ! $this->shipping_carrier || ! $this->tracking_code || ! $this->tracking_url ) {
                    return;
                }

                $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
            }
        }

        public function status_changed_trigger( $order_id, $from_status, $to_status ) {
            // Only trigger when changing to completed status and we have tracking info
            if ( 'completed' === $to_status ) {
                $this->trigger( $order_id );
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

// Display tracking information on the "My Account" order view page
add_action( 'woocommerce_order_details_after_order_table', 'ost_display_tracking_info_on_account_page', 20 );

function ost_display_tracking_info_on_account_page( $order ) {
    if ( ! $order instanceof WC_Order ) {
        return;
    }

    $shipping_carrier = $order->get_meta( '_ost_shipping_carrier', true );
    $tracking_code    = $order->get_meta( '_ost_tracking_code', true );
    $tracking_url     = $order->get_meta( '_ost_tracking_url', true );

    if ( $shipping_carrier && $tracking_code && $tracking_url ) {
        ?>
        <section class="woocommerce-customer-details">
            <h2 class="woocommerce-column__title"><?php esc_html_e( 'Shipping Information', 'open-shipping-tracking' ); ?></h2>
            <p><strong><?php esc_html_e( 'Carrier:', 'open-shipping-tracking' ); ?></strong> <?php echo esc_html( $shipping_carrier ); ?></p>
            <p><strong><?php esc_html_e( 'Tracking Code:', 'open-shipping-tracking' ); ?></strong> <?php echo esc_html( $tracking_code ); ?></p>
            <p><strong><?php esc_html_e( 'Track your order:', 'open-shipping-tracking' ); ?></strong> <a href="<?php echo esc_url( $tracking_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Click here to track your shipment', 'open-shipping-tracking' ); ?></a></p>
        </section>
        <?php
    }
}

// Add a custom column to the admin orders list for shipping tracking
add_filter( 'manage_edit-shop_order_columns', 'ost_add_tracking_column_header' );
// HPOS compatibility - add column for new orders screen
add_filter( 'manage_woocommerce_page_wc-orders_columns', 'ost_add_tracking_column_header' );
function ost_add_tracking_column_header( $columns ) {
    $new_columns = array();
    foreach ( $columns as $column_name => $column_info ) {
        $new_columns[ $column_name ] = $column_info;
        // Add our column after the order status column
        if ( 'order_status' === $column_name ) {
            $new_columns['shipping_tracking'] = __( 'Shipping Tracking', 'open-shipping-tracking' );
        }
    }
    return $new_columns;
}

// Populate the custom column with tracking data
add_action( 'manage_shop_order_posts_custom_column', 'ost_add_tracking_column_content', 10, 2 );
// HPOS compatibility - populate column for new orders screen
add_action( 'manage_woocommerce_page_wc-orders_custom_column', 'ost_add_tracking_column_content', 10, 2 );
function ost_add_tracking_column_content( $column, $post_id ) {
    if ( 'shipping_tracking' === $column ) {
		$order = wc_get_order( $post_id );
		if ( ! $order ) {
			echo '&mdash;';
			return;
		}

        $shipping_carrier = $order->get_meta( '_ost_shipping_carrier', true );
        $tracking_code    = $order->get_meta( '_ost_tracking_code', true );
        $tracking_url     = $order->get_meta( '_ost_tracking_url', true );

        if ( ! empty( $tracking_code ) ) {
            $content = '';
            if ( ! empty( $shipping_carrier ) ) {
                $content .= '<strong>' . esc_html( $shipping_carrier ) . '</strong><br/>';
            }
            
            if ( ! empty( $tracking_url ) ) {
                $content .= '<a href="' . esc_url( $tracking_url ) . '" target="_blank" title="' . esc_attr__( 'Track this shipment', 'open-shipping-tracking' ) . '">' . esc_html( $tracking_code ) . '</a>';
            } else {
                $content .= esc_html( $tracking_code );
            }
            // Using echo within this hook is standard practice. The content is escaped above.
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $content;
        } else {
            // Display a dash if no tracking info is present.
            echo '&mdash;';
        }
    }
}

// Display an admin notice after manually sending the tracking email
add_action( 'admin_notices', 'ost_display_manual_email_sent_notice' );
function ost_display_manual_email_sent_notice() {
    $notice_type = get_transient( 'ost_manual_email_sent_notice_' . get_current_user_id() );
    if ( $notice_type ) {
        if ( 'success' === $notice_type ) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e( 'The shipping tracking email has been sent to the customer.', 'open-shipping-tracking' ); ?></p>
            </div>
            <?php
        } elseif ( 'error' === $notice_type ) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php esc_html_e( 'Failed to send the shipping tracking email. Please try again or check the error logs.', 'open-shipping-tracking' ); ?></p>
            </div>
            <?php
        }
        delete_transient( 'ost_manual_email_sent_notice_' . get_current_user_id() );
    }
}
