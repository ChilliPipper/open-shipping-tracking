<?php
/**
 * Customer shipping tracking email
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Hi %s,', 'open-shipping-tracking' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<p><?php esc_html_e( 'Your order has been shipped. You can find the tracking details below.', 'open-shipping-tracking' ); ?></p>

<h3><?php esc_html_e( 'Shipping Information', 'open-shipping-tracking' ); ?></h3>
<ul>
    <li><strong><?php esc_html_e( 'Carrier:', 'open-shipping-tracking' ); ?></strong> <?php echo esc_html( $shipping_carrier ); ?></li>
    <li><strong><?php esc_html_e( 'Tracking Code:', 'open-shipping-tracking' ); ?></strong> <?php echo esc_html( $tracking_code ); ?></li>
    <li><strong><?php esc_html_e( 'Track your order:', 'open-shipping-tracking' ); ?></strong> <a href="<?php echo esc_url( $tracking_url ); ?>" target="_blank"><?php echo esc_html( $tracking_url ); ?></a></li>
</ul>

<?php
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_footer', $email ); 