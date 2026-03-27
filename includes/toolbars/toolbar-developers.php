<?php
/**
 * WPSeed - Developer Toolbar
 *
 * The developer toolbar requires the "seniordeveloper" custom capability. The
 * toolbar allows actions not all key holders should be giving access to. The
 * menu is intended for developers to already have access to a range of
 *
 * @author   Ryan Bayne
 * @category Admin
 * @package  WPSeed/Toolbars
 * @since    1.0.0
 * @version  1.2.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}  

if( !class_exists( 'WPSeed_Admin_Toolbar_Developers' ) ) :

class WPSeed_Admin_Toolbar_Developers {
    public function __construct() {
        if( !current_user_can( 'seniordeveloper' ) ) return false;
        $this->init(); 
    }    
    
    /**
     * Initialise toolbar menus for senior developers.
     *
     * The page GET parameter is a read-only navigation value used solely to
     * build the debug mode switch URL. No state is mutated on this read, so a
     * nonce is not required; the existing seniordeveloper capability check in
     * the constructor is sufficient to satisfy NonceVerification.Recommended.
     *
     * @since   1.0.0
     * @version 1.2.0
     * @return void
     */
    private function init() {
        global $wp_admin_bar, $wpseed_settings;  
        
        // Add custom icon CSS
        add_action('admin_head', array($this, 'add_toolbar_icon_css'));
        add_action('wp_head', array($this, 'add_toolbar_icon_css'));
        
        // Top Level/Level One
        $args = array(
            'id'     => 'wpseed-toolbarmenu-developers',
            'title'  => '<span class="ab-icon wpseed-toolbar-icon"></span><span class="ab-label">' . __( 'WP Seed Developers', 'wpseed' ) . '</span>',          
        );
        $wp_admin_bar->add_menu( $args );
        
            // Group - Debug Tools
            $args = array(
                'id'     => 'wpseed-toolbarmenu-debugtools',
                'parent' => 'wpseed-toolbarmenu-developers',
                'title'  => __( 'Debug Tools', 'wpseed' ), 
                'meta'   => array( 'class' => 'first-toolbar-group' )         
            );        
            $wp_admin_bar->add_menu( $args );

                // Read-only navigation parameter used only to build the action URL.
                // Restricted to seniordeveloper capability; sanitize_key() is correct
                // for a WordPress admin page slug value.
                $page_param = ( current_user_can( 'seniordeveloper' ) && isset( $_GET['page'] ) )
                    ? sanitize_key( wp_unslash( $_GET['page'] ) )
                    : '';
                $href = wp_nonce_url( admin_url() . 'admin.php?page=' . $page_param . '&wpseedaction=' . 'debugmodeswitch'  . '', 'debugmodeswitch' );
                if( !isset( $wpseed_settings['displayerrors'] ) || $wpseed_settings['displayerrors'] !== true ) 
                {
                    $error_display_title = __( 'Hide Errors', 'wpseed' );
                } 
                else 
                {
                    $error_display_title = __( 'Display Errors', 'wpseed' );
                }
                $args = array(
                    'id'     => 'wpseed-toolbarmenu-errordisplay',
                    'parent' => 'wpseed-toolbarmenu-debugtools',
                    'title'  => $error_display_title,
                    'href'   => $href,            
                );
                $wp_admin_bar->add_menu( $args );    
    }
    
    /**
     * Add custom toolbar icon CSS
     */
    public function add_toolbar_icon_css() {
        ?>
        <style>
            #wpadminbar .wpseed-toolbar-icon:before {
                content: '';
                display: inline-block;
                width: 20px;
                height: 20px;
                background-image: url('data:image/svg+xml;base64,<?php echo esc_attr( base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12,22C12,22 11,17 11,13C11,9 13,6 17,4C17,4 16,8 16,11C16,14 17,17 17,17M7,18C7,18 6,14 8,11C10,8 13,7 13,7C13,7 12,10 11,12C10,14 10,18 10,18" /></svg>') ); ?>');
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
                vertical-align: middle;
                margin-right: 5px;
            }
        </style>
        <?php
    }
    
}   

endif;

return new WPSeed_Admin_Toolbar_Developers();
