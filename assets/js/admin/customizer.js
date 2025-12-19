( function( $ ) {
    $( document ).ready( function() {
        // Tabs functionality
        $( '.napoleon-customize-tabs a' ).on( 'click', function( e ) {
            e.preventDefault();
            var tab = $( this ).data( 'tab' );
            $( this ).parent().addClass( 'active' ).siblings().removeClass( 'active' );
            $( '#customize-control-napoleon_variation_swatches_position, #customize-control-napoleon_variation_swatches_columns' ).toggle( tab === 'global' );
            $( '#customize-control-napoleon_variation_swatches_specific' ).toggle( tab === 'specific' );
        } );
        $( '.napoleon-customize-tabs a' ).first().trigger( 'click' );

        // Repeater functionality
        function napoleon_repeater_init( control ) {
            var container = control.container;
            var value = JSON.parse( container.find( '.repeater-value' ).val() );

            function napoleon_repeater_update_value() {
                container.find( '.repeater-value' ).val( JSON.stringify( value ) ).trigger( 'change' );
            }

            container.on( 'click', '.repeater-add', function( e ) {
                e.preventDefault();
                var new_item = {};
                _.each( control.params.fields, function( field, id ) {
                    new_item[id] = '';
                } );
                value.push( new_item );
                napoleon_repeater_render();
            } );

            container.on( 'click', '.repeater-field-remove', function( e ) {
                e.preventDefault();
                var index = $( this ).closest( '.repeater-field' ).data( 'index' );
                value.splice( index, 1 );
                napoleon_repeater_render();
            } );

            container.on( 'change', 'input, select', function() {
                var index = $( this ).closest( '.repeater-field' ).data( 'index' );
                var key = $( this ).data( 'key' );
                value[index][key] = $( this ).val();
                napoleon_repeater_update_value();
            } );

            function napoleon_repeater_render() {
                var template = wp.template( 'napoleon-repeater-field-template' );
                container.find( '.repeater-fields' ).html( '' );
                _.each( value, function( item, index ) {
                    var field = $( template( { item: item, index: index, fields: control.params.fields } ) );
                    container.find( '.repeater-fields' ).append( field );
                    napoleon_product_select_init( field );
                } );
                napoleon_repeater_update_value();
            }

            function napoleon_product_select_init( field ) {
                field.find( '.napoleon-product-select' ).select2( {
                    ajax: {
                        url: ajaxurl,
                        dataType: 'json',
                        delay: 250,
                        data: function( params ) {
                            return {
                                action: 'napoleon_search_products',
                                q: params.term
                            };
                        },
                        processResults: function( data ) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 2
                } );
            }

            napoleon_repeater_render();
        }

        wp.customize.control( 'napoleon_variation_swatches_specific', function( control ) {
            control.deferred.embedded.done( function() {
                napoleon_repeater_init( control );
            } );
        } );
    } );
} )( jQuery );
