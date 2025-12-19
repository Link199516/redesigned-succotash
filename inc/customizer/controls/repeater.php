<?php
/**
 * Customizer Repeater Control.
 *
 * @package Napoleon
 */

if ( class_exists( 'WP_Customize_Control' ) ) {
    class Napoleon_Customize_Repeater_Control extends WP_Customize_Control {
        public $type = 'repeater';
        public $fields = array();

        public function to_json() {
            parent::to_json();
            $this->json['fields'] = $this->fields;
            $this->json['value'] = json_decode( $this->value(), true );
        }

        public function content_template() {
            ?>
            <# if ( data.label ) { #>
                <span class="customize-control-title">{{ data.label }}</span>
            <# } #>
            <# if ( data.description ) { #>
                <span class="description customize-control-description">{{{ data.description }}}</span>
            <# } #>

            <ul class="repeater-fields">
                <# _.each( data.value, function( item, index ) { #>
                    <li class="repeater-field" data-index="{{ index }}">
                        <div class="repeater-field-header">
                            <span class="repeater-field-title"><?php _e( 'Condition', 'napoleon' ); ?> {{ index + 1 }}</span>
                            <a href="#" class="repeater-field-remove"><?php _e( 'Remove', 'napoleon' ); ?></a>
                        </div>
                        <div class="repeater-field-body">
                            <# _.each( data.fields, function( field, id ) { #>
                                <div class="repeater-field-{{ field.type }}">
                                    <label>
                                        <span class="customize-control-title">{{ field.label }}</span>
                                        <# if ( 'select' === field.type ) { #>
                                            <select data-key="{{ id }}">
                                                <# _.each( field.choices, function( label, choice ) { #>
                                                    <option value="{{ choice }}" <# if ( item[id] === choice ) { #>selected="selected"<# } #>>{{ label }}</option>
                                                <# } ) #>
                                            </select>
                                        <# } else if ( 'products' === field.type ) { #>
                                            <select class="napoleon-product-select" multiple="multiple" data-key="{{ id }}" style="width: 100%;">
                                                <# _.each( item[id], function( product_id ) { #>
                                                    <option value="{{ product_id }}" selected="selected">{{ product_id }}</option>
                                                <# } ) #>
                                            </select>
                                        <# } else { #>
                                            <input type="{{ field.type }}" value="{{ item[id] }}" data-key="{{ id }}" />
                                        <# } #>
                                    </label>
                                </div>
                            <# } ) #>
                        </div>
                    </li>
                <# } ) #>
            </ul>

            <button type="button" class="button repeater-add"><?php _e( 'Add New Condition', 'napoleon' ); ?></button>
            <input type="hidden" class="repeater-value" value="{{ JSON.stringify( data.value ) }}" {{{ data.link }}} />

            <script type="text/template" class="napoleon-repeater-field-template">
                <li class="repeater-field" data-index="{{ data.index }}">
                    <div class="repeater-field-header">
                        <span class="repeater-field-title"><?php _e( 'Condition', 'napoleon' ); ?> {{ data.index + 1 }}</span>
                        <a href="#" class="repeater-field-remove"><?php _e( 'Remove', 'napoleon' ); ?></a>
                    </div>
                    <div class="repeater-field-body">
                        <# _.each( data.fields, function( field, id ) { #>
                            <div class="repeater-field-{{ field.type }}">
                                <label>
                                    <span class="customize-control-title">{{ field.label }}</span>
                                    <# if ( 'select' === field.type ) { #>
                                        <select data-key="{{ id }}">
                                            <# _.each( field.choices, function( label, choice ) { #>
                                                <option value="{{ choice }}" <# if ( data.item[id] === choice ) { #>selected="selected"<# } #>>{{ label }}</option>
                                            <# } ) #>
                                        </select>
                                    <# } else if ( 'products' === field.type ) { #>
                                        <select class="napoleon-product-select" multiple="multiple" data-key="{{ id }}" style="width: 100%;">
                                            <# _.each( data.item[id], function( product_id ) { #>
                                                <option value="{{ product_id }}" selected="selected">{{ product_id }}</option>
                                            <# } ) #>
                                        </select>
                                    <# } else { #>
                                        <input type="{{ field.type }}" value="{{ data.item[id] }}" data-key="{{ id }}" />
                                    <# } #>
                                </label>
                            </div>
                        <# } ) #>
                    </div>
                </li>
            </script>
            <?php
        }
    }
}
