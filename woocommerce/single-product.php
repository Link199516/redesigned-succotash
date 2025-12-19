<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' ); ?>

<main class="main">

	<div class="container">

		<div class="row <?php napoleon_the_row_classes(); ?>">

			<?php
				/**
				 * woocommerce_before_main_content hook.
				 *
				 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
				 * @hooked woocommerce_breadcrumb - 20
				 */
				do_action( 'woocommerce_before_main_content' );
			?>

			<div class="<?php napoleon_the_container_classes(); ?>">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php wc_get_template_part( 'content', 'single-product' ); ?>

				<?php endwhile; // end of the loop. ?>

			</div>

			<?php
				/**
				 * woocommerce_sidebar hook.
				 *
				 * @hooked woocommerce_get_sidebar - 10
				 */
				do_action( 'woocommerce_sidebar' );
			?>

		</div>

		<?php
			/**
			 * woocommerce_after_main_content hook.
			 *
			 * @hooked woocommerce_output_product_data_tabs - 4 // Conditionally added by the theme.
			 * @hooked woocommerce_upsell_display - 6 // Added by the theme.
			 * @hooked woocommerce_output_related_products - 8 // Added by the theme.
			 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
			 */
			do_action( 'woocommerce_after_main_content' ); 
		?>

	</div>



		
	<?php if ( 1 === get_theme_mod( 'show_sticky_atc', 1 ) ) { ?>
		<div class="sticky-atc-btn">
			<?php if ( 1 ===  get_theme_mod( 'display_order_form', 1 ) ) : ?>
				<a href="#codplugin-checkout"><?php echo get_theme_mod( 'sticky_atc_text', __('Buy it now','napoleon') ); ?></a>
			<?php else:  ?>
				<a href="#product-buy-form"><?php echo get_theme_mod( 'sticky_atc_text', __('Buy it now','napoleon') ); ?></a>
			<?php endif;  ?>
		</div>
	<?php } ?>
</main>

<?php get_footer( 'shop' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
