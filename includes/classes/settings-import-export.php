<?php
/**
 * WPSeed Settings Import/Export
 *
 * @package WPSeed/Admin
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_Settings_Import_Export Class
 */
class WPSeed_Settings_Import_Export {

    /**
     * Initialize
     */
    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'handle_export' ) );
        add_action( 'admin_init', array( __CLASS__, 'handle_import' ) );
        add_action( 'wpseed_settings_export_import', array( __CLASS__, 'output_ui' ) );
    }

    /**
     * Handle settings export
     */
    public static function handle_export() {
        if ( ! isset( $_POST['wpseed_export_settings'] ) ) {
            return;
        }

        check_admin_referer( 'wpseed_export_settings' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to export settings.', 'wpseed' ) );
        }

        $settings = self::get_all_settings();
        $export_data = array(
            'version'   => WPSEED_VERSION,
            'timestamp' => current_time( 'mysql' ),
            'site_url'  => get_site_url(),
            'settings'  => $settings,
        );

        $filename = 'wpseed-settings-' . gmdate( 'Y-m-d-His' ) . '.json';

        header( 'Content-Type: application/json' );
        header( 'Content-Disposition: attachment; filename=' . $filename );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        echo json_encode( $export_data, JSON_PRETTY_PRINT );
        exit;
    }

    /**
     * Handle settings import
     */
    public static function handle_import() {
        if ( ! isset( $_POST['wpseed_import_settings'] ) ) {
            return;
        }

        check_admin_referer( 'wpseed_import_settings' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to import settings.', 'wpseed' ) );
        }

        if ( empty( $_FILES['import_file']['tmp_name'] ) ) {
            add_settings_error( 'wpseed_import', 'no_file', __( 'Please select a file to import.', 'wpseed' ) );
            return;
        }

        $file_content = file_get_contents( $_FILES['import_file']['tmp_name'] );
        $import_data = json_decode( $file_content, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            add_settings_error( 'wpseed_import', 'invalid_json', __( 'Invalid JSON file.', 'wpseed' ) );
            return;
        }

        if ( ! isset( $import_data['settings'] ) ) {
            add_settings_error( 'wpseed_import', 'invalid_format', __( 'Invalid settings file format.', 'wpseed' ) );
            return;
        }

        $imported = self::import_settings( $import_data['settings'] );

        if ( $imported ) {
            add_settings_error( 'wpseed_import', 'success', __( 'Settings imported successfully!', 'wpseed' ), 'success' );
        } else {
            add_settings_error( 'wpseed_import', 'failed', __( 'Failed to import settings.', 'wpseed' ) );
        }
    }

    /**
     * Get all plugin settings
     */
    private static function get_all_settings() {
        global $wpdb;

        $settings = array();
        
        // Check cache first
        $option_names = wp_cache_get( 'wpseed_all_settings_names' );
        
        if ( false === $option_names ) {
            $option_names = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
                    'wpseed_%'
                )
            );
            // Cache the results
            wp_cache_set( 'wpseed_all_settings_names', $option_names );
        }

        foreach ( $option_names as $option_name ) {
            $settings[ $option_name ] = get_option( $option_name );
        }

        return $settings;
    }

    /**
     * Import settings
     */
    private static function import_settings( $settings ) {
        if ( ! is_array( $settings ) ) {
            return false;
        }

        foreach ( $settings as $option_name => $option_value ) {
            // Only import wpseed options
            if ( strpos( $option_name, 'wpseed_' ) === 0 ) {
                update_option( $option_name, $option_value );
            }
        }

        return true;
    }

    /**
     * Output import/export UI
     */
    public static function output_ui() {
        ?>
        <div class="wpseed-import-export">
            <h2><?php esc_html_e( 'Import/Export Settings', 'wpseed' ); ?></h2>
            
            <?php settings_errors( 'wpseed_import' ); ?>

            <div class="wpseed-export-section">
                <h3><?php esc_html_e( 'Export Settings', 'wpseed' ); ?></h3>
                <p><?php esc_html_e( 'Export your plugin settings as a JSON file for backup or transfer to another site.', 'wpseed' ); ?></p>
                
                <form method="post">
                    <?php wp_nonce_field( 'wpseed_export_settings' ); ?>
                    <button type="submit" name="wpseed_export_settings" class="button button-primary">
                        <span class="dashicons dashicons-download" style="vertical-align: middle;"></span>
                        <?php esc_html_e( 'Export Settings', 'wpseed' ); ?>
                    </button>
                </form>
            </div>

            <hr style="margin: 30px 0;">

            <div class="wpseed-import-section">
                <h3><?php esc_html_e( 'Import Settings', 'wpseed' ); ?></h3>
                <p><?php esc_html_e( 'Import settings from a previously exported JSON file.', 'wpseed' ); ?></p>
                
                <div class="notice notice-warning inline">
                    <p>
                        <strong><?php esc_html_e( 'Warning:', 'wpseed' ); ?></strong>
                        <?php esc_html_e( 'Importing will overwrite your current settings. Make sure to export your current settings first as a backup.', 'wpseed' ); ?>
                    </p>
                </div>

                <form method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field( 'wpseed_import_settings' ); ?>
                    
                    <p>
                        <input type="file" name="import_file" accept=".json" required />
                    </p>
                    
                    <button type="submit" name="wpseed_import_settings" class="button button-secondary">
                        <span class="dashicons dashicons-upload" style="vertical-align: middle;"></span>
                        <?php esc_html_e( 'Import Settings', 'wpseed' ); ?>
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
}

WPSeed_Settings_Import_Export::init();
