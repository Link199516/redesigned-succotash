<?php

if ( ! class_exists( 'napoleon_Onboarding_Page' ) ) {
	return;
}

/**
 * Class used to generate the onboarding page.
 */
class napoleon_Onboarding_Page {

	/**
	 * Used to pass all custom onboarding data
	 *
	 * @var null
	 */
	protected $data = null;

	/**
	 * The theme name
	 *
	 * @var null
	 */
	private $theme_name = null;

	/**
	 * The theme slug
	 *
	 * @var null
	 */
	private $theme_slug = null;

	/**
	 * Stores the theme object
	 *
	 * @var null
	 */
	private $theme = null;

	/**
	 * The theme version
	 *
	 * @var null
	 */
	private $theme_version = null;

	/**
	 * The title of the onboarding page on the WP menu
	 *
	 * @var null
	 */
	private $menu_title = null;

	/**
	 * The title of the onboarding page on the page
	 *
	 * @var null
	 */
	private $page_title = null;

	/**
	 * Onboarding page initialization
	 *
	 * @param array $data Custom onboarding data.
	 */
	public function init( $data ) {
		if ( ! empty( $data ) && is_array( $data ) && true === $data['show_page'] ) {
			$defaults   = $this->default_data();
			$this->data = wp_parse_args( $data, $defaults );

			$this->data['tabs'] = wp_parse_args( $this->data['tabs'], $defaults['tabs'] );
			if ( ! function_exists( 'napoleon_get_theme_variations' ) ) {
				$this->data['tabs']['theme_variations'] = '';
				if ( 'theme_variations' === $this->data['default_tab'] ) {
					$this->data['default_tab'] = 'recommended_plugins';
				}
			}

			foreach ( $this->data['recommended_plugins_page']['plugins'] as $slug => $plugin ) {
				$plugin = $this->plugin_entry_defaults( $plugin );

				$this->data['recommended_plugins_page']['plugins'][ $slug ] = $plugin;
			}

			$this->themedata_setup();
			$this->page_setup();
		}
	}

	/**
	 * Setup theme and custom data
	 *
	 * @return void
	 */
	public function themedata_setup() {
		$theme    = wp_get_theme();
		$defaults = $this->default_data();

		if ( is_child_theme() ) {
			$this->theme_name = $theme->parent()->get( 'Name' );
			$this->theme      = $theme->parent();
		} else {
			$this->theme_name = $theme->get( 'Name' );
			$this->theme      = $theme;
		}
		$this->theme_version = $theme->get( 'Version' );
		$this->theme_slug    = $theme->get_template();

		$this->menu_title = ! empty( $this->data['menu_title'] ) ? $this->data['menu_title'] : $defaults['menu_title'];
		$this->page_title = ! empty( $this->data['page_title'] ) ? $this->data['page_title'] : $defaults['page_title'];

		if ( ! empty( $this->data['theme_variations_page']['variations'] ) ) {
			$variations       = $this->data['theme_variations_page']['variations'];
			$theme_screenshot = $theme->get_screenshot();

			foreach ( $this->data['theme_variations_page']['variations'] as $slug => $variation ) {
				if ( empty( $variation['screenshot'] ) ) {
					$variation['screenshot'] = $theme_screenshot;

					if ( '' !== $slug ) {
						$path            = "/theme-variations/{$slug}/screenshot.png";
						$screenshot_path = get_theme_file_path( $path );
						if ( file_exists( $screenshot_path ) ) {
							$variation['screenshot'] = get_theme_file_uri( $path );
						}
					}
				}

				$variations[ $slug ] = $variation;
			}

			$this->data['theme_variations_page']['variations'] = $variations;
		}
	}

