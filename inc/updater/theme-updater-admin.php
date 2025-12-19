<?php
/**
 * Theme updater admin page and functions.
 *
 * @package EDD Sample Theme
 */

class EDD_Theme_Updater_Admin {

	/**
	 * Variables required for the theme updater
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected $remote_api_url = null;
	protected $theme_slug     = null;
	protected $version        = null;
	protected $author         = null;
	protected $download_id    = null;
	protected $renew_url      = null;
	protected $strings        = null;
	protected $item_name      = '';
	protected $beta           = false;
	protected $item_id        = null;

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $config = array(), $strings = array() ) {

		$config = wp_parse_args(
			$config,
			array(
				'remote_api_url' => 'https://bitherhood.com/',
				'theme_slug'     => get_template(),
				'item_name'      => '',
				'license'        => '',
				'version'        => '',
				'author'         => '',
				'download_id'    => '',
				'renew_url'      => '',
				'beta'           => false,
				'item_id'        => '',
			)
		);

		/**
		 * Fires after the theme $config is setup.
		 *
		 * @since x.x.x
		 *
		 * @param array $config Array of EDD SL theme data.
		 */
		do_action( 'post_edd_sl_theme_updater_setup', $config );

		// Set config arguments
		$this->remote_api_url = $config['remote_api_url'];
		$this->item_name      = $config['item_name'];
		$this->theme_slug     = sanitize_key( $config['theme_slug'] );
		$this->version        = $config['version'];
		$this->author         = $config['author'];
		$this->download_id    = $config['download_id'];
		$this->renew_url      = $config['renew_url'];
		$this->beta           = $config['beta'];
		$this->item_id        = $config['item_id'];

		// Populate version fallback
		if ( '' === $config['version'] ) {
			$theme         = wp_get_theme( $this->theme_slug );
			$this->version = $theme->get( 'Version' );
		}

		// Strings passed in from the updater config
		$this->strings = $strings;

