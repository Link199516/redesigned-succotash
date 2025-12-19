(function( $ ) {
    'use strict';

    wp.customize.bind( 'ready', function() {

        var masterSetting = 'codform_enable_file_upload';
        var dependentControls = [
            'codform_file_upload_label',
            'codform_allowed_file_types',
            'codform_max_file_size_mb',
            'codform_file_upload_button_text',
            'codform_file_upload_required' // Added new control ID
        ];

        function updateVisibility() {
            // Ensure wp.customize(masterSetting) exists before calling .get()
            var masterSettingInstance = wp.customize(masterSetting);
            if (!masterSettingInstance) {
                // console.warn('Master Customizer setting ' + masterSetting + ' not found.');
                return;
            }
            var show = masterSettingInstance.get();

            $.each(dependentControls, function(i, controlId) {
                var control = wp.customize.control(controlId);
                if (control && control.container) { // Check for control.container
                    // The toggle method expects a boolean. 'show' is already 0 or 1 (false or true from absint).
                    control.container.toggle(!!show); 
                } else {
                    // console.warn('Dependent Customizer control ' + controlId + ' or its container not found.');
                }
            });
        }

        // When the Customizer is ready, update visibility.
        // This should run after PHP has rendered the controls.
        updateVisibility();

        // When the master setting changes, update visibility.
        wp.customize(masterSetting, function(setting) {
            setting.bind(updateVisibility);
        });
    });

})( jQuery );
