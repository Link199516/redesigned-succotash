<?php
/**
 * Variation Swatches Product-Level Support Only
 *
 * @package Napoleon
 * Note: Global variation swatches controls removed - only product-level settings used
 */

// Add inline CSS for variation swatches (product-level only)
add_action( 'wp_head', 'napoleon_variation_swatches_inline_style' );
function napoleon_variation_swatches_inline_style() {
    global $post;
    $product_id = 0;
    if ( is_product() ) {
        $product = wc_get_product( $post->ID );
        if ( $product ) {
            $product_id = $product->get_id();
        }
    }

    $position = 'bottom'; // Default position
    $columns  = 4; // Default columns
    $hide_label = false; // Default to show label

    if ( $product_id ) {
        $use_specific = get_post_meta( $product_id, '_napoleon_use_specific_variations', true );
        if ( $use_specific ) {
            $specific_position = get_post_meta( $product_id, '_napoleon_variation_position', true );
            $specific_columns  = get_post_meta( $product_id, '_napoleon_variation_columns', true );
            $specific_hide_label = get_post_meta( $product_id, '_napoleon_hide_variation_label', true );

            if ( $specific_position ) {
                $position = $specific_position;
            }
            if ( $specific_columns ) {
                $columns = $specific_columns;
            }
            if ( '' !== $specific_hide_label ) {
                $hide_label = (bool) $specific_hide_label;
            }
        }
    }
    ?>
    <style>
        #codplugin-checkout {
            display: flex;
            flex-direction: column;
        }

        <?php if ( 'top' === $position ) : ?>
        #codplugin-checkout .variations_form {
            order: -1;
            margin-bottom: 20px;
        }
        <?php endif; ?>

        /* Clean Swatch Container - Remove conflicts with 3rd party plugin */
        .cfvsw-swatches-container.cfvsw-product-container {
            display: grid !important;
            grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, minmax(0, min-content));
            grid-auto-rows: auto;
            gap: 10px;
            width: fit-content;
            max-width: 100%;
            margin-bottom: 10px;
            justify-content: start;
        }
        
        /* Ensure variations table takes full width */
        .variations {
            width: 100%;
        }
        
        /* Label */
        .variations tr .label{
            border-width:0px;
            <?php if ( $hide_label ) : ?>
            display: none;
            <?php endif; ?>
        }

        /* Hide the inline selected label that appears inside the main label */
        .variations .label .cfvsw-selected-label {
            display: none !important;
        }

        /* Fix variations table layout - ensure proper display */
        .variations {
            width: 100%;
            display: table;
        }

        .variations tr {
            display: table-row;
        }

        .variations th.label,
        .variations td.value {
            display: table-cell;
            vertical-align: top;
        }

        /* Label column - fixed width */
        .variations th.label {
            width: 100px;
            padding-right: 15px;
        }

        /* Value column - takes remaining space */
        .variations td.value {
            width: auto;
        }

        /* RTL adjustments */
        .rtl .variations th.label {
            padding-right: 0;
            padding-left: 15px;
        }

        /* Remove width constraint - let grid container control sizing */
        .variations td.value {
            /* Removed width: 100% - this was causing the stretch issue */
        }

        /* Professional Swatch Sizing - Fix for different types */
        .cfvsw-swatches-option {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 200ms;
            text-align: center;
            cursor: pointer;
            border: 1px solid #fff;
            background: #fff;
            padding: 1px;
            user-select: none;
            overflow: hidden;
        }

        /* Text swatches - size to content */
        .cfvsw-label-option {
            width: auto !important;
            min-width: fit-content;
            max-width: 100%;
            height: auto !important;
            min-height: 40px;
            font-size: var(--cfvsw-swatches-font-size, 12px);
            border-width: var(--cfvsw-swatches-border-width, 1px);
            padding: 8px 12px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* Image and Color swatches - respects WooCommerce settings with fallback */
        .cfvsw-image-option {
            width: var(--cfvsw-swatch-width, 45px);
            height: var(--cfvsw-swatch-height, 45px);
            min-width: var(--cfvsw-swatch-width, 45px);
            max-width: var(--cfvsw-swatch-width, 45px);
            min-height: var(--cfvsw-swatch-height, 45px);
            max-height: var(--cfvsw-swatch-height, 45px);
            flex-shrink: 0 !important;
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            border-radius: 4px;
        }

        /* Swatch inner content - proper sizing */
        .cfvsw-swatches-option .cfvsw-swatch-inner {
            display: flex;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
            border-radius: inherit;
            transition: 200ms;
            box-sizing: border-box;
        }

        /* Label swatch inner - text handling */
        .cfvsw-label-option .cfvsw-swatch-inner {
            white-space: nowrap;
            word-break: keep-all;
            line-height: 1.2;
        }

        /* Hide default variation links */
        .variations tr a{
            display: none !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .cfvsw-swatches-container.cfvsw-product-container {
                grid-template-columns: repeat(<?php echo esc_attr( min($columns, 2) ); ?>, minmax(0, min-content));
                gap: 8px;
            }
            
            .cfvsw-label-option {
                font-size: 11px;
                padding: 6px 10px;
            }
            
            .cfvsw-image-option {
                width: var(--cfvsw-swatch-width, 40px);
                height: var(--cfvsw-swatch-height, 40px);
                min-width: var(--cfvsw-swatch-width, 40px);
                max-width: var(--cfvsw-swatch-width, 40px);
                min-height: var(--cfvsw-swatch-height, 40px);
                max-height: var(--cfvsw-swatch-height, 40px);
            }
        }

        @media (max-width: 480px) {
            .cfvsw-swatches-container.cfvsw-product-container {
                grid-template-columns: repeat(<?php echo esc_attr( min($columns, 2) ); ?>, minmax(0, min-content));
                gap: 6px;
            }
            
            .cfvsw-label-option {
                font-size: 10px;
                padding: 4px 8px;
            }
            
            .cfvsw-image-option {
                width: var(--cfvsw-swatch-width, 35px);
                height: var(--cfvsw-swatch-height, 35px);
                min-width: var(--cfvsw-swatch-width, 35px);
                max-width: var(--cfvsw-swatch-width, 35px);
                min-height: var(--cfvsw-swatch-height, 35px);
                max-height: var(--cfvsw-swatch-height, 35px);
            }
        }
    </style>
    <?php
}