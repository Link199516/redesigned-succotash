<?php
$info = napoleon_get_layout_info();

if ( ! $info['has_sidebar'] ) {
	return;
}
?>
<div class="<?php napoleon_the_sidebar_classes(); ?>">
	<?php if ( is_singular( 'product' ) ) : ?>
			<div class="sidebar">
			<?php dynamic_sidebar( 'shop' ); ?>
			</div>
		<?php else : ?>
			<div class="sidebar sidebar-drawer with-drawer">
				<div class="sidebar-drawer-header">
					<a href="#" class="sidebar-dismiss">&times; <span
								class="screen-reader-text"><?php esc_html_e( 'Close drawer', 'napoleon' ); ?></span></a>
				</div>

				<div class="sidebar-drawer-content custom-scrollbar">
					<?php dynamic_sidebar( 'shop' ); ?>
				</div>
			</div>
		<?php endif; ?>
</div>
