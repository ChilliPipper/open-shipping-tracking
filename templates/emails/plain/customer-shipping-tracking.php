<?php
/**
 * Customer shipping tracking email (plain text)
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo sprintf( esc_html__( 'Hi %s,', 'open-shipping-tracking' ), esc_html( $order->get_billing_first_name() ) ) . "\n\n";
echo esc_html__( 'Your order has been shipped. You can find the tracking details below.', 'open-shipping-tracking' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html__( 'Shipping Information', 'open-shipping-tracking' ) . "\n";
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html__( 'Carrier:', 'open-shipping-tracking' ) . ' ' . esc_html( $shipping_carrier ) . "\n";
echo esc_html__( 'Tracking Code:', 'open-shipping-tracking' ) . ' ' . esc_html( $tracking_code ) . "\n";
echo esc_html__( 'Track your order:', 'open-shipping-tracking' ) . ' ' . esc_url( $tracking_url ) . "\n\n";

echo "\n----------------------------------------\n\n";

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n----------------------------------------\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ); 