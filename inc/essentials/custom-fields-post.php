<?php
add_action( 'admin_init', 'napoleon_cpt_post_add_metaboxes' );
add_action( 'save_post', 'napoleon_cpt_post_update_meta' );

if ( ! function_exists( 'napoleon_cpt_post_add_metaboxes' ) ) :
	function napoleon_cpt_post_add_metaboxes() {
		add_meta_box( 'napoleon-hero', esc_html__( 'Hero section', 'napoleon' ), 'napoleon_add_post_hero_meta_box', 'post', 'normal', 'high' );
		add_meta_box( 'napoleon-sidebar', esc_html__( 'Sidebar', 'napoleon' ), 'napoleon_add_post_sidebar_meta_box', 'post', 'side', 'low' );
	}
endif;

if ( ! function_exists( 'napoleon_cpt_post_update_meta' ) ) :
	function napoleon_cpt_post_update_meta( $post_id ) {

		if ( ! napoleon_can_save_meta( 'post' ) ) {
			return;
		}

		napoleon_sanitize_metabox_tab_sub_title( $post_id );

		napoleon_sanitize_metabox_tab_hero( $post_id );

		napoleon_sanitize_metabox_tab_sidebar( $post_id );
	}
endif;

if ( ! function_exists( 'napoleon_add_post_hero_meta_box' ) ) :
	function napoleon_add_post_hero_meta_box( $object, $box ) {
		napoleon_prepare_metabox( 'post' );

		?><div class="at-cf-wrap"><?php

			napoleon_print_metabox_tab_sub_title( $object, $box );

			napoleon_print_metabox_tab_hero( $object, $box );

		?></div><?php
	}
endif;

if ( ! function_exists( 'napoleon_add_post_sidebar_meta_box' ) ) :
	function napoleon_add_post_sidebar_meta_box( $object, $box ) {
		napoleon_prepare_metabox( 'post' );

		?><div class="at-cf-wrap"><?php

			napoleon_print_metabox_tab_sidebar( $object, $box );

		?></div><?php
	}
endif;
