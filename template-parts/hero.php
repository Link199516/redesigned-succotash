<?php
	$hero = napoleon_get_hero_data();

	if ( ! $hero['show'] ) {
		return;
	}

	$text_align = $hero['text_align'] ? sprintf( 'page-hero-align-%s', $hero['text_align'] ) : 'page-hero-align-center';

	$button_text = get_post_meta( get_queried_object_id(), 'hero_button_text', true );
	$button_url  = get_post_meta( get_queried_object_id(), 'hero_button_url', true );

	do_action( 'napoleon_before_hero', $hero );

	?>
	<div class="<?php napoleon_the_hero_classes(); ?> <?php echo esc_attr( $text_align ); ?>">

		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="page-hero-content">
						<?php if ( $hero['title'] ) : ?>
							<h2 class="page-hero-title"><?php echo wp_kses( $hero['title'], napoleon_get_allowed_tags() ); ?></h2>
						<?php endif; ?>

						<?php if ( $hero['subtitle'] ) : ?>
							<p class="page-hero-subtitle"><?php echo wp_kses( $hero['subtitle'], napoleon_get_allowed_tags( 'guide' ) ); ?></p>
						<?php endif; ?>

						<?php if ( $button_text && $button_url ) : ?>
							<a href="<?php echo esc_url( $button_url ); ?>" class="btn"><?php echo esc_html( $button_text ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

	</div>
	<?php

	do_action( 'napoleon_after_hero', $hero );
