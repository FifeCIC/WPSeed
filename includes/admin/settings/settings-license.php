<?php
/**
 * WPSeed License Settings Page
 *
 * @package WPSeed/Admin/Settings
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPSeed_Settings_License' ) ) :

/**
 * WPSeed_Settings_License
 */
class WPSeed_Settings_License extends WPSeed_Settings_Page {

    /**
     * License manager instance
     */
    private $license_manager;

    /**
     * Constructor
     */
    public function __construct() {
        $this->id    = 'license';
        $this->label = __( 'License', 'wpseed' );

        parent::__construct();
    }

    /**
     * Get settings array
     */
    public function get_settings() {
        $settings = array(

            array(
                'title' => __( 'License Management', 'wpseed' ),
                'type'  => 'title',
                'desc'  => __( 'Manage your plugin license and access premium features.', 'wpseed' ),
                'id'    => 'license_section'
            ),

            array(
                'type' => 'sectionend',
                'id'   => 'license_section'
            ),

        );

        return apply_filters( 'wpseed_license_settings', $settings );
    }

    /**
     * Output the settings
     */
    public function output() {
        $settings = $this->get_settings();
        WPSeed_Admin_Settings::output_fields( $settings );
        
        // Output license UI
        $this->output_license_ui();
    }

    /**
     * Output license management UI
     */
    private function output_license_ui() {
        $license_key = get_option( 'wpseed_license_key', '' );
        $license_status = get_option( 'wpseed_license_status', 'inactive' );
        $license_data = get_option( 'wpseed_license_data', array() );
        
        ?>
        <div class="wpseed-license-manager">
            <?php settings_errors( 'wpseed_license' ); ?>

            <?php if ( $license_status === 'active' ) : ?>
                <!-- Active License -->
                <div class="wpseed-license-active">
                    <div class="license-status-badge active">
                        <span class="dashicons dashicons-yes-alt"></span>
                        <?php _e( 'License Active', 'wpseed' ); ?>
                    </div>

                    <table class="form-table">
                        <tr>
                            <th><?php _e( 'License Key', 'wpseed' ); ?></th>
                            <td>
                                <code><?php echo esc_html( $this->mask_license_key( $license_key ) ); ?></code>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Status', 'wpseed' ); ?></th>
                            <td>
                                <span class="license-status-text active"><?php _e( 'Active', 'wpseed' ); ?></span>
                            </td>
                        </tr>
                        <?php if ( ! empty( $license_data['license_type'] ) ) : ?>
                        <tr>
                            <th><?php _e( 'License Type', 'wpseed' ); ?></th>
                            <td><?php echo esc_html( ucfirst( $license_data['license_type'] ) ); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ( ! empty( $license_data['expires'] ) ) : ?>
                        <tr>
                            <th><?php _e( 'Expires', 'wpseed' ); ?></th>
                            <td>
                                <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $license_data['expires'] ) ) ); ?>
                                <?php
                                $days_left = floor( ( strtotime( $license_data['expires'] ) - time() ) / DAY_IN_SECONDS );
                                if ( $days_left > 0 && $days_left <= 30 ) {
                                    echo ' <span class="license-expiring">(' . sprintf( __( '%d days left', 'wpseed' ), $days_left ) . ')</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php if ( isset( $license_data['sites_allowed'] ) && isset( $license_data['sites_used'] ) ) : ?>
                        <tr>
                            <th><?php _e( 'Sites', 'wpseed' ); ?></th>
                            <td>
                                <?php echo esc_html( $license_data['sites_used'] ); ?> / 
                                <?php echo $license_data['sites_allowed'] == 999 ? __( 'Unlimited', 'wpseed' ) : esc_html( $license_data['sites_allowed'] ); ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php if ( ! empty( $license_data['customer_name'] ) ) : ?>
                        <tr>
                            <th><?php _e( 'Licensed To', 'wpseed' ); ?></th>
                            <td><?php echo esc_html( $license_data['customer_name'] ); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>

                    <div class="license-actions">
                        <form method="post" style="display: inline-block; margin-right: 10px;">
                            <?php wp_nonce_field( 'wpseed_license_action' ); ?>
                            <input type="hidden" name="wpseed_license_action" value="deactivate" />
                            <button type="submit" class="button button-secondary">
                                <?php _e( 'Deactivate License', 'wpseed' ); ?>
                            </button>
                        </form>
                        
                        <button type="button" class="button" onclick="document.getElementById('transfer-form').style.display='block';">
                            <?php _e( 'Transfer License', 'wpseed' ); ?>
                        </button>
                        
                        <button type="button" class="button" onclick="document.getElementById('upgrade-form').style.display='block';">
                            <?php _e( 'Upgrade License', 'wpseed' ); ?>
                        </button>
                    </div>

                    <!-- Transfer Form (Hidden) -->
                    <div id="transfer-form" style="display:none; margin-top: 20px; padding: 15px; background: #f8f9fa; border: 1px solid #ddd;">
                        <h4><?php _e( 'Transfer License to New Site', 'wpseed' ); ?></h4>
                        <form method="post">
                            <?php wp_nonce_field( 'wpseed_license_action' ); ?>
                            <input type="hidden" name="wpseed_license_action" value="transfer" />
                            <p>
                                <label><?php _e( 'New Site URL', 'wpseed' ); ?></label><br>
                                <input type="url" name="new_site_url" class="regular-text" required />
                            </p>
                            <button type="submit" class="button button-primary"><?php _e( 'Transfer', 'wpseed' ); ?></button>
                            <button type="button" class="button" onclick="document.getElementById('transfer-form').style.display='none';"><?php _e( 'Cancel', 'wpseed' ); ?></button>
                        </form>
                    </div>

                    <!-- Upgrade Form (Hidden) -->
                    <div id="upgrade-form" style="display:none; margin-top: 20px; padding: 15px; background: #f8f9fa; border: 1px solid #ddd;">
                        <h4><?php _e( 'Upgrade License', 'wpseed' ); ?></h4>
                        <p><?php _e( 'Enter your new license key to upgrade your license type.', 'wpseed' ); ?></p>
                        <form method="post">
                            <?php wp_nonce_field( 'wpseed_license_action' ); ?>
                            <input type="hidden" name="wpseed_license_action" value="upgrade" />
                            <p>
                                <label><?php _e( 'New License Key', 'wpseed' ); ?></label><br>
                                <input type="text" name="new_license_key" class="regular-text" required />
                            </p>
                            <button type="submit" class="button button-primary"><?php _e( 'Upgrade', 'wpseed' ); ?></button>
                            <button type="button" class="button" onclick="document.getElementById('upgrade-form').style.display='none';"><?php _e( 'Cancel', 'wpseed' ); ?></button>
                        </form>
                    </div>
                </div>

            <?php else : ?>
                <!-- Inactive License -->
                <div class="wpseed-license-inactive">
                    <div class="license-status-badge inactive">
                        <span class="dashicons dashicons-warning"></span>
                        <?php _e( 'No Active License', 'wpseed' ); ?>
                    </div>

                    <p><?php _e( 'Enter your license key to activate premium features and receive updates.', 'wpseed' ); ?></p>

                    <form method="post">
                        <?php wp_nonce_field( 'wpseed_license_action' ); ?>
                        <input type="hidden" name="wpseed_license_action" value="activate" />
                        
                        <table class="form-table">
                            <tr>
                                <th>
                                    <label for="license_key"><?php _e( 'License Key', 'wpseed' ); ?></label>
                                </th>
                                <td>
                                    <input type="text" 
                                           name="license_key" 
                                           id="license_key" 
                                           class="regular-text" 
                                           placeholder="XXXX-XXXX-XXXX-XXXX"
                                           value="<?php echo esc_attr( $license_key ); ?>" 
                                           required />
                                    <p class="description">
                                        <?php _e( 'Enter your license key received after purchase.', 'wpseed' ); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <button type="submit" class="button button-primary">
                            <?php _e( 'Activate License', 'wpseed' ); ?>
                        </button>
                    </form>

                    <div class="license-purchase-info">
                        <h3><?php _e( 'Don\'t have a license?', 'wpseed' ); ?></h3>
                        <p><?php _e( 'Purchase a license to unlock premium features, priority support, and automatic updates.', 'wpseed' ); ?></p>
                        <a href="https://your-site.com/pricing" class="button button-secondary" target="_blank">
                            <?php _e( 'View Pricing', 'wpseed' ); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- License Types -->
            <div class="license-types-info">
                <h3><?php _e( 'License Types', 'wpseed' ); ?></h3>
                <div class="license-types-grid">
                    <div class="license-type">
                        <h4><?php _e( 'Single Site', 'wpseed' ); ?></h4>
                        <p><?php _e( 'Use on one website', 'wpseed' ); ?></p>
                    </div>
                    <div class="license-type">
                        <h4><?php _e( 'Multi-Site', 'wpseed' ); ?></h4>
                        <p><?php _e( 'Use on up to 5 websites', 'wpseed' ); ?></p>
                    </div>
                    <div class="license-type">
                        <h4><?php _e( 'Unlimited', 'wpseed' ); ?></h4>
                        <p><?php _e( 'Use on unlimited websites', 'wpseed' ); ?></p>
                    </div>
                    <div class="license-type">
                        <h4><?php _e( 'Developer', 'wpseed' ); ?></h4>
                        <p><?php _e( 'For agencies and developers', 'wpseed' ); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Mask license key for display
     */
    private function mask_license_key( $key ) {
        if ( strlen( $key ) <= 8 ) {
            return $key;
        }
        
        return substr( $key, 0, 4 ) . str_repeat( '*', strlen( $key ) - 8 ) . substr( $key, -4 );
    }

    /**
     * Save settings
     */
    public function save() {
        // License activation/deactivation is handled by License_Manager
    }
}

endif;

return new WPSeed_Settings_License();
