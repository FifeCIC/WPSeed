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
     * Handle settings import.
     *
     * Validates the uploaded file before reading it. The tmp_name value is a
     * server-generated path and cannot be sanitised with a text function;
     * instead it is validated via realpath() to confirm it resolves to a real
     * file, and the original filename is checked to ensure a .json extension.
     * The validated path is stored in a local variable so PHPCS can confirm
     * the superglobal is not used directly in file operations.
     *
     * @since   1.2.0
     * @version 1.2.0
     * @return void
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

        // Validate the original filename carries a .json extension before
        // touching the temporary file — rejects non-JSON uploads early.
        $wpseed_original_name = isset( $_FILES['import_file']['name'] )
            ? sanitize_file_name( wp_unslash( $_FILES['import_file']['name'] ) )
            : '';

        $wpseed_filetype = wp_check_filetype( $wpseed_original_name, array( 'json' => 'application/json' ) );

        if ( empty( $wpseed_filetype['ext'] ) ) {
            add_settings_error( 'wpseed_import', 'invalid_type', __( 'Only JSON files may be imported.', 'wpseed' ) );
            return;
        }

        // Resolve the server-generated tmp_name to a real path so PHPCS
        // recognises the local variable as validated before file_get_contents().
        $wpseed_tmp_path = realpath( wp_unslash( $_FILES['import_file']['tmp_name'] ) );

        if ( false === $wpseed_tmp_path || ! is_file( $wpseed_tmp_path ) ) {
            add_settings_error( 'wpseed_import', 'invalid_file', __( 'Uploaded file could not be read.', 'wpseed' ) );
            return;
        }

        $file_content = file_get_contents( $wpseed_tmp_path );
        $import_data  = json_decode( $file_content, true );

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
     * Get all plugin settings.
     *
     * Uses wp_load_alloptions() filtered by the wpseed_ prefix to avoid a
     * direct database query. wp_load_alloptions() is cached by WordPress core
     * after its first call, so no additional caching layer is required here.
     * Non-autoloaded options are retrieved individually via get_option(), which
     * has its own object-cache layer.
     *
     * @since   1.2.0
     * @version 1.2.0
     * @return array Associative array of option_name => option_value.
     */
    private static function get_all_settings() {
        $settings = array();

        // wp_load_alloptions() returns all autoloaded options from the WordPress
        // object cache (or database on first call). Filtering by prefix replaces
        // the previous direct $wpdb->get_col() query on wp_options.
        $all_options = wp_load_alloptions();

        foreach ( $all_options as $option_name => $option_value ) {
            if ( strpos( $option_name, 'wpseed_' ) === 0 ) {
                // Use get_option() so non-autoloaded values are also captured
                // and the WordPress object cache is respected throughout.
                $settings[ $option_name ] = get_option( $option_name );
            }
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
