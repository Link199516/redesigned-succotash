<?php
add_action( 'admin_init', 'napoleon_cpt_page_add_metaboxes' );
add_action( 'save_post', 'napoleon_cpt_page_update_meta' );

if ( ! function_exists( 'napoleon_cpt_page_add_metaboxes' ) ) :
	function napoleon_cpt_page_add_metaboxes() {


		add_meta_box( 'napoleon-hero', esc_html__( 'Hero section', 'napoleon' ), 'napoleon_add_page_hero_meta_box', 'page', 'normal', 'high' );
		add_meta_box( 'napoleon-sidebar', esc_html__( 'Sidebar', 'napoleon' ), 'napoleon_add_page_sidebar_meta_box', 'page', 'side', 'low' );
		add_meta_box( 'napoleon-tpl-front-page', esc_html__( 'Front Page Options', 'napoleon' ), 'napoleon_add_page_front_page_meta_box', 'page', 'normal', 'high' );
}
endif;

if ( ! function_exists( 'napoleon_cpt_page_update_meta' ) ) :
	function napoleon_cpt_page_update_meta( $post_id ) {

		if ( ! napoleon_can_save_meta( 'page' ) ) {
			return;
		}

		napoleon_sanitize_metabox_tab_sub_title( $post_id );

		napoleon_sanitize_metabox_tab_hero( $post_id );

		napoleon_sanitize_metabox_tab_sidebar( $post_id );

		update_post_meta( $post_id, 'hero_button_text', sanitize_text_field( $_POST['hero_button_text'] ) );
		update_post_meta( $post_id, 'hero_button_url', esc_url_raw( $_POST['hero_button_url'] ) );

		update_post_meta( $post_id, 'napoleon_front_slider_id', napoleon_sanitize_intval_or_empty( $_POST['napoleon_front_slider_id'] ) );
	}
endif;

if ( ! function_exists( 'napoleon_add_page_hero_meta_box' ) ) :
	function napoleon_add_page_hero_meta_box( $object, $box ) {
		napoleon_prepare_metabox( 'page' );

		?><div class="at-cf-wrap"><?php

			napoleon_print_metabox_tab_sub_title( $object, $box );

			napoleon_print_metabox_tab_hero( $object, $box );

		?></div><?php
	}
endif;

if ( ! function_exists( 'napoleon_add_page_sidebar_meta_box' ) ) :
	function napoleon_add_page_sidebar_meta_box( $object, $box ) {
		napoleon_prepare_metabox( 'page' );

		?><div class="at-cf-wrap"><?php

			napoleon_print_metabox_tab_sidebar( $object, $box );

		?></div><?php

		// napoleon_bind_metabox_to_page_template( 'napoleon-sidebar', 'default', 'napoleon_sidebar_metabox_tpl' );
	}
endif;

if ( ! function_exists( 'napoleon_add_page_front_page_meta_box' ) ) :
	function napoleon_add_page_front_page_meta_box( $object, $box ) {
		napoleon_prepare_metabox( 'page' );

		?><div class="at-cf-wrap"><?php

			napoleon_metabox_open_tab( esc_html__( 'Hero Button', 'napoleon' ) );
				napoleon_metabox_guide( esc_html__( 'The following options allow you to show a custom button below the Hero Title/Subtitle options. Please note that both a text and a URL are needed for the button to work.', 'napoleon' ) );
				napoleon_metabox_input( 'hero_button_text', esc_html__( 'Button text:', 'napoleon' ) );
				napoleon_metabox_input( 'hero_button_url', esc_html__( 'Button URL:', 'napoleon' ) );
			napoleon_metabox_close_tab();

			napoleon_metabox_open_tab( esc_html__( 'Slider', 'napoleon' ) );
				napoleon_metabox_guide( esc_html__( 'You can select a MaxSlider slideshow to display on your front page. If you choose a slideshow, it will be displayed instead of the image that you have set on "Hero section".', 'napoleon' ) );
				?>
				<p class="at-field-group at-field-dropdown">
					<label for="background_slider_id"><?php esc_html_e( 'MaxSlider Slideshow', 'napoleon' ); ?></label>
					<?php
						$post_type = 'maxslider_slide';
						if ( function_exists( 'MaxSlider' ) ) {
							$post_type = MaxSlider()->post_type;
						}
						napoleon_dropdown_posts( array(
							'post_type'            => $post_type,
							'selected'             => get_post_meta( $object->ID, 'napoleon_front_slider_id', true ),
							'class'                => 'posts_dropdown',
							'show_option_none'     => esc_html__( 'Disable Slideshow', 'napoleon' ),
							'select_even_if_empty' => true,
						), 'napoleon_front_slider_id' );
					?>
				</p>
				<?php

			napoleon_metabox_close_tab();

		?></div><?php

		//napoleon_bind_metabox_to_page_template( 'napoleon-tpl-front-page', 'templates/front-page.php', 'napoleon_front_page_metabox_tpl' );
	
}
endif;