		add_action( 'init', array( $this, 'updater' ) );
		add_action( 'admin_init', array( $this, 'register_option' ) );
		add_action( 'admin_init', array( $this, 'license_action' ) );
		add_action( 'admin_menu', array( $this, 'license_menu' ) );
		// Removed: add_action( 'update_option_' . $this->theme_slug . '_license_key', array( $this, 'handle_license_key_update' ), 10, 2 );
		add_filter( 'http_request_args', array( $this, 'disable_wporg_request' ), 5, 2 );

	}

	// Removed: handle_license_key_update method
	/**
	 * Creates the updater class.
	 *
	 * since 1.0.0
	 */
	public function updater() {

		// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
		$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
		if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
			return;
		}

		/* If there is no valid license key status, don't allow updates. */
		if ( 'valid' !== get_option( $this->theme_slug . '_license_key_status', false ) ) {
			return;
		}

		if ( ! class_exists( 'EDD_Theme_Updater' ) ) {
			// Load our custom theme updater
			include dirname( __FILE__ ) . '/theme-updater-class.php';
		}

		new EDD_Theme_Updater(
			array(
				'remote_api_url' => $this->remote_api_url,
				'version'        => $this->version,
				'license'        => trim( get_option( $this->theme_slug . '_license_key' ) ),
				'item_name'      => $this->item_name,
				'author'         => $this->author,
				'beta'           => $this->beta,
				'item_id'        => $this->item_id,
				'theme_slug'     => $this->theme_slug,
			),
			$this->strings
		);
	}

	/**
	 * Adds a menu item for the theme license under the appearance menu.
	 *
	 * since 1.0.0
	 */
	public function license_menu() {

		$strings = $this->strings;

		add_theme_page(
			$strings['theme-license'],
			$strings['theme-license'],
			'manage_options',
			$this->theme_slug . '-license',
			array( $this, 'license_page' )
		);
	}

	/**
	 * Outputs the markup used on the theme license page.
	 *
	 * since 1.0.0
	 */
	public function license_page() {

		$strings = $this->strings;

		$license = trim( get_option( $this->theme_slug . '_license_key' ) );
		$status  = get_option( $this->theme_slug . '_license_key_status', false );

		// Initialize display message
		$display_message = '';
		$license_key_pending_activation = get_option( $this->theme_slug . '_license_key_pending_activation' );

		// 1. Check for activation error message passed in URL first (after an activation attempt)
		if ( isset( $_GET['sl_theme_activation'] ) && 'false' === $_GET['sl_theme_activation'] && isset( $_GET['message'] ) ) {
			$url_message = urldecode( $_GET['message'] );
			if ( "License activation limit reached." === $url_message ) {
				$display_message = "can&#39;t use this license because its reach to max if you want to delate the license you can go to your <a href='https://bitherhood.com/dashboard' target='_blank' class='dashboard-link-button'>dashboard</a>";
			} else {
				$display_message = $url_message; // Display other errors from activation attempt
			}
			// Clear the pending flag if we just attempted activation, regardless of success/failure
			delete_option( $this->theme_slug . '_license_key_pending_activation' );
		}
		// 2. Else, check if a key was just saved and is pending activation
		elseif ( 'true' === $license_key_pending_activation && ! empty( $license ) ) {
			// Sanitize the license key for display, but don't show the full key for security if not needed.
			// For this message, showing a portion or just confirming it's saved is fine.
			// Using esc_html for the license key if you choose to display it.
			// $display_message = sprintf( // Text removed as per request, relying on overlay
			// 	esc_html__( 'Your license key has been saved. Please click %s to complete the activation.', 'napoleon' ),
			// 	'<strong>' . esc_html( $strings['activate-license'] ) . '</strong>' // Emphasize the button text
			// );
			$display_message = ''; // Ensure it's empty so nothing shows before overlay
			// HTML for the overlay and modal
			?>
			<div id="napoleon-activation-overlay" style="display: none;">
				<div class="napoleon-activation-modal">
					<div class="napoleon-loader"></div>
					<div class="napoleon-activation-message">
						<?php esc_html_e( 'The license is saved, the automatic activation is in process!', 'napoleon' ); ?>
					</div>
				</div>
			</div>
			<?php
			// Add JavaScript to show overlay and auto-click the activate button
			$activate_button_name = $this->theme_slug . '_license_activate';
			?>
			<script type="text/javascript">
				document.addEventListener('DOMContentLoaded', function() {
					const overlay = document.getElementById('napoleon-activation-overlay');
					const activateButton = document.querySelector('input[name="<?php echo esc_js( $activate_button_name ); ?>"]');

					if (overlay && activateButton) {
						overlay.style.display = 'flex'; // Show the overlay
						// console.log('Auto-clicking activate button: <?php echo esc_js( $activate_button_name ); ?>'); // For debugging
						activateButton.click();
					} else {
						// console.log('Overlay or activate button not found.'); // For debugging
					}
				});
			</script>
			<?php
			// Do NOT clear the pending flag here, it's cleared when 'Activate License' is clicked (in license_action)
			// or if an activation attempt is made (handled above).
		}
		// 3. Else, it's a normal page load or after successful activation/deactivation: use existing logic
		else {
			if ( ! $license ) {
				$display_message = $strings['enter-key'];
			} else {
				// Force delete transient before trying to get/set it, to avoid stale messages after deactivation
				delete_transient( $this->theme_slug . '_license_message' );
				if ( ! get_transient( $this->theme_slug . '_license_message', false ) ) {
					$checked_message = $this->check_license();
					if ( $checked_message !== $strings['enter-key'] ) {
						set_transient( $this->theme_slug . '_license_message', $checked_message, ( 60 * 60 * 24 ) );
						$display_message = $checked_message;
					} else {
						$display_message = $strings['enter-key'];
					}
				} else {
					$display_message = get_transient( $this->theme_slug . '_license_message' );
				}
			}
		}
		?>
		<div class="wrap napoleon-license-wrap"> <?php // Added class for potential future targeting ?>
			<?php /* <h2><?php echo esc_html( $strings['theme-license'] ); ?></h2> */ // Title removed ?>

			<div class="napoleon-license-container">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/bthd-logo.png' ); ?>" alt="Napoleon Logo" class="napoleon-license-logo">

				<form method="post" action="options.php">
					<?php settings_fields( $this->theme_slug . '-license' ); ?>

					<?php // Only show the input field if the license is NOT valid
					if ( 'valid' !== $status ) : ?>
						<input id="<?php echo esc_attr( $this->theme_slug ); ?>_license_key" name="<?php echo $this->theme_slug; ?>_license_key" type="text" class="regular-text" placeholder="<?php echo esc_attr($strings['enter-key']); ?>" value="<?php echo esc_attr( $license ); ?>" /> <?php // Display saved license value ?>
					<?php endif; ?>

					<p class="description">
						<?php
						$allowed_html = array(
							'a' => array(
								'href'   => array(),
								'target' => array(),
								'class'  => array(), // Allow class for the button
							),
							// Add other simple tags if they might appear in messages
							'br'     => array(),
							'strong' => array(),
							'em'     => array(),
						);
						// Use $display_message which has been processed
						echo wp_kses( $display_message, $allowed_html );
						?>
					</p>


					<?php
					// Always show the nonce field
					wp_nonce_field( $this->theme_slug . '_nonce', $this->theme_slug . '_nonce' );

					if ( $license ) {
						if ( 'valid' === $status ) {
							?>
							<input type="submit" class="button-secondary" name="<?php echo esc_attr( $this->theme_slug ); ?>_license_deactivate" value="<?php echo esc_attr( $strings['deactivate-license'] ); ?>"/>
							<?php
						} else {
							// Show activate button if license exists but is not valid
							?>
							<input type="submit" class="button-secondary" name="<?php echo esc_attr( $this->theme_slug ); ?>_license_activate" value="<?php echo esc_attr( $strings['activate-license'] ); ?>"/>
							<?php
						}
					} else {
						// Show activate button if no license key is entered yet (uses the main submit_button below)
						// We still need a submit button to save the key initially
						submit_button( $strings['activate-license'] ); // Use 'activate' text for the main button
					}
					?>

					<?php // If license exists, the specific activate/deactivate buttons are shown above.
					// If no license exists yet, the submit_button() call above handles the initial submission.
					// We might not need the generic submit_button() if a license exists.
					// Let's hide the generic submit button if a license key already exists.
					if ( ! $license ) {
						// submit_button(); // This was causing a duplicate button when no license was present. Handled above now.
					}
					?>
				</form>
			</div> <?php // end .napoleon-license-container ?>
		</div> <?php // end .wrap ?>
		<?php
	}

	/**
	 * Registers the option used to store the license key in the options table.
	 *
	 * since 1.0.0
	 */
	public function register_option() {
		register_setting(
			$this->theme_slug . '-license',
			$this->theme_slug . '_license_key',
			array( $this, 'sanitize_license' )
		);
	}

	/**
	 * Sanitizes the license key.
	 *
	 * since 1.0.0
	 */
	public function sanitize_license( $new ) {

		$old_license_key = get_option( $this->theme_slug . '_license_key' );
		$new_license_key = trim( $new );

		// If the new key is different from the old key AND the new key is not empty,
		// OR if there was no old key AND a new key is provided and not empty.
		if ( ( $old_license_key !== $new_license_key && ! empty( $new_license_key ) ) || ( empty( $old_license_key ) && ! empty( $new_license_key ) ) ) {
			// New or changed license has been entered
			delete_option( $this->theme_slug . '_license_key_status' ); // Clear current status
			delete_transient( $this->theme_slug . '_license_message' ); // Clear any cached messages
			update_option( $this->theme_slug . '_license_key_pending_activation', 'true' ); // Mark as pending activation
		} elseif ( empty( $new_license_key ) && ! empty( $old_license_key ) ) {
			// License key was cleared
			delete_option( $this->theme_slug . '_license_key_status' );
			delete_transient( $this->theme_slug . '_license_message' );
			delete_option( $this->theme_slug . '_license_key_pending_activation' );
		}
		// If $new_license_key is empty and $old_license_key was also empty, do nothing.
		// If $new_license_key is the same as $old_license_key, do nothing.

		return $new_license_key; // Return the trimmed new key
	}

	/**
	 * Makes a call to the API.
	 *
	 * @since 1.0.0
	 *
	 * @param array $api_params to be used for wp_remote_get.
	 * @return array $response decoded JSON response.
	 */
	public function get_api_response( $api_params ) {

		// Call the custom API.
		$verify_ssl = (bool) apply_filters( 'edd_sl_api_request_verify_ssl', true );
		$response   = wp_remote_post(
			$this->remote_api_url,
			array(
				'timeout'   => 15,
				'sslverify' => $verify_ssl,
				'body'      => $api_params,
			)
		);

		return $response;
	}

	/**
	 * Activates the license key.
	 *
	 * @since 1.0.0
	 */
	public function activate_license( $license_key_to_activate ) {

		$license = trim( $license_key_to_activate );

		if ( empty( $license ) ) {
			// If the key is empty, don't proceed with API call. Redirect with 'enter-key' message.
			$base_url = admin_url( 'themes.php?page=' . $this->theme_slug . '-license' );
			$redirect = add_query_arg(
				array(
					'sl_theme_activation' => 'false', // Or a specific code for empty key
					'message'             => urlencode( $this->strings['enter-key'] ),
				),
				$base_url
			);
			wp_redirect( $redirect );
			exit();
		}

		// Data to send in our API request.
		$api_params = array(
			'edd_action'  => 'activate_license',
			'license'     => $license,
			'item_name'   => urlencode( $this->item_name ),
			'url'         => home_url(),
			'item_id'     => $this->item_id,
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		$response = $this->get_api_response( $api_params );

		// Deletion logic moved to license_action()

		// Now, check the response primarily to display any potential error messages from the API.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = $this->strings['error-generic'];
			}

			$base_url = admin_url( 'themes.php?page=' . $this->theme_slug . '-license' );
			$redirect = add_query_arg(
				array(
					'sl_theme_activation' => 'false',
					'message'             => urlencode( $message ),
				),
				$base_url
			);

			wp_redirect( $redirect );
			exit();

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch ( $license_data->error ) {

					case 'expired':
						$message = sprintf(
							$this->strings['license-expired-on'],
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled':
					case 'revoked':
						$message = $this->strings['license-key-is-disabled'];
						break;

					case 'missing':
						$message = $this->strings['license-key-invalid'];
						break;

					case 'invalid':
						// $message = $this->strings['site-is-inactive']; // Original message for invalid
						// For 'invalid' we might want a more generic "invalid license" message
						// For now, let's keep it separate unless specifically requested to merge with activation limit
						$message = $this->strings['license-key-invalid']; // Using the 'license-key-invalid' string
						break;

					case 'site_inactive': // Fall through to no_activations_left
					case 'no_activations_left':
						// Use a simple, unique message identifier
						$message = "License activation limit reached.";
						break;

					case 'item_name_mismatch':
						$message = sprintf( $this->strings['item-mismatch'], $this->item_name );
						break;

					case 'no_activations_left':
						$message = $this->strings['activation-limit'];
						break;

					default:
						$message = $this->strings['error-generic'];
						break;
				}

				if ( ! empty( $message ) ) {
					$base_url = admin_url( 'themes.php?page=' . $this->theme_slug . '-license' );
					$redirect = add_query_arg(
						array(
							'sl_theme_activation' => 'false',
							'message'             => urlencode( $message ),
						),
						$base_url
					);

					wp_redirect( $redirect );
					exit();
				}
			}
		}

			// $response->license will be either "active" or "inactive"
			if ( $license_data && isset( $license_data->license ) ) {
				update_option( $this->theme_slug . '_license_key_status', $license_data->license );
				delete_transient( $this->theme_slug . '_license_message' );

				// Store license expiration timestamp
				if ( isset( $license_data->expires ) ) {
					if ( 'lifetime' === $license_data->expires ) {
						update_option( $this->theme_slug . '_license_expiration_timestamp', 'lifetime' );
					} else {
						update_option( $this->theme_slug . '_license_expiration_timestamp', strtotime( $license_data->expires ) );
					}
				} else {
					delete_option( $this->theme_slug . '_license_expiration_timestamp' );
				}

				// Handle trial and full version flags
				if ( 'valid' === $license_data->license ) {
					$user_id = get_current_user_id();
					// If a trial was active, assume this is an upgrade to full version
					if ( get_user_meta( $user_id, '_napoleon_trial_active', true ) ) {
						delete_user_meta( $user_id, '_napoleon_trial_active' );
						delete_user_meta( $user_id, '_napoleon_trial_start_time' );
						update_user_meta( $user_id, '_napoleon_full_version_activated', true );
					} else {
						// This is a new activation, treat as trial if not already full version
						if ( ! get_user_meta( $user_id, '_napoleon_full_version_activated', true ) ) {
							update_user_meta( $user_id, '_napoleon_trial_active', true );
							update_user_meta( $user_id, '_napoleon_trial_start_time', current_time( 'timestamp' ) );
						}
					}
				} else {
					// If license is not valid, clear any trial/full version flags
					$user_id = get_current_user_id();
					delete_user_meta( $user_id, '_napoleon_trial_active' );
					delete_user_meta( $user_id, '_napoleon_trial_start_time' );
					delete_user_meta( $user_id, '_napoleon_full_version_activated' );
				}
			}

		// Redirect to onboarding page on successful activation
		wp_redirect( admin_url( 'themes.php?page=napoleon-onboard' ) );
		exit();

	}

	/**
	 * Deactivates the license key.
	 *
	 * @since 1.0.0
	 */
	public function deactivate_license() {

		// Local options are now cleared reliably in license_action() before this function is called.
		// There is no need to make an API call here anymore, as the local state is already handled.
		// We just need to redirect back to the license page.

		wp_redirect( admin_url( 'themes.php?page=' . $this->theme_slug . '-license' ) );
		exit();

	}

	/**
	 * Constructs a renewal link
	 *
	 * @since 1.0.0
	 */
	public function get_renewal_link() {

		// If a renewal link was passed in the config, use that
		if ( '' !== $this->renew_url ) {
			return $this->renew_url;
		}

		// If download_id was passed in the config, a renewal link can be constructed
		$license_key = trim( get_option( $this->theme_slug . '_license_key', false ) );
		if ( '' !== $this->download_id && $license_key ) {
			$url  = esc_url( $this->remote_api_url );
			$url .= '/checkout/?edd_license_key=' . urlencode( $license_key ) . '&download_id=' . urlencode( $this->download_id );
			return $url;
		}

		// Otherwise return the remote_api_url
		return $this->remote_api_url;

	}

	/**
	 * Checks if a license action was submitted.
	 *
	 * @since 1.0.0
	 */
	public function license_action() {

		if ( isset( $_POST[ $this->theme_slug . '_license_activate' ] ) ) {
			if ( check_admin_referer( $this->theme_slug . '_nonce', $this->theme_slug . '_nonce' ) ) {
				$license_key_from_post = '';
				if ( isset( $_POST[ $this->theme_slug . '_license_key' ] ) ) {
					$license_key_from_post = sanitize_text_field( wp_unslash( $_POST[ $this->theme_slug . '_license_key' ] ) );
				}

				// First, save the license key if it's not already saved by the options.php process
				// (though sanitize_license should handle the primary saving via options.php)
				// update_option( $this->theme_slug . '_license_key', $license_key_from_post );

				// Clear the pending activation flag before attempting activation
				delete_option( $this->theme_slug . '_license_key_pending_activation' );

				// Then, attempt to activate it.
				$this->activate_license( $license_key_from_post );
				// activate_license() should handle exit, but as a fallback:
				exit;
			}
		}

		if ( isset( $_POST[ $this->theme_slug . '_license_deactivate' ] ) ) {
			if ( check_admin_referer( $this->theme_slug . '_nonce', $this->theme_slug . '_nonce' ) ) {
				// Force update options to empty/false before deleting
				update_option( $this->theme_slug . '_license_key', '' );
				update_option( $this->theme_slug . '_license_key_status', 'inactive' ); // Set to 'inactive' or false

				// Delete options immediately when deactivation is requested
				delete_option( $this->theme_slug . '_license_key_status' );
				delete_option( $this->theme_slug . '_license_key' );
				delete_transient( $this->theme_slug . '_license_message' );

				// Clear trial and full version flags upon deactivation
				$user_id = get_current_user_id();
				delete_user_meta( $user_id, '_napoleon_trial_active' );
				delete_user_meta( $user_id, '_napoleon_trial_start_time' );
				delete_user_meta( $user_id, '_napoleon_full_version_activated' );

				// Explicitly clear WP object cache for these options
				wp_cache_delete( $this->theme_slug . '_license_key_status', 'options' );
				wp_cache_delete( $this->theme_slug . '_license_key', 'options' );
				// Transients have their own cache group, but delete_transient handles it.

				// Now call the rest of the deactivation logic (API call, redirect)
				$this->deactivate_license(); // Restore the API call
			}
		}
	}

	/**
	 * Checks if license is valid and gets expire date.
	 *
	 * @since 1.0.0
	 *
	 * @return string $message License status message.
	 */
	public function check_license() {

		$license = trim( get_option( $this->theme_slug . '_license_key' ) );
		$status  = get_option( $this->theme_slug . '_license_key_status', false ); // Get status
		$strings = $this->strings;

		// If the status is explicitly 'inactive' (set during deactivation), OR if the license key is empty,
		// return the 'enter key' message immediately without an API call.
		if ( 'inactive' === $status || ! $license ) {
			// Ensure status is also marked inactive if key is empty but status wasn't explicitly set
			if ( ! $license && 'inactive' !== $status ) {
				 update_option( $this->theme_slug . '_license_key_status', 'inactive' );
			}
			return $strings['enter-key'];
		}

		$api_params = array(
			'edd_action'  => 'check_license',
			'license'     => $license,
			'item_name'   => rawurlencode( $this->item_name ),
			'url'         => home_url(),
			'item_id'     => $this->item_id,
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		$response = $this->get_api_response( $api_params );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = $strings['license-status-unknown'];
			}

			$base_url = admin_url( 'themes.php?page=' . $this->theme_slug . '-license' );
			$redirect = add_query_arg(
				array(
					'sl_theme_activation' => 'false',
					'message'             => urlencode( $message ),
				),
				$base_url
			);

			wp_redirect( $redirect );
			exit();

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// If response doesn't include license data, return
			if ( ! isset( $license_data->license ) ) {
				$message = $strings['license-status-unknown'];
				return $message;
			}

			// We need to update the license status at the same time the message is updated
			if ( $license_data && isset( $license_data->license ) ) {
				update_option( $this->theme_slug . '_license_key_status', $license_data->license );

				// Store license expiration timestamp
				if ( isset( $license_data->expires ) ) {
					if ( 'lifetime' === $license_data->expires ) {
						update_option( $this->theme_slug . '_license_expiration_timestamp', 'lifetime' );
					} else {
						update_option( $this->theme_slug . '_license_expiration_timestamp', strtotime( $license_data->expires ) );
					}
				} else {
					delete_option( $this->theme_slug . '_license_expiration_timestamp' );
				}
			}

			// Get expire date
			$expires = false;
			if ( isset( $license_data->expires ) && 'lifetime' != $license_data->expires ) {
				$expires    = date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) );
				$renew_link = '<a href="' . esc_url( $this->get_renewal_link() ) . '" target="_blank">' . $strings['renew'] . '</a>';
			} elseif ( isset( $license_data->expires ) && 'lifetime' == $license_data->expires ) {
				$expires = 'lifetime';
			}

			// Get site counts
			$site_count    = $license_data->site_count;
			$license_limit = $license_data->license_limit;

			// If unlimited
			if ( 0 === $license_limit ) {
				$license_limit = $strings['unlimited'];
			}

			if ( 'valid' === $license_data->license ) {
				$message = $strings['license-key-is-active'] . ' ';
				if ( isset( $expires ) && 'lifetime' != $expires ) {
					$message .= sprintf( $strings['expires%s'], $expires ) . ' ';
				}
				if ( isset( $expires ) && 'lifetime' == $expires ) {
					$message .= $strings['expires-never'];
				}
				if ( $site_count && $license_limit ) {
					$message .= sprintf( $strings['%1$s/%2$-sites'], $site_count, $license_limit );
				}
			} elseif ( 'expired' === $license_data->license ) {
				if ( $expires ) {
					$message = sprintf( $strings['license-key-expired-%s'], $expires );
				} else {
					$message = $strings['license-key-expired'];
				}
				if ( $renew_link ) {
					$message .= ' ' . $renew_link;
				}
			} elseif ( 'invalid' === $license_data->license ) {
				$message = $strings['license-keys-do-not-match'];
			} elseif ( 'inactive' === $license_data->license ) {
				$message = $strings['license-is-inactive'];
			} elseif ( 'disabled' === $license_data->license ) {
				$message = $strings['license-key-is-disabled'];
			} elseif ( 'site_inactive' === $license_data->license ) {
				// Site is inactive
				$message = $strings['site-is-inactive'];
			} else {
				$message = $strings['license-status-unknown'];
			}
		}

		return $message;
	}

	/**
	 * Disable requests to wp.org repository for this theme.
	 *
	 * @since 1.0.0
	 */
	public function disable_wporg_request( $r, $url ) {

		// If it's not a theme update request, bail.
		if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check/1.1/' ) ) {
			return $r;
		}

		// Decode the JSON response
		$themes = json_decode( $r['body']['themes'] );

		// Remove the active parent and child themes from the check
		$parent = get_option( 'template' );
		$child  = get_option( 'stylesheet' );
		unset( $themes->themes->$parent );
		unset( $themes->themes->$child );

		// Encode the updated JSON response
		$r['body']['themes'] = json_encode( $themes );

		return $r;
	}

}

