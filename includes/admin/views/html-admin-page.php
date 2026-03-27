<?php
/**
 * Admin Views Default Structure 
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}    
                        
?>
<div class="wrap wpseed">

    <?php
    // Establish Title — read-only navigation parameters gated behind current_user_can()
    // as this template is only included in admin context.
    $wpseed_title = '';
    if ( ! current_user_can( 'manage_options' ) ) {
        $wpseed_title = '';
    } elseif ( ! isset( $_GET['listtable'] ) ) {
        $wpseed_title = array_values( $tabs[ $current_tab ]['maintabviews'] )[0]['title'];
    } elseif ( isset( $_GET['seedview'] ) ) {
        // isset() check added — $_GET['seedview'] used as array key requires validation.
        $wpseed_seedview = sanitize_key( wp_unslash( $_GET['seedview'] ) );
        $wpseed_title    = isset( $tabs[ $current_tab ]['maintabviews'][ $wpseed_seedview ] )
            ? $tabs[ $current_tab ]['maintabviews'][ $wpseed_seedview ]['title']
            : '';
    }

    echo '<h1>WPSeed: ' . esc_html( $wpseed_title ) . '</h1>';
    ?>
    
    <!-- TABS -->
    <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
        <?php
            foreach ( $tabs as $wpseed_key => $wpseed_report_group ) {
                echo '<a href="' . esc_url( admin_url( 'admin.php?page=wpseed&tab=' . urlencode( $wpseed_key ) ) ) . '" class="nav-tab ';
                if ( $current_tab == $wpseed_key ) {
                    echo 'nav-tab-active';
                }
                echo '">' . esc_html( $wpseed_report_group[ 'title' ] ) . '</a>';
            }

            do_action( 'wpseed_mainview_tabs' );
        ?>
    </nav>
    
    
    <?php if ( sizeof( $tabs[ $current_tab ]['maintabviews'] ) > 1 ) { ?>
        <!-- SUB VIEWS (within selected tab) -->
        <ul class="subsubsub">
            <li><?php

                $wpseed_links = array();

                foreach ( $tabs[ $current_tab ]['maintabviews'] as $wpseed_key => $tab ) {

                    $link = '<a href="admin.php?page=wpseed&tab=' . urlencode( $current_tab ) . '&amp;seedview=' . urlencode( $wpseed_key ) . '" class="';
  
                    if ( $wpseed_key == $current_tablelist ) {
                        $link .= 'current';
                    }

                    $link .= '">' . $tab['title'] . '</a>';

                    $wpseed_links[] = $link;

                }

                echo wp_kses_post( implode( ' | </li><li>', $wpseed_links ) );

            ?></li>
        </ul>
        <br class="clear" />
        <?php
    }

    if ( isset( $tabs[ $current_tab ][ 'maintabviews' ][ $current_tablelist ] ) ) {

        $tabs = $tabs[ $current_tab ][ 'maintabviews' ][ $current_tablelist ];

        if ( ! isset( $tabs['hide_title'] ) || $tabs['hide_title'] != true ) {
            echo '<h1>' . esc_html( $tabs['title'] ) . '</h1>';
        } else {
            echo '<h1 class="screen-reader-text">' . esc_html( $tabs['title'] ) . '</h1>';
        }

        if ( $tabs['description'] ) {
            echo '<p>' . wp_kses_post( $tabs['description'] ) . '</p>';
        }

        if ( $tabs['callback'] && ( is_callable( $tabs['callback'] ) ) ) {
            call_user_func( $tabs['callback'], $current_tablelist );
        }
    }
    ?>
</div>
