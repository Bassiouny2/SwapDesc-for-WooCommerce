<?php

namespace PVT;

if (! defined('ABSPATH')) {
    exit;
}

class Shortcodes
{
    public static function init()
    {
        add_shortcode('pvt_description', [__CLASS__, 'render_description_shortcode']);
    }

    public static function get_option_style_settings()
    {
        return [
            'font_family'      => get_option('pvt_style_font_family', ''),
            'font_size'        => get_option('pvt_style_font_size', ''),
            'font_weight'      => get_option('pvt_style_font_weight', ''),
            'text_color'       => get_option('pvt_style_text_color', ''),
            'background_color' => get_option('pvt_style_background_color', ''),
            'padding'          => get_option('pvt_style_padding', ''),
            'margin'           => get_option('pvt_style_margin', ''),
            'border_width'     => get_option('pvt_style_border_width', ''),
            'border_style'     => get_option('pvt_style_border_style', ''),
            'border_color'     => get_option('pvt_style_border_color', ''),
            'border_radius'    => get_option('pvt_style_border_radius', ''),
        ];
    }

    public static function is_dimension_empty($dimension)
    {
        if (! is_array($dimension)) {
            return trim((string) $dimension) === '';
        }
        $keys = ['top', 'right', 'bottom', 'left'];
        foreach ($keys as $key) {
            if (isset($dimension[$key]) && $dimension[$key] !== '') {
                return false;
            }
        }
        return true;
    }

    public static function dimension_to_css($dimension)
    {
        if (! is_array($dimension)) {
            return trim((string) $dimension);
        }
        $unit = isset($dimension['unit']) ? $dimension['unit'] : 'px';
        $values = [];
        foreach (['top', 'right', 'bottom', 'left'] as $key) {
            $val = isset($dimension[$key]) ? $dimension[$key] : '';
            if ($val === '') {
                $values[] = '0';
            } else {
                $values[] = $val . $unit;
            }
        }
        return implode(' ', $values);
    }

    public static function merge_style_settings($overrides = [])
    {
        $defaults = self::get_option_style_settings();
        foreach ($overrides as $key => $value) {
            if (is_array($value)) {
                if (! self::is_dimension_empty($value)) {
                    $defaults[$key] = $value;
                }
            } elseif ($value !== '' && $value !== null) {
                $defaults[$key] = $value;
            }
        }
        return $defaults;
    }

    public static function build_style_attributes($settings)
    {
        $style = [];
        if (! empty($settings['font_family'])) {
            $style[] = 'font-family:' . $settings['font_family'];
        }
        if (! empty($settings['font_size'])) {
            $style[] = 'font-size:' . $settings['font_size'];
        }
        if (! empty($settings['font_weight'])) {
            $style[] = 'font-weight:' . $settings['font_weight'];
        }
        if (! empty($settings['text_color'])) {
            $style[] = 'color:' . $settings['text_color'];
        }
        if (! empty($settings['padding'])) {
            $style[] = 'padding:' . self::dimension_to_css($settings['padding']);
        }
        if (! empty($settings['margin'])) {
            $style[] = 'margin:' . self::dimension_to_css($settings['margin']);
        }
        if (! empty($settings['border_width'])) {
            $style[] = 'border-width:' . self::dimension_to_css($settings['border_width']);
        }
        if (! empty($settings['border_style'])) {
            $style[] = 'border-style:' . $settings['border_style'];
        }
        if (! empty($settings['border_color'])) {
            $style[] = 'border-color:' . $settings['border_color'];
        }
        if (! empty($settings['border_radius'])) {
            $style[] = 'border-radius:' . self::dimension_to_css($settings['border_radius']);
        }
        $background = ! empty($settings['background_color']) ? $settings['background_color'] : '';
        return [
            'style' => implode(';', $style),
            'background' => $background,
        ];
    }

    public static function render_description_shortcode($atts = [], $content = '')
    {
        if (! is_product()) {
            return '';
        }
        global $post;
        if (! $post) {
            return '';
        }
        $product = function_exists('wc_get_product') ? wc_get_product($post->ID) : null;
        if (! $product || ! $product->is_type('variable')) {
            return '';
        }
        $settings = self::get_option_style_settings();
        $attrs = self::build_style_attributes($settings);
        $style_attr = $attrs['style'] ? ' style="' . esc_attr($attrs['style']) . '"' : '';
        $bg_attr = $attrs['background'] ? ' data-pvt-bg="' . esc_attr($attrs['background']) . '"' : '';
        return '<div id="pvt-description-root" class="pvt-description-root" data-product-id="' . esc_attr($post->ID) . '"' . $style_attr . $bg_attr . '>' .
            apply_filters('the_content', get_post_field('post_content', $post->ID)) .
            '</div>';
    }
}
