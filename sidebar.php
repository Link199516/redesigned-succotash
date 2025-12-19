<?php
	$info = napoleon_get_layout_info();

	if ( ! $info['has_sidebar'] ) {
		return;
	}
?>
<div class="<?php napoleon_the_sidebar_classes(); ?>">
	<div class="sidebar">
		<?php
			if ( ! is_page() ) {
				dynamic_sidebar( 'sidebar-1' );
			} else {
				dynamic_sidebar( 'sidebar-2' );
			}
		?>
	</div>
</div>
