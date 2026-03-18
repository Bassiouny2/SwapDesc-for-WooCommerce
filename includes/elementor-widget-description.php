<?php
namespace PVT;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Elementor_Description_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'swapdesc_description';
    }

    public function get_title() {
        return __( 'SwapDesc Description', 'product-var-trea' );
    }

    public function get_icon() {
        return 'eicon-text';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {
        $defaults = \PVT\Shortcodes::get_option_style_settings();
        $this->start_controls_section(
            'pvt_style',
            [
                'label' => __( 'Style', 'product-var-trea' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'font_family',
            [
                'label' => __( 'Font Family', 'product-var-trea' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => $defaults['font_family'],
            ]
        );
        $this->add_control(
            'font_size',
            [
                'label' => __( 'Font Size', 'product-var-trea' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => $defaults['font_size'],
            ]
        );
        $this->add_control(
            'font_weight',
            [
                'label' => __( 'Font Weight', 'product-var-trea' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => __( 'Default', 'product-var-trea' ),
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900',
                    'normal' => __( 'Normal', 'product-var-trea' ),
                    'bold' => __( 'Bold', 'product-var-trea' ),
                    'bolder' => __( 'Bolder', 'product-var-trea' ),
                    'lighter' => __( 'Lighter', 'product-var-trea' ),
                ],
                'default' => $defaults['font_weight'],
            ]
        );
        $this->add_control(
            'text_color',
            [
                'label' => __( 'Text Color', 'product-var-trea' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => $defaults['text_color'],
            ]
        );
        $this->add_control(
            'background_color',
            [
                'label' => __( 'Background Color', 'product-var-trea' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => $defaults['background_color'],
            ]
        );
        $this->add_control(
            'padding',
            [
                'label' => __( 'Padding', 'product-var-trea' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
            ]
        );
        $this->add_control(
            'margin',
            [
                'label' => __( 'Margin', 'product-var-trea' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
            ]
        );
        $this->add_control(
            'border_width',
            [
                'label' => __( 'Border Width', 'product-var-trea' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
            ]
        );
        $this->add_control(
            'border_style',
            [
                'label' => __( 'Border Style', 'product-var-trea' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => __( 'Default', 'product-var-trea' ),
                    'none' => __( 'None', 'product-var-trea' ),
                    'solid' => __( 'Solid', 'product-var-trea' ),
                    'dashed' => __( 'Dashed', 'product-var-trea' ),
                    'dotted' => __( 'Dotted', 'product-var-trea' ),
                    'double' => __( 'Double', 'product-var-trea' ),
                    'groove' => __( 'Groove', 'product-var-trea' ),
                    'ridge' => __( 'Ridge', 'product-var-trea' ),
                    'inset' => __( 'Inset', 'product-var-trea' ),
                    'outset' => __( 'Outset', 'product-var-trea' ),
                ],
                'default' => $defaults['border_style'],
            ]
        );
        $this->add_control(
            'border_color',
            [
                'label' => __( 'Border Color', 'product-var-trea' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => $defaults['border_color'],
            ]
        );
        $this->add_control(
            'border_radius',
            [
                'label' => __( 'Border Radius', 'product-var-trea' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
            ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $style_overrides = [
            'font_family'      => isset( $settings['font_family'] ) ? $settings['font_family'] : '',
            'font_size'        => isset( $settings['font_size'] ) ? $settings['font_size'] : '',
            'font_weight'      => isset( $settings['font_weight'] ) ? $settings['font_weight'] : '',
            'text_color'       => isset( $settings['text_color'] ) ? $settings['text_color'] : '',
            'background_color' => isset( $settings['background_color'] ) ? $settings['background_color'] : '',
            'padding'          => isset( $settings['padding'] ) ? $settings['padding'] : '',
            'margin'           => isset( $settings['margin'] ) ? $settings['margin'] : '',
            'border_width'     => isset( $settings['border_width'] ) ? $settings['border_width'] : '',
            'border_style'     => isset( $settings['border_style'] ) ? $settings['border_style'] : '',
            'border_color'     => isset( $settings['border_color'] ) ? $settings['border_color'] : '',
            'border_radius'    => isset( $settings['border_radius'] ) ? $settings['border_radius'] : '',
        ];
        $final_settings = \PVT\Shortcodes::merge_style_settings( $style_overrides );
        $attrs = \PVT\Shortcodes::build_style_attributes( $final_settings );
        $style_attr = $attrs['style'] ? ' style="' . esc_attr( $attrs['style'] ) . '"' : '';
        $bg_attr = $attrs['background'] ? ' data-pvt-bg="' . esc_attr( $attrs['background'] ) . '"' : '';
        $product_id = 0;
        if ( function_exists( 'is_product' ) && is_product() ) {
            global $post;
            if ( $post ) {
                $product_id = (int) $post->ID;
            }
        }
        if ( ! $product_id ) {
            echo '<div class="pvt-description-root"' . $style_attr . $bg_attr . '>' . esc_html__( 'Select a product to preview the variation description.', 'product-var-trea' ) . '</div>';
            return;
        }
        $product = function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
        if ( ! $product || ! $product->is_type( 'variable' ) ) {
            return;
        }
        echo '<div id="pvt-description-root" class="pvt-description-root" data-product-id="' . esc_attr( $product_id ) . '"' . $style_attr . $bg_attr . '>';
        echo apply_filters( 'the_content', get_post_field( 'post_content', $product_id ) );
        echo '</div>';
    }
}
