<?php

namespace PVT;

if (! defined('ABSPATH')) {
    exit;
}

class Frontend
{
    public static function init()
    {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }

    public static function enqueue_assets()
    {
        if (! is_product()) {
            return;
        }
        wp_enqueue_style('pvt-frontend', PVT_PLUGIN_URL . 'assets/frontend.css', [], '1.1.0');
        wp_enqueue_script('pvt-frontend', PVT_PLUGIN_URL . 'assets/frontend.js', ['jquery'], '1.1.0', true);
        wp_localize_script('pvt-frontend', 'PVT_DATA', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'fade'     => true,
        ]);
    }
}
