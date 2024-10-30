<?php 
/*
Plugin Name: Classy Blocks - Custom Fields for Block Classes
Plugin URI: https://webheadcoder.com/classy-block/
Description: Easily add classes to your blocks
Author: Webhead LLC
Author URI: https://webheadcoder.com 
Version: 0.2.1
*/

if( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WHCB' ) ) :

require_once( 'fields/fields.php' );

class WHCB {

    const VERSION = '0.2.1';
    const PLUGIN = __FILE__;
    const OPTIONS_NAME = 'whcb_options';
    const DEBUG = true;

    static $instance;

    /**
     * Get the only instance of the plugin.
     */
    public static function get_instance() {
        if ( NULL === self::$instance ) {
            self::$instance = new WHCB();
        }
        return self::$instance;
    }

    /**
     * Prevent initiation from the outside.
     */
    private function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'rest_api_init', array( $this, 'load_save_filters' ) );

        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Init the plugin
     */
    public function init() {
        $this->plugin_path = plugin_dir_path( self::PLUGIN );
        $fields = WHCBFields::convert_simple_fields( WHCBFields::get_simple_fields_for_editor() );
        $this->whcb_field_groups = apply_filters( 'whcb_field_groups', $fields );
        WHCBFields::register_post_types();
        if ( is_admin() ) {
            $this->load_save_filters();
            require_once( dirname( __FILE__ ) . '/admin/admin.php' );
        }
    }

    public function load_save_filters() {
        add_filter( 'wp_insert_post_data' , 'WHCBFields::filter_post_data' , 99, 2 );
        add_action( 'save_post_'.WHCBFields::POST_TYPE_FIELD_GROUP, 'WHCBFields::save_fields' );
    }

    /**
     * Get option
     */
    public function options( $name ) {
        $options = get_option( self::OPTIONS_NAME );
        if ( !empty( $options ) && isset( $options[$name] ) ) {
            $ret = $options[$name];
        }
        else {
            switch ( $name ) {
                case 'capability':
                    $ret = 'edit_posts';
                    break;
                
                default:
                    break;
            }
        }
        return $ret;
    }

    /**
     * Get the template part within this plugin.
     */
    public function get_template_part( $path, $args = array() ) {

        if ( substr( $path, -4 ) !== '.php' ) {
            $path = dirname( $this::PLUGIN ) . '/admin/template-parts/' . $path . '.php';
        }

        extract( $args );
        include( $path );
    }

    /**
     * Enqueue the scripts for the editor
     */
    public function enqueue_assets() {
        $version = WHCB::DEBUG ? filemtime( $this->plugin_path . '/assets/js/classy-blocks.js' ) : WHCB::VERSION;

        wp_enqueue_script(
            'whcb-blocks',
            plugins_url( 'assets/js/classy-blocks.js', self::PLUGIN ),
            array( 'wp-blocks', 'wp-element', 'wp-editor' ),
            $version
        );
        wp_localize_script( 'whcb-blocks', 'whcb_field_groups', $this->whcb_field_groups );
    }

    /** 
     * Return true if the current screen is the screen_id
     */
    public function is_current_screen( $screen_id ) {
        if ( !function_exists( 'get_current_screen' ) ) {
            return false;
        }

        $current_screen = get_current_screen();

        if ( !$current_screen ) {
            return false;
        } 
        else if ( is_array( $screen_id ) ) {
            return in_array( $current_screen->id, $screen_id );
        }
        else {
            return ( $screen_id === $current_screen->id );
        }
    }

}

/**
 * Convenience method to get whcb instance.
 */
function whcb() {
    global $whcb;

    if ( !isset( $whcb )  ) {
        $whcb = WHCB::get_instance();
    }

    return $whcb;
}

whcb();

endif;