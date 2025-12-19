/**
 * Customizer Controls enhancements for a better user experience.
 */

// https://make.xwp.co/2016/07/24/dependently-contextual-customizer-controls/
// https://gist.github.com/westonruter/2c1e87e381ca0c9a3dcb1e3a61a9eb4d
( function ( api ) {
	'use strict';

	// Add callback for when the header_layout setting exists.
	api( 'header_layout_menu_type', function( setting ) {
		var isLayoutNavbar, linkSettingValueToControlActiveState;

		// Determine whether the dependent control should be displayed.
		isLayoutNavbar = function() {
			var navBarLayouts = [
				'navbar_top_left',
				'navbar_top_center',
				'navbar_top_right',
				'navbar_bottom_left',
				'navbar_bottom_center',
				'navbar_bottom_right',
			];
			return navBarLayouts.includes( setting.get() );
		};

		linkSettingValueToControlActiveState = function( control ) {
			var setActiveState = function() {
				control.active.set( isLayoutNavbar() );
			};

			control.active.validate = isLayoutNavbar;

			setActiveState();

			setting.bind( setActiveState );
		};

		api.control( 'header_layout_is_menu_stretched', linkSettingValueToControlActiveState );
		api.control( 'header_layout_menu_height', linkSettingValueToControlActiveState );
		api.control( 'header_colors_menu_container_background', linkSettingValueToControlActiveState );
		api.control( 'header_sticky_colors_menu_container_background', linkSettingValueToControlActiveState );
	} );

	// Add callback for when the header_layout_menu_is_sticky setting exists.
	api( 'header_layout_menu_is_sticky', function( setting ) {
		var isSticky, linkSettingValueToControlActiveState;

		// Determine whether the dependent control should be displayed.
		isSticky = function() {
			return setting.get();
		};

		linkSettingValueToControlActiveState = function( control ) {
			var setActiveState = function() {
				control.active.set( isSticky() );
			};

			control.active.validate = isSticky;

			setActiveState();

			setting.bind( setActiveState );
		};

		api.control( 'header_layout_menu_sticky_alt_logo', linkSettingValueToControlActiveState );
	} );

	// Add callback for when the global_layout_type setting exists.
	api( 'global_layout_type', function( setting ) {
		var isLayoutBoxed, linkSettingValueToControlActiveState;

		// Determine whether the dependent control should be displayed.
		isLayoutBoxed = function() {
			var boxedLayouts = [ 'boxed' ];
			return boxedLayouts.includes( setting.get() );
		};

		linkSettingValueToControlActiveState = function( control ) {
			var setActiveState = function() {
				control.active.set( isLayoutBoxed() );
			};

			control.active.validate = isLayoutBoxed;

			setActiveState();

			setting.bind( setActiveState );
		};

		api.control( 'global_layout_boxed_width', linkSettingValueToControlActiveState );
		api.control( 'global_colors_boxed_background', linkSettingValueToControlActiveState );
		api.control( 'global_colors_boxed_background_image', linkSettingValueToControlActiveState );
	} );

	// Add callback for when the header_layout setting exists.
	api( 'blog_layout_posts_layout_type', function( setting ) {
		var isLayoutMulticolumn, linkSettingValueToControlActiveState;

		// Determine whether the dependent control should be displayed.
		isLayoutMulticolumn = function() {
			var singleLayouts = [ '1col', '1col-horz' ];
			return ! singleLayouts.includes( setting.get() );
		};

		linkSettingValueToControlActiveState = function( control ) {
			var setActiveState = function() {
				control.active.set( isLayoutMulticolumn() );
			};

			control.active.validate = isLayoutMulticolumn;

			setActiveState();

			setting.bind( setActiveState );
		};

		api.control( 'blog_layout_posts_layout_masonry', linkSettingValueToControlActiveState );
	} );

}( wp.customize ) );
