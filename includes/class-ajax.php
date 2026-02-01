<?php
namespace PVT;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Ajax {
    public static function init() {
        add_action( 'wp_ajax_pvt_get_variation_description', [ __CLASS__, 'get_variation_description' ] );
        add_action( 'wp_ajax_nopriv_pvt_get_variation_description', [ __CLASS__, 'get_variation_description' ] );
    }

    public static function get_variation_description() {
        $variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
        if ( ! $variation_id ) {
            wp_send_json_error( [ 'message' => __( 'Invalid variation.', 'product-var-trea' ) ] );
        }
        $desc = get_post_meta( $variation_id, '_pvt_variation_description', true );
        $desc = apply_filters( 'the_content', $desc );
        wp_send_json_success( [ 'description' => $desc ] );
    }
}