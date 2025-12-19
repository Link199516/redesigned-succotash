<?php get_header(); ?>

<?php get_template_part( 'template-parts/hero' ); ?>

<main class="main">
	<div class="container">

		<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) : ?>

			<div class="row">

				<?php get_template_part( 'template-parts/breadcrumbs' ); ?>

				<div class="<?php napoleon_the_container_classes(); ?>">

					<?php
						$hero = napoleon_get_hero_data();
					?>
					<?php if ( ! $hero['show'] ) : ?>
						<article class="entry error-404 not-found">
							<header class="entry-header">
								<?php if ( $hero['title'] ) : ?>
									<h2 class="entry-title">
										<?php echo wp_kses( $hero['title'], napoleon_get_allowed_tags() ); ?>
									</h2>
								<?php endif; ?>
							</header>

							<div class="entry-content">
								<?php if ( $hero['subtitle'] ) : ?>
									<p>
										<?php echo wp_kses( $hero['subtitle'], napoleon_get_allowed_tags( 'guide' ) ); ?>
									</p>
								<?php endif; ?>
							</div>
						</article>
					<?php endif; ?>

					<?php
						if ( have_posts() ) :

							while ( have_posts() ) : the_post();

								get_template_part( 'template-parts/item-media', get_post_type() );

							endwhile;

							napoleon_posts_pagination();

						else :

							get_template_part( 'template-parts/article', 'none' );

						endif;
					?>
				</div>

				<?php get_sidebar(); ?>
			</div>

		<?php endif; ?>

	</div>
</main>

<?php get_footer();
