<?php
/**
 * Customizer Tabs Control.
 *
 * @package Napoleon
 */

if ( class_exists( 'WP_Customize_Control' ) ) {
    class Napoleon_Customize_Tabs_Control extends WP_Customize_Control {
        public $type = 'tabs';
        public $tabs = array();

        public function to_json() {
            parent::to_json();
            $this->json['tabs'] = $this->tabs;
        }

        public function content_template() {
            ?>
            <# if ( data.tabs ) { #>
                <ul class="napoleon-customize-tabs">
                    <# _.each( data.tabs, function( tab, id ) { #>
                        <li class="napoleon-customize-tab <# if ( tab.active ) { #>active<# } #>" data-tab="{{ id }}">
                            <a href="#" data-tab="{{ id }}">{{ tab.label }}</a>
                        </li>
                    <# } ) #>
                </ul>
            <# } #>
            <?php
        }
    }
}