/**
 * This is a means of catching errors from the activation method above and displyaing it to the customer
 */
function edd_sample_theme_admin_notices() {
	// Check if the required GET parameters are set
	if ( ! isset( $_GET['sl_theme_activation'], $_GET['message'] ) || empty( $_GET['message'] ) ) {
		return;
	}

	// Check if we are on the license page
	$current_screen  = get_current_screen();
	$theme_slug      = get_template();
	$license_page_id = 'appearance_page_' . $theme_slug . '-license';

	// If it's an error ('false') AND we are on the license page, simply return and display nothing.
	// This prevents any activation/deactivation error messages (like 'invalid license') from showing
	// on the license page itself after the redirect.
	if ( 'false' === $_GET['sl_theme_activation'] && $current_screen && $current_screen->id === $license_page_id ) {
		return;
	}

	// If it's an error but NOT on the license page, display it.
	if ( 'false' === $_GET['sl_theme_activation'] ) {
		$message = urldecode( $_GET['message'] );
		?>
		<div class="error">
			<p><?php echo esc_html( $message ); ?></p>
		</div>
		<?php
	}
	// Removed incorrect break; and case remnants from previous switch statement
}
add_action( 'admin_notices', 'edd_sample_theme_admin_notices' );

/**
 * Add CSS to admin head for the dashboard link button style.
 */
