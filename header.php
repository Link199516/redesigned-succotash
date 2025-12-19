<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="<?php echo get_theme_mod( 'site_accent_color', '#4C3BCF' ); ?>">

	<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page">

	<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) : ?>

		<?php napoleon_header(); ?>

		<div id="mobilemenu">
			<div class="close-btn clear">
				<div class="bar"></div>
				<div class="bar"></div>
			</div>
			<?php do_action( 'napoleon_head_search' ); ?>
			<?php
			    wp_nav_menu(array(
			        'theme_location' => 'menu-1', 
			        'container'      => '',
			        'menu_class'     => 'mobile-menu-list',
			       // 'walker'         => new Custom_Walker_Nav_Menu()
			    ));
			    ?>
		</div>
		<div class="fly-menu-fade"></div>
		
	<?php endif;
