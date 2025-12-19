<?php
/**
 * Admin notice
 *
 * Warning when theme is not activated.
 *
 */

function art_admin_notice_activate_theme()
{
	$current_theme = get_template();
	$screen = get_current_screen();
	if (isset($screen->parent_file) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id) {
		return;
	}

	if ('true' === get_user_meta(get_current_user_id(), '_' . $current_theme . '_activate_notice', true)) {
		return;
	}



	if (!napoleon_is_theme_license_valid()) {
		if (!current_user_can('activate_plugins')) {
			return;
		}

		$message = __('Please activate your license to access all theme features.', 'napoleon');

		$button_text = __('Activate License', 'napoleon');
		$button_link = wp_nonce_url('themes.php?page=' . $current_theme . '-license', 'activate-license_' . $current_theme);
		$learn_more = 'https://bitherhood.com/napoleon-guide/';
	}

	?>
	<style>
		.notice.art-notice {
			border-left-color: #00e7bb !important;
			padding: 20px;
		}

		.rtl .notice.art-notice {
			border-right-color: #00e7bb !important;
		}

		.notice.art-notice .art-notice-inner {
			display: table;
			width: 100%;
		}

		.notice.art-notice .art-notice-inner .art-notice-icon,
		.notice.art-notice .art-notice-inner .art-notice-content,
		.notice.art-notice .art-notice-inner .art-install-now {
			display: table-cell;
			vertical-align: middle;
		}

		.notice.art-notice .art-notice-icon {
			color: #00e7bb;
			font-size: 50px;
			width: 50px;
		}

		.notice.art-notice .art-notice-content {
			padding: 0 20px;
		}

		.notice.art-notice p {
			padding: 0;
			margin: 0;
		}

		.notice.art-notice h3 {
			margin: 0 0 5px;
		}

		.notice.art-notice .art-install-now {
			text-align: center;
		}

		.notice.art-notice .art-install-now .art-install-button {
			padding: 5px 30px;
			height: auto;
			line-height: 20px;
			text-transform: capitalize;
		}

		.notice.art-notice .art-install-now .art-install-button i {
			padding-right: 5px;
		}

		.rtl .notice.art-notice .art-install-now .art-install-button i {
			padding-right: 0;
			padding-left: 5px;
		}

		.notice.art-notice .art-install-now .art-install-button:active {
			transform: translateY(1px);
		}

		@media (max-width: 767px) {
			.notice.art-notice {
				padding: 10px;
			}

			.notice.art-notice .art-notice-inner {
				display: block;
			}

			.notice.art-notice .art-notice-inner .art-notice-content {
				display: block;
				padding: 0;
			}

			.notice.art-notice .art-notice-inner .art-notice-icon,
			.notice.art-notice .art-notice-inner .art-install-now {
				display: none;
			}
		}
	</style>
	<script>jQuery(function ($) {
			$('div.notice.art-install-elementor').on('click', 'button.notice-dismiss', function (event) {
				event.preventDefault();

				$.post(ajaxurl, {
					action: 'art_set_admin_notice_viewed'
				});
			});
		});</script>
	<div class="notice updated is-dismissible art-notice art-install-elementor">
		<div class="art-notice-inner">
			<div class="art-notice-icon">
				<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/bthd-logo.png'); ?>"
					alt="Napoleon Logo" width="60" />
			</div>

			<div class="art-notice-content">
				<h3><?php esc_html_e('Thanks for installing Theme!', 'napoleon'); ?></h3>
				<p>
				<p><?php echo esc_html($message); ?></p>
				<a href="<?php echo $learn_more; ?>" target="_blank"><?php esc_html_e('Learn more', 'napoleon'); ?></a>
				</p>
			</div>

			<div class="art-install-now">
				<a class="button button-primary art-install-button" href="<?php echo esc_attr($button_link); ?>"><i
						class="dashicons dashicons-unlock"></i><?php echo esc_html($button_text); ?></a>
			</div>
		</div>
	</div>
	<?php
}


/**
 * Set Admin Notice Viewed.
 *
 * @return void
 */
function ajax_art_set_admin_notice_viewed()
{
	update_user_meta(get_current_user_id(), '_' . get_template() . '_install_notice', 'true');
	die;
}

add_action('wp_ajax_art_set_admin_notice_viewed', 'ajax_art_set_admin_notice_viewed');


// Check if Theme activated
if (!napoleon_is_theme_license_valid()) {
	add_action('admin_notices', 'art_admin_notice_activate_theme');
}


// Theme license check
function napoleon_is_theme_license_valid()
{
	return true;
}

if (!napoleon_is_theme_license_valid()) {
	function hide_one_click_demo_import()
	{
		remove_submenu_page('themes.php', 'one-click-demo-import');
	}
	add_action('admin_menu', 'hide_one_click_demo_import');
}


/**
 * Customizer controls.
 */
if (napoleon_is_theme_license_valid()) {
	require_once get_theme_file_path('/inc/onboarding.php');
	require_once get_theme_file_path('/inc/customizer.php');
}
