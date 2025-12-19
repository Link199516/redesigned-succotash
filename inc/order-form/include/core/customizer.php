<?php
/**
 * Standard Customizer Sections and Settings
 */

// Include the custom control class
require_once get_theme_file_path( '/inc/order-form/include/controls/class-napoleon-multi-select-search-control.php' );

/**
 * Sanitize callback for an array of selected state codes.
 * Ensures each item in array is a sanitized string.
 *
 * @param array|string $value The value to sanitize.
 * @return array Sanitized array of state codes.
 */
function napoleon_sanitize_multi_select( $value ) {
	$multi_values = ! is_array( $value ) ? explode( ',', $value ) : $value;
	if ( ! empty( $multi_values ) ) {
		return array_map( 'sanitize_text_field', $multi_values );
	} else {
		return array();
	}
}

add_action( 'customize_register', 'codplugin_customize_register' );

function codplugin_customize_register( $wp_customize ) {

	$wp_customize->add_section(
		'upsells_settings',
		array(
			'title'       => esc_html__( 'Upsells Settings', 'napoleon' ),
			'panel'       => 'woocommerce',
			'description' => esc_html__( 'After checkout upsell settings', 'napoleon' ),
			'priority'    => 10,
		)
	);

	$wp_customize->add_setting( 'show_upsells', array(
		'default'           => false,
		'capability'        => 'edit_theme_options',
		// 'type'				=> 'option',
		// 'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'show_upsells', array(
			'label'	  => __( 'Show checkout upsell products', 'napoleon' ),
			'section' => 'upsells_settings',
			'type'    => 'checkbox',
		) );

	$wp_customize->add_setting( 'upsell_title', array(
		'default'           => __( 'Wait! Your order is not completed!', 'napoleon' ),
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'upsell_title', array(
			'label'	  => __( 'Checkout Upsell Section Title', 'napoleon' ),
			'section' => 'upsells_settings',
			'type'    => 'text',
		) );

	$wp_customize->add_panel(
		'plugin_settings',
		array(
			'title'    => esc_html__( 'Order Form Settings', 'napoleon' ),
			//'priority' => 1000,
		)
	);

	$wp_customize->add_section(
		'plugin_titles',
		array(
			'title'       => esc_html__( 'Titles', 'napoleon' ),
			'panel'       => 'plugin_settings',
			'description' => esc_html__( 'Customize default texts of order form', 'napoleon' ),
			'priority'    => 10,
		)
	);
	$wp_customize->add_setting( 'add_info', array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
	) );
			$wp_customize->add_control( 'add_info', array(
				'label'	  => __( 'Add Information Title', 'napoleon' ),
				'section' => 'plugin_titles',
				'type'    => 'text',
			) );

	$wp_customize->add_setting( 'form_full_name', array(
			'default'           => esc_html__( 'Full Name', 'napoleon' ),
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
	) );
			$wp_customize->add_control( 'form_full_name', array(
				'label'	  => __( 'Full Name', 'napoleon' ),
				'section' => 'plugin_titles',
				'type'    => 'text',
			) );

	$wp_customize->add_setting( 'form_phone', array(
			'default'           => esc_html__( 'Phone Number', 'napoleon' ),
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
	) );
			$wp_customize->add_control( 'form_phone', array(
				'label'	  => __( 'Phone Number', 'napoleon' ),
				'section' => 'plugin_titles',
				'type'    => 'text',
			) );

	$wp_customize->add_setting( 'form_state', array(
			'default'           => esc_html__( 'State', 'napoleon' ),
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
	) );
			$wp_customize->add_control( 'form_state', array(
				'label'	  => __( 'State', 'napoleon' ),
				'section' => 'plugin_titles',
				'type'    => 'text',
			) );

	$wp_customize->add_setting( 'form_city', array(
			'default'           => esc_html__( 'City', 'napoleon' ),
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
	) );
			$wp_customize->add_control( 'form_city', array(
				'label'	  => __( 'City', 'napoleon' ),
				'section' => 'plugin_titles',
				'type'    => 'text',
			) );

	$wp_customize->add_setting( 'form_address', array(
			'default'           => esc_html__( 'Full Address', 'napoleon' ),
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
	) );
			$wp_customize->add_control( 'form_address', array(
				'label'	  => __( 'Address', 'napoleon' ),
				'section' => 'plugin_titles',
				'type'    => 'text',
			) );
			// edit placehold order note
			$wp_customize->add_setting( 'form_order_notes', array(
			'default'           => esc_html__( 'Add Note', 'napoleon' ),
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
	) );
			$wp_customize->add_control( 'form_order_notes', array(
				'label'	  => __( 'Order Note', 'napoleon' ),
				'section' => 'plugin_titles',
				'type'    => 'text',
			) );

	$wp_customize->add_setting( 'form_email', array(
			'default'           => esc_html__( 'Email', 'napoleon' ),
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
	) );
			$wp_customize->add_control( 'form_email', array(
				'label'	  => __( 'Email', 'napoleon' ),
				'section' => 'plugin_titles',
				'type'    => 'text',
			) );

	$wp_customize->add_setting( 'form_button', array(
			'default'           => esc_html__( 'Click Here to Confirm Order', 'napoleon' ),
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
	) );
			$wp_customize->add_control( 'form_button', array(
				'label'	  => __( 'Checkout Button', 'napoleon' ),
				'section' => 'plugin_titles',
				'type'    => 'text',
			) );

	$wp_customize->add_section(
		'plugin_colors',
		array(
			'title'       => esc_html__( 'Colors', 'napoleon' ),
			'panel'       => 'plugin_settings',
			'description' => esc_html__( 'Customize default colors of order form', 'napoleon' ),
			'priority'    => 20,
		)
	);

	$wp_customize->add_setting( 'enable_variation_styling', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'enable_variation_styling', array(
				'type'    => 'checkbox',
				'section' => 'plugin_colors',
				'label'   => esc_html__( 'Enable variations styling', 'napoleon' ),
			) );

$wp_customize->add_setting( 'accent_color', array(
			'default'    => '#4C3BCF',
			'transport'  => 'refresh',
		) );

	// Color controls using standard WordPress color control
	$wp_customize->add_control( 
		new WP_Customize_Color_Control( 
			$wp_customize, 
			'accent_color', 
			array(
				'label'    => __( 'Accent Color', 'napoleon' ),
				'section'  => 'plugin_colors',
				'settings' => 'accent_color',
			) 
		) 
	);

	$wp_customize->add_setting( 'secondary_color', array(
		'default'   => '#bce0f7',
		'transport' => 'refresh',
	) );

	$wp_customize->add_control( 
		new WP_Customize_Color_Control( 
			$wp_customize, 
			'secondary_color', 
			array(
				'label'    => __( 'Secondary Color', 'napoleon' ),
				'section'  => 'plugin_colors',
				'settings' => 'secondary_color',
			) 
		) 
	);

	$wp_customize->add_setting( 'order_summary_background', array(
		'default'   => '#f0f9ff',
		'transport' => 'refresh',
	) );

	$wp_customize->add_control( 
		new WP_Customize_Color_Control( 
			$wp_customize, 
			'order_summary_background', 
			array(
				'label'    => __( 'Order Summary Background', 'napoleon' ),
				'section'  => 'plugin_colors',
				'settings' => 'order_summary_background',
			) 
		) 
	);

	$wp_customize->add_setting( 'button_color', array(
		'default'   => '#4C3BCF',
		'transport' => 'refresh',
	) );

	$wp_customize->add_control( 
		new WP_Customize_Color_Control( 
			$wp_customize, 
			'button_color', 
			array(
				'label'    => __( 'Button Color', 'napoleon' ),
				'section'  => 'plugin_colors',
				'settings' => 'button_color',
			) 
		) 
	);

	$wp_customize->add_setting( 'form_background_color', array(
		'default'   => '#ffffff',
		'transport' => 'refresh',
	) );

	$wp_customize->add_control( 
		new WP_Customize_Color_Control( 
			$wp_customize, 
			'form_background_color', 
			array(
				'label'    => __( 'Background Color', 'napoleon' ),
				'section'  => 'plugin_colors',
				'settings' => 'form_background_color',
			) 
		) 
	);

	$wp_customize->add_setting( 'text_color', array(
		'default'   => '#404040',
		'transport' => 'refresh',
	) );

	$wp_customize->add_control( 
		new WP_Customize_Color_Control( 
			$wp_customize, 
			'text_color', 
			array(
				'label'    => __( 'Text Color', 'napoleon' ),
				'section'  => 'plugin_colors',
				'settings' => 'text_color',
			) 
		) 
	);
	
	$wp_customize->add_section(
		'plugin_fields_display_options', // New Section for Fields Display
		array(
			'title'       => esc_html__( 'Fields Display', 'napoleon' ),
			'panel'       => 'plugin_settings',
			'priority'    => 30,
		)
	);

	// REMOVED: Redundant/early definition of codform_file_upload_section

	$wp_customize->add_section(
		'plugin_options',
		array(
			'title'       => esc_html__( 'General Options', 'napoleon' ), // Renamed for clarity
			'panel'       => 'plugin_settings',
			'priority'    => 50, // Assign priority
		)
	);

	$wp_customize->add_setting( 'enable_abandoned_carts', array(
		'default'           => 0,
			'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'enable_abandoned_carts', array(
				'type'    => 'checkbox',
				'section' => 'plugin_options',
				'label'   => esc_html__( 'Save abandoned orders', 'napoleon' ),
			) );

	$wp_customize->add_setting( 'autocomplete_state_list', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'autocomplete_state_list', array(
				'type'    => 'checkbox',
				'section' => 'plugin_options',
				'label'   => esc_html__( 'Autocomplete states list ', 'napoleon' ),
			) );

	$wp_customize->add_setting( 'display_city_field', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'display_city_field', array(
				'type'    => 'checkbox',
				'section' => 'plugin_fields_display_options', // Moved
				'label'   => esc_html__( 'Display city field in form', 'napoleon' ),
			) );

	$wp_customize->add_setting( 'hide_address_field', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'hide_address_field', array(
				'type'    => 'checkbox',
				'section' => 'plugin_fields_display_options', // Moved
				'label'   => esc_html__( 'Hide address field in order form ', 'napoleon' ),
			) );

	$wp_customize->add_setting( 'hide_email_field', array(
			'default'           => 1,
			'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'hide_email_field', array(
				'type'    => 'checkbox',
				'section' => 'plugin_fields_display_options', // Moved
				'label'   => esc_html__( 'Hide email field in order form ', 'napoleon' ),
			) );

	$wp_customize->add_setting( 'display_atc_button', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'display_atc_button', array(
				'type'    => 'checkbox',
				'section' => 'plugin_options',
				'label'   => esc_html__( 'Display both Add to cart and buy buttons', 'napoleon' ),
			) );

        // Order Note 
	
	$wp_customize->add_setting( 'hide_order_notes', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
	) );
		
	$wp_customize->add_control( 'hide_order_notes', array(
				'type'    => 'checkbox',
				'section' => 'plugin_fields_display_options', // Moved
				'label'   => esc_html__( 'Show order note field in order form', 'napoleon' ),
			) );
	$wp_customize->add_setting( 'display_atc_button', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'display_atc_button', array(
				'type'    => 'checkbox',
				'section' => 'plugin_options',
				'label'   => esc_html__( 'Display both Add to cart and buy buttons', 'napoleon' ),
			) );
			
	$wp_customize->add_setting( 'display_order_summary', array(
			'default'           => 1,
			'sanitize_callback' => 'absint',
	) );
			$wp_customize->add_control( 'display_order_summary', array(
				'type'    => 'checkbox',
				'section' => 'codplugin_other_settings',
				'label'   => esc_html__( 'Keep order summary open in order form ', 'napoleon' ),
			) );
	$wp_customize->add_setting( 'create_orders_with_php', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
	) );
			$wp_customize->add_control( 'create_orders_with_php', array(
				'type'    => 'checkbox',
				'section' => 'plugin_options',
				'label'   => esc_html__( 'Create orders with PHP instead of jQuery', 'napoleon' ),
			) );

	$wp_customize->add_setting( 'animate_order_btn', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
	) );
			$wp_customize->add_control( 'animate_order_btn', array(
				'type'    => 'checkbox',
				'section' => 'codplugin_other_settings',
				'label'   => esc_html__( 'Animate order button', 'napoleon' ),
			) );

	// New Setting: Layout Preset
	$wp_customize->add_setting( 'codform_layout_preset', array(
			'default'           => 'default',
			'sanitize_callback' => 'sanitize_key', // Use sanitize_key for slugs/keys
		) );
		$wp_customize->add_control( 'codform_layout_preset', array(
				'type'    => 'select',
				'section' => 'codplugin_other_settings', // Add to existing Options section
				'label'   => esc_html__( 'Order Form preset', 'napoleon' ),
				'choices' => array(
					'default' => esc_html__( 'Default Preset', 'napoleon' ),
					'preset2' => esc_html__( 'Horizontal Shipping', 'napoleon' ),
				),
			) );
	// End New Setting

	// REMOVED: Setting and Control for codplugin_enable_chargily_pay

	// New Setting: Excluded States
	$excluded_states_choices = array();
	$excluded_states_description_list = '';

	if ( class_exists( 'WooCommerce' ) ) {
		$countries_obj   = new WC_Countries();
			$default_country = $countries_obj->get_base_country();
			// Define $states globally or ensure it's properly scoped if included files define it.
			// It's better if state files return array rather than relying on global.
			// For now, assuming state files might declare `global $states;`
			global $states;
			if (!is_array($states)) { // Initialize if not already an array (e.g. by a state file)
				$states = array();
			}

			$states_file = get_theme_file_path( "/inc/order-form/include/states/{$default_country}.php" );

			if ( file_exists( $states_file ) ) {
				include_once( $states_file ); // This file should populate $states[$default_country]
				if ( isset( $states[ $default_country ] ) && is_array( $states[ $default_country ] ) ) {
					$excluded_states_choices = $states[ $default_country ]; // Use directly for choices
				}
			} else {
				// Fallback description if state file not found
                $theme_name = wp_get_theme()->get('Name');
				$excluded_states_description_list = "\n\n" . sprintf( esc_html__( 'Note: Could not automatically list states for country code "%1$s". Please ensure a state file exists at: %2$s', 'napoleon' ), esc_html( $default_country ), esc_html( "wp-content/themes/{$theme_name}/inc/order-form/include/states/{$default_country}.php" ) );
			}
		}
        $main_description = esc_html__( 'Select states to exclude from order form dropdown.', 'napoleon' ) . $excluded_states_description_list;


	$wp_customize->add_setting( 'codplugin_excluded_states', array(
			'default'           => array(), // Default to an empty array for multi-select
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'napoleon_sanitize_multi_select',
			'type'              => 'theme_mod',
		) );
	$wp_customize->add_control( 
		new Napoleon_Multi_Select_Search_Control( 
			$wp_customize, 
			'codplugin_excluded_states', 
			array(
				'label'       => esc_html__( 'State Filtering (Exclude)', 'napoleon' ),
				'description' => $main_description,
				'section'     => 'plugin_options',
				'settings'    => 'codplugin_excluded_states',
				'choices'     => $excluded_states_choices, // Pass array of states here
			) 
		) 
	);
	// End New Setting

	// *** PAYMENT METHODS SECTION ***
	$wp_customize->add_section(
		'napoleon_payment_methods_section',
		array(
			'title'       => esc_html__( 'Payment Methods Settings', 'napoleon' ),
			'panel'       => 'plugin_settings',
			'priority'    => 40,
		)
	);

	// Grid Column Setting
	$wp_customize->add_setting( 'napoleon_payment_columns', array(
		'default'           => 4,
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'napoleon_payment_columns', array(
		'label'       => esc_html__( 'Grid Columns', 'napoleon' ),
		'section'     => 'napoleon_payment_methods_section',
		'type'        => 'select',
		'choices'     => array(
			'1' => esc_html__( '1 Column', 'napoleon' ),
			'2' => esc_html__( '2 Columns', 'napoleon' ),
			'3' => esc_html__( '3 Columns', 'napoleon' ),
			'4' => esc_html__( '4 Columns', 'napoleon' ),
			'5' => esc_html__( '5 Columns', 'napoleon' ),
			'6' => esc_html__( '6 Columns', 'napoleon' ),
			'7' => esc_html__( '7 Columns', 'napoleon' ),
			'8' => esc_html__( '8 Columns', 'napoleon' ),
			'9' => esc_html__( '9 Columns', 'napoleon' ),
			'10' => esc_html__( '10 Columns', 'napoleon' ),
			'11' => esc_html__( '11 Columns', 'napoleon' ),
			'12' => esc_html__( '12 Columns', 'napoleon' ),
		),
		'description' => esc_html__( 'Number of columns to display payment methods in a grid layout', 'napoleon' ),
	) );

	$wp_customize->add_section(
		'codplugin_other_settings',
		array(
			'title'       => esc_html__( 'Other Settings', 'napoleon' ),
			'panel'       => 'plugin_settings',
			'priority'    => 55, // After General Options
		)
	);

	// Setting: Force Compact Form Layout
	$wp_customize->add_setting( 'force_compact_form_layout', array(
		'default'           => false,
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'absint', // 0 or 1
	) );
	$wp_customize->add_control( 'force_compact_form_layout', array(
				'type'        => 'checkbox',
				'section'     => 'codplugin_other_settings',
				'label'       => esc_html__( 'Force Compact Form Layout', 'napoleon' ),
				'description' => esc_html__( 'If enabled, main form fields will try to remain in two columns on smaller screens.', 'napoleon' ),
			) );

	// Setting: Force Single-Line Quantity & Button
	$wp_customize->add_setting( 'force_single_line_qty_button', array(
		'default'           => false,
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'absint', // 0 or 1
	) );
	$wp_customize->add_control( 'force_single_line_qty_button', array(
				'type'        => 'checkbox',
				'section'     => 'codplugin_other_settings',
				'label'       => esc_html__( 'Force Single-Line Quantity & Button', 'napoleon' ),
				'description' => esc_html__( 'If enabled, quantity input and main order button will each take a full line, regardless of screen size.', 'napoleon' ),
			) );
	// End New "Other Settings" Section

	$wp_customize->add_section(
			'plugin_thanks',
		array(
				'title'       => esc_html__( 'Thank You Page', 'napoleon' ),
				'panel'       => 'plugin_settings',
				'priority'    => 60, // Assign priority
			)
	);

			$wp_customize->add_setting( 'thanks_related_products', array(
				'default'           => false,
				'capability'        => 'edit_theme_options',
			) );
				$wp_customize->add_control( 'thanks_related_products', array(
					'label'	  => __( 'Display related products in thank you page', 'napoleon' ),
					'section' => 'plugin_thanks',
					'type'    => 'checkbox',
				) );

			$wp_customize->add_setting( 'enable_fast_thanks', array(
				'default'           => false,
				'capability'        => 'edit_theme_options',
			) );
				$wp_customize->add_control( 'enable_fast_thanks', array(
					'label'	  => __( 'Enable fast thanks page', 'napoleon' ),
					'section' => 'plugin_thanks',
					'type'    => 'checkbox',
				) );

		    $wp_customize->add_setting( 'thanks_editor', array(
		        'default'           => '',
		        'sanitize_callback' => 'wp_kses_post',
		        'transport'         => 'postMessage',
		    ));

		    // Add a new control
	$wp_customize->add_control( 
		new WP_Customize_Control( 
			$wp_customize, 
			'thanks_control', 
			array(
				'label'    => __( 'Thanks Page Content', 'napoleon' ),
				'section'  => 'plugin_thanks',
				'settings' => 'thanks_editor',
				'type'     => 'textarea',
			) 
		) 
	);


	$wp_customize->add_section(
			'plugin_whatsapp',
			array(
				'title'       => esc_html__( 'Whatsapp Orders', 'napoleon' ),
				'panel'       => 'plugin_settings',
				'priority'    => 70, // Assign priority
			)
	);

			$wp_customize->add_setting( 'whatsapp_number', array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
			) );
				$wp_customize->add_control( 'whatsapp_number', array(
					'label'	  => __( 'Add Your Whatsapp Number', 'napoleon' ),
					'section' => 'plugin_whatsapp',
					'type'    => 'text',
				) );

			$wp_customize->add_setting( 'whatsapp_text', array(
				'default'           => __('Order from Whatsapp','napoleon'),
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
			) );
				$wp_customize->add_control( 'whatsapp_text', array(
					'label'	  => __( 'Add Whatsapp button text', 'napoleon' ),
					'section' => 'plugin_whatsapp',
					'type'    => 'text',
				) );

		// REMOVED: Main definition of codform_file_upload_section as its controls are moved.

		// Setting: Enable File Upload (Control already moved to plugin_fields_display_options)
	$wp_customize->add_setting( 'codform_enable_file_upload', array(
			'default'           => false,
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'absint',
	) );
			$wp_customize->add_control( 'codform_enable_file_upload', array(
				'label'	  => __( 'Enable File Upload Field', 'napoleon' ),
				'section' => 'plugin_fields_display_options', // Moved
				'type'    => 'checkbox',
			) );

	// Setting: File Upload Label (Control already moved)
	$wp_customize->add_setting( 'codform_file_upload_label', array(
			'default'           => esc_html__( 'Upload File', 'napoleon' ),
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
	) );
			$wp_customize->add_control( 'codform_file_upload_label', array(
				'label'	  => __( 'File Upload Field Label', 'napoleon' ),
				'section' => 'plugin_fields_display_options', // Moved
				'type'    => 'text',
				'active_callback' => '__return_true',
			) );

	// Setting: Allowed File Types
	$wp_customize->add_setting( 'codform_allowed_file_types', array(
			'default'           => 'jpg,png,pdf',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
	) );
			$wp_customize->add_control( 'codform_allowed_file_types', array(
				'label'	      => __( 'Allowed File Types (comma-separated)', 'napoleon' ),
				'description' => esc_html__( 'E.g., jpg,png,pdf,docx', 'napoleon' ),
				'section'     => 'plugin_fields_display_options', // Moved
				'type'        => 'text',
				'active_callback' => '__return_true',
			) );

	// Setting: Max File Size (MB)
	$wp_customize->add_setting( 'codform_max_file_size_mb', array(
			'default'           => 10,
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'absint',
	) );
			$wp_customize->add_control( 'codform_max_file_size_mb', array(
				'label'	      => __( 'Maximum File Size (MB)', 'napoleon' ),
				'section'     => 'plugin_fields_display_options', // Moved
				'type'        => 'number',
				'input_attrs' => array(
					'min' => 1,
					'max' => 1,
					'step' => 1,
				),
				'active_callback' => '__return_true',
			) );

	// Setting: File Upload Button Text
	$wp_customize->add_setting( 'codform_file_upload_button_text', array(
			'default'           => esc_html__( 'Choose File', 'napoleon' ),
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
	) );
			$wp_customize->add_control( 'codform_file_upload_button_text', array(
				'label'	  => __( 'File Upload Button Text', 'napoleon' ),
				'section' => 'plugin_fields_display_options', // Moved
				'type'    => 'text',
				'active_callback' => '__return_true',
			) );

	// Setting: Make File Upload Required
	$wp_customize->add_setting( 'codform_file_upload_required', array(
			'default'           => false,
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'absint', // 0 or 1
	) );
			$wp_customize->add_control( 'codform_file_upload_required', array(
				'type'            => 'checkbox',
				'section'         => 'plugin_fields_display_options', // Moved
				'label'           => esc_html__( 'Make file upload required?', 'napoleon' ),
				'active_callback' => '__return_true', 
			) );

}


