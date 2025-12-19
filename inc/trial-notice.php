<?php
/**
 * Napoleon Theme License Notice
 *
 * Displays a dismissible admin notice for the theme license status.
 *
 * @package Napoleon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class to handle the Napoleon theme license notice.
 */
class Napoleon_License_Notice {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'display_license_notice' ) );
		add_action( 'wp_ajax_napoleon_dismiss_trial_notice', array( $this, 'dismiss_license_notice' ) ); // Keep AJAX action name for compatibility
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
	}

	/**
	 * Displays the license notice if active and not dismissed.
	 */
	public function display_license_notice() {
		// Do not show the notice on the onboarding page or the One Click Demo Import page.
		$current_screen = get_current_screen();
		$theme_slug = get_template();
		$license_page_id = 'appearance_page_' . $theme_slug . '-license'; // The ID of the theme license page.

		// Do not show the notice on the onboarding page or the One Click Demo Import page.
		if ( $current_screen && ( $current_screen->id === 'appearance_page_' . $theme_slug . '-onboard' || $current_screen->id === 'appearance_page_one-click-demo-import' ) ) {
			return;
		}

		$user_id = get_current_user_id();
		$is_trial_active = get_user_meta( $user_id, '_napoleon_trial_active', true );
		$full_version_activated = get_user_meta( $user_id, '_napoleon_full_version_activated', true );

		// Get the actual license expiration timestamp and status.
		$license_expiration_timestamp = get_option( $theme_slug . '_license_expiration_timestamp', false );
		$license_status = get_option( $theme_slug . '_license_key_status', false );

		// Determine the effective expiration timestamp for display.
		$effective_expiration_timestamp = 0;
		if ( 'lifetime' === $license_expiration_timestamp ) {
			$effective_expiration_timestamp = PHP_INT_MAX; // A very large number for "lifetime"
		} elseif ( $license_expiration_timestamp ) {
			$effective_expiration_timestamp = (int) $license_expiration_timestamp;
		} else {
			// If no license expiration is found, and it's not a valid license, do not display notice.
			if ( 'valid' !== $license_status ) {
				return;
			}
			// If valid but no timestamp (e.g., new activation before check_license runs), treat as lifetime for display.
			$effective_expiration_timestamp = PHP_INT_MAX;
		}

		$current_time = current_time( 'timestamp' );
		$remaining_seconds = $effective_expiration_timestamp - $current_time;

		// If remaining time is more than 30 days, do not show the notice.
		if ( $remaining_seconds > ( 30 * DAY_IN_SECONDS ) ) {
			return;
		}

		// If license is not valid and not expired, do not show the notice.
		if ( 'valid' !== $license_status && 'expired' !== $license_status ) {
			return;
		}

		$is_less_than_6_hours = ( $remaining_seconds > 0 && $remaining_seconds < ( 6 * HOUR_IN_SECONDS ) );

		// Prepare the notice content based on license status.
		$notice_content_html = '';
		$cta_url = '';
		$cta_text = '';
		$notice_title = __( 'Napoleon Theme Trail Activated ⌛', 'napoleon' ); // Default title for active trial

		if ( 'lifetime' === $license_expiration_timestamp ) {
			// Only display lifetime notice if on the license page.
			if ( $current_screen && $current_screen->id !== $license_page_id ) {
				return; // Do not display notice if not on license page.
			}
			$notice_title = __( 'Napoleon Theme License Status 🗝️', 'napoleon' ); // Updated title for lifetime license
			$notice_content_html = '<p>' . __( 'Your Napoleon theme license is active (Lifetime).', 'napoleon' ) . '</p>';
			$cta_url = 'https://bitherhood.com/dashboard'; // Link to dashboard
			$cta_text = __( 'Manage License ⚙️', 'napoleon' ); // Updated CTA text
		} elseif ( ! $is_trial_active && $remaining_seconds <= 0 && ! $full_version_activated ) {
			// New: Trial ended and license expired message (title remains same as per user request)
			$notice_title = __( 'Napoleon Theme Trial Ended! 🚫', 'napoleon' );
			$notice_content_html .= '<p>⚠️ ' . __( 'Your trial has ended. The Napoleon theme is still active, but advanced features have been disabled.', 'napoleon' ) . '</p>';
			$notice_content_html .= '<p>🎁 ' . __( 'Final offer: Get 20% off if you upgrade.', 'napoleon' ) . '</p>';
			$cta_url = 'http://bitherhood.com/checkout/?edd_action=add_to_cart&download_id=189&edd_options[price_id]=1&discount=20bthd';
			$cta_text = __( 'Unlock Full Version Now 🔥', 'napoleon' );
		} elseif ( $remaining_seconds <= 0 ) {
			$notice_content_html = '<p>' . __( 'Your Napoleon theme license has expired!', 'napoleon' ) . '</p>';
			$cta_url = 'http://bitherhood.com/checkout/?edd_action=add_to_cart&download_id=189&edd_options[price_id]=1&discount=20bthd'; // Link to renewal
			$cta_text = __( 'Renew License', 'napoleon' );
		} elseif ( $is_less_than_6_hours ) {
			// For countdown with offer, PHP outputs full text with placeholder.
			$notice_title = __( 'Napoleon Theme Almost Over 😱', 'napoleon' ); // Updated title for almost over
			$notice_content_html = '<p>⏳ ' . __( 'Your Napoleon theme trial is almost over — less than ', 'napoleon' ) . '<strong id="napoleon-trial-countdown"></strong>' . __( ' left! 🎁 Special offer: 20% off if you upgrade before the trial ends.', 'napoleon' ) . '</p>';
			$cta_url = 'http://bitherhood.com/checkout/?edd_action=add_to_cart&download_id=189&edd_options[price_id]=1&discount=20bthd';
			$cta_text = __( 'Get the Offer Now! 🎁', 'napoleon' );
		} else {
			// For regular countdown, PHP outputs full text with placeholder.
			$notice_content_html = '<p>' . __( "You've activated the Napoleon theme trial. You have ", 'napoleon' ) . '<strong id="napoleon-trial-countdown"></strong>' . __( " remaining to take advantage of all its features.", 'napoleon' ) . '</p>';
			$cta_url = 'https://bitherhood.com/'; // Original URL
			$cta_text = __( 'Get the full version 🔐', 'napoleon' );
		}

		?>
		<div class="notice updated is-dismissible art-notice napoleon-trial-notice">
			<div class="art-notice-inner">
				<div class="art-notice-icon">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/limitd.png' ); ?>" alt="Napoleon Logo" width="60" />
				</div>

				<div class="art-notice-content">
					<h3><?php esc_html_e( $notice_title, 'napoleon' ); ?></h3>
					<?php echo wp_kses( $notice_content_html, array( 'strong' => array( 'id' => array() ), 'br' => array(), 'p' => array() ) ); ?>
				</div>

				<div class="art-install-now">
					<a class="button button-primary art-install-button" href="<?php echo esc_url( $cta_url ); ?>" target="_blank">
						<?php echo esc_html( $cta_text ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handles dismissal of the license notice via AJAX.
	 * As per user request, the notice should always reappear, so we don't store a dismissal flag.
	 * We still send success to allow the client-side JS to proceed with hiding the notice for the current view.
	 */
	public function dismiss_license_notice() {
		check_ajax_referer( 'napoleon-dismiss-trial-notice', 'nonce' );

		if ( current_user_can( 'manage_options' ) && ! empty( $_POST['dismissed'] ) && 'true' === $_POST['dismissed'] ) {
			// We no longer update user meta for dismissal, as the user wants it to reappear.
			wp_send_json_success( 'OK' );
		}

		wp_send_json_error( 'BAD' );
	}

	/**
	 * Enqueues scripts and styles for the license notice.
	 */
	public function enqueue_scripts_styles() {
		$theme_slug = get_template();

		// Get the actual license expiration timestamp and status.
		$license_expiration_timestamp = get_option( $theme_slug . '_license_expiration_timestamp', false );
		$license_status = get_option( $theme_slug . '_license_key_status', false );

		// Determine the effective expiration timestamp for JS.
		$effective_expiration_timestamp_for_js = 0;
		if ( 'lifetime' === $license_expiration_timestamp ) {
			$effective_expiration_timestamp_for_js = PHP_INT_MAX;
		} elseif ( $license_expiration_timestamp ) {
			$effective_expiration_timestamp_for_js = (int) $license_expiration_timestamp;
		} else {
			// If no license expiration is found, and it's not a valid license, do not enqueue.
			if ( 'valid' !== $license_status ) {
				return;
			}
			// If valid but no timestamp, treat as lifetime for JS purposes.
			$effective_expiration_timestamp_for_js = PHP_INT_MAX;
		}

		// Only enqueue if license is 'valid', or if it's 'expired' and not a lifetime license.
		if ( 'valid' !== $license_status && ( 'expired' !== $license_status || 'lifetime' === $license_expiration_timestamp ) ) {
			return;
		}

		// Enqueue existing onboarding styles.
		wp_enqueue_style( 'napoleon-onboarding-styles', get_theme_file_uri( '/inc/onboarding/css/onboarding-styles.css' ), array(), wp_get_theme( 'napoleon' )->get( 'Version' ) );

		// Enqueue new countdown script.
		wp_enqueue_script( 'napoleon-trial-countdown', get_theme_file_uri( '/inc/onboarding/js/napoleon-trial-countdown.js' ), array( 'jquery' ), wp_get_theme( 'napoleon' )->get( 'Version' ) );

		// Localize script with necessary data.
		wp_localize_script(
			'napoleon-trial-countdown',
			'napoleonTrial',
			array(
				'expirationTimestamp' => $effective_expiration_timestamp_for_js,
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'dismissNonce'        => wp_create_nonce( 'napoleon-dismiss-trial-notice' ),
				'expiredText'         => esc_html__( 'Your license has expired!', 'napoleon' ), // This will be overridden by PHP for the new state
				'thresholdSeconds'    => 6 * HOUR_IN_SECONDS, // 6 hours in seconds
				'normalPrefix'        => '', // PHP now handles the full prefix
				'normalSuffix'        => '', // PHP now handles the full suffix
				'offerPrefix'         => '', // PHP now handles the full prefix
				'offerSuffix'         => '', // PHP now handles the full suffix
				'normalCtaUrl'        => 'https://bitherhood.com/',
				'normalCtaText'       => __( 'Get the full version 🔐', 'napoleon' ),
				'offerCtaUrl'         => 'http://bitherhood.com/checkout/?edd_action=add_to_cart&download_id=189&edd_options[price_id]=1&discount=20bthd',
				'offerCtaText'        => __( 'Get the Offer Now! 🎁', 'napoleon' ), // This is the CTA for the <6 hours offer
			)
		);
	}
}

new Napoleon_License_Notice();
