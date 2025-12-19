<?php


	$wp_customize->add_setting('font_options', array(
		'transport'         => 'refresh',
        'default' => 		'option1',
        'sanitize_callback' => 'sanitize_font_choice', 
    ));

    $wp_customize->add_control('font_options', array(
        'label'    => __('Select site font:', 'napoleon'),
        'section'  => 'theme_colors_fonts',
        'type'     => 'select',
        'choices'  => array(
            'cairo' => 'Cairo',
            'Avenir Next World' => 'Avenir Next World',
            'IBM Plex Sans Arabic' => 'IBM Plex Sans Arabic',
        ),
    ));


    function sanitize_font_choice($input) {
    $valid_choices = array(
        'cairo' => 'Cairo',
        'Avenir Next World' => 'Avenir Next World',
        'IBM Plex Sans Arabic' => 'IBM Plex Sans Arabic',
    );

    if (array_key_exists($input, $valid_choices)) {
        return $input;
    } 
}


