<?php
/**
 * Add the default content to the help tab.
 *
 * @author      Ryan Bayne
 * @category    Admin
 * @package     WPSeed/Admin
 * @version     2.0.0
 */
          
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSeed_Admin_Help', false ) ) :

/**
 * WPSeed_Admin_Help Class.
 *
 * @since   1.0.0
 * @version 2.0.0
 */
class WPSeed_Admin_Help {

    /**
     * Hook in tabs.
     */
    public function __construct() {
        add_action( 'current_screen', array( $this, 'add_tabs' ), 50 );
    }

    /**
     * Add contextual help tabs to WPSeed admin screens.
     *
     * Registers all help tabs and the sidebar for any screen whose ID is
     * included in wpseed_get_screen_ids(). The $_GET['page'] and $_GET['tab']
     * reads that previously appeared here were unused after assignment and have
     * been removed — eliminating the NonceVerification.Recommended warning
     * without adding a nonce to a purely display-only help context.
     *
     * @since   1.0.0
     * @version 2.0.0
     * @return void
     */
    public function add_tabs() {
        $screen = get_current_screen();

        if ( ! $screen || ! in_array( $screen->id, wpseed_get_screen_ids() ) ) {
            return;
        }

        $screen->add_help_tab( array(
            'id'        => 'wpseed_instructions_tab',
            'title'     => __( 'Instructions', 'wpseed' ),
            'content'   => '',
            'callback'  => array( $this, 'instructions' ),
        ) );

        $screen->add_help_tab( array(
            'id'        => 'wpseed_support_tab',
            'title'     => __( 'Help &amp; Support', 'wpseed' ),
            'content'   => '<h2>' . __( 'Help &amp; Support', 'wpseed' ) . '</h2>' .
            '<p>' . __( 'Support resources for this boilerplate are being updated. In the meantime, refer to the inline documentation throughout the codebase, the docs directory, and the README for guidance on getting started and extending the plugin.', 'wpseed' ) . '</p>',
        ) );

        if( defined( 'WPSEED_GITHUB' ) ) { 
            $screen->add_help_tab( array(
                'id'        => 'wpseed_bugs_tab',
                'title'     => __( 'Found a bug?', 'wpseed' ),
                'content'   =>
                    '<h2>' . __( 'Please Report Bugs!', 'wpseed' ) . '</h2>' .
                    '<p>You could save a lot of people a lot of time by reporting issues. Tell the developers and community what has gone wrong by creating a ticket. Please explain what you were doing, what you expected from your actions and what actually happened. Screenshots and short videos are often a big help as the evidence saves us time, we will give you cookies in return.</p>' .  
                    '<p><a href="' . WPSEED_GITHUB . '/issues?state=open' . '" class="button button-primary">' . __( 'Report a bug', 'wpseed' ) . '</a></p>',
            ) );
        }
        
        /**
        * This is the right side sidebar, usually displaying a list of links. 
        * 
        * @var {WP_Screen|WP_Screen}
        */
        $screen->set_help_sidebar(
            '<p><strong>' . __( 'For more information:', 'wpseed' ) . '</strong></p>' .
            '<p><a href="' . WPSEED_GITHUB . '/wiki" target="_blank">' . __( 'About WPSeed', 'wpseed' ) . '</a></p>' .
            '<p><a href="' . WPSEED_GITHUB . '" target="_blank">' . __( 'GitHub project', 'wpseed' ) . '</a></p>' .
            '<p><a href="' . WPSEED_GITHUB . '/blob/master/CHANGELOG.txt" target="_blank">' . __( 'Change Log', 'wpseed' ) . '</a></p>' .
            '<p><a href="https://pluginseed.wordpress.com" target="_blank">' . __( 'Blog', 'wpseed' ) . '</a></p>'
        );
        
        $screen->add_help_tab( array(
            'id'        => 'wpseed_wizard_tab',
            'title'     => __( 'Setup wizard', 'wpseed' ),
            'content'   =>
                '<h2>' . __( 'Setup wizard', 'wpseed' ) . '</h2>' .
                '<p>' . __( 'If you need to access the setup wizard again, please click on the button below.', 'wpseed' ) . '</p>' .
                '<p><a href="' . admin_url( 'index.php?page=wpseed-setup' ) . '" class="button button-primary">' . __( 'Setup wizard', 'wpseed' ) . '</a></p>',
        ) );   
             
        $screen->add_help_tab( array(
            'id'        => 'wpseed_tutorial_tab',
            'title'     => __( 'Tutorial', 'wpseed' ),
            'content'   =>
                '<h2>' . __( 'Pointers Tutorial', 'wpseed' ) . '</h2>' .
                '<p>' . __( 'The plugin will explain some features using WordPress pointers.', 'wpseed' ) . '</p>' .
                '<p><a href="' . admin_url( 'admin.php?page=wpseed&amp;wpseedtutorial=normal' ) . '" class="button button-primary">' . __( 'Star Tutorial', 'wpseed' ) . '</a></p>',
        ) );
  
        $screen->add_help_tab( array(
            'id'        => 'wpseed_contribute_tab',
            'title'     => __( 'Contribute', 'wpseed' ),
            'content'   => '<h2>' . __( 'Everyone Can Contribute', 'wpseed' ) . '</h2>' .
            '<p>' . __( 'You can contribute in many ways and by doing so you will help the project thrive.', 'wpseed' ) . '</p>' .
            '<p><a href="' . WPSEED_DONATE . '" class="button button-primary">' . __( 'Donate', 'wpseed' ) . '</a> <a href="' . WPSEED_GITHUB . '/wiki" class="button button-primary">' . __( 'Update Wiki', 'wpseed' ) . '</a> <a href="' . WPSEED_GITHUB . '/issues" class="button button-primary">' . __( 'Fix Bugs', 'wpseed' ) . '</a></p>',
        ) );

        $screen->add_help_tab( array(
            'id'        => 'wpseed_newsletter_tab',
            'title'     => __( 'Newsletter', 'wpseed' ),
            'content'   => '<h2>' . __( 'Annual Newsletter', 'wpseed' ) . '</h2>' .
            '<p>' . __( 'Mailchip is used to manage the projects newsletter subscribers list.', 'wpseed' ) . '</p>' .
            '<p>' . __( 'Visit the MailChimp website to subscribe to the WPSeed newsletter.', 'wpseed' ) . '</p>' .
            '<p><a href="http://eepurl.com/2W_2n" class="button button-primary" target="_blank">' . __( 'Subscribe to Newsletter', 'wpseed' ) . '</a></p>',
        ) );
        
        $screen->add_help_tab( array(
            'id'        => 'wpseed_credits_tab',
            'title'     => __( 'Credits', 'wpseed' ),
            'content'   => '<h2>' . __( 'Credits', 'wpseed' ) . '</h2>' .
            '<p>Please do not remove credits from the plugin. You may edit them or give credit somewhere else in your project.</p>' . 
            '<h4>' . __( 'Automattic - they created the best way to create plugins so we can all get more from WP.', 'wpseed' ) . '</h4>' .
            '<h4>' . __( 'Brian at WPMUDEV - our discussion led to this project and entirely new approach in my development.', 'wpseed' ) . '</h4>' . 
            '<h4>' . __( 'Ignacio Cruz at WPMUDEV - has provided a great approach to handling shortcodes.', 'wpseed' ) . '</h4>' .
            '<h4>' . __( 'Ashley Rich (A5shleyRich) - author of a crucial piece of the puzzle, related to asynchronous background tasks.', 'wpseed' ) . '</h4>' .
            '<h4>' . __( 'Igor Vaynberg - thank you for an elegant solution to searching within a menu.', 'wpseed' ) . '</h4>'
        ) );

        $screen->add_help_tab( array(
            'id'        => 'wpseed_about_tab',
            'title'     => __( 'FifeCIC', 'wpseed' ),
            'content'   => '<!-- FifeCIC About Tab v1.0 --><h2>' . __( 'About FifeCIC', 'wpseed' ) . '</h2>' .
            '<p>' . __( 'This plugins developer is supported by FifeCIC (Fife Community Interest Company), a non-profit organization dedicated to serving our local community through technology and innovation.', 'wpseed' ) . '</p>' .
            '<h3>' . __( 'Our Mission', 'wpseed' ) . '</h3>' .
            '<p>' . __( 'FifeCIC exists to empower communities through accessible digital solutions. We believe that quality software should be available to everyone, regardless of budget, and that technology can be a force for positive social change.', 'wpseed' ) . '</p>' .
            '<h3>' . __( 'Volunteer Development', 'wpseed' ) . '</h3>' .
            '<p>' . __( 'This plugin was lovingly crafted by Ryan Bayne, a volunteer developer committed to FifeCIC\'s vision. Every feature, every line of code, represents hours of unpaid dedication to making WordPress better for everyone.', 'wpseed' ) . '</p>' .
            '<p>' . __( 'As a Community Interest Company, we reinvest everything back into our projects and community initiatives. We don\'t have corporate backing or venture capital—just passionate people who believe in what we\'re doing.', 'wpseed' ) . '</p>' .
            '<h3>' . __( 'How You Can Help', 'wpseed' ) . '</h3>' .
            '<p>💝 <strong>' . __( 'Donate:', 'wpseed' ) . '</strong> ' . __( 'Your financial support helps us dedicate more time to development, hosting, and community outreach. Every contribution, no matter how small, makes a real difference.', 'wpseed' ) . '</p>' .
            '<p>🤝 <strong>' . __( 'Get Involved:', 'wpseed' ) . '</strong> ' . __( 'Whether you\'re a developer, designer, tester, or just enthusiastic about our mission, we\'d love to have you join us. Check out our GitHub repository or contact us directly.', 'wpseed' ) . '</p>' .
            '<p>⭐ <strong>' . __( 'Spread the Word:', 'wpseed' ) . '</strong> ' . __( 'Leave a review, share with colleagues, or simply tell others about FifeCIC. Community support is our lifeblood.', 'wpseed' ) . '</p>' .
            '<p>🐛 <strong>' . __( 'Report Issues:', 'wpseed' ) . '</strong> ' . __( 'Help us improve by reporting bugs and suggesting features. Your feedback shapes our roadmap.', 'wpseed' ) . '</p>' .
            '<h3>' . __( 'Connect With Us', 'wpseed' ) . '</h3>' .
            '<p><a href="#" class="button">' . __( 'Website', 'wpseed' ) . '</a> ' .
            '<a href="#" class="button">' . __( 'GitHub', 'wpseed' ) . '</a> ' .
            '<a href="#" class="button">' . __( 'Email', 'wpseed' ) . '</a> ' .
            '<a href="#" class="button button-primary">' . __( 'Donate', 'wpseed' ) . '</a></p>'
        ) );
                    
        $screen->add_help_tab( array(
            'id'        => 'wpseed_faq_tab',
            'title'     => __( 'FAQ', 'wpseed' ),
            'content'   => '',
            'callback'  => array( $this, 'faq' ),
        ) );
                        
    }
    
