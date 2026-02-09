<?php
/**
 * Yoast SEO Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_Yoast_Integration
 */
class WPSeed_Yoast_Integration {

    public function __construct() {
        // Check if Yoast SEO is active
        if ( ! defined( 'WPSEO_VERSION' ) ) {
            return;
        }

        add_filter( 'wpseo_metadesc', array( $this, 'custom_meta_description' ), 10, 2 );
        add_filter( 'wpseo_title', array( $this, 'custom_title' ), 10, 2 );
        add_filter( 'wpseo_opengraph_desc', array( $this, 'custom_og_description' ) );
        add_action( 'wpseo_add_opengraph_additional_images', array( $this, 'add_og_images' ) );
    }

    /**
     * Customize meta description
     */
    public function custom_meta_description( $description, $presentation ) {
        if ( is_singular( 'your_post_type' ) ) {
            $custom_desc = get_post_meta( get_the_ID(), '_custom_description', true );
            if ( $custom_desc ) {
                return $custom_desc;
            }
        }
        return $description;
    }

    /**
     * Customize page title
     */
    public function custom_title( $title, $presentation ) {
        if ( is_singular( 'your_post_type' ) ) {
            $custom_title = get_post_meta( get_the_ID(), '_custom_title', true );
            if ( $custom_title ) {
                return $custom_title;
            }
        }
        return $title;
    }

    /**
     * Customize Open Graph description
     */
    public function custom_og_description( $description ) {
        if ( is_singular( 'your_post_type' ) ) {
            $custom_desc = get_post_meta( get_the_ID(), '_custom_og_description', true );
            if ( $custom_desc ) {
                return $custom_desc;
            }
        }
        return $description;
    }

    /**
     * Add custom Open Graph images
     */
    public function add_og_images( $object ) {
        if ( is_singular( 'your_post_type' ) ) {
            $image_url = get_post_meta( get_the_ID(), '_custom_og_image', true );
            if ( $image_url ) {
                $object->add_image( $image_url );
            }
        }
    }
}

// Initialize
new WPSeed_Yoast_Integration();
