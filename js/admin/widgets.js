jQuery( document ).ready( function( $ ) {
	"use strict";
	var $body = $( 'body' );


	var napoleon_initialize_widget = function ( widget_el ) {
		napoleon_repeating_sortable_init( widget_el );
		napoleon_colorpicker_init( widget_el );
		napoleon_alpha_colorpicker_init( widget_el );
		napoleon_collapsible_init( widget_el );
	};

	napoleon_initialize_widget();

	function napoleon_on_customizer_widget_form_update( e, widget_el ) {
		napoleon_initialize_widget( widget_el );
	}
	// Widget init doesn't occur for some reason, when added through the customizer. Therefore the event handler below is needed.
	// https://make.wordpress.org/core/2014/04/17/live-widget-previews-widget-management-in-the-customizer-in-wordpress-3-9/
	// 'widget-added' is complemented by 'widget-updated'. However, alpha-color-picker shows multiple alpha channel
	// pickers if called on 'widget-updated'
	// $( document ).on( 'widget-updated', napoleon_on_customizer_widget_form_update );
	$( document ).on( 'widget-added', napoleon_on_customizer_widget_form_update );


	// Widget Actions on Save
	$( document ).ajaxSuccess( function( e, xhr, options ) {
		if ( options.data.search( 'action=save-widget' ) != -1 ) {
			var widget_id;

			if ( ( widget_id = options.data.match( /widget-id=(at-.*?-\d+)\b/ ) ) !== null ) {
				var widget = $( "input[name='widget-id'][value='" + widget_id[1] + "']" ).parent();
				napoleon_initialize_widget( widget );
			}
		}
	} );


	$body.on( 'click', '.at-collapsible legend', function() {
		var arrow = $( this ).find( 'i' );
		if ( arrow.hasClass( 'dashicons-arrow-down' ) ) {
			arrow.removeClass( 'dashicons-arrow-down' ).addClass( 'dashicons-arrow-right' );
			$( this ).siblings( '.elements' ).slideUp();
		} else {
			arrow.removeClass( 'dashicons-arrow-right' ).addClass( 'dashicons-arrow-down' );
			$( this ).siblings( '.elements' ).slideDown();
		}
	} );


	// CI Home Post Type Items widget
	$body.on( 'change', '.at-repeating-fields .posts_dropdown', function() {
		$( this ).parent().data( 'value', $( this ).val() );
	} );

	$body.on( 'change', ':has(input.id_base[value="at-home-post-type-items"]) .napoleon-post-type-select', function() {
		var widget = $( this ).parent().parent();
		var field_post_type = $( this );
		var ajax_posts = field_post_type.parent().data( 'ajaxposts' );

		$.ajax({
			type: 'post',
			url: ThemeWidget.ajaxurl,
			data: {
				action        : ajax_posts,
				post_type_name: field_post_type.val(),
				name_field    : field_post_type.attr( 'name' ),
				nonce         : ThemeWidget.widget_post_type_items_nonce
			},
			dataType: 'text',
			beforeSend: function() {
				widget.find( '.post-field' ).addClass( 'loading' );
				widget.find( '.at-repeating-fields .posts_dropdown' ).prop( 'disabled', 'disabled' ).css( 'opacity', '0.5' );
			},
			success: function( response ) {
				var selects = widget.find( '.at-repeating-fields .posts_dropdown' );
				if ( response != '' ) {
					selects.html( response );
					selects.each( function(){
						$( this ).val( $( this ).parent().data( 'value' ) );
					} );
					selects.removeAttr( 'disabled' ).css( 'opacity', '1' );
				} else {
					selects.html( '' ).prop( 'disabled', 'disabled' ).css( 'opacity', '0.5' );
				}

				widget.find( '.post-field' ).removeClass( 'loading' );
			}
		});

	});

});

var napoleon_collapsible_init = function( selector ) {
	if ( selector === undefined ) {
		jQuery( '.at-collapsible .elements' ).hide();
		jQuery( '.at-collapsible legend i' ).removeClass( 'dashicons-arrow-down' ).addClass( 'dashicons-arrow-right' );
	} else {
		jQuery( '.at-collapsible .elements', selector ).hide();
		jQuery( '.at-collapsible legend i', selector ).removeClass( 'dashicons-arrow-down' ).addClass( 'dashicons-arrow-right' );
	}
};

var napoleon_alpha_colorpicker_init = function( selector ) {
	if ( selector === undefined ) {
		var napoleon_AlphaColorPicker = jQuery( '#widgets-right .napoleon-alpha-color-picker, #wp_inactive_widgets .napoleon-alpha-color-picker' ).filter( function() {
			return !jQuery( this ).parents( '.field-prototype' ).length;
		} );

		napoleon_AlphaColorPicker.alphaColorPicker();
	} else {
		jQuery( '.napoleon-alpha-color-picker', selector ).alphaColorPicker();
	}
};

var napoleon_colorpicker_init = function( selector ) {
	if ( selector === undefined ) {
		var napoleon_ColorPicker = jQuery( '#widgets-right .napoleon-color-picker, #wp_inactive_widgets .napoleon-color-picker' ).filter( function() {
			return !jQuery( this ).parents( '.field-prototype' ).length;
		} );

		// The use of throttle was taken by: https://wordpress.stackexchange.com/questions/5515/update-widget-form-after-drag-and-drop-wp-save-bug/212676#212676
		napoleon_ColorPicker.each( function() {
			jQuery( this ).wpColorPicker( {
				change: _.throttle( function () {
					jQuery( this ).trigger( 'change' );
				}, 1000, { leading: false } )
			} );
		} );
	} else {
		jQuery( '.napoleon-color-picker', selector ).each( function() {
			jQuery( this ).wpColorPicker( {
				change: _.throttle( function () {
					jQuery( this ).trigger( 'change' );
				}, 1000, { leading: false } )
			} );
		} );
	}
};
