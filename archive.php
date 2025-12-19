<?php get_header(); ?>

<?php get_template_part( 'template-parts/hero' ); ?>

<main class="main">
	<div class="container">

		<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) : ?>

			<div class="row <?php napoleon_the_row_classes(); ?>">

				<?php get_template_part( 'template-parts/breadcrumbs' ); ?>

				<div class="<?php napoleon_the_container_classes(); ?>">
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
