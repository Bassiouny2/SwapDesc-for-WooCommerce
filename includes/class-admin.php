<?php

namespace PVT;

if (! defined('ABSPATH')) {
    exit;
}

class Admin
{
    public static function init()
    {
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_assets']);
        add_action('admin_menu', [__CLASS__, 'register_dashboard_page']);
        add_action('woocommerce_product_after_variable_attributes', [__CLASS__, 'render_variation_description_field'], 10, 3);
        add_action('woocommerce_save_product_variation', [__CLASS__, 'save_variation_description_field'], 10, 2);
        add_filter('woocommerce_available_variation', [__CLASS__, 'add_variation_description_to_payload'], 10, 3);
    }

    public static function enqueue_admin_assets($hook)
    {
        if ('woocommerce_page_pvt-dashboard' === $hook) {
            wp_enqueue_style('pvt-admin', PVT_PLUGIN_URL . 'assets/admin.css', [], '1.1.0');
            return;
        }
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }
        $screen = get_current_screen();
        if (empty($screen) || 'product' !== $screen->id) {
            return;
        }
        wp_enqueue_style('pvt-admin', PVT_PLUGIN_URL . 'assets/admin.css', [], '1.1.0');
    }

    public static function register_dashboard_page()
    {
        add_submenu_page(
            'woocommerce',
            __('Variation Descriptions', 'product-var-trea'),
            __('Variation Descriptions', 'product-var-trea'),
            'manage_woocommerce',
            'pvt-dashboard',
            [__CLASS__, 'render_dashboard_page']
        );
    }

    public static function render_dashboard_page()
    {
        $notice = '';
        if (isset($_POST['pvt_style_save'])) {
            if (current_user_can('manage_woocommerce')) {
                check_admin_referer('pvt_style_settings', 'pvt_style_nonce');
                $settings = self::sanitize_style_settings($_POST);
                update_option('pvt_style_font_family', $settings['font_family']);
                update_option('pvt_style_font_size', $settings['font_size']);
                update_option('pvt_style_font_weight', $settings['font_weight']);
                update_option('pvt_style_text_color', $settings['text_color']);
                update_option('pvt_style_background_color', $settings['background_color']);
                update_option('pvt_style_padding', $settings['padding']);
                update_option('pvt_style_margin', $settings['margin']);
                update_option('pvt_style_border_width', $settings['border_width']);
                update_option('pvt_style_border_style', $settings['border_style']);
                update_option('pvt_style_border_color', $settings['border_color']);
                update_option('pvt_style_border_radius', $settings['border_radius']);
                $notice = esc_html__('Style settings saved.', 'product-var-trea');
            }
        }
        global $wpdb;
        $meta_key = '_pvt_variation_description';
        $variation_count = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE pm.meta_key = %s AND pm.meta_value <> '' AND p.post_type = 'product_variation'",
                $meta_key
            )
        );
        $product_count = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(DISTINCT p.post_parent) FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE pm.meta_key = %s AND pm.meta_value <> '' AND p.post_type = 'product_variation' AND p.post_parent <> 0",
                $meta_key
            )
        );
        $last_updated = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT MAX(p.post_modified_gmt) FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE pm.meta_key = %s AND pm.meta_value <> '' AND p.post_type = 'product_variation'",
                $meta_key
            )
        );
        $last_updated_display = $last_updated ? get_date_from_gmt($last_updated, 'Y-m-d H:i') : '';
        $style_settings = Shortcodes::get_option_style_settings();
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Variation Descriptions', 'product-var-trea') . '</h1>';
        if ($notice) {
            echo '<div class="notice notice-success is-dismissible"><p>' . $notice . '</p></div>';
        }
        echo '<div class="pvt-dashboard">';
        echo '<div class="pvt-dashboard-cards">';
        echo '<div class="pvt-dashboard-card"><div class="pvt-dashboard-value">' . esc_html($variation_count) . '</div><div class="pvt-dashboard-label">' . esc_html__('Variations with description', 'product-var-trea') . '</div></div>';
        echo '<div class="pvt-dashboard-card"><div class="pvt-dashboard-value">' . esc_html($product_count) . '</div><div class="pvt-dashboard-label">' . esc_html__('Products affected', 'product-var-trea') . '</div></div>';
        echo '<div class="pvt-dashboard-card"><div class="pvt-dashboard-value">' . esc_html($last_updated_display ? $last_updated_display : '—') . '</div><div class="pvt-dashboard-label">' . esc_html__('Last update (GMT)', 'product-var-trea') . '</div></div>';
        echo '</div>';
        echo '<div class="pvt-dashboard-section">';
        echo '<h2>' . esc_html__('Usage', 'product-var-trea') . '</h2>';
        echo '<p><code>[pvt_description]</code></p>';
        echo '<p>' . esc_html__('Place the shortcode on the single product page where you want the description to appear.', 'product-var-trea') . '</p>';
        echo '<p>' . esc_html__( 'Elementor: use the widget named “SwapDesc Description”.', 'product-var-trea' ) . '</p>';
        echo '</div>';
        echo '<div class="pvt-dashboard-section">';
        echo '<h2>' . esc_html__('Shortcode Style Settings', 'product-var-trea') . '</h2>';
        echo '<p>' . esc_html__('Background color applies only when the description is visible.', 'product-var-trea') . '</p>';
        echo '<form method="post">';
        wp_nonce_field('pvt_style_settings', 'pvt_style_nonce');
        echo '<table class="form-table">';
        echo '<tr><th scope="row"><label for="pvt_style_font_family">' . esc_html__('Font Family', 'product-var-trea') . '</label></th><td><input type="text" id="pvt_style_font_family" name="pvt_style_font_family" class="regular-text" value="' . esc_attr($style_settings['font_family']) . '"></td></tr>';
        echo '<tr><th scope="row"><label for="pvt_style_font_size">' . esc_html__('Font Size', 'product-var-trea') . '</label></th><td><input type="text" id="pvt_style_font_size" name="pvt_style_font_size" class="regular-text" value="' . esc_attr($style_settings['font_size']) . '"></td></tr>';
        echo '<tr><th scope="row"><label for="pvt_style_font_weight">' . esc_html__('Font Weight', 'product-var-trea') . '</label></th><td><input type="text" id="pvt_style_font_weight" name="pvt_style_font_weight" class="regular-text" value="' . esc_attr($style_settings['font_weight']) . '"></td></tr>';
        echo '<tr><th scope="row"><label for="pvt_style_text_color">' . esc_html__('Text Color', 'product-var-trea') . '</label></th><td><input type="text" id="pvt_style_text_color" name="pvt_style_text_color" class="regular-text" value="' . esc_attr($style_settings['text_color']) . '"></td></tr>';
        echo '<tr><th scope="row"><label for="pvt_style_background_color">' . esc_html__('Background Color', 'product-var-trea') . '</label></th><td><input type="text" id="pvt_style_background_color" name="pvt_style_background_color" class="regular-text" value="' . esc_attr($style_settings['background_color']) . '"></td></tr>';
        echo '<tr><th scope="row"><label for="pvt_style_padding">' . esc_html__('Padding', 'product-var-trea') . '</label></th><td><input type="text" id="pvt_style_padding" name="pvt_style_padding" class="regular-text" value="' . esc_attr($style_settings['padding']) . '"></td></tr>';
        echo '<tr><th scope="row"><label for="pvt_style_margin">' . esc_html__('Margin', 'product-var-trea') . '</label></th><td><input type="text" id="pvt_style_margin" name="pvt_style_margin" class="regular-text" value="' . esc_attr($style_settings['margin']) . '"></td></tr>';
        echo '<tr><th scope="row"><label for="pvt_style_border_width">' . esc_html__('Border Width', 'product-var-trea') . '</label></th><td><input type="text" id="pvt_style_border_width" name="pvt_style_border_width" class="regular-text" value="' . esc_attr($style_settings['border_width']) . '"></td></tr>';
        echo '<tr><th scope="row"><label for="pvt_style_border_style">' . esc_html__('Border Style', 'product-var-trea') . '</label></th><td><input type="text" id="pvt_style_border_style" name="pvt_style_border_style" class="regular-text" value="' . esc_attr($style_settings['border_style']) . '"></td></tr>';
        echo '<tr><th scope="row"><label for="pvt_style_border_color">' . esc_html__('Border Color', 'product-var-trea') . '</label></th><td><input type="text" id="pvt_style_border_color" name="pvt_style_border_color" class="regular-text" value="' . esc_attr($style_settings['border_color']) . '"></td></tr>';
        echo '<tr><th scope="row"><label for="pvt_style_border_radius">' . esc_html__('Border Radius', 'product-var-trea') . '</label></th><td><input type="text" id="pvt_style_border_radius" name="pvt_style_border_radius" class="regular-text" value="' . esc_attr($style_settings['border_radius']) . '"></td></tr>';
        echo '</table>';
        submit_button(__('Save Style Settings', 'product-var-trea'), 'primary', 'pvt_style_save');
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    private static function sanitize_style_settings($data)
    {
        $allowed_border_styles = ['', 'none', 'solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset'];
        $allowed_font_weights = ['', 'normal', 'bold', 'bolder', 'lighter', '100', '200', '300', '400', '500', '600', '700', '800', '900'];
        $font_weight = sanitize_text_field(isset($data['pvt_style_font_weight']) ? $data['pvt_style_font_weight'] : '');
        if (! in_array($font_weight, $allowed_font_weights, true)) {
            $font_weight = '';
        }
        $border_style = sanitize_text_field(isset($data['pvt_style_border_style']) ? $data['pvt_style_border_style'] : '');
        if (! in_array($border_style, $allowed_border_styles, true)) {
            $border_style = '';
        }
        $text_color = sanitize_hex_color(isset($data['pvt_style_text_color']) ? $data['pvt_style_text_color'] : '');
        $background_color = sanitize_hex_color(isset($data['pvt_style_background_color']) ? $data['pvt_style_background_color'] : '');
        $border_color = sanitize_hex_color(isset($data['pvt_style_border_color']) ? $data['pvt_style_border_color'] : '');
        return [
            'font_family'      => sanitize_text_field(isset($data['pvt_style_font_family']) ? $data['pvt_style_font_family'] : ''),
            'font_size'        => sanitize_text_field(isset($data['pvt_style_font_size']) ? $data['pvt_style_font_size'] : ''),
            'font_weight'      => $font_weight,
            'text_color'       => $text_color ? $text_color : '',
            'background_color' => $background_color ? $background_color : '',
            'padding'          => sanitize_text_field(isset($data['pvt_style_padding']) ? $data['pvt_style_padding'] : ''),
            'margin'           => sanitize_text_field(isset($data['pvt_style_margin']) ? $data['pvt_style_margin'] : ''),
            'border_width'     => sanitize_text_field(isset($data['pvt_style_border_width']) ? $data['pvt_style_border_width'] : ''),
            'border_style'     => $border_style,
            'border_color'     => $border_color ? $border_color : '',
            'border_radius'    => sanitize_text_field(isset($data['pvt_style_border_radius']) ? $data['pvt_style_border_radius'] : ''),
        ];
    }

    // Render a rich text editor per variation in admin
    public static function render_variation_description_field($loop, $variation_data, $variation)
    {
        echo '<div class="form-row form-row-full">';
        echo '<label>' . esc_html__('Variation Long Description', 'product-var-trea') . '</label>';

        $content = get_post_meta($variation->ID, '_pvt_variation_description', true);
        wp_editor($content, 'pvt_variation_description_' . $variation->ID, [
            'textarea_name' => 'pvt_variation_description[' . $variation->ID . ']',
            'textarea_rows' => 6,
            'tinymce'       => true,
            'quicktags'     => true,
            'media_buttons' => true,
        ]);
        echo '</div>';
    }

    public static function save_variation_description_field($variation_id, $i)
    {
        if (isset($_POST['pvt_variation_description'][$variation_id])) {
            $content = wp_kses_post($_POST['pvt_variation_description'][$variation_id]);
            update_post_meta($variation_id, '_pvt_variation_description', $content);
        }
    }

    // Push description into variation payload for AJAX on the frontend
    public static function add_variation_description_to_payload($variation, $product, $variation_obj)
    {
        $custom = get_post_meta($variation_obj->get_id(), '_pvt_variation_description', true);
        $native = method_exists($variation_obj, 'get_description') ? $variation_obj->get_description() : '';
        $variation['pvt_description'] = $custom ? $custom : $native;
        return $variation;
    }
}
