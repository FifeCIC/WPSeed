<?php
/**
 * WPSeed License Manager
 *
 * Handles license key validation, activation, and update checks
 *
 * @package WPSeed/Classes
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_License_Manager Class
 */
class WPSeed_License_Manager {

    /**
     * License server URL
     */
    private $server_url;

    /**
     * Product ID
     */
    private $product_id;

    /**
     * Product version
     */
    private $version;

    /**
     * License key option name
     */
    private $license_key_option;

    /**
     * License status option name
     */
    private $license_status_option;

    /**
     * License data option name
     */
    private $license_data_option;

    /**
     * Grace period in days
     */
    private $grace_period = 7;

    /**
     * Constructor
     */
    public function __construct( $server_url, $product_id, $version ) {
        $this->server_url = trailingslashit( $server_url );
        $this->product_id = $product_id;
        $this->version = $version;
        $this->license_key_option = 'wpseed_license_key_' . $product_id;
        $this->license_status_option = 'wpseed_license_status_' . $product_id;
        $this->license_data_option = 'wpseed_license_data_' . $product_id;

        // License system disabled - keeping for future use
        // add_action( 'admin_init', array( $this, 'check_license' ) );
        // add_action( 'admin_init', array( $this, 'handle_license_actions' ) );
        // add_action( 'admin_notices', array( $this, 'license_notices' ) );
        // add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_updates' ) );
        // add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
    }

