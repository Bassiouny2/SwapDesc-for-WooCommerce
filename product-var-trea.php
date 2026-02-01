<?php

/**
 * Plugin Name: SwapDesc for WooCommerce
 * Plugin URI: https://abprojects.org/ahmed-magdy/
 * Description: Allows WooCommerce product variations to have their own long descriptions and dynamically swaps the main product description when a variation is selected. Elementor compatible.
 * Version: 1.1.0
 * Author: Ahmed Magdy
 * Author URI: https://abprojects.org/ahmed-magdy/
 * License: GPLv2 or later
 * Text Domain: product-var-trea
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

// Constants
define('PVT_PLUGIN_FILE', __FILE__);
define('PVT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PVT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Simple dependency check for WooCommerce
add_action('plugins_loaded', function () {
  if (! class_exists('WooCommerce')) {
    add_action('admin_notices', function () {
      echo '<div class="notice notice-error"><p>' . esc_html__('SwapDesc for WooCommerce requires WooCommerce to be installed and active.', 'product-var-trea') . '</p></div>';
    });
    return;
  }

  // Includes
  require_once PVT_PLUGIN_DIR . 'includes/class-admin.php';
  require_once PVT_PLUGIN_DIR . 'includes/class-frontend.php';
  require_once PVT_PLUGIN_DIR . 'includes/class-ajax.php';
  require_once PVT_PLUGIN_DIR . 'includes/class-compatibility.php';
  require_once PVT_PLUGIN_DIR . 'includes/class-shortcodes.php';
  require_once PVT_PLUGIN_DIR . 'includes/class-elementor.php';

  // Bootstrap
  \PVT\Admin::init();
  \PVT\Frontend::init();
  \PVT\Ajax::init();
  \PVT\Compatibility::init();
  \PVT\Shortcodes::init();
  \PVT\ElementorIntegration::init();
});

// Activation: no-op for now, reserved for future DB upgrades
register_activation_hook(__FILE__, function () {});