    /**
     * Instructions tab content - step-by-step guide for using verification tabs
     */
    public function instructions() {
        ?>
        <div class="wpseed-instructions">
            <h2><?php esc_html_e( 'Step-by-Step Verification Process', 'wpseed' ); ?></h2>
            <p><?php esc_html_e( 'Follow these tabs in order to achieve optimal verification results:', 'wpseed' ); ?></p>
            
            <div class="instruction-steps">
                <div class="step-card">
                    <h3><span class="step-number">1</span> <?php esc_html_e( 'Configure', 'wpseed' ); ?></h3>
                    <p><?php esc_html_e( 'Set up your verification preferences, exclusion rules, and scanning options. This determines what files will be checked and which rules to apply.', 'wpseed' ); ?></p>
                    <p><strong><?php esc_html_e( 'Key Actions:', 'wpseed' ); ?></strong> <?php esc_html_e( 'Select verification rules, configure exclusions, set scanning depth.', 'wpseed' ); ?></p>
                </div>
                
                <div class="step-card">
                    <h3><span class="step-number">2</span> <?php esc_html_e( 'Hash Generation', 'wpseed' ); ?></h3>
                    <p><?php esc_html_e( 'Generate file hashes for incremental scanning. This creates a baseline to detect which files have changed since the last scan.', 'wpseed' ); ?></p>
                    <p><strong><?php esc_html_e( 'Key Actions:', 'wpseed' ); ?></strong> <?php esc_html_e( 'Generate initial hashes, validate hash creation, review file coverage.', 'wpseed' ); ?></p>
                </div>
                
                <div class="step-card">
                    <h3><span class="step-number">3</span> <?php esc_html_e( 'Exclusions', 'wpseed' ); ?></h3>
                    <p><?php esc_html_e( 'Manage files and directories to exclude from verification. This step processes your exclusion rules and creates the final scan list.', 'wpseed' ); ?></p>
                    <p><strong><?php esc_html_e( 'Key Actions:', 'wpseed' ); ?></strong> <?php esc_html_e( 'Review excluded files, add new exclusions, validate exclusion patterns.', 'wpseed' ); ?></p>
                </div>
                
                <div class="step-card">
                    <h3><span class="step-number">4</span> <?php esc_html_e( 'Readiness Check', 'wpseed' ); ?></h3>
                    <p><?php esc_html_e( 'Verify your configuration is ready for verification. This generates a readiness score based on your current settings and file status.', 'wpseed' ); ?></p>
                    <p><strong><?php esc_html_e( 'Key Actions:', 'wpseed' ); ?></strong> <?php esc_html_e( 'Review readiness score, address any issues, confirm scan parameters.', 'wpseed' ); ?></p>
                </div>
                
                <div class="step-card">
                    <h3><span class="step-number">5</span> <?php esc_html_e( 'Advanced Verification', 'wpseed' ); ?></h3>
                    <p><?php esc_html_e( 'Run the comprehensive verification scan. This performs the actual code analysis and generates your final results.', 'wpseed' ); ?></p>
                    <p><strong><?php esc_html_e( 'Key Actions:', 'wpseed' ); ?></strong> <?php esc_html_e( 'Start verification, monitor progress, review results and recommendations.', 'wpseed' ); ?></p>
                </div>
            </div>
            
            <div class="instruction-tips">
                <h3><?php esc_html_e( 'Important Tips', 'wpseed' ); ?></h3>
                <ul>
                    <li><?php esc_html_e( 'Complete each step before moving to the next for best results', 'wpseed' ); ?></li>
                    <li><?php esc_html_e( 'Use the validation features in each step to ensure proper configuration', 'wpseed' ); ?></li>
                    <li><?php esc_html_e( 'The readiness score helps identify potential issues before running the full scan', 'wpseed' ); ?></li>
                    <li><?php esc_html_e( 'Review exclusions carefully to avoid scanning unnecessary files', 'wpseed' ); ?></li>
                </ul>
            </div>
        </div>
        
        <style>
        .wpseed-instructions {
            max-width: 800px;
        }
        .instruction-steps {
            margin: 20px 0;
        }
        .step-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #0073aa;
        }
        .step-card h3 {
            margin-top: 0;
            color: #0073aa;
        }
        .step-number {
            background: #0073aa;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            margin-right: 10px;
            font-weight: bold;
        }
        .instruction-tips {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
        }
        .instruction-tips h3 {
            margin-top: 0;
            color: #856404;
        }
        .instruction-tips ul {
            margin-bottom: 0;
        }
        </style>
        <?php
    }
    
    public function faq() {
        $questions = array(
            0 => __( '-- Select a question --', 'wpseed' ),
            1 => __( "Do I need to give credit to you (Ryan Bayne) if I create a plugin using the seed?", 'wpseed' ),
            2 => __( "Can I hire you (Ryan Bayne) to create a plugin for me using the seed?", 'wpseed' ),
            3 => __( "Is there support for anyone using this boilerplate to create a plugin?", 'wpseed' ),
        );  
        
        wp_add_inline_style( 'wp-admin', '.faq-answers li { background:white; padding:10px 20px; border:1px solid #cacaca; }' );
        
        ?>

        <p>
            <ul id="faq-index">
                <?php foreach ( $questions as $question_index => $question ): ?>
                    <li data-answer="<?php echo esc_attr($question_index); ?>"><a href="#q<?php echo esc_attr($question_index); ?>"><?php echo esc_html($question); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </p>
        
        <ul class="faq-answers">
            <li class="faq-answer" id='q1'>
                <?php esc_html_e('There are multiple developers mentioned in the documentation of this plugin. You must continue to give credit to them all. Removing credits and any reference to repositories will make it difficult for developers to maintain the plugin you create. If you want my support you must also mentioned myself and the WordPress Plugin Seed on your plugins main page.', 'wpseed');?>
            </li>
            <li class="faq-answer" id='q2'>
                <p> <?php esc_html_e('Yes, you can hire me (the plugin author) to create a plugin for you and prices vary but start very low. Technically it takes a only a few minutes to create a new plugin using my boilerplate. You can pay me a small fee to start your plugin and then make separate agreements for doing more work to it.', 'wpseed');?> </p>
            </li>

            <li class="faq-answer" id='q3'>
                <p> <?php esc_html_e('There is always some level of free support but I will expect to see some credit giving to myself and the project. Support is only offered when getting started or your plugin is already available on the WordPress.org repository. If you require support for a premium/commercial plugin project then you will have to pay a small consultation fee.', 'wpseed');?> </p>
            </li>
     
        </ul>
             
        <?php
        $faq_script = "
            jQuery( document).ready( function( $ ) {
                var selectedQuestion = '';

                function selectQuestion() {
                    var q = $( '#' + $(this).val() );
                    if ( selectedQuestion.length ) {
                        selectedQuestion.hide();
                    }
                    q.show();
                    selectedQuestion = q;
                }

                var faqAnswers = $('.faq-answer');
                var faqIndex = $('#faq-index');
                faqAnswers.hide();
                faqIndex.hide();

                var indexSelector = $('<select/>')
                    .attr( 'id', 'question-selector' )
                    .addClass( 'widefat' );
                var questions = faqIndex.find( 'li' );
                var advancedGroup = false;
                questions.each( function () {
                    var self = $(this);
                    var answer = self.data('answer');
                    var text = self.text();
                    var option;

                    if ( answer === 39 ) {
                        advancedGroup = $( '<optgroup />' )
                            .attr( 'label', '" . esc_js( __( 'Advanced: This part of FAQ requires some knowledge about HTML, PHP and/or WordPress coding.', 'wpseed' ) ) . "' );

                        indexSelector.append( advancedGroup );
                    }

                    if ( answer !== '' && text !== '' ) {
                        option = $( '<option/>' )
                            .val( 'q' + answer )
                            .text( text );
                        if ( advancedGroup ) {
                            advancedGroup.append( option );
                        }
                        else {
                            indexSelector.append( option );
                        }

                    }

                });

                faqIndex.after( indexSelector );
                indexSelector.before(
                    $('<label />')
                        .attr( 'for', 'question-selector' )
                        .text( '" . esc_js( __( 'Select a question', 'wpseed' ) ) . "' )
                        .addClass( 'screen-reader-text' )
                );

                indexSelector.change( selectQuestion );
            });
        ";
        wp_add_inline_script( 'jquery', $faq_script );
        ?>        

        <?php 
    }
}

endif;

return new WPSeed_Admin_Help();