    /**
     * Activate license
     */
    public function activate_license( $license_key ) {
        $response = $this->api_request( 'activate', array(
            'license_key' => $license_key,
            'site_url'    => get_site_url(),
            'environment' => $this->get_environment(),
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        if ( isset( $response['success'] ) && $response['success'] ) {
            update_option( $this->license_key_option, $license_key );
            update_option( $this->license_status_option, 'active' );
            update_option( $this->license_data_option, array(
                'license_type'   => $response['license_type'] ?? 'single',
                'expires'        => $response['expires'] ?? '',
                'sites_allowed'  => $response['sites_allowed'] ?? 1,
                'sites_used'     => $response['sites_used'] ?? 1,
                'activated_date' => current_time( 'mysql' ),
                'customer_email' => $response['customer_email'] ?? '',
                'customer_name'  => $response['customer_name'] ?? '',
            ) );
            
            // Clear renewal reminder
            delete_transient( 'wpseed_renewal_reminder_' . $this->product_id );
            
            return true;
        }

        return new WP_Error( 'activation_failed', $response['message'] ?? __( 'License activation failed.', 'wpseed' ) );
    }

    /**
     * Deactivate license
     */
    public function deactivate_license() {
        $license_key = $this->get_license_key();

        if ( ! $license_key ) {
            return new WP_Error( 'no_license', __( 'No license key found.', 'wpseed' ) );
        }

        $response = $this->api_request( 'deactivate', array(
            'license_key' => $license_key,
            'site_url'    => get_site_url(),
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        delete_option( $this->license_key_option );
        delete_option( $this->license_status_option );

        return true;
    }

    /**
     * Check license status
     */
    public function check_license() {
        $license_key = $this->get_license_key();

        if ( ! $license_key ) {
            return;
        }

        // Check once per day
        $last_check = get_transient( 'wpseed_license_check_' . $this->product_id );
        if ( $last_check ) {
            return;
        }

        $response = $this->api_request( 'check', array(
            'license_key' => $license_key,
            'site_url'    => get_site_url(),
        ) );

        if ( ! is_wp_error( $response ) && isset( $response['status'] ) ) {
            update_option( $this->license_status_option, $response['status'] );
        }

        set_transient( 'wpseed_license_check_' . $this->product_id, true, DAY_IN_SECONDS );
    }

    /**
     * Check for plugin updates
     */
    public function check_for_updates( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $license_key = $this->get_license_key();
        if ( ! $license_key || ! $this->is_license_active() ) {
            return $transient;
        }

        $response = $this->api_request( 'update_check', array(
            'license_key' => $license_key,
            'version'     => $this->version,
        ) );

        if ( is_wp_error( $response ) || ! isset( $response['new_version'] ) ) {
            return $transient;
        }

        if ( version_compare( $this->version, $response['new_version'], '<' ) ) {
            $plugin_slug = plugin_basename( WPSEED_PLUGIN_FILE );
            
            $transient->response[ $plugin_slug ] = (object) array(
                'slug'        => $this->product_id,
                'new_version' => $response['new_version'],
                'package'     => $response['download_url'],
                'url'         => $response['url'] ?? '',
            );
        }

        return $transient;
    }

    /**
     * Plugin information for updates
     */
    public function plugin_info( $result, $action, $args ) {
        if ( 'plugin_information' !== $action || $this->product_id !== $args->slug ) {
            return $result;
        }

        $license_key = $this->get_license_key();
        if ( ! $license_key ) {
            return $result;
        }

        $response = $this->api_request( 'plugin_info', array(
            'license_key' => $license_key,
        ) );

        if ( is_wp_error( $response ) ) {
            return $result;
        }

        return (object) $response;
    }

    /**
     * Handle license form actions
     */
    public function handle_license_actions() {
        if ( ! isset( $_POST['wpseed_license_action'] ) ) {
            return;
        }

        check_admin_referer( 'wpseed_license_action' );

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $action = sanitize_text_field( $_POST['wpseed_license_action'] );

        if ( 'activate' === $action && ! empty( $_POST['license_key'] ) ) {
            $result = $this->activate_license( sanitize_text_field( $_POST['license_key'] ) );
            
            if ( is_wp_error( $result ) ) {
                add_settings_error( 'wpseed_license', 'activation_failed', $result->get_error_message() );
            } else {
                add_settings_error( 'wpseed_license', 'activated', __( 'License activated successfully!', 'wpseed' ), 'success' );
            }
        } elseif ( 'deactivate' === $action ) {
            $result = $this->deactivate_license();
            
            if ( is_wp_error( $result ) ) {
                add_settings_error( 'wpseed_license', 'deactivation_failed', $result->get_error_message() );
            } else {
                add_settings_error( 'wpseed_license', 'deactivated', __( 'License deactivated successfully!', 'wpseed' ), 'success' );
            }
        } elseif ( 'transfer' === $action && ! empty( $_POST['new_site_url'] ) ) {
            $result = $this->transfer_license( esc_url_raw( $_POST['new_site_url'] ) );
            
            if ( is_wp_error( $result ) ) {
                add_settings_error( 'wpseed_license', 'transfer_failed', $result->get_error_message() );
            } else {
                add_settings_error( 'wpseed_license', 'transferred', __( 'License transferred successfully!', 'wpseed' ), 'success' );
            }
        } elseif ( 'upgrade' === $action && ! empty( $_POST['new_license_key'] ) ) {
            $result = $this->upgrade_license( sanitize_text_field( $_POST['new_license_key'] ) );
            
            if ( is_wp_error( $result ) ) {
                add_settings_error( 'wpseed_license', 'upgrade_failed', $result->get_error_message() );
            } else {
                add_settings_error( 'wpseed_license', 'upgraded', __( 'License upgraded successfully!', 'wpseed' ), 'success' );
            }
        }
    }

    /**
     * Make API request to license server
     */
    private function api_request( $endpoint, $data = array() ) {
        $data['product_id'] = $this->product_id;

        $response = wp_remote_post( $this->server_url . 'wp-json/wpseed-license/v1/' . $endpoint, array(
            'body'    => $data,
            'timeout' => 15,
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'invalid_response', __( 'Invalid server response.', 'wpseed' ) );
        }

        return $data;
    }

    /**
     * Get license key
     */
    public function get_license_key() {
        return get_option( $this->license_key_option, '' );
    }

    /**
     * Get license status
     */
    public function get_license_status() {
        return get_option( $this->license_status_option, 'inactive' );
    }

    /**
     * Check if license is active
     */
    public function is_license_active() {
        return 'active' === $this->get_license_status();
    }

    /**
     * Get license info
     */
    public function get_license_info() {
        $license_key = $this->get_license_key();

        if ( ! $license_key ) {
            return null;
        }

        $response = $this->api_request( 'info', array(
            'license_key' => $license_key,
        ) );

        if ( is_wp_error( $response ) ) {
            return null;
        }

        return $response;
    }

    /**
     * Get license data
     */
    public function get_license_data() {
        return get_option( $this->license_data_option, array() );
    }

    /**
     * Check if license is in grace period
     */
    public function is_in_grace_period() {
        $data = $this->get_license_data();
        
        if ( empty( $data['expires'] ) ) {
            return false;
        }

        $expires = strtotime( $data['expires'] );
        $grace_end = $expires + ( $this->grace_period * DAY_IN_SECONDS );
        
        return time() <= $grace_end && time() > $expires;
    }

    /**
     * Check if license is expired
     */
    public function is_license_expired() {
        $data = $this->get_license_data();
        
        if ( empty( $data['expires'] ) ) {
            return false;
        }

        $expires = strtotime( $data['expires'] );
        $grace_end = $expires + ( $this->grace_period * DAY_IN_SECONDS );
        
        return time() > $grace_end;
    }

    /**
     * Get days until expiration
     */
    public function get_days_until_expiration() {
        $data = $this->get_license_data();
        
        if ( empty( $data['expires'] ) ) {
            return null;
        }

        $expires = strtotime( $data['expires'] );
        $days = floor( ( $expires - time() ) / DAY_IN_SECONDS );
        
        return $days;
    }

    /**
     * Transfer license to new site
     */
    public function transfer_license( $new_site_url ) {
        $license_key = $this->get_license_key();

        if ( ! $license_key ) {
            return new WP_Error( 'no_license', __( 'No license key found.', 'wpseed' ) );
        }

        $response = $this->api_request( 'transfer', array(
            'license_key'  => $license_key,
            'old_site_url' => get_site_url(),
            'new_site_url' => $new_site_url,
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        if ( isset( $response['success'] ) && $response['success'] ) {
            return true;
        }

        return new WP_Error( 'transfer_failed', $response['message'] ?? __( 'License transfer failed.', 'wpseed' ) );
    }

    /**
     * Upgrade license
     */
    public function upgrade_license( $new_license_key ) {
        // Deactivate old license
        $this->deactivate_license();
        
        // Activate new license
        return $this->activate_license( $new_license_key );
    }

    /**
     * Check if site is localhost/staging
     */
    private function get_environment() {
        $site_url = get_site_url();
        
        if ( strpos( $site_url, 'localhost' ) !== false ||
             strpos( $site_url, '127.0.0.1' ) !== false ||
             strpos( $site_url, '.local' ) !== false ||
             strpos( $site_url, '.test' ) !== false ||
             strpos( $site_url, 'staging' ) !== false ) {
            return 'development';
        }
        
        return 'production';
    }

    /**
     * Display license notices
     */
    public function license_notices() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Expiration warning (30 days)
        $days_left = $this->get_days_until_expiration();
        if ( $days_left !== null && $days_left > 0 && $days_left <= 30 ) {
            $dismissed = get_transient( 'wpseed_renewal_reminder_' . $this->product_id );
            if ( ! $dismissed ) {
                ?>
                <div class="notice notice-warning is-dismissible" data-notice="renewal">
                    <p>
                        <strong><?php _e( 'License Expiring Soon', 'wpseed' ); ?></strong><br>
                        <?php printf( __( 'Your license will expire in %d days. Renew now to continue receiving updates and support.', 'wpseed' ), $days_left ); ?>
                        <a href="<?php echo admin_url( 'options-general.php?page=wpseed-settings&tab=license' ); ?>" class="button button-small"><?php _e( 'Manage License', 'wpseed' ); ?></a>
                    </p>
                </div>
                <?php
            }
        }

        // Grace period warning
        if ( $this->is_in_grace_period() ) {
            ?>
            <div class="notice notice-error">
                <p>
                    <strong><?php _e( 'License Expired - Grace Period Active', 'wpseed' ); ?></strong><br>
                    <?php printf( __( 'Your license has expired but is in a %d-day grace period. Renew now to avoid service interruption.', 'wpseed' ), $this->grace_period ); ?>
                    <a href="<?php echo admin_url( 'options-general.php?page=wpseed-settings&tab=license' ); ?>" class="button button-primary button-small"><?php _e( 'Renew License', 'wpseed' ); ?></a>
                </p>
            </div>
            <?php
        }

        // Expired (past grace period)
        if ( $this->is_license_expired() ) {
            ?>
            <div class="notice notice-error">
                <p>
                    <strong><?php _e( 'License Expired', 'wpseed' ); ?></strong><br>
                    <?php _e( 'Your license has expired. Updates and support are no longer available. Please renew your license.', 'wpseed' ); ?>
                    <a href="<?php echo admin_url( 'options-general.php?page=wpseed-settings&tab=license' ); ?>" class="button button-primary button-small"><?php _e( 'Renew License', 'wpseed' ); ?></a>
                </p>
            </div>
            <?php
        }
    }
}
