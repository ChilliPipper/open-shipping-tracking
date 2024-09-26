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

// Hook to send email when order status changes to completed
add_action( 'woocommerce_order_status_completed', 'ost_send_tracking_email', 10, 1 );

function ost_send_tracking_email( $order_id ) {
    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );

    // Get tracking information
    $shipping_carrier = get_post_meta( $order_id, '_ost_shipping_carrier', true );
    $tracking_code    = get_post_meta( $order_id, '_ost_tracking_code', true );
    $tracking_url     = get_post_meta( $order_id, '_ost_tracking_url', true );

    // Proceed only if tracking information is available
    if ( $shipping_carrier && $tracking_code && $tracking_url ) {

        // Get customer email
        $to = $order->get_billing_email();

        // Email subject
        $subject = __( 'Your Order Has Been Shipped', 'open-shipping-tracking' );

        // Define your desired sender name and email
        $sender_name  = get_bloginfo( 'name' ); // Or replace with 'Your Site Name'
        $sender_email = 'noreply@your-domain.com'; // Replace with your desired sender email
        
        // Email headers with custom From name and email
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $sender_name . ' <' . $sender_email . '>'
        );

        // Email content
        $message  = '<p>' . sprintf( __( 'Dear %s,', 'open-shipping-tracking' ), esc_html( $order->get_billing_first_name() ) ) . '</p>';
        $message .= '<p>' . __( 'Thank you for your purchase! Here is the tracking info.', 'open-shipping-tracking' ) . '</p>';
        $message .= '<h3>' . __( 'Shipping Information', 'open-shipping-tracking' ) . '</h3>';
        $message .= '<p><strong>' . __( 'Carrier:', 'open-shipping-tracking' ) . '</strong> ' . esc_html( $shipping_carrier ) . '</p>';
        $message .= '<p><strong>' . __( 'Tracking Code:', 'open-shipping-tracking' ) . '</strong> ' . esc_html( $tracking_code ) . '</p>';
        $message .= '<p><strong>' . __( 'Tracking URL:', 'open-shipping-tracking' ) . '</strong> <a href="' . esc_url( $tracking_url ) . '" target="_blank">' . esc_html( $tracking_url ) . '</a></p>';
        $message .= '<p>' . __( 'You can track your shipment using the link above.', 'open-shipping-tracking' ) . '</p>';
        $message .= '<p>' . __( 'Thank you for shopping with us!', 'open-shipping-tracking' ) . '</p>';

        // Send the email
        wp_mail( $to, $subject, $message, $headers );
    }
}
