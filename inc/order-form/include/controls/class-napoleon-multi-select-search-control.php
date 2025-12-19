<?php
/**
 * Customizer Multi-Select Searchable Control
 *
 * @package Napoleon
 */

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

/**
 * Class Napoleon_Multi_Select_Search_Control
 *
 * Custom control for a multi-select dropdown with search.
 */
class Napoleon_Multi_Select_Search_Control extends WP_Customize_Control {

	/**
	 * The type of control being rendered
	 *
	 * @var string
	 */
	public $type = 'napoleon_multi_select_searchable';

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @return void
	 */
	public function enqueue() {
		// We'll enqueue Select2 and custom JS via `customize_controls_enqueue_scripts` action
		// to ensure they are loaded correctly in the Customizer.
	}

	/**
	 * Render the control's content.
	 *
	 * Allows the content to be overriden without having to rewrite the wrapper.
	 *
	 * @return void
	 */
	protected function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}

		$input_id = '_customize-input-' . $this->id;
		$description_id = '_customize-description-' . $this->id;
		$control_name = '_customize-control-' . $this->id . '-content';
		?>
		<label for="<?php echo esc_attr( $input_id ); ?>">
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>
		</label>

		<select
			id="<?php echo esc_attr( $input_id ); ?>"
			class="customize-control-multi-select-searchable"
			multiple="multiple"
			style="width:100%;"
			<?php $this->link(); // This links the select to the setting value ?>
		>
			<?php foreach ( $this->choices as $value => $label ) : ?>
				<?php
				// The $this->value() returns the saved setting value.
				// For a multi-select, it's an array of selected values.
				$selected = ( is_array( $this->value() ) && in_array( (string) $value, $this->value(), true ) ) ? 'selected="selected"' : '';
				?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php echo esc_html($selected); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
}
