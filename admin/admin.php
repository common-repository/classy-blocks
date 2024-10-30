<?php

class WHCBAdmin {

    /**
     * Start everything
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );

        require_once( plugin_dir_path( WHCB::PLUGIN ) . '/admin/admin-fields.php' );
    }

    /**
     * Show the CB menu
     */
    function admin_menu() {
        $slug = 'edit.php?post_type=whcb-field-group';
        $cap = whcb()->options( 'capability' );


        // add parent
        add_menu_page( __( 'Classy Blocks', 'whcb' ), __('Classy Blocks', 'whcb' ), $cap, $slug, false, 'dashicons-palmtree', 81 );


        // add children
        add_submenu_page( $slug, __( 'Field Groups', 'whcb' ), __( 'Field Groups', 'whcb' ), $cap, $slug );
        add_submenu_page( $slug, __( 'Add New', 'whcb' ), __( 'Add New', 'whcb' ), $cap, 'post-new.php?post_type=whcb-field-group' );
    }
}

new WHCBAdmin();