<?php

namespace PVT;

if (! defined('ABSPATH')) {
    exit;
}

class ElementorIntegration
{
    public static function init()
    {
        add_action('elementor/init', [__CLASS__, 'setup']);
    }

    public static function setup()
    {
        if (defined('ELEMENTOR_VERSION') && class_exists('\\Elementor\\Plugin')) {
            add_action('elementor/widgets/register', [__CLASS__, 'register_widget']);
            add_action('elementor/widgets/widgets_registered', [__CLASS__, 'register_widget_legacy']);
        }
    }

    public static function register_widget($widgets_manager)
    {
        if (! class_exists('\\PVT\\Elementor_Description_Widget')) {
            require_once __DIR__ . '/elementor-widget-description.php';
        }
        $widgets_manager->register(new \PVT\Elementor_Description_Widget());
    }

    public static function register_widget_legacy()
    {
        if (! class_exists('\\Elementor\\Plugin')) {
            return;
        }
        if (! class_exists('\\PVT\\Elementor_Description_Widget')) {
            require_once __DIR__ . '/elementor-widget-description.php';
        }
        $widgets_manager = \Elementor\Plugin::$instance->widgets_manager;
        if (method_exists($widgets_manager, 'register')) {
            $widgets_manager->register(new \PVT\Elementor_Description_Widget());
        } elseif (method_exists($widgets_manager, 'register_widget_type')) {
            $widgets_manager->register_widget_type(new \PVT\Elementor_Description_Widget());
        }
    }
}
