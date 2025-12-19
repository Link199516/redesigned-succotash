<?php
/**
 * Admin new order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-new-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails\HTML
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<div style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 14px; color: #333;">

    <p style="margin-bottom: 20px; font-weight: bold; font-size: 16px;">
        لقد تلقيت طلبًا جديدًا!
    </p>

    <h2 style="font-size: 18px; margin-bottom: 10px;">الطلبية: #<?php echo $order->get_order_number(); ?></h2>

    <h3 style="font-size: 18px; margin-bottom: 10px;">المنتجات:</h3>
    <table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1">
        <thead>
            <tr>
                <th scope="col" style="text-align: left; border: 1px solid #eee; padding: 12px;"><?php esc_html_e( 'المنتج', 'woocommerce' ); ?></th>
                <th scope="col" style="text-align: left; border: 1px solid #eee; padding: 12px;"><?php esc_html_e( 'الكمية', 'woocommerce' ); ?></th>
                <th scope="col" style="text-align: left; border: 1px solid #eee; padding: 12px;"><?php esc_html_e( 'السعر', 'woocommerce' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ( $order->get_items() as $item_id => $item ) :
                $product       = $item->get_product();
                $sku           = $product ? $product->get_sku() : '';
                $purchase_note = $product ? $product->get_purchase_note() : '';
                ?>
                <tr>
                    <td style="text-align: left; vertical-align: middle; border: 1px solid #eee; padding: 12px;">
                        <?php echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ); ?>
                        <?php
                        // Product attributes (color, size, etc.)
                        wc_display_item_meta(
                            $item,
                            array(
                                'before'    => '<br/><small>',
                                'after'     => '</small>',
                                'separator' => '<br/>',
                            )
                        );
                        ?>
                    </td>
                    <td style="text-align: left; vertical-align: middle; border: 1px solid #eee; padding: 12px;">
                        <?php echo esc_html( $item->get_quantity() ); ?>
                    </td>
                    <td style="text-align: left; vertical-align: middle; border: 1px solid #eee; padding: 12px;">
                        <?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p style="margin-top: 20px; text-align: right; font-weight: bold;">
        <?php esc_html_e( 'الإجمالي:', 'woocommerce' ); ?> <?php echo wp_kses_post( $order->get_formatted_order_total() ); ?>
    </p>

    <p style="margin-top: 25px;">
        <strong style="display: block; margin-bottom: 5px; font-size: 16px;">معلومات الزبون:</strong>
        الاسم: <?php echo esc_html( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ); ?><br/>
        الولاية: <?php echo esc_html( $order->get_billing_state() ); ?><br/>
        البلدية: <?php echo esc_html( $order->get_billing_city() ); ?><br/>
        <strong style="font-size: 15px; display: inline-block; margin-top: 5px;">رقم الهاتف: +213<?php echo esc_html( ltrim( $order->get_billing_phone(), '0' ) ); ?></strong>
    </p>

</div>

<?php
/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
?>