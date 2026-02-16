<?php
/**
 * WPSeed Extension Installer
 *
 * Handles downloading and installing extensions from remote servers
 *
 * @package WPSeed/Classes
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_Extension_Installer Class
 */
class WPSeed_Extension_Installer {

    /**
     * Server URL
     */
    private $server_url;

    /**
     * Constructor
     */
    public function __construct( $server_url ) {
        $this->server_url = trailingslashit( $server_url );
        
        add_action( 'admin_init', array( $this, 'handle_install' ) );
        add_action( 'wp_ajax_wpseed_install_extension', array( $this, 'ajax_install_extension' ) );
    }

    /**
     * Get available extensions
     */
    public function get_extensions( $license_key = '' ) {
        $response = wp_remote_get( $this->server_url . 'wp-json/wpseed-extensions/v1/list', array(
            'body' => array(
                'license_key' => $license_key,
            ),
            'timeout' => 15,
        ) );

        if ( is_wp_error( $response ) ) {
            return array();
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        return $data['extensions'] ?? array();
    }

    /**
     * Install extension
     */
    public function install_extension( $extension_slug, $license_key = '' ) {
        // Get download URL
        $download_url = $this->get_download_url( $extension_slug, $license_key );

        if ( is_wp_error( $download_url ) ) {
            return $download_url;
        }

        // Download file
        $temp_file = download_url( $download_url );

        if ( is_wp_error( $temp_file ) ) {
            return $temp_file;
        }

        // Install plugin
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

        $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $result = $upgrader->install( $temp_file );

        @unlink( $temp_file );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        return true;
    }

    /**
     * Get download URL for extension
     */
    private function get_download_url( $extension_slug, $license_key = '' ) {
        $response = wp_remote_post( $this->server_url . 'wp-json/wpseed-extensions/v1/download', array(
            'body' => array(
                'extension'   => $extension_slug,
                'license_key' => $license_key,
                'site_url'    => get_site_url(),
            ),
            'timeout' => 15,
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( ! isset( $data['download_url'] ) ) {
            return new WP_Error( 'no_download_url', $data['message'] ?? __( 'Could not get download URL.', 'wpseed' ) );
        }

        return $data['download_url'];
    }

    /**
     * Handle extension installation from admin
     */
    public function handle_install() {
        if ( ! isset( $_GET['wpseed_install_extension'] ) ) {
            return;
        }

        check_admin_referer( 'wpseed_install_extension' );

        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_die( esc_html__( 'You do not have permission to install plugins.', 'wpseed' ) );
        }

        $extension_slug = sanitize_text_field( $_GET['wpseed_install_extension'] );
        $license_key = get_option( 'wpseed_license_key', '' );

        $result = $this->install_extension( $extension_slug, $license_key );

        if ( is_wp_error( $result ) ) {
            wp_die( esc_html( $result->get_error_message() ) );
        }

        wp_redirect( admin_url( 'plugins.php?wpseed_extension_installed=1' ) );
        exit;
    }

    /**
     * AJAX handler for extension installation
     */
    public function ajax_install_extension() {
        check_ajax_referer( 'wpseed_install_extension', 'nonce' );

        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'wpseed' ) ) );
        }

        $extension_slug = sanitize_text_field( $_POST['extension'] ?? '' );
        $license_key = get_option( 'wpseed_license_key', '' );

        if ( empty( $extension_slug ) ) {
            wp_send_json_error( array( 'message' => __( 'Extension slug is required.', 'wpseed' ) ) );
        }

        $result = $this->install_extension( $extension_slug, $license_key );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Extension installed successfully!', 'wpseed' ) ) );
    }

    /**
     * Check if extension is installed
     */
    public function is_extension_installed( $extension_slug ) {
        $plugins = get_plugins();
        
        foreach ( $plugins as $plugin_file => $plugin_data ) {
            if ( strpos( $plugin_file, $extension_slug ) !== false ) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if extension is active
     */
    public function is_extension_active( $extension_slug ) {
        $plugins = get_plugins();
        
        foreach ( $plugins as $plugin_file => $plugin_data ) {
            if ( strpos( $plugin_file, $extension_slug ) !== false ) {
                return is_plugin_active( $plugin_file );
            }
        }
        
        return false;
    }
}