	/**
	 * Actions used on the onboarding page
	 *
	 * @return void
	 */
	public function page_setup() {
		if ( $this->data['redirect_on_activation'] ) {
			add_action( 'after_switch_theme', array( $this, 'redirect_to_onboarding' ) );
		}

		add_action( 'admin_notices', array( $this, 'onboarding_notice' ) );
		add_action( 'wp_ajax_napoleon_dismiss_onboarding', array( $this, 'dismiss_onboarding' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_menu', array( $this, 'register' ) );
		add_action( 'wp_ajax_install_napoleon_plugin', array( $this, 'install_plugin' ) );
		add_action( 'wp_ajax_napoleon_activate_variation', array( $this, 'activate_variation' ) );
		add_action( 'wp_ajax_napoleon_reset_theme_mods', array( $this, 'reset_theme_mods' ) );
	}

	/**
	 * Redirect to the onboarding page after activation
	 *
	 * @return void
	 */
	public function redirect_to_onboarding() {
		global $pagenow;
		if ( is_admin() && 'themes.php' === $pagenow && isset( $_GET['activated'] ) && current_user_can( 'manage_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			wp_safe_redirect( admin_url( 'themes.php?page=' . $this->theme_slug . '-onboard' ) );
			exit;
		}
	}

	/**
	 * Add admin notice for the onboarding page
	 *
	 * @return void
	 */
	public function onboarding_notice() {

		$dismissed = get_theme_mod( 'dismissed_onboarding', false );

		// Do not show the notice on the One Click Demo Import page or the onboarding page itself.
		if ( get_current_screen()->id === 'appearance_page_one-click-demo-import' || get_current_screen()->id === 'appearance_page_' . $this->theme_slug . '-onboard' ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) || $dismissed ) {
			return;
		}

		$onboarding_page_url = get_admin_url( '', 'themes.php?page=' . $this->theme_slug . '-onboard' );

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
				display: table !important;
				width: 100%;
			}
			.notice.art-notice .art-notice-inner .art-notice-icon,
			.notice.art-notice .art-notice-inner .art-notice-content,
			.notice.art-notice .art-notice-inner .art-install-now {
				display: table-cell !important;
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
		<div class="notice updated is-dismissible art-notice art-install-elementor">
			<div class="art-notice-inner">
<div class="art-notice-icon">
    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/bthd-logo.png' ); ?>" alt="Napoleon Logo" width="60" />
</div>

				<div class="art-notice-content">
					<h3><?php esc_html_e( 'Only One Step Left.. ! 🎉', 'napoleon' ); ?></h3>
					<p><?php esc_html_e( 'Check out the onboarding page to get things started.', 'napoleon' ); ?></p>
				</div>

				<div class="art-install-now">
					<a class="button button-primary art-install-button" href="<?php echo esc_url( $onboarding_page_url ); ?>"><?php esc_html_e( 'Go to Onboarding Page', 'napoleon' ); ?></a>
				</div>
			</div>
		</div>
		<?php

		wp_enqueue_script( 'napoleon-onboarding-notice', get_theme_file_uri( '/inc/onboarding/js/onboarding-notice.js' ), array(), $this->theme_version, true );

		$settings = array(
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'dismiss_nonce' => wp_create_nonce( 'napoleon-dismiss-onboarding' ),
		);
		wp_localize_script( 'napoleon-onboarding-notice', 'napoleon_Onboarding', $settings );
	}

	/**
	 * Handle dismissal of the admin notice
	 *
	 * @return void
	 */
	public function dismiss_onboarding() {
		check_ajax_referer( 'napoleon-dismiss-onboarding', 'nonce' );

		if ( current_user_can( 'manage_options' ) && ! empty( $_POST['dismissed'] ) && 'true' === $_POST['dismissed'] ) {
			set_theme_mod( 'dismissed_onboarding', true );
			wp_send_json_success( 'OK' );
		}

		wp_send_json_error( 'BAD' );
	}

	/**
	 * Enqueue onboarding page styles and scripts
	 *
	 * @return void
	 */
	public function enqueue_admin_styles() {
		if ( get_current_screen()->id !== 'appearance_page_' . $this->theme_slug . '-onboard' ) {
			return;
		}

		wp_enqueue_style( 'plugin-install' );

		wp_enqueue_style( 'napoleon-onboarding', get_theme_file_uri( '/inc/onboarding/css/onboarding-styles.css' ), array(), $this->theme_version );

		wp_enqueue_script( 'napoleon-onboarding', get_theme_file_uri( '/inc/onboarding/js/onboarding.js' ), array(
			'plugin-install',
			'updates',
		), $this->theme_version, true );

		wp_localize_script(
			'napoleon-onboarding', 'napoleon_onboarding', array(
				'onboarding_nonce'        => wp_create_nonce( 'onboarding_nonce' ),
				'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
				'template_directory'      => get_template_directory_uri(),
				'activating_text'         => esc_html__( 'Activating', 'napoleon' ),
				'activate_text'           => esc_html__( 'Activate', 'napoleon' ),
				'installing_text'         => esc_html__( 'Installing...', 'napoleon' ),
				'activate_variation_text' => esc_html__( 'Activate variation', 'napoleon' ),
				'deleting_text'           => esc_html__( 'Deleting...', 'napoleon' ),
				'reset_mods_confirm_text' => esc_html__( 'Are you sure you want to delete your theme customizations?', 'napoleon' ),
				'needs_auto_activate'     => $this->check_for_plugins_to_auto_activate(),
				'redirect_to_sample_url'  => admin_url( 'themes.php?page=one-click-demo-import' ),
				'countdown_text'          => esc_html__( 'Activating in %s seconds...', 'napoleon' ),
				'activating_all_text'     => esc_html__( 'Activating all required plugins...', 'napoleon' ),
			)
		);
	}

	/**
	 * Register the page
	 *
	 * @return void
	 */
	public function register() {
		add_theme_page( $this->page_title, $this->menu_title, 'edit_theme_options', $this->theme_slug . '-onboard', array( $this, 'render_page' ) );
	}

	/**
	 * Render the onboarding page
	 *
	 * @return void
	 */
	public function render_page() {
		$title = $this->data['title'];
		$title = str_replace(
			array( ':theme_name:', ':theme_version:' ),
			array( $this->theme_name, $this->theme_version ),
		$title );

		
			$logo_src = ! empty( $this->data['logo_src'] ) ? $this->data['logo_src'] : get_theme_file_uri( '/inc/onboarding/assets/bthd_logo.png' );
			$logo_url = ! empty( $this->data['logo_url'] ) ? $this->data['logo_url'] : 'https://bitherhood.com/';
		
		?>
		<div class="wrap about-wrap napoleon-onboarding-wrap full-width-layout">
			<h1><?php echo esc_html( $title ); ?></h1>

			<?php if ( ! empty( $this->data['description'] ) ) : ?>
				<p class="about-text"><?php echo wp_kses( $this->data['description'], napoleon_get_allowed_tags( 'guide' ) ); ?></p>
			<?php endif; ?>

			<?php if ( $this->data['logo_show'] && $logo_src ) : ?>
				<div class="wp-badge">
					<a href="<?php echo esc_url( $logo_url ); ?>" target="_blank">
						<img src="<?php echo esc_url( $logo_src ); ?>">
					</a>
				</div>
			<?php endif; ?>
			<?php
				if ( array_key_exists( 'tabs', $this->data ) && ! empty( $this->data['tabs'] ) ) {
					$this->generate_tabs();
				}
			?>
		</div>
		<?php
	}

	/**
	 * Create the navigation tabs
	 *
	 * @return void
	 */
	public function generate_tabs() {
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : $this->data['default_tab']; // phpcs:ignore WordPress.Security.NonceVerification
		?>
		<h2 class="nav-tab-wrapper wp-clearfix">
			<?php foreach ( $this->data['tabs'] as $tab => $title ) : ?>
				<?php if ( empty( $title ) ) {
					continue;
				} ?>
				<a href="<?php echo esc_url( get_admin_url( '', 'themes.php?page=' . $this->theme_slug . '-onboard' ) . '&tab=' . $tab ); ?>" class="nav-tab <?php echo esc_attr( $active_tab === $tab ? 'nav-tab-active' : '' ); ?>" role="tab" data-toggle="tab"><?php echo esc_html( $title ); ?></a>
			<?php endforeach; ?>
		</h2>
		<div class="tab-content tab-<?php echo esc_attr( $active_tab ); ?>">
			<?php
				if ( is_callable( array( $this, $active_tab ) ) ) {
					$this->$active_tab();
				}
			?>
		</div>
		<?php
	}

	/**
	 * Populate the theme variations tab
	 *
	 * @return false|void
	 */
	public function theme_variations() {
		?><h3><?php esc_html_e( 'Select a theme variation. You can always come back and pick another.', 'napoleon' ); ?></h3><?php

		if ( ! current_user_can( 'edit_theme_options' ) ) :
			?><p><?php echo wp_kses( __( 'You do not have sufficient permissions to install plugins, please contact your administrator.', 'napoleon' ), napoleon_get_allowed_tags( 'guide' ) ); ?></p><?php
			return false;
		endif;

		?><div class="napoleon-onboarding-list"><?php
			$variations = $this->data['theme_variations_page']['variations'];

			$this->get_variation_boxes( $variations );
		?></div><?php

		$reset_button = isset( $this->data['theme_variations_page']['reset_mods_button'] ) && true === (bool) $this->data['theme_variations_page']['reset_mods_button'];
		if ( $reset_button ) {
			?>
			<div class="reset-theme-mods-wrap">
				<p><?php esc_html_e( "Your existing theme customizations made via the Customizer (theme mods) may prevent you from viewing the selected variation's defaults correctly. In this case, you might want to delete your customizations.", 'napoleon' ); ?></p>
				<p><em><?php esc_html_e( 'WARNING: Deleting your customizations cannot be undone. Make sure you keep a backup before deleting your customizations.', 'napoleon' ); ?></em></p>
				<p><a href="#" class="button reset-theme-mods"><?php esc_html_e( 'Delete customizations', 'napoleon' ); ?></a></p>
			</div>
			<?php
		}
	}

	/**
	 * Layout for the variation tab
	 *
	 * @param array $variations Array of the required or recommended plugins.
	 * @return void
	 */
	public function get_variation_boxes( $variations ) {
		if ( ! function_exists( 'napoleon_get_theme_variation' ) ) {
			return;
		}

		$current_variation = napoleon_get_theme_variation();

		foreach ( $variations as $slug => $variation ) {
			$enabled_class = '';
			if ( $current_variation === $slug ) {
				$enabled_class = 'enabled';
			}

			?>
			<div class="col">
				<div class="napoleon-onboarding-box napoleon-variation <?php echo esc_attr( $enabled_class ); ?>">
					<figure class="box-thumb">
						<img src="<?php echo esc_url( $variation['screenshot'] ); ?>">
					</figure>

					<h4 class="box-title"><?php echo esc_html( $variation['title'] ); ?></h4>

					<?php if ( ! empty( $variation['description'] ) ) : ?>
						<p class="box-description"><?php echo wp_kses( $variation['description'], napoleon_get_allowed_tags( 'guide' ) ); ?></p>
					<?php endif; ?>

					<p><a href="#" class="button activate-variation" data-variation-slug="<?php echo esc_attr( $slug ); ?>"><?php esc_html_e( 'Activate variation', 'napoleon' ); ?></a></p>
				</div>
			</div>
			<?php
		}

	}

	/**
	 * Populate the recommended plugins tab
	 *
	 * @return false|void
	 */
	public function recommended_plugins() {
		?><h3><?php
			/* translators: %s is the theme's name. */
			echo esc_html( sprintf( __( 'The following plugins will provide additional functionality to %s.', 'napoleon' ), $this->theme_name ) );
		?></h3>
		<style>
			/* Hide buttons only in the required list */
			.tab-recommended_plugins .required-plugins-list .col .button.install-now,
			.tab-recommended_plugins .required-plugins-list .col .button.activate-now {
				display: none !important;
			}
			.plugin-status-indicator {
				display: inline-block;
				font-size: 9px;
				line-height: 1.5;
				font-weight: 600;
				margin: 0 5px;
				padding: 1px 5px;
				vertical-align: middle;
				border: 1px solid transparent; /* Base border */
				border-radius: 3px;
				background-color: transparent; /* Base background */
				color: #555; /* Default text color */
			}
			.plugin-status-indicator.processing {
				color: #0073aa; /* WordPress Blue */
				padding-left: 20px; /* Make space for icon */
				position: relative;
				border-color: #0073aa; /* Add border for processing */
				background-color: #f0f6fc; /* Light blue background */
			}
			.plugin-status-indicator.processing::before {
				content: '';
				box-sizing: border-box;
				position: absolute;
				top: 50%;
				left: 5px; /* Adjusted left position */
				width: 12px; /* Smaller spinner */
				height: 12px; /* Smaller spinner */
				margin-top: -6px; /* Half of height */
				border-radius: 50%;
				border: 2px solid #ccc;
				border-top-color: #0073aa; /* Spinner color */
				animation: rotate 0.8s linear infinite;
			}
			.plugin-status-indicator.success {
				color: #46b450; /* WordPress Green */
				border-color: #46b450;
				background-color: #eaf7ea; /* Light green background */
			}
			.plugin-status-indicator.success::before {
				content: '✓'; /* Checkmark */
				margin-right: 3px;
				font-weight: bold;
				animation: none; /* Override spinner animation */
				border: none; /* Override spinner border */
				position: static; /* Override absolute positioning */
				width: auto; height: auto; margin-top: 0; border-radius: 0; /* Reset spinner styles */
			}
			.plugin-status-indicator.error {
				color: #dc3232; /* WordPress Red */
				border-color: #dc3232;
				background-color: #fbeaea; /* Light red background */
			}
			.plugin-status-indicator.error::before {
				content: '✗'; /* Cross mark */
				margin-right: 3px;
				font-weight: bold;
				animation: none; /* Override spinner animation */
				border: none; /* Override spinner border */
				position: static; /* Override absolute positioning */
				width: auto; height: auto; margin-top: 0; border-radius: 0; /* Reset spinner styles */
			}
			@keyframes rotate {
				to { transform: rotate(360deg); }
			}
		</style>
		<?php

		if ( ! current_user_can( 'install_plugins' ) ) :
			?><p><?php echo wp_kses( __( 'You do not have sufficient permissions to install plugins, please contact your administrator.', 'napoleon' ), napoleon_get_allowed_tags( 'guide' ) ); ?></p><?php
			return false;
		endif;

		$all_plugins = $this->data['recommended_plugins_page']['plugins'];

		// Separate plugins into Required (Bundled or Other) and Optional
		$all_required_plugins = array_filter($all_plugins, function($plugin) {
			// Consider both 'required' and 'required_by_sample' flags
			$is_required = isset($plugin['required_by_sample']) ? $plugin['required_by_sample'] : (isset($plugin['required']) ? $plugin['required'] : false);
			return $is_required;
		});

		$optional_plugins = array_filter($all_plugins, function($plugin) {
			$is_required = isset($plugin['required_by_sample']) ? $plugin['required_by_sample'] : (isset($plugin['required']) ? $plugin['required'] : false);
			return !$is_required;
		});

		// Calculate if any required plugins need action for the button
		$all_required_actions = $this->get_plugin_action( $all_required_plugins );
		$actions_needed = array_filter( $all_required_actions, function( $action ) {
			return $action !== 'none';
		} );

		// Add the "Install All" button if actions are needed for any required plugin
		if ( ! empty( $actions_needed ) ) : ?>
			<p>
				<button id="install-required-plugins-button" class="button button-primary">
					<?php esc_html_e( 'Install & Activate All Required', 'napoleon' ); ?>
				</button>
				<span class="spinner" style="float: none; vertical-align: middle; margin-left: 5px; display: none;"></span>
			</p>
		<?php endif; ?>

		<?php
		// Display All Required Plugins
		if ( ! empty( $all_required_plugins ) ) {
			?><h4><?php esc_html_e( 'Required to Install Plugins', 'napoleon' ); ?></h4>
			<div class="napoleon-onboarding-list required-plugins-list"><?php // Keep class for JS targeting
				// Actions already calculated above
				$this->get_plugin_boxes( $all_required_plugins, $all_required_actions, 'required' ); // Pass 'required' type for styling/logic
			?></div><?php
		}

		// Display Optional Plugins
		if ( ! empty( $optional_plugins ) ) {
			?><h4><?php esc_html_e( 'Optional Plugins', 'napoleon' ); ?></h4>
			<div class="napoleon-onboarding-list optional-plugins-list"><?php
				$optional_actions = $this->get_plugin_action( $optional_plugins );
				$this->get_plugin_boxes( $optional_plugins, $optional_actions, 'optional' ); // Pass 'optional' type
			?></div><?php
		}
	}

	/**
		* Populate the sample content tab
	 *
	 * @return void
	 */
	public function sample_content() {

		$plugins = array_merge( $this->data['recommended_plugins_page']['plugins'] );
		$plugins = wp_list_filter( $plugins, array( 'required_by_sample' => true ) );

		if ( ! empty( $plugins['one-click-demo-import'] ) ) {
			$plugins['one-click-demo-import']['required_by_sample'] = true;
		}

		$actions = $this->get_plugin_action( $plugins );

		foreach ( $actions as $slug => $action ) {
			if ( 'none' === $action ) {
				unset( $plugins[ $slug ] );
			}
		}

		if ( ! empty( $plugins ) ) {
			?>
			<h3><?php esc_html_e( 'Plugins required for sample content. Please install and activate.', 'napoleon' ); ?></h3>
			<p><?php esc_html_e( 'While these plugins are not required for the theme to work, they are needed to ensure the sample content is correctly imported. You can deactivate/remove them if you want, after the sample content is imported.', 'napoleon' ); ?></p>

			<div class="napoleon-onboarding-list">
				<?php
					$actions = $this->get_plugin_action( $plugins );

					$this->get_plugin_boxes( $plugins, $actions );
				?>
			</div>
			<?php
		}

		?><h3><?php esc_html_e( 'Import our sample content.', 'napoleon' ); ?></h3><?php

		$check = $this->get_plugin_action( array( 'one-click-demo-import' => __( 'One Click Demo Import', 'napoleon' ) ) );

		if ( in_array( $check['one-click-demo-import'], array( 'install-plugin', 'activate' ), true ) ) {
			?>
			<div class="napoleon-onboarding-box napoleon-onboarding-box-warning">
				<h4 class="box-title"><?php esc_html_e( 'Please note:', 'napoleon' ); ?></h4>
				<p><?php echo wp_kses( __( 'You need to install and activate <strong>One Click Demo Import</strong> before proceeding.', 'napoleon' ), napoleon_get_allowed_tags( 'guide' ) ); ?></p>
			</div>
			<?php
		} else {
			?>
			<div class="napoleon-onboarding-box napoleon-onboarding-box-success">
				<h4 class="box-title"><?php esc_html_e( 'Good to go!', 'napoleon' ); ?></h4>
				<p><?php esc_html_e( 'Now you can import the sample content and have your theme set up like the demo using the One Click Demo Import Plugin.', 'napoleon' ); ?></p>
				<p><a class="button button-primary" href="<?php echo esc_url( get_admin_url( '', 'themes.php?page=one-click-demo-import' ) ); ?>"><?php esc_html_e( 'Get Started', 'napoleon' ); ?></a></p>
			</div>
			<?php
		}

	}

	/**
	 * Populate the support tab
	 *
	 * @return void
	 */
	public function support() {
		if ( empty( $this->data['support_page']['sections'] ) ) {
			return;
		}

		?>
		<h3><?php esc_html_e( 'Here are a few useful links to get you started.', 'napoleon' ); ?></h3>
		<div class="napoleon-onboarding-list">
			<?php
				$sections = $this->data['support_page']['sections'];

				foreach ( $sections as $section_id => $section ) {
					?>
					<div class="col">
						<div class="napoleon-onboarding-box napoleon-support-tab-box-<?php echo esc_attr( $section_id ); ?>">
							<h4 class="box-title"><a href="<?php echo esc_url( $section['link_url'] ); ?>" target="_blank"><?php echo esc_html( $section['title'] ); ?></a></h4>
							<?php if ( ! empty( $section['description'] ) ) : ?>
								<p><?php echo wp_kses( $section['description'], napoleon_get_allowed_tags( 'guide' ) ); ?></p>
							<?php endif; ?>
							<p><a class="button" href="<?php echo esc_url( $section['link_url'] ); ?>" target="_blank"><?php echo esc_html( $section['title'] ); ?></a></p>
						</div>
					</div>
					<?php
				}
			?>
		</div>
		<?php
	}

	/**
	 * Layout for the plugin tabs
	 *
	 * @param array $plugins Array of the required or recommended plugins.
	 * @param array $actions Array of the plugin actions.
	 * @param string $plugin_type Type of plugin ('required', 'optional'). Bundled is handled by checking the plugin data itself now.
	 * @return void
	 */
	public function get_plugin_boxes( $plugins, $actions, $plugin_type = 'optional' ) {
	 // Determine if we should use the $is_required logic based on the type passed
	 $is_required = ( $plugin_type === 'required' );

	 foreach ( $actions as $slug => $action ) {

	 	$data        = $plugins[ $slug ];
			$plugin_file = ! empty( $data['plugin_file'] ) ? $slug . '/' . $data['plugin_file'] : $slug . '/' . $slug . '.php';
			$is_bundled = isset( $data['bundled'] ) && true === $data['bundled']; // Check if it's bundled

			// Determine the box class based on action and requirement status
			$box_class = 'napoleon-onboarding-box'; // Base class

			if ( $is_required ) {
				// Required Plugins (Bundled or Repo)
				if ( 'upload-plugin' === $action || 'install-plugin' === $action ) {
					$box_class .= ' napoleon-onboarding-box-warning'; // Needs install (Blue)
				} elseif ( 'activate' === $action ) {
					$box_class .= ' napoleon-onboarding-box-info';    // Needs activation (Yellow)
				} elseif ( 'none' === $action ) {
					$box_class .= ' napoleon-onboarding-box-success'; // Active (Green)
				}
			} else {
				// Optional Plugins
				if ( 'upload-plugin' === $action || 'install-plugin' === $action ) {
					$box_class .= ' optional-needs-install'; // Needs install (Grey)
				} elseif ( 'activate' === $action ) {
					$box_class .= ' napoleon-onboarding-box-info';    // Needs activation (Yellow)
				} elseif ( 'none' === $action ) {
					$box_class .= ' napoleon-onboarding-box-success'; // Active (Green)
				}
			}

			// Restore original structure with class applied correctly within each block
			if ( 'upload-plugin' === $action ) {
				$link = add_query_arg(
					array(
						'action'   => 'activate',
						'plugin'   => rawurlencode( $plugin_file ),
						'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $plugin_file ),
					), admin_url( 'plugins.php' )
				);
				// Prepare data attribute string - remove plugin_type as it's less relevant now
				$data_attributes = 'data-slug="' . esc_attr( $slug ) . '" data-is-bundled="' . ($is_bundled ? 'true' : 'false') . '"';
				?>
				<div class="col plugin-status-<?php echo esc_attr( $action ); ?> <?php echo $is_bundled ? 'plugin-is-bundled' : 'plugin-is-repo'; ?> <?php echo $is_required ? 'plugin-is-required' : 'plugin-is-optional'; ?>">
					<div class="<?php echo esc_attr( $box_class ); ?> napoleon-onboarding-box-<?php echo esc_attr( $slug ); ?>">
						<h4 class="box-title">
							<?php echo esc_html( $plugins[ $slug ]['title'] ); ?>
							<?php if ( ! $is_required ) : // Check if optional for badge ?>
								<span class="plugin-badge optional-badge"><?php esc_html_e( 'Optional', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( $is_bundled ) : // Add bundled badge ?>
								<span class="plugin-badge bundled-badge"><?php esc_html_e( 'Bundled', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( 'jet-woo-builder' === $slug ) : // Add NEW badge specifically for JetWooBuilder, always displayed ?>
								<span class="plugin-badge new-badge"><?php esc_html_e( 'NEW', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( 'woo-friendly-user-agent' === $slug ) : // Add HOT badge for Woo Friendly User Agent ?>
								<span class="plugin-badge hot-badge"><?php esc_html_e( 'HOT', 'napoleon' ); ?> </span>
							<?php endif; ?>
							<?php if ( 'elementor-pro' === $slug ) : // Add PRO badge for Elementor Pro ?>
								<span class="plugin-badge pro-badge"><?php esc_html_e( 'PRO', 'napoleon' ); ?></span>
							<?php endif; ?>
							<span class="plugin-status-indicator"></span>
						</h4>

						<?php if ( ! empty( $plugins[ $slug ]['description'] ) ) : ?>
							<p class="box-description"><?php echo wp_kses( $plugins[ $slug ]['description'], napoleon_get_allowed_tags( 'guide' ) ); ?></p>
						<?php endif; ?>

						<div class="plugin-card-<?php echo esc_attr( $slug ); ?>">
							<p>
							<?php
								/* translators: %s is the plugin name. */
								echo esc_html( sprintf( __( 'The %s plugin was not found. Click to install and activate.', 'napoleon' ), $data['title'] ) );
							?>
							</p>
							<?php // Use specific ID for bundled install button for JS targeting ?>
							<p><a id="install-bundled-<?php echo esc_attr( $slug ); ?>" href="<?php echo esc_url( $link ); ?>" class="install-now button ajax-install-plugin" <?php echo $data_attributes; ?>><?php esc_html_e( 'Install & activate ', 'napoleon' ); ?></a></p>
						</div>
					</div>
				</div>
				<?php
			} elseif ( 'install-plugin' === $action ) {
				$link = add_query_arg(
					array(
						'action'   => $action,
						'plugin'   => $slug,
						'_wpnonce' => wp_create_nonce( $action . '_' . $slug ),
					), admin_url( 'update.php' )
				);
				// Prepare data attribute string
				$data_attributes = 'data-slug="' . esc_attr( $slug ) . '" data-is-bundled="' . ($is_bundled ? 'true' : 'false') . '"';
				?>
				<div class="col plugin-status-<?php echo esc_attr( $action ); ?> <?php echo $is_bundled ? 'plugin-is-bundled' : 'plugin-is-repo'; ?> <?php echo $is_required ? 'plugin-is-required' : 'plugin-is-optional'; ?>">
					<div class="<?php echo esc_attr( $box_class ); ?> napoleon-onboarding-box-<?php echo esc_attr( $slug ); ?>">
						<h4 class="box-title">
							<?php echo esc_html( $plugins[ $slug ]['title'] ); ?>
							<?php if ( ! $is_required ) : // Check if optional for badge ?>
								<span class="plugin-badge optional-badge"><?php esc_html_e( 'Optional', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( $is_bundled ) : // Add bundled badge ?>
								<span class="plugin-badge bundled-badge"><?php esc_html_e( 'Bundled', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( 'jet-woo-builder' === $slug ) : // Add NEW badge specifically for JetWooBuilder, always displayed ?>
								<span class="plugin-badge new-badge"><?php esc_html_e( 'NEW', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( 'woo-friendly-user-agent' === $slug ) : // Add HOT badge for Woo Friendly User Agent ?>
								<span class="plugin-badge hot-badge"><?php esc_html_e( 'HOT', 'napoleon' ); ?> </span>
							<?php endif; ?>
							<?php if ( 'elementor-pro' === $slug ) : // Add PRO badge for Elementor Pro ?>
								<span class="plugin-badge pro-badge"><?php esc_html_e( 'PRO', 'napoleon' ); ?></span>
							<?php endif; ?>
							<span class="plugin-status-indicator"></span>
						</h4>

						<?php if ( ! empty( $plugins[ $slug ]['description'] ) ) : ?>
							<p class="box-description"><?php echo wp_kses( $plugins[ $slug ]['description'], napoleon_get_allowed_tags( 'guide' ) ); ?></p>
						<?php endif; ?>

						<div class="plugin-card-<?php echo esc_attr( $slug ); ?>">
							<p>
							<?php
								/* translators: %s is the plugin name. */
								echo esc_html( sprintf( __( 'The %s plugin was not found. Click to install and activate.', 'napoleon' ), $data['title'] ) );
							?>
							</p>
							<p><a href="<?php echo esc_url( $link ); ?>" class="install-now button" <?php echo $data_attributes; ?>><?php esc_html_e( 'Install & activate ', 'napoleon' ); ?></a></p>
						</div>
					</div>
				</div>
				<?php
			} elseif ( 'activate' === $action ) {
				$link = add_query_arg(
					array(
						'action'   => 'activate',
						'plugin'   => rawurlencode( $plugin_file ),
						'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $plugin_file ),
					), admin_url( 'plugins.php' )
				);
				// Prepare data attribute string
				$data_attributes = 'data-slug="' . esc_attr( $slug ) . '" data-is-bundled="' . ($is_bundled ? 'true' : 'false') . '"';
				?>
				<div class="col plugin-status-<?php echo esc_attr( $action ); ?> <?php echo $is_bundled ? 'plugin-is-bundled' : 'plugin-is-repo'; ?> <?php echo $is_required ? 'plugin-is-required' : 'plugin-is-optional'; ?>">
					<div class="<?php echo esc_attr( $box_class ); ?> napoleon-onboarding-box-<?php echo esc_attr( $slug ); ?>">
						<h4 class="box-title">
							<?php echo esc_html( $plugins[ $slug ]['title'] ); ?>
							<?php if ( ! $is_required ) : // Check if optional for badge ?>
								<span class="plugin-badge optional-badge"><?php esc_html_e( 'Optional', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( $is_bundled ) : // Add bundled badge ?>
								<span class="plugin-badge bundled-badge"><?php esc_html_e( 'Bundled', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( 'jet-woo-builder' === $slug ) : // Add NEW badge specifically for JetWooBuilder, always displayed ?>
								<span class="plugin-badge new-badge"><?php esc_html_e( 'NEW', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( 'woo-friendly-user-agent' === $slug ) : // Add HOT badge for Woo Friendly User Agent ?>
								<span class="plugin-badge hot-badge"><?php esc_html_e( 'HOT', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( 'elementor-pro' === $slug ) : // Add PRO badge for Elementor Pro ?>
								<span class="plugin-badge pro-badge"><?php esc_html_e( 'PRO', 'napoleon' ); ?></span>
							<?php endif; ?>
							<span class="plugin-status-indicator"></span>
						</h4>

						<?php if ( ! empty( $plugins[ $slug ]['description'] ) ) : ?>
							<p class="box-description"><?php echo wp_kses( $plugins[ $slug ]['description'], napoleon_get_allowed_tags( 'guide' ) ); ?></p>
						<?php endif; ?>

						<div class="plugin-card-<?php echo esc_attr( $slug ); ?>">
							<p>
							<?php
								/* translators: %s is the plugin name. */
								echo esc_html( sprintf( __( 'The %s plugin is installed but not active. Click to activate.', 'napoleon' ), $data['title'] ) );
							?>
							</p>
							<p><a <?php echo $data_attributes; // Add data attributes here too ?> href="<?php echo esc_url( $link ); ?>" class="activate-now button button-primary"><?php esc_html_e( 'Activate ', 'napoleon' ); ?></a></p>
						</div>
					</div>
				</div>
				<?php
			} else { // 'none' action (already installed and active)
				?>
				<div class="col plugin-status-<?php echo esc_attr( $action ); ?> <?php echo $is_bundled ? 'plugin-is-bundled' : 'plugin-is-repo'; ?> <?php echo $is_required ? 'plugin-is-required' : 'plugin-is-optional'; ?>">
					<div class="<?php echo esc_attr( $box_class ); ?> napoleon-onboarding-box-<?php echo esc_attr( $slug ); ?>">
						<h4 class="box-title">
							<?php echo esc_html( $plugins[ $slug ]['title'] ); ?>
							<?php if ( ! $is_required ) : // Check if optional for badge ?>
								<span class="plugin-badge optional-badge"><?php esc_html_e( 'Optional', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( $is_bundled ) : // Add bundled badge ?>
								<span class="plugin-badge bundled-badge"><?php esc_html_e( 'Bundled', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( 'jet-woo-builder' === $slug ) : // Add NEW badge specifically for JetWooBuilder, always displayed ?>
								<span class="plugin-badge new-badge"><?php esc_html_e( 'NEW', 'napoleon' ); ?></span>
							<?php endif; ?>
							<?php if ( 'woo-friendly-user-agent' === $slug ) : // Add HOT badge for Woo Friendly User Agent ?>
								<span class="plugin-badge hot-badge"><?php esc_html_e( 'HOT', 'napoleon' ); ?> </span>
							<?php endif; ?>
							<?php if ( 'elementor-pro' === $slug ) : // Add PRO badge for Elementor Pro ?>
								<span class="plugin-badge pro-badge"><?php esc_html_e( 'PRO', 'napoleon' ); ?></span>
							<?php endif; ?>
							<span class="plugin-status-indicator"></span>
						</h4>

						<?php if ( ! empty( $plugins[ $slug ]['description'] ) ) : ?>
							<p class="box-description"><?php echo wp_kses( $plugins[ $slug ]['description'], napoleon_get_allowed_tags( 'guide' ) ); ?></p>
						<?php endif; ?>

						<?php
							/* translators: %s is the plugin name. */
							echo esc_html( sprintf( __( '%s is installed and activated.', 'napoleon' ), $plugins[ $slug ]['title'] ) );
						?>
					</div>
				</div>
				<?php
			}
		} // End foreach
	}


	/**
	 * Check if a plugin is installed, active or absent
	 *
	 * @param array $plugins Array of the required or recommended plugins.
	 *
	 * @return array
	 */
	public function get_plugin_action( $plugins ) {

		$plugin_action = array();

		foreach ( $plugins as $slug => $data ) {
			$plugin_file      = ! empty( $data['plugin_file'] ) ? $slug . '/' . $data['plugin_file'] : $slug . '/' . $slug . '.php';
			$plugin_file_path = WP_PLUGIN_DIR . '/' . $plugin_file;

			$is_callable  = ! empty( $data['is_callable'] ) ? is_callable( $data['is_callable'] ) : false;
			$is_active    = is_plugin_active( $plugin_file );
			$is_installed = file_exists( $plugin_file_path );
			$is_bundled   = isset( $data['bundled'] ) && true === $data['bundled'] ? true : false;

			if ( $is_callable || $is_active ) {
				$plugin_action[ $slug ] = 'none';
				continue;
			}

			if ( $is_installed ) {
				$plugin_action[ $slug ] = 'activate';
			} else {
				if ( $is_bundled ) {
					$plugin_action[ $slug ] = 'upload-plugin';
				} else {
					$plugin_action[ $slug ] = 'install-plugin';
				}
			}
		}

		return $plugin_action;
	}

	/**
	 * Installs theme specific plugins
	 *
	 * @return void
	 */
	public function install_plugin() {
		// Check nonce and permissions first
		check_ajax_referer( 'onboarding_nonce', 'onboarding_nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) { // Changed from 'upload_plugins' to 'install_plugins' for consistency
			wp_send_json_error( array( 'message' => esc_html__( 'You do not have sufficient permissions to install plugins.', 'napoleon' ) ) );
		}

		$plugin_slug = isset( $_POST['plugin_slug'] ) ? sanitize_key( wp_unslash( $_POST['plugin_slug'] ) ) : '';

		if ( empty( $plugin_slug ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid plugin slug.', 'napoleon' ) ) );
		}

		// Include necessary files for plugin installation
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php'; // Needed for get_plugins()

		// Define path to bundled plugin zip using file path
		$plugin_source = get_theme_file_path( '/bundled-plugins/' . $plugin_slug . '.zip' );

		if ( ! file_exists( $plugin_source ) ) {
			wp_send_json_error( array( 'slug' => $plugin_slug, 'errorCode' => 'plugin_source_not_found', 'message' => esc_html__( 'Plugin source file not found.', 'napoleon' ) . ' Path: ' . $plugin_source ) );
		}

		// Use WP_Ajax_Upgrader_Skin for AJAX context
		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		// Clear previous results/errors from skin
		$skin->error = false;
		if ( isset( $skin->errors ) ) {
			$skin->errors = array();
		}

		// Perform the installation
		$result   = $upgrader->install( $plugin_source ); // Use file path

		// --- Robust Error Checking ---
		$error_message = '';
		$error_code = 'install_failed'; // Default error code

		// 1. Check explicit WP_Error result
		if ( is_wp_error( $result ) ) {
			$error_code = $result->get_error_code();
			$error_message = $result->get_error_message();
		}
		// 2. Check skin for errors (even if $result isn't WP_Error, skin might have details)
		elseif ( ! empty( $skin->errors ) ) {
			// Concatenate all error messages from the skin
			$messages = array();
			foreach ( $skin->errors as $error_key => $error_msgs ) {
				if (is_array($error_msgs)) {
					$messages[] = implode( ', ', $error_msgs );
				} elseif (is_string($error_msgs)) {
					$messages[] = $error_msgs;
				}
			}
			$error_message = implode( '; ', $messages );
			// Try to get a specific code if available
			$error_codes = array_keys($skin->errors);
			$error_code = !empty($error_codes[0]) ? $error_codes[0] : 'install_failed_skin';

		}
		// 3. Check for false result (generic failure)
		elseif ( $result === false ) {
			$error_message = esc_html__( 'Plugin installation failed. Check filesystem permissions or WP_DEBUG logs.', 'napoleon' );
		}

		// If any error message was generated, send JSON error
		if ( ! empty( $error_message ) ) {
			wp_send_json_error( array( 'slug' => $plugin_slug, 'errorCode' => $error_code, 'message' => $error_message ) );
		}
		// --- End Robust Error Checking ---

		// If we reach here, installation was successful OR plugin already existed ($result === null)
		if ( $result === null ) {
			// Plugin already installed - Check if it needs activation
			$all_plugins = get_plugins();
			$plugin_file = '';
			// Find the plugin file path based on slug (this might need adjustment if slugs don't match folder/file names)
			foreach ( $all_plugins as $file => $data ) {
				if ( strpos( $file, $plugin_slug . '/' ) === 0 ) { // Simple check if file path starts with slug/
					$plugin_file = $file;
					break;
				}
			}

			if ( $plugin_file && ! is_plugin_active( $plugin_file ) ) {
				// Send success, indicate it's already installed but needs activation
				wp_send_json_success( array( 'slug' => $plugin_slug, 'message' => esc_html__( 'Plugin already installed.', 'napoleon' ), 'status' => 'installed' ) );
			} else {
				// Already installed and active or couldn't find file
				// Send success but indicate it's already active
				wp_send_json_success( array( 'slug' => $plugin_slug, 'message' => esc_html__( 'Plugin already installed and active.', 'napoleon' ), 'status' => 'active' ) );
				// Note: Previously sent error here, changing to success as install didn't fail
			}
		} else {
			// Installation successful ($result === true)
			wp_send_json_success( array( 'slug' => $plugin_slug, 'message' => esc_html__( 'Plugin installed successfully.', 'napoleon' ), 'status' => 'installed' ) );
		}
	}

	/**
	 * Activates a theme variation.
	 *
	 * @return void
	 */
	public function activate_variation() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to change theme options on this site.', 'napoleon' ) );
		}

		// Verify nonce.
		if ( ! isset( $_POST['onboarding_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['onboarding_nonce'] ) ), 'onboarding_nonce' ) ) {
			die( 'Permission denied' );
		}

		$variation = isset( $_POST['variation'] ) ? sanitize_key( wp_unslash( $_POST['variation'] ) ) : '';

		if ( array_key_exists( $variation, napoleon_get_theme_variations() ) ) {
			set_theme_mod( 'theme_variation', $variation );
		}

		die;
	}

	/**
	 * Resets theme modifications.
	 *
	 * @return void
	 */
	public function reset_theme_mods() {
		// Check that we can actually perform this action.
		if ( ! isset( $this->data['theme_variations_page']['reset_mods_button'] ) || false === (bool) $this->data['theme_variations_page']['reset_mods_button'] ) {
			die( 'Permission denied' );
		}

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to change theme options on this site.', 'napoleon' ) );
		}

		// Verify nonce.
		if ( ! isset( $_POST['onboarding_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['onboarding_nonce'] ) ), 'onboarding_nonce' ) ) {
			die( 'Permission denied' );
		}

		$defaults   = napoleon_customizer_defaults();
		$theme_mods = get_theme_mods();

		// Only remove theme-specific theme mods.
		foreach ( $defaults as $key => $value ) {
			if ( array_key_exists( $key, $theme_mods ) ) {
				remove_theme_mod( $key );
			}
		}

		die;
	}

	public function default_data() {
		$theme = wp_get_theme();

		return array(
			// Required. Turns the onboarding page on/off.
			'show_page'                => true,
			// Optional. Turns the redirection to the onboarding page on/off.
			'redirect_on_activation'   => true,
			// Optional. The text to be used for the admin menu. If empty, defaults to "About theme_name"
			'menu_title'               => __( 'About napoleon', 'napoleon' ),
			// Optional. The text to be displayed in the page's title tag. If empty, defaults to "About theme_name"
			'page_title'               => __( 'About napoleon', 'napoleon' ),
			// Optional. The onboarding page's title, placeholders available :theme_name:, :theme_version:. If empty, defaults to "Welcome to :theme_name:! - Version :theme_version:"
			'title'                    => __( 'Welcome to :theme_name:! - Version :theme_version:', 'napoleon' ),
			// Optional. The theme's description. Some HTML is allowed (no p).
			'description'              => '',
	
			'logo_show'                => true,
			// Optional. The logo's image source URL. Defaults to the bundled logo.
			'logo_src'                 => get_theme_file_uri( '/inc/onboarding/assets/bthd_logo.png' ),
			// Required. The logo's link URL.
			'logo_url'                 => 'https://bitherhood.com/',
			// Optional. The default active tab. Default 'recommended_plugins'. Must be one of the keys in the tabs[] array.
			'default_tab'              => 'recommended_plugins',
			// Optional. slug => label pairs for each tab. Empty label to disable. Defaults are as follows:
			'tabs'                     => array(
				'theme_variations'    => __( 'Theme Variations', 'napoleon' ),
				'recommended_plugins' => __( 'Recommended Plugins', 'napoleon' ),
				'sample_content'      => __( 'Sample Content', 'napoleon' ),
				'support'             => __( 'Support', 'napoleon' ),
				
			),
			'theme_variations_page'    => array(
				'reset_mods_button' => true,
				'variations'        => array(
//					// Each variation is registered as 'slug' => array()
//					'variation-slug' => array(
//						// Required. The variation's title.
//						'title'       => __( 'Variation Title', 'napoleon' ),
//						// Optional. The variation's description.
//						'description' => '',
//						// Required. The variation's screenshot. Defaults to /theme-variations/variation-slug/screenshot.png and falls back to the theme's screenshot.
//						'screenshot'  => '',
//					),
				),
			),
			
			'recommended_plugins_page' => array(
				'plugins' => array(


//					// Each plugin is registered as 'slug' => array()
//					'plugin-slug' => array(
//						// Required. The plugin's title.
//						'title'              => __( 'Plugin Title', 'napoleon' ),
//						// Optional. The plugin's description, or why the plugin is required.
//						'description'        => '',
//						// Optional. If both 'version' and 'bundle' are set, the theme will prompt for a plugin update if applicable.
//						'version'            => '1.0',
//						// Optional. If true, the plugin zip will be searched in the theme's plugins/ directory, named "plugin-slug.zip". Default false.
//						'bundled'            => false,
//						// Optional. If passed string or array is callable, then the plugin will appear as activated.
//						'is_callable'        => '',
//						// Optional. If not passed, it's assumed to be "plugin-slug.php". Only pass a filename. It gets combined with the plugin slug as needed.
//						'plugin_file'        => '',
//						// Optional. Declares that the plugin must be active for sample content import to succeed. Default false.
//						'required_by_sample' => false,
//					),
				),
			),
			'support_page'             => array(
				'sections' => array(
					'documentation' => array(
						'title'       => __( 'Theme Documentation', 'napoleon' ),
						'description' => __( "If you don't want to import our demo sample content, just visit this page and learn how to set things up individually.", 'napoleon' ),
						'link_url'    => 'https://bitherhood.com/napoleon-guide/',
					),
					
					'support'       => array(
						'title'       => __( 'Request Support', 'napoleon' ),
						'description' => __( 'Got stuck? No worries, just visit our support page, submit your ticket and we will be there for you as soon as possible.', 'napoleon' ),
						'link_url'    => 'https://bitherhood.com/contact/',
					),
				),
			),

		);

	}

	public function plugin_entry_defaults( $plugin ) {
		return wp_parse_args( $plugin, array(
			// Required. The plugin's title.
			'title'              => __( 'Plugin Title', 'napoleon' ),
			// Optional. The plugin's description, or why the plugin is required.
			'description'        => '',
			// Optional. E.g. '1.0'. If both 'version' and 'bundle' are set, the theme will prompt for a plugin update if applicable.
			'version'            => '',
			// Optional. If true, the plugin zip will be searched in the theme's plugins/ directory, named "plugin-slug.zip". Default false.
			'bundled'            => false,
			// Optional. If passed string or array is callable, then the plugin will appear as activated.
			'is_callable'        => '',
			// Optional. Declares that the plugin must be active for sample content import to succeed. Default false.
			'required_by_sample' => false,
		) );
	}

	/**
	 * Checks if any required plugins are installed but not active.
	 *
	 * @return bool True if auto-activation is needed, false otherwise.
	 */
	private function check_for_plugins_to_auto_activate() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins = get_plugins();
		$required_plugins_data = $this->data['recommended_plugins_page']['plugins'];

		foreach ( $required_plugins_data as $slug => $data ) {
			$is_required = isset( $data['required_by_sample'] ) ? $data['required_by_sample'] : ( isset( $data['required'] ) ? $data['required'] : false );

			if ( $is_required ) {
				$plugin_file = ! empty( $data['plugin_file'] ) ? $slug . '/' . $data['plugin_file'] : $slug . '/' . $slug . '.php';

				// Check if the plugin is installed but not active
				if ( array_key_exists( $plugin_file, $all_plugins ) && ! is_plugin_active( $plugin_file ) ) {
					return true; // Found a required plugin that needs activation
				}
			}
		}
		return false; // No required plugins need auto-activation
	}

}
