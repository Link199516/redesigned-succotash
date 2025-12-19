jQuery( document ).ready( function ( $ ) {
	var deactivateAllButtons = function () {
		var $buttons = $('.napoleon-onboarding-list').find('.button');

		$buttons.addClass('disabled');
	};

	var activateAllButtons = function () {
		var $buttons = $('.napoleon-onboarding-list').find('.button');

		$buttons.removeClass('disabled');
	};

	$( 'body' ).on( 'click', '.napoleon-onboarding-wrap .activate-variation', function () {
		var button = $( this );
		var $box   = $( this ).closest( '.napoleon-variation' );
		var slug   = $( this ).data( 'variation-slug' );

		$.ajax( {
			type: 'post',
			url: napoleon_onboarding.ajaxurl,
			data: {
				action: 'napoleon_activate_variation',
				onboarding_nonce: napoleon_onboarding.onboarding_nonce,
				variation: slug,
			},
			dataType: 'text',
			beforeSend: function() {
				button.addClass( 'updating-message' );
				button.text( napoleon_onboarding.activating_text );
				deactivateAllButtons();
			},
			success: function( response ) {
				button.removeClass( 'updating-message' );
				button.text( napoleon_onboarding.activate_variation_text );
				activateAllButtons();
				$( '.napoleon-variation' ).removeClass( 'enabled' );
				$box.addClass( 'enabled' );
			}
		} );

		return false;
	} );

	$( 'body' ).on( 'click', '.napoleon-onboarding-wrap .reset-theme-mods', function () {
		var button = $( this );
		var text   = button.text();

		if ( ! window.confirm( napoleon_onboarding.reset_mods_confirm_text ) ) {
			return false;
		}

		$.ajax( {
			type: 'post',
			url: napoleon_onboarding.ajaxurl,
			data: {
				action: 'napoleon_reset_theme_mods',
				onboarding_nonce: napoleon_onboarding.onboarding_nonce,
			},
			dataType: 'text',
			beforeSend: function() {
				button.addClass( 'updating-message' );
				button.text( napoleon_onboarding.deleting_text );
				deactivateAllButtons();
			},
			success: function( response ) {
				button.removeClass( 'updating-message' );
				button.text( text );
				activateAllButtons();
			}
		} );

		return false;
	} );

	$( 'body' ).on( 'click', '.napoleon-onboarding-wrap .install-now', function () {
		var slug = $( this ).attr( 'data-slug' );

		deactivateAllButtons();

		wp.updates.installPlugin( {
			slug: slug
		}).done(function () {
			activateAllButtons();
		}).fail(function () {
			activateAllButtons();
		});

		return false;
	} );


	$( '.ajax-install-plugin' ).on( 'click', function( e ) {
		var button = $(this);
		var plugin_slug = button.data('plugin-slug');

		$.ajax( {
			type: 'post',
			url: napoleon_onboarding.ajaxurl,
			data: {
				action: 'install_napoleon_plugin',
				onboarding_nonce: napoleon_onboarding.onboarding_nonce,
				plugin_slug: plugin_slug,
			},
			dataType: 'text',
			beforeSend: function() {
				button.addClass('updating-message');
				button.text(napoleon_onboarding.installing_text);
				deactivateAllButtons();
			},
			success: function( response ) {
				button.removeClass('updating-message');
				button.addClass('activate-now button-primary');
				button.text(napoleon_onboarding.activate_text);
				activateAllButtons();
			}
		} );
	} );

	// --- Install/Activate All Required Plugins ---
	$( 'body' ).on( 'click', '#install-required-plugins-button', function( e ) {
		e.preventDefault();

		var $button = $( this );
		var $spinner = $button.next( '.spinner' );
		var processingQueue = []; // Simple queue, no prioritization

		// Disable button and show spinner
		$button.prop( 'disabled', true ).addClass('disabled updating-message'); // Add updating-message class
		$spinner.css( 'display', 'inline-block' );

		// Find all required plugins needing action and add them to the queue in order
		$( '.required-plugins-list .col' ).each( function() {
			var $col = $( this );
			var $installBundledBtn = $col.find( '.ajax-install-plugin' );
			var $installRepoBtn = $col.find( '.install-now:not(.ajax-install-plugin)' );
			var $activateBtn = $col.find( '.activate-now' );
			var slug = $col.find( '[data-slug]' ).first().data( 'slug' );
			var isBundled = $col.find( '[data-is-bundled="true"]' ).length > 0;
			var pluginData = { slug: slug, isBundled: isBundled, $box: $col };

			if ( $installBundledBtn.length ) {
				pluginData.action = 'install_bundled';
			} else if ( $installRepoBtn.length ) {
				pluginData.action = 'install_repo';
			} else if ( $activateBtn.length ) {
				pluginData.action = 'activate';
				pluginData.activateUrl = $activateBtn.attr( 'href' );
			} else {
				return; // Skip if no action needed
			}

			// Add directly to the processing queue
			processingQueue.push( pluginData );
		} );

		// Function to process plugins sequentially
		var processNextPlugin = function() {
			if ( processingQueue.length === 0 ) {
				// All installations done
				$button.prop( 'disabled', false ).removeClass('disabled updating-message');
				$spinner.hide();

				// Reload the page with a flag to indicate auto-activation should proceed
				var currentUrl = window.location.href;
				var separator = currentUrl.indexOf('?') > -1 ? '&' : '?';
				window.location.href = currentUrl + separator + 'auto_activate=true';
				return;
			}

			var currentPlugin = processingQueue.shift();
			var $currentBox = currentPlugin.$box;
			// var $actionButton = $currentBox.find( '.button' ); // Button is hidden, no longer needed for status text
			var $statusIndicator = $currentBox.find( '.plugin-status-indicator' );

			// Update UI for current plugin using the status indicator
			$statusIndicator.removeClass('success error').addClass('processing');
			if ( currentPlugin.action === 'activate' ) {
				$statusIndicator.text( napoleon_onboarding.activating_text );
			} else {
				$statusIndicator.text( napoleon_onboarding.installing_text );
			}


			// --- Perform Action ---
			if ( currentPlugin.action === 'install_bundled' ) {
				$.ajax( {
					type: 'post', url: napoleon_onboarding.ajaxurl,
					data: { action: 'install_napoleon_plugin', onboarding_nonce: napoleon_onboarding.onboarding_nonce, plugin_slug: currentPlugin.slug },
					dataType: 'json', // Expect JSON
					success: function( response ) {
						if ( response.success ) {
							// Installation successful or already installed, now check for activation
							var $activateBtn = $currentBox.find( '.activate-now' ); // Re-find activate button after potential DOM changes
							if ( !$activateBtn.length ) {
								// If install was successful, the activate button might appear dynamically.
								// We need a slight delay or a more robust way to find it.
								// For now, let's assume reload handles activation if needed, or manually activate.
								// A better approach would be to update the button state based on response and then activate.
								// Let's try finding the activation URL from the original scan if available
								var activateUrl = null;
								$currentBox.find('.activate-now').each(function() { // Find any activate button just added
									activateUrl = $(this).attr('href');
								});

								if (activateUrl) {
									// Found activate button, proceed with activation
									$statusIndicator.text(napoleon_onboarding.activating_text).addClass('processing').removeClass('success error'); // Update status indicator
									$.ajax({
										async: true, type: 'GET', url: activateUrl,
										success: function() {
											$statusIndicator.text('Active').addClass('success').removeClass('processing error');
											processNextPlugin();
										},
										error: function() {
											$statusIndicator.text('Activation Failed').addClass('error').removeClass('processing success');
											processNextPlugin();
										} // Continue even if activation fails
									});
								} else {
									// No activation button found after install, assume active or issue
									$statusIndicator.text('Installed (Reload needed)').addClass('success').removeClass('processing error'); // Indicate reload might be needed
									processNextPlugin();
								}

							} else {
								// Activate button already exists (meaning install returned 'already installed')
								$statusIndicator.text( napoleon_onboarding.activating_text ).addClass('processing').removeClass('success error');
								$.ajax( {
									async: true, type: 'GET', url: $activateBtn.attr( 'href' ),
									success: function() {
										$statusIndicator.text('Active').addClass('success').removeClass('processing error');
										processNextPlugin();
									},
									error: function() {
										$statusIndicator.text('Activation Failed').addClass('error').removeClass('processing success');
										processNextPlugin();
									} // Continue on error
								} );
							}
						} else {
							// Installation failed
							$statusIndicator.text( response.data.message || 'Install Failed' ).addClass('error').removeClass('processing success');
							$currentBox.find( '.napoleon-onboarding-box' ).css( 'border-left-color', 'red' );
							processNextPlugin(); // Continue processing next plugin
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						// AJAX call itself failed
						$statusIndicator.text( 'Install Error: ' + (jqXHR.responseJSON && jqXHR.responseJSON.data && jqXHR.responseJSON.data.message ? jqXHR.responseJSON.data.message : textStatus) ).addClass('error').removeClass('processing success');
						$currentBox.find( '.napoleon-onboarding-box' ).css( 'border-left-color', 'red' );
						processNextPlugin(); // Continue processing next plugin
					}
				} );
			} else if ( currentPlugin.action === 'install_repo' ) {
				wp.updates.installPlugin( { slug: currentPlugin.slug } )
					.always( function() { // Use always to proceed regardless of success/fail
						// After install attempt, find the activate button (might appear now)
						var $activateBtn = $currentBox.find( '.activate-now' );
						if ( $activateBtn.length ) {
							$statusIndicator.text( napoleon_onboarding.activating_text ).addClass('processing').removeClass('success error');
							$.ajax( {
								async: true, type: 'GET', url: $activateBtn.attr( 'href' ),
								success: function() {
									$statusIndicator.text('Active').addClass('success').removeClass('processing error');
									processNextPlugin();
								},
								error: function() {
									$statusIndicator.text('Activation Failed').addClass('error').removeClass('processing success');
									processNextPlugin();
								} // Continue on error
							} );
						} else {
							// Install failed or no activation needed
							var installFailed = wp.updates.ajaxStatus && wp.updates.ajaxStatus.errorMessage;
							if ( installFailed ) {
								$statusIndicator.text( wp.updates.ajaxStatus.errorMessage || 'Install Failed' ).addClass('error').removeClass('processing success');
								$currentBox.find( '.napoleon-onboarding-box' ).css( 'border-left-color', 'red' );
							} else {
								// Assume installed but not needing activation (or already active)
								$statusIndicator.text('Installed').addClass('success').removeClass('processing error');
							}
							processNextPlugin(); // Continue processing next plugin
						}
					} );
			} else if ( currentPlugin.action === 'activate' ) {
				$statusIndicator.text( napoleon_onboarding.activating_text ).addClass('processing').removeClass('success error');
				$.ajax( {
					async: true, type: 'GET', url: currentPlugin.activateUrl,
					success: function() {
						$statusIndicator.text('Active').addClass('success').removeClass('processing error');
						processNextPlugin();
					},
					error: function() {
						$statusIndicator.text('Activation Failed').addClass('error').removeClass('processing success');
						processNextPlugin();
					} // Continue on error
				} );
			} else {
				// Should not happen, but just in case
				processNextPlugin();
			}
		};

		// Start processing the queue
		processNextPlugin();
	} );
	// --- End Install/Activate All ---

	// --- Auto-activate and Redirect Logic ---
	console.log('napoleon_onboarding.needs_auto_activate:', napoleon_onboarding.needs_auto_activate);

	// Function to get URL parameter
	function getUrlParameter(name) {
		name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
		var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
		var results = regex.exec(location.search);
		return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
	}

	// Check if auto_activate flag is present in URL
	var autoActivateFlag = getUrlParameter('auto_activate');

	if ( napoleon_onboarding.needs_auto_activate && autoActivateFlag === 'true' ) {
		var countdown = 5; // Start countdown from 5 seconds
		var $countdownMessage = $( '<p class="auto-activate-countdown-message" style="font-size: 1.2em; font-weight: bold; color: #0073aa; margin-top: 15px;"></p>' );
		$( '.napoleon-onboarding-wrap' ).prepend( $countdownMessage );

		var updateCountdown = function() {
			$countdownMessage.text( napoleon_onboarding.countdown_text.replace( '%s', countdown ) );
			if ( countdown <= 0 ) {
				clearInterval( countdownInterval );
				$countdownMessage.text( napoleon_onboarding.activating_all_text );
				// Add a small delay before starting activation to allow user to read the message
				setTimeout(startAutoActivation, 1000); 
			}
			countdown--;
		};

		var countdownInterval = setInterval( updateCountdown, 1000 );
		updateCountdown(); // Call immediately to show initial countdown

		var startAutoActivation = function() {
			var activationQueue = [];
			$( '.required-plugins-list .col.plugin-status-activate' ).each( function() {
				var $col = $( this );
				var $activateBtn = $col.find( '.activate-now' );
				if ( $activateBtn.length ) {
					var slug = $col.find( '[data-slug]' ).first().data( 'slug' );
					var activateUrl = $activateBtn.attr( 'href' );
					activationQueue.push( { slug: slug, activateUrl: activateUrl, $box: $col } );
				}
			} );

			var processNextActivation = function() {
				if ( activationQueue.length === 0 ) {
					// All activations done, redirect to sample content
					window.location.href = napoleon_onboarding.redirect_to_sample_url;
					return;
				}

				var currentPlugin = activationQueue.shift();
				var $currentBox = currentPlugin.$box;
				var $statusIndicator = $currentBox.find( '.plugin-status-indicator' );

				$statusIndicator.removeClass('success error').addClass('processing');
				$statusIndicator.text( napoleon_onboarding.activating_text );

				$.ajax( {
					async: true,
					type: 'GET',
					url: currentPlugin.activateUrl,
					success: function() {
						$statusIndicator.text('Active').addClass('success').removeClass('processing error');
						processNextActivation();
					},
					error: function() {
						$statusIndicator.text('Activation Failed').addClass('error').removeClass('processing success');
						processNextActivation(); // Continue even if one fails
					}
				} );
			};

			processNextActivation(); // Start processing activations
		};
	}
	// --- End Auto-activate and Redirect Logic ---

} ); // End document ready
