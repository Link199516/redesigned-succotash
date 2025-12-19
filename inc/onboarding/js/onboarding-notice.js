jQuery( document ).ready( function ( $ ) {
	$( '.napoleon-onboarding-notice' ).parents( '.is-dismissible' ).on( 'click', 'button', function ( e ) {
		$.ajax( {
			type: 'post',
			url: ajaxurl,
			data: {
				action: 'napoleon_dismiss_onboarding',
				nonce: napoleon_Onboarding.dismiss_nonce,
				dismissed: true
			},
			dataType: 'text',
			success: function ( response ) {
			}
		} );
	} );
} );
