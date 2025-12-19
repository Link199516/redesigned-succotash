	<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) : ?>

		<?php if ( is_active_sidebar( 'prefooter' ) ) : ?>
			<div class="widget-sections-footer">
				<?php dynamic_sidebar( 'prefooter' ); ?>
			</div>
		<?php endif; ?>

		<?php napoleon_footer(); ?>

	<?php endif; ?>

	<?php if ( get_theme_mod( 'theme_contact_number' ) ):  ?> 
		<a href="<?php echo esc_html( get_theme_mod( 'theme_contact_number' ) ); ?>" class="callus-icon"><i class="<?php echo esc_html( get_theme_mod( 'contact_icon', 'fas fa-phone-volume' ) ); ?>"></i></a>
	<?php endif; ?>

</div>
<?php wp_footer(); ?>

</body>
</html>
