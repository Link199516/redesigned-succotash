<?php
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	global $product;
	$old_product = $product;

	$product = wc_get_product( get_the_ID() );
?>
<div class="item item-product">

	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10 // Removed by the theme.
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );
	?>

	<div class="item-content">

		<?php
		/**
		 * Hook: woocommerce_shop_loop_item_title.
		 *
		 * @hooked napoleon_woocommerce_show_product_loop_categories - 5 // Added by the theme.
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		do_action( 'woocommerce_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_after_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_rating - 5 // Removed by the theme.
		 * @hooked woocommerce_template_loop_price - 10
		 * @hooked woocommerce_template_loop_rating - 15 // Added by the theme.
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );
		?>

		<?php
		/**
		 * Hook: woocommerce_after_shop_loop_item.
		 *
		 * @hooked woocommerce_template_loop_product_link_close - 5 // Removed by the theme.
		 * @hooked woocommerce_template_loop_add_to_cart - 10 // Removed by the theme.
		 */
		do_action( 'woocommerce_after_shop_loop_item' );
		?>
	</div>
</div>
<?php

$product = $old_product;
