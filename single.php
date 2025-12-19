<?php get_header(); ?>

<?php get_template_part( 'template-parts/hero' ); ?>

<main class="main">

	<div class="container">

		<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) : ?>

			<div class="row <?php napoleon_the_row_classes(); ?>">

				<?php get_template_part( 'template-parts/breadcrumbs' ); ?>

				<div class="<?php napoleon_the_container_classes(); ?>">

					<?php while ( have_posts() ) : the_post(); ?>

						<article id="entry-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>

							<?php napoleon_the_post_thumbnail(); ?>

							<div class="article-content-container">
								<div class="row">
									<?php
										$share_cols   = 'col-lg-2 col-12';
										$content_cols = 'col-lg-10 col-12';

										if ( ! get_theme_mod( 'post_show_sharing', 1 ) ) {
											$share_cols   = '';
											$content_cols = 'col-12';
										}
									?>
									<?php if ( get_theme_mod( 'post_show_sharing', 1 ) ) : ?>
										<div class="col-lg-2 col-12">
											<?php get_template_part( 'template-parts/social-sharing' ); ?>
										</div>
									<?php endif; ?>

									<div class="<?php echo esc_attr( $content_cols ); ?>">
										<?php napoleon_the_post_header(); ?>

										<div class="entry-content">
											<?php the_content(); ?>

											<?php wp_link_pages( napoleon_wp_link_pages_default_args() ); ?>
										</div>

										<?php if ( has_tag() && get_theme_mod( 'post_show_tags', 1 ) ) : ?>
											<div class="entry-tags">
												<?php
													/* translators: There is a space at the end. */
													the_tags( esc_html__( 'Tags: ', 'napoleon' ), ', ' );
												?>
											</div>
										<?php endif; ?>

										<?php if ( get_theme_mod( 'post_show_authorbox', 1 ) ) {
											napoleon_the_post_author_box();
										} ?>
									</div>
								</div>
							</div>

						</article>

						<?php if ( get_theme_mod( 'post_show_related', 1 ) ) {
							get_template_part( 'template-parts/related', get_post_type() );
						} ?>

						<div class="row">
							<div class="col-lg-10 col-12 ml-lg-auto">
							<?php if ( get_theme_mod( 'post_show_comments', 1 ) ) {
								comments_template();
							} ?>
							</div>
						</div>

					<?php endwhile; ?>

				</div>

				<?php get_sidebar(); ?>

			</div>

		<?php endif; ?>

	</div>

</main>

<?php get_footer();
