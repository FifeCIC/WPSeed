<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Library Update Monitor
 * 
 * Tracks bundled third-party libraries and checks for updates via GitHub API
 */
class WPSeed_Library_Update_Monitor {
    
    private static $instance = null;
    private $libraries = array();
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->register_libraries();
        add_action( 'admin_init', array( $this, 'check_outdated_libraries' ) );
        add_action( 'wp_ajax_wpseed_dismiss_library_notice', array( $this, 'ajax_dismiss_notice' ) );
    }
    
    /**
     * Register all bundled libraries
     */
    private function register_libraries() {
        $this->libraries = array(
            'action-scheduler' => array(
                'name' => 'Action Scheduler',
                'version' => '3.9.3',
                'license' => 'GPL-3.0',
                'github_repo' => 'woocommerce/action-scheduler',
                'local_path' => 'includes/libraries/action-scheduler/',
                'bundled_date' => '2025-07-15',
                'description' => 'Background task processing library by WooCommerce',
            ),
            'carbon-fields' => array(
                'name' => 'Carbon Fields',
                'version' => '3.6.9',
                'license' => 'GPL-2.0',
                'github_repo' => 'htmlburger/carbon-fields',
                'local_path' => 'includes/libraries/carbon-fields/',
                'bundled_date' => '2025-06-11',
                'description' => 'Modern WordPress custom fields library',
            ),
        );
    }
    
    /**
     * Get all registered libraries
     */
    public function get_libraries() {
        return $this->libraries;
    }
    
    /**
     * Get library info
     */
    public function get_library( $slug ) {
        return isset( $this->libraries[ $slug ] ) ? $this->libraries[ $slug ] : null;
    }
    
    /**
     * Check for updates via GitHub API
     */
    public function check_updates( $slug ) {
        $library = $this->get_library( $slug );
        if ( ! $library ) {
            return new WP_Error( 'invalid_library', 'Library not found' );
        }
        
        $transient_key = 'wpseed_library_update_' . $slug;
        $cached = get_transient( $transient_key );
        
        if ( false !== $cached ) {
            return $cached;
        }
        
        $url = 'https://api.github.com/repos/' . $library['github_repo'] . '/releases/latest';
        $response = wp_remote_get( $url, array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
            ),
        ) );
        
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        
        if ( empty( $data['tag_name'] ) ) {
            return new WP_Error( 'no_release', 'No release found' );
        }
        
        $result = array(
            'current_version' => $library['version'],
            'latest_version' => ltrim( $data['tag_name'], 'v' ),
            'release_url' => $data['html_url'],
            'release_date' => $data['published_at'],
            'release_notes' => $data['body'],
            'download_url' => $data['zipball_url'],
            'needs_update' => version_compare( ltrim( $data['tag_name'], 'v' ), $library['version'], '>' ),
        );
        
        set_transient( $transient_key, $result, 6 * HOUR_IN_SECONDS );
        
        return $result;
    }
    
    /**
     * Check all libraries for updates
     */
    public function check_all_updates() {
        $results = array();
        
        foreach ( $this->libraries as $slug => $library ) {
            $results[ $slug ] = $this->check_updates( $slug );
        }
        
        return $results;
    }
    
    /**
     * Check if library is outdated (12+ months).
     *
     * Uses a 12-month threshold because bundled libraries in a boilerplate
     * are updated less frequently than application dependencies.
     *
     * @since  1.0.0
     * @since  3.1.0 Changed threshold from 6 months to 12 months.
     *
     * @param  string $slug Library slug.
     * @return bool
     */
    public function is_outdated( $slug ) {
        $library = $this->get_library( $slug );
        if ( ! $library ) {
            return false;
        }
        
        $bundled_time = strtotime( $library['bundled_date'] );
        $threshold = strtotime( '-12 months' );
        
        return $bundled_time < $threshold;
    }
    
    /**
     * Show admin notice for outdated libraries
     */
    public function check_outdated_libraries() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        $dismissed = get_user_meta( get_current_user_id(), 'wpseed_library_notice_dismissed', true );
        if ( $dismissed && $dismissed > strtotime( '-1 week' ) ) {
            return;
        }
        
        $outdated = array();
        foreach ( $this->libraries as $slug => $library ) {
            if ( $this->is_outdated( $slug ) ) {
                $outdated[] = $library['name'];
            }
        }
        
        if ( ! empty( $outdated ) ) {
            add_action( 'admin_notices', function() use ( $outdated ) {
                $url = admin_url( 'admin.php?page=wpseed_development&tab=libraries' );
                echo '<div class="notice notice-warning is-dismissible" data-notice="wpseed-library-outdated">';
                echo '<p><strong>WPSeed:</strong> ' . esc_html( count( $outdated ) ) . ' bundled ' . esc_html( _n( 'library is', 'libraries are', count( $outdated ), 'wpseed' ) ) . ' outdated (12+ months): ' . esc_html( implode( ', ', $outdated ) ) . '</p>';
                echo '<p><a href="' . esc_url( $url ) . '" class="button button-primary">Check for Updates</a> <button type="button" class="button wpseed-dismiss-library-notice">Remind Me Later</button></p>';
                echo '</div>';
            } );
        }
    }
    
    /**
     * Clear update cache
     */
    public function clear_cache( $slug = null ) {
        if ( $slug ) {
            delete_transient( 'wpseed_library_update_' . $slug );
        } else {
            foreach ( $this->libraries as $slug => $library ) {
                delete_transient( 'wpseed_library_update_' . $slug );
            }
        }
    }
    
    /**
     * AJAX handler for dismissing notice
     */
    public function ajax_dismiss_notice() {
        update_user_meta( get_current_user_id(), 'wpseed_library_notice_dismissed', time() );
        wp_send_json_success();
    }
}

// Initialize
WPSeed_Library_Update_Monitor::instance();