function napoleon_theme_license_admin_styles() {
	// Only add styles on the theme license page
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_napoleon-license' === $screen->id ) {
		?>
		<style type="text/css">
			.dashboard-link-button {
				display: inline-block; /* Allows padding and background */
				background-color: #007bff; /* Solid modern blue */
				color: white !important; /* Use !important to override potential conflicts */
				padding: 3px 8px;
				text-decoration: none;
				border-radius: 4px;
				margin-left: 5px; /* Add some space before the button */
				margin-bottom: 0 !important; /* Force remove any bottom margin */
				vertical-align: middle; /* Align better with surrounding text */
				border: none; /* Ensure no border */
				transition: background-color 0.3s ease; /* Smooth transition for hover */
			}
			.dashboard-link-button:hover {
				background-color: #0056b3; /* Darker solid blue on hover */
				color: white !important;
				border: none; /* Ensure no border on hover */
			}

			.napoleon-license-container { /* Ensure this container can contain the absolute overlay */
				position: relative;
				/* Add any other existing styles for this container if needed, or ensure they are in a separate CSS file */
			}

			#napoleon-activation-overlay {
				position: absolute; /* Changed from fixed to be contained by .napoleon-license-container */
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background-color: rgba(0, 0, 0, 0.1); /* Subtle semi-transparent black for dimming */
				display: none; /* Hidden by default */
				justify-content: center;
				align-items: center; /* Center modal vertically */
				/* padding-top: 20%; /* Removed to allow true centering */
				z-index: 1000; /* Still high, but relative to its container */
				border-radius: 6px; /* Match container if it has one */
				/* backdrop-filter: blur(3px); /* Optional: blur only the container's content */
				/* -webkit-backdrop-filter: blur(3px); */
			}

			.napoleon-activation-modal {
				background-color: #ffffff;
				padding: 20px 25px; /* Reduced padding for a smaller modal */
				border-radius: 6px;
				box-shadow: 0 4px 20px rgba(0,0,0,0.2);
				text-align: center;
				color: #333;
				margin-right: 20px; /* Centered by flexbox, no need for margin */
				margin-top: -170px; /* Centered by flexbox, no need for margin */
			}

			.napoleon-loader {
				border: 3px solid #f3f3f3; /* Light grey */
				border-top: 3px solid #007bff; /* Blue */
				border-radius: 50%;
				width: 30px; /* Reduced size */
				height: 30px; /* Reduced size */
				animation: napoleon-spin 1s linear infinite;
				margin: 0 auto 10px auto; /* Adjusted margin */
			}

			.napoleon-activation-message {
				font-size: 1em; /* Adjusted font size */
				font-weight: 500;
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; /* Modern system font stack */
				color: #2c3e50; /* Darker, more modern text color */
			}

			@keyframes napoleon-spin {
				0% { transform: rotate(0deg); }
				100% { transform: rotate(360deg); }
			}
		</style>
		<?php
	}
}
add_action( 'admin_head', 'napoleon_theme_license_admin_styles' );