if ( ! function_exists( 'codplugin_custom_styles' ) ) :
/**
 * Custom style declarations
 * and custom user defined CSS in document <head>
 *
 * Outputs CSS declarations generated by theme options
 *
 * @since codplugin 1.0
 */
function codplugin_custom_styles() { ?>

	<!--Customizer CSS--> 
	<style>


	#codplugin-checkout {border-color: <?php echo esc_html( get_theme_mod( 'accent_color', '#4C3BCF') ); ?>; }

	.form-qte, #codplugin_add_button, #codplugin_count_button, #codplugin_remove_button, #codplugin_woo_single_form input, #codplugin_state, #codplugin_city, div#codplugin_order_history, #codplugin-checkout td, #codplugin-checkout .chosen-single, #codplugin-checkout .chosen-container .chosen-drop, .radio-variation-prices tr, .radio-variation-prices input[type="radio"]:before  {border-color: <?php echo esc_html( get_theme_mod( 'secondary_color', '#bce0f7') ); ?> !important; }

		span#codplugin_h_o i, div#codplugin_h_left i { color: <?php echo esc_html( get_theme_mod( 'secondary_color', '#bce0f7') ); ?>; }

		 div#codplugin_order_history,
		 #codplugin_show_hide, .radio-variation-prices tbody { background-color: <?php echo esc_html( get_theme_mod( 'order_summary_background', '#f0f9ff') ); ?>; }

	#codplugin-checkout, #codplugin_woo_single_form input, #codplugin_state, #codplugin_city {background-color: <?php echo esc_html( get_theme_mod( 'form_background_color', '#ffffff') ); ?>; }

	#codplugin-checkout,#codplugin_add_button, #codplugin_count_button, #codplugin_remove_button, #codplugin_woo_single_form input::placeholder, #codplugin_state, #codplugin_city, #codplugin-checkout .chosen-single {color: <?php echo esc_html( get_theme_mod( 'text_color', '#404040') ); ?>; }

		input#nrwooconfirm, .sticky-atc-btn a, #codplugin_count_number, #codplugin_d_free span, #nrwooconfirm.atc-buy-button .button,
#nrwooconfirm.atc-buy-button input, .radio-variation-prices input[type="radio"]:checked::before {background-color: <?php echo esc_html( get_theme_mod( 'button_color', '#4C3BCF') ) ; ?> !important; }

	/* Price colors */
	#codplugin-checkout .variation-prices bdi, 
	#codplugin-checkout .full-price td:last-child { /* Total Price uses Button Color */
			color: <?php echo esc_html( get_theme_mod( 'button_color', '#4C3BCF') ) ; ?> !important; 
		}
		/* Removed rule that forced accent color on delivery price */
	/* #codplugin_show_hide span#codplugin_d_price { color: <?php echo esc_html( get_theme_mod( 'accent_color', '#4C3BCF') ); ?> !important; } */
		
		/* Text alignment for price cells */
	#codplugin_show_hide td#codplugin_d_has_price,
	#codplugin-checkout .full-price td:last-child { 
			text-align: right; 
		}
		.rtl #codplugin_show_hide td#codplugin_d_has_price,
	.rtl #codplugin-checkout .full-price td:last-child { 
			text-align: left; 
		}


	/* Variation Radio Styles */
	.radio-variation-prices input[type="radio"]:checked::before,
	.radio-variation-prices tr.checked-var {
			border-color: <?php echo esc_html( get_theme_mod( 'button_color', '#4C3BCF') ) ; ?> !important;
		}
	.checked::after {
			background-color: <?php echo esc_html( get_theme_mod( 'button_color', '#4C3BCF') ) ; ?> !important;
		}
	/* === End Variation Radio Styles === */


		/* === Customizations for Order Notes & Shipping Radios === */

	/* Order Notes Border */
	#order-notes {
			border-color: <?php echo esc_html( get_theme_mod( 'secondary_color', '#bce0f7') ); ?> !important;
	}

	/* Shipping Radio Unchecked Border (Applies to both presets) */
	.codplugin-shipping-label::before {
			border-color: <?php echo esc_html( get_theme_mod( 'secondary_color', '#bce0f7') ); ?> !important;
	}

	/* Shipping Radio Checked State (using inset shadow) */
	.codform-preset-1 .codplugin-shipping-input:checked + .codplugin-shipping-label::before {
			background-color: <?php echo esc_html( get_theme_mod( 'button_color', '#4C3BCF') ); ?> !important; /* Reverted to Button Color */
			border-color: <?php echo esc_html( get_theme_mod( 'button_color', '#4C3BCF') ); ?> !important; /* Reverted to Button Color */
			box-shadow: inset 0 0 2px #fff !important; /* Small gap */
		}
	/* Shipping Radio Focus State */
	.codform-preset-1 .codplugin-shipping-input:focus + .codplugin-shipping-label::before {
			border-color: <?php echo esc_html( get_theme_mod( 'button_color', '#4C3BCF') ); ?> !important; /* Reverted to Button Color */
            outline: none;
		}
		/* --- End Preset 1 Styles --- */


	/* --- Preset 2 Styles (Horizontal - Background Fill) --- */
	/* Shipping Radio Checked State (background fill) */
	.codform-preset-2 .codplugin-shipping-input:checked + .codplugin-shipping-label::before {
			background-color: <?php echo esc_html( get_theme_mod( 'button_color', '#4C3BCF') ); ?> !important; /* Reverted to Button Color */
			border-color: <?php echo esc_html( get_theme_mod( 'button_color', '#4C3BCF') ); ?> !important; /* Reverted to Button Color */
			/* box-shadow: none !important; */ /* Ensure no inset shadow */
		}
        /* Text color for selected label in preset 2 */
        .codform-preset-2 .codplugin-shipping-input:checked + .codplugin-shipping-label {
            color: #fff !important; /* Or maybe use a text color setting? Using white for now. */
        }
	/* Shipping Radio Focus State */
	.codform-preset-2 .codplugin-shipping-input:focus + .codplugin-shipping-label::before {
			border-color: <?php echo esc_html( get_theme_mod( 'button_color', '#4C3BCF') ); ?> !important; /* Reverted to Button Color */
            outline: none;
		}
        /* --- End Preset 2 Styles --- */

		/* === End Customizations === */

	/* === File Upload Field Styles === */
	.codform-custom-file-input {
			border: 2px solid <?php echo esc_html( get_theme_mod( 'secondary_color', '#bce0f7') ); ?> !important; /* Increased border thickness */
			padding: 0; 
			display: flex; 
			align-items: stretch; /* Children will stretch to container height */
			min-height: 36px; /* Adjust this value to match other form inputs */
		}
	button.codform-file-upload-button {
			border: none !important; 
			background-color: <?php echo esc_html( get_theme_mod( 'button_color', '#4C3BCF') ) ; ?> !important;
			color: #fff !important; 
			padding: 0 12px !important; /* Horizontal padding, vertical handled by stretch */
            display: flex;
            align-items: center;
            justify-content: center;
			margin-right: 8px !important; 
			margin-left: 0; 
			cursor: pointer;
		}
	button.codform-file-upload-button i.fa-file-arrow-up {
			margin-right: 5px; /* Gap between icon and text for LTR */
			font-size: 1.1em; /* Make icon slightly larger */
			position: relative;
			top: -1px; /* Nudge icon up */
		}
	button.codform-file-upload-button:hover {
			opacity: 0.9;
		}
	span.codform-file-upload-filename {
			color: <?php echo esc_html( get_theme_mod( 'text_color', '#404040') ); ?>;
			font-size: 0.9em;
            display: flex; 
            align-items: center; 
            padding-left: 3px; /* Small padding to not touch button if margin is removed in RTL */
		}

	/* RTL Specific styles for file upload */
	.rtl .codform-custom-file-input button.codform-file-upload-button {
			margin-right: 0 !important;
			margin-left: 8px !important; 
		}
		.rtl button.codform-file-upload-button i.fa-file-arrow-up {
			margin-right: 0;
			margin-left: 5px; /* Gap for RTL */
			position: relative; /* Ensure consistency for positioning context */
			top: -1px; /* Nudge icon up for RTL too */
		}
        .rtl span.codform-file-upload-filename {
            padding-left: 0;
            padding-right: 3px; /* Small padding for RTL */
        }
	/* === End File Upload Field Styles === */

	</style>
	<!-- // Customizer CSS--> 
<?php }
endif;

add_action( 'wp_head', 'codplugin_custom_styles' );

// Pass payment grid settings to JavaScript
add_action( 'wp_head', 'codplugin_payment_grid_settings', 5 );
function codplugin_payment_grid_settings() {
	$columns = get_theme_mod( 'napoleon_payment_columns', 4 );
	
	?>
	<script type="text/javascript">
		var payment_grid_settings = {
			columns: <?php echo (int) $columns; ?>
		};
		console.log('Payment grid settings loaded:', payment_grid_settings);
	</script>
	<?php
}
