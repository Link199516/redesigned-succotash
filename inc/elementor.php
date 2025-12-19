<?php
namespace Elementor;

/**
 * napoleon Elementor related code.
 */

add_action( 'elementor/theme/register_locations', 'Elementor\napoleon_register_elementor_locations' );
function napoleon_register_elementor_locations( $elementor_theme_manager ) {
	$elementor_theme_manager->register_location( 'header' );
	$elementor_theme_manager->register_location( 'footer' );
	$elementor_theme_manager->register_location( 'single' );
	$elementor_theme_manager->register_location( 'archive' );
}

add_action( 'elementor/init', 'Elementor\napoleon_elementor_init' );
function napoleon_elementor_init() {
	Plugin::instance()->elements_manager->add_category(
		'napoleon-elements',
		[
			'title' => __( 'napoleon Elements', 'napoleon' ),
			'icon'  => 'font',
		],
		1
	);
}

add_action( 'elementor/widgets/widgets_registered', 'Elementor\napoleon_elementor_add_elements' );
function napoleon_elementor_add_elements() {

	require_once get_theme_file_path( '/inc/elementor/post-type.php' );
	Plugin::instance()->widgets_manager->register_widget_type( new Widget_Post_Type() );

	require_once get_theme_file_path( '/inc/elementor/latest-posts.php' );
	Plugin::instance()->widgets_manager->register_widget_type( new Widget_Latest_Posts() );

	require_once get_theme_file_path( '/inc/elementor/latest-products.php' );
	Plugin::instance()->widgets_manager->register_widget_type( new Widget_Latest_Products() );

	require_once get_theme_file_path( '/inc/elementor/post-type-items.php' );
	Plugin::instance()->widgets_manager->register_widget_type( new Widget_Post_Type_Items() );

	require_once get_theme_file_path( '/inc/elementor/woocommerce-categories.php' );
	Plugin::instance()->widgets_manager->register_widget_type( new Widget_WooCommerce_Categories() );

}


add_action( 'elementor/editor/before_enqueue_scripts', 'Elementor\napoleon_elementor_enqueue_scripts' );
function napoleon_elementor_enqueue_scripts() {
	napoleon_register_scripts();
	napoleon_admin_scripts( '' );

	wp_enqueue_media();
	wp_enqueue_style( 'napoleon-widgets' );
	wp_enqueue_script( 'napoleon-widgets' );

	wp_enqueue_script( 'napoleon-elementor-ajax' );
}

add_action( 'wp_ajax_napoleon_elementor_get_posts', 'Elementor\napoleon_ajax_elementor_get_posts' );
function napoleon_ajax_elementor_get_posts() {

	check_ajax_referer( 'napoleon_get_posts_nonce', 'get_posts_nonce' );

	$post_type = isset( $_POST['post_type'] ) ? sanitize_key( $_POST['post_type'] ) : 'post';

	$q = new \WP_Query( array(
		'post_type'      => $post_type,
		'posts_per_page' => - 1,
	) );

	?><option><?php esc_html_e( 'Select an item', 'napoleon' ); ?></option><?php
	while ( $q->have_posts() ) : $q->the_post();
		?><option value="<?php echo esc_attr( get_the_ID() ); ?>"><?php echo wp_kses( get_the_title(), 'strip' ); ?></option><?php
	endwhile;
	wp_reset_postdata();
	wp_die();
}

function napoleon_get_available_post_types() {

	$post_types = get_post_types( array(
		'public' => true,
	), 'objects' );

	unset( $post_types['attachment'] );
	unset( $post_types['elementor_library'] );

	$post_types = apply_filters( 'napoleon_widget_post_types_dropdown', $post_types, __CLASS__ );

	$labels = [];

	foreach ( $post_types as $key => $type ) {
		$labels[ $type->name ] = $type->labels->name;
	}

	return $labels;
}
