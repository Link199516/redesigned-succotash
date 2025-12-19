var napoleon_repeating_sortable_init = function( selector ) {
	if ( typeof selector === 'undefined' ) {
		jQuery('.at-repeating-fields .inner').sortable({ placeholder: 'ui-state-highlight' });
	} else {
		jQuery('.at-repeating-fields .inner', selector).sortable({ placeholder: 'ui-state-highlight' });
	}
};

var napoleon_repeating_colorpicker_init = function( selector ) {
	if ( selector === undefined ) {
		var ciColorPicker = jQuery( '#widgets-right .napoleon-color-picker, #wp_inactive_widgets .napoleon-color-picker' ).filter( function() {
			return !jQuery( this ).parents( '.field-prototype' ).length;
		} );

		ciColorPicker.wpColorPicker();
	} else {
		jQuery( '.napoleon-color-picker', selector ).wpColorPicker();
	}
};

jQuery(document).ready(function($) {
	"use strict";
	var $body = $( 'body' );

	// Repeating fields
	napoleon_repeating_sortable_init();

	$body.on( 'click', '.at-repeating-add-field', function( e ) {
		var repeatable_area = $( this ).siblings( '.inner' );
		var fields = repeatable_area.children( '.field-prototype' ).clone( true ).removeClass( 'field-prototype' ).removeAttr( 'style' ).appendTo( repeatable_area );
		napoleon_repeating_sortable_init();
		napoleon_repeating_colorpicker_init();
		e.preventDefault();
	} );


	$body.on( 'click', '.at-repeating-remove-field', function( e ) {
		var field = $(this).parents('.post-field');
		field.trigger( 'change' ).remove();
		e.preventDefault();
	});
});
