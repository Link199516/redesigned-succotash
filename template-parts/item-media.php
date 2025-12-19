<div id="item-<?php the_ID(); ?>" <?php post_class( 'item item-media' ); ?>>
	<?php napoleon_the_item_thumbnail( 'napoleon_item' ); ?>

	<div class="item-content">
		<p class="item-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</p>

		<div class="item-excerpt">
			<?php the_excerpt(); ?>
		</div>

		<a href="<?php the_permalink(); ?>" class="btn item-read-more"><?php esc_html_e( 'Read More', 'napoleon' ); ?></a>
	</div>
</div>
