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
        global $post;
        if (! $post) {
            return;
        }
        $product = function_exists('wc_get_product') ? wc_get_product($post->ID) : null;
        if (! $product || ! $product->is_type('variable')) {
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
