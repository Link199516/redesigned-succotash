<div class="entry-social-share">
	<p class="entry-social-share-title"><?php esc_html_e( 'Share', 'napoleon' ); ?></p>

	<ul class="list-social-icons">
		<?php
			$thumb_id = get_post_thumbnail_id();

			$target_safe = '';
			if ( 1 === get_theme_mod( 'theme_social_target', 1 ) ) {
				$target_safe = 'target="_blank"';
			}

			$facebook = add_query_arg( array(
				'u' => get_permalink(),
			), 'https://www.facebook.com/sharer.php' );

			$twitter = add_query_arg( array(
				'url' => get_permalink(),
			), 'https://twitter.com/share' );

			$linkedin = add_query_arg( array(
				'mini'    => 'true',
				'url'     => get_permalink(),
				'title'   => get_the_title(),
				'summary' => get_the_excerpt(),
				'source'  => get_bloginfo( 'name' ),
			), 'https://www.linkedin.com/shareArticle' );

			$pinterest = add_query_arg( array(
				'url'         => get_permalink(),
				'description' => get_the_title(),
				'media'       => wp_get_attachment_image_url( get_post_thumbnail_id(), 'large' ),
			), 'https://pinterest.com/pin/create/bookmarklet/' );

			$email = add_query_arg( array(
				'url'     => get_permalink(),
				'subject' => get_the_title(),
				'body'    => get_permalink(),
			), 'mailto:' );
		?>
		<li><a class="social-icon entry-share entry-share-facebook" href="<?php echo esc_url( $facebook ); ?>" <?php echo $target_safe; ?>><i class="fab fa-facebook"></i> <span class="sr-only"><?php esc_html_e( 'Facebook', 'napoleon' ); ?></span></a></li>
		<li><a class="social-icon entry-share entry-share-twitter" href="<?php echo esc_url( $twitter ); ?>" <?php echo $target_safe; ?>><i class="fab fa-twitter"></i> <span class="sr-only"><?php esc_html_e( 'Twitter', 'napoleon' ); ?></span></a></li>
		<li><a class="social-icon entry-share entry-share-linkedin" href="<?php echo esc_url( $linkedin ); ?>" <?php echo $target_safe; ?>><i class="fab fa-linkedin"></i> <span class="sr-only"><?php esc_html_e( 'LinkedIn', 'napoleon' ); ?></span></a></li>

		<?php if ( ! empty( $thumb_id ) ) : ?>
			<li><a class="social-icon entry-share entry-share-pinterest" href="<?php echo esc_url( $pinterest ); ?>" <?php echo $target_safe; ?>><i class="fab fa-pinterest-p"></i> <span class="sr-only"><?php esc_html_e( 'Pinterest', 'napoleon' ); ?></span></a></li>
		<?php endif; ?>

		<li><a class="social-icon entry-share entry-share-email" href="<?php echo esc_url( $email ); ?>" <?php echo $target_safe; ?>><i class="fas fa-envelope"></i> <span class="sr-only"><?php esc_html_e( 'Email', 'napoleon' ); ?></span></a></li>
	</ul>
</div>
