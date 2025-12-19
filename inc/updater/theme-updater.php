<?php
/**
 * Easy Digital Downloads Theme Updater
 *
 * @package EDD Sample Theme
 */

// Includes the files needed for the theme updater
if ( ! class_exists( 'EDD_Theme_Updater_Admin' ) ) {
	include dirname( __FILE__ ) . '/theme-updater-admin.php';
}


// The theme version to use in the updater
define( 'napoleon_THEME_VERSION', wp_get_theme( 'napoleon' )->get( 'Version' ) );


// Loads the updater classes
$updater = new EDD_Theme_Updater_Admin(
	// Config settings
	array(
		'remote_api_url' => 'https://bitherhood.com/', // Site where EDD is hosted
		'item_name'      => '', // Name of theme
		'theme_slug'     => 'napoleon', // Theme slug
		'version'        => napoleon_THEME_VERSION, // The current version of this theme
		'author'         => 'bitherhood', // The author of this theme
		'download_id'    => '189', // Optional, used for generating a license renewal link
		'renew_url'      => 'https://bitherhood.com/', // Optional, allows for a custom license renewal link
		'beta'           => false, // Optional, set to true to opt into beta versions
		'item_id'        => '189',
	),
	// Strings
	array(
		'theme-license'             => __( 'Theme License', 'napoleon' ),
		'enter-key'                 => __( 'Enter your theme license key.', 'napoleon' ),
		'license-key'               => __( 'License Key', 'napoleon' ),
		'license-action'            => __( 'License Action', 'napoleon' ),
		'deactivate-license'        => __( 'Deactivate License', 'napoleon' ),
		'activate-license'          => __( 'Activate License', 'napoleon' ),
		'status-unknown'            => __( 'License status is unknown.', 'napoleon' ),
		'renew'                     => __( 'Renew?', 'napoleon' ),
		'unlimited'                 => __( 'unlimited', 'napoleon' ),
		'license-key-is-active'     => __( 'License key is active.', 'napoleon' ),
		/* translators: the license expiration date */
		'expires%s'                 => __( 'Expires %s.', 'napoleon' ),
		'expires-never'             => __( 'Lifetime License.', 'napoleon' ),
		/* translators: 1. the number of sites activated 2. the total number of activations allowed. */
		'%1$s/%2$-sites'            => __( 'You have %1$s / %2$s sites activated.', 'napoleon' ),
		'activation-limit'          => sprintf( __( 'This license has reached its maximum activation limit or is already active on another site. Please go to your dashboard to manage your licenses: %s', 'napoleon' ), '<a href="https://bitherhood.com/dashboard/" target="_blank">https://bitherhood.com/dashboard/</a>' ),
		/* translators: the license expiration date */
		'license-key-expired-%s'    => __( 'License key expired %s.', 'napoleon' ),
		'license-key-expired'       => __( 'License key has expired.', 'napoleon' ),
		/* translators: the license expiration date */
		'license-expired-on'        => __( 'Your license key expired on %s.', 'napoleon' ),
		'license-keys-do-not-match' => __( 'License keys do not match.', 'napoleon' ),
		'license-is-inactive'       => __( 'License is inactive.', 'napoleon' ),
		'license-key-is-disabled'   => __( 'License key is disabled.', 'napoleon' ),
		'license-key-invalid'       => __( 'Invalid license.', 'napoleon' ),
		'site-is-inactive'          => __( 'Site is inactive.', 'napoleon' ),
		/* translators: the theme name */
		'item-mismatch'             => __( 'This appears to be an invalid license key for %s.', 'napoleon' ),
		'license-status-unknown'    => __( 'License status is unknown.', 'napoleon' ),
		'update-notice'             => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'napoleon' ),
		'error-generic'             => __( 'An error occurred, please try again.', 'napoleon' ),
	)
);
