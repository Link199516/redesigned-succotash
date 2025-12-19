(function( $ ) {
	'use strict';

	$( document ).ready( function() {
		// Initialize Select2 for our custom multi-select controls
		$( '.customize-control-multi-select-searchable' ).each( function() {
			$( this ).select2({
				// placeholder: "Select states to exclude", // Optional placeholder
				allowClear: true
			});
		});

		// Ensure Select2 updates the Customizer setting when a value changes.
		// The WP_Customize_Control::link() method in PHP handles the initial binding.
		// For dynamically added controls or complex scenarios, you might need more specific event handling here,
		// but for Select2 on a standard select[multiple], WordPress's built-in change detection usually works.
		// However, it's good practice to trigger 'change' on the original select element after Select2 changes.
		$( '.customize-control-multi-select-searchable' ).on( 'change', function() {
			// Trigger the change event on the original select element to notify WordPress Customizer.
			// This is important because Select2 replaces the original select with its own UI.
			$(this).trigger('change');
		});
	});

	// If controls are added dynamically (e.g., via JS templates in more complex Customizer setups),
	// you might need to re-initialize Select2 when new controls are added.
	// wp.customize.control.each(function (control) {
	// 	control.container.on('DOMNodeInserted', function () {
	// 		if (control.params.type === 'napoleon_multi_select_searchable') {
	// 			control.container.find('.customize-control-multi-select-searchable').select2({
	// 				allowClear: true
	// 			});
	// 		}
	// 	});
	// });

})( jQuery );
