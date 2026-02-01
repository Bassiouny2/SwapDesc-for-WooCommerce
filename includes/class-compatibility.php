<?php
namespace PVT;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Compatibility {
    public static function init() {
        add_action( 'init', [ __CLASS__, 'detect_theme_and_plugins' ] );
        add_action( 'wp', [ __CLASS__, 'elementor_hooks' ] );
    }

    public static function detect_theme_and_plugins() {
        // Placeholder: in future, can adjust behaviors based on theme
    }

    public static function elementor_hooks() {
        // After Elementor renders product content, ensure our container remains and JS swaps description
        if ( defined( 'ELEMENTOR_VERSION' ) && class_exists( '\\Elementor\\Plugin' ) ) {
            add_action( 'elementor/frontend/after_render', function( $widget ) {
                try {
                    $name = method_exists( $widget, 'get_name' ) ? $widget->get_name() : '';
                    if ( 'woocommerce-product-content' === $name || 'woocommerce-product-tabs' === $name ) {
                        // Nothing specific required; our JS listens to variation changes
                    }
                } catch ( \Exception $e ) {
                    // Silently fail to avoid breaking rendering
                }
            }, 10 );
        }
    }
}