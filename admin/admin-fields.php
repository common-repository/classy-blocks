<?php


class WHCBAdminFields {

    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_filter( 'enter_title_here', array( $this, 'edit_field_title_placeholder' ), 10, 2 );
        add_action( 'admin_head-edit.php', array( $this, 'hide_quick_edit_links' ) );
        add_action( 'add_meta_boxes', array( $this , 'fields_box' ) );
        add_filter( 'display_post_states', array( $this, 'hide_post_state' ), 10, 2 );
    }

    /**
     * Add the meta box which contains all the fields.
     */
    function fields_box() {
        add_meta_box(
            'whcb-field-box',
            __( 'Fields', 'whcb' ),
            array( $this, 'field_box_html' ),
            'whcb-field-group',
            'normal',
            'high'
        );
    }

    function admin_enqueue_scripts() {
        global $post;

        if ( !whcb()->is_current_screen( 'whcb-field-group' ) ) {
            return;
        }

        wp_enqueue_style( 
            'select2', 
            'https://cdn.jsdelivr.net/npm/select2@4.0.6/dist/css/select2.min.css', 
            array(), 
            '4.0.6'
        );
        wp_enqueue_script( 
            'select2', 
            'https://cdn.jsdelivr.net/npm/select2@4.0.6/dist/js/select2.min.js', 
            array( 'jquery' ),
            '4.0.6'
        );

        $version = WHCB::DEBUG ? time() : WHCB::VERSION;
        
        wp_enqueue_style( 
            'whcb-admin-field-group', 
            plugins_url( 'assets/css/admin.css', WHCB::PLUGIN ), 
            array(), 
            $version 
        );
        wp_enqueue_script( 
            'whcb-admin-field-group', 
            plugins_url( 'assets/js/admin.js', WHCB::PLUGIN), 
            array( 'wp-blocks', 'wp-element', 'wp-dom-ready', 'jquery', 'select2' ), 
            $version 
        );

        $selected_block_types = array();
        $fields = get_post_meta( $post->ID, WHCBFields::POST_META_KEY_FIELD, true );
        if ( !empty( $fields ) ) {
            foreach ( $fields as $key => $field ) {
                $selected_block_types[$key] = $field['block_types'];
            }
        }
        wp_localize_script( 'whcb-admin-field-group', 'whcb_selected_block_types',  $selected_block_types );

        $group_selected_block_types = array();
        $field_group_options = get_post_meta( $post->ID, WHCBFields::POST_META_KEY_GROUP, true );
        if ( !empty( $field_group_options ) && !empty( $field_group_options['block_types'] ) ) {
            $group_selected_block_types = $field_group_options['block_types'];
        }
        wp_localize_script( 'whcb-admin-field-group', 'whcb_group_selected_block_types',  $group_selected_block_types );
    }

    /**
     * Hide some quickedit stuff
     */
    function hide_quick_edit_links() {
        global $current_screen;
        if( 'edit-whcb-field-group' != $current_screen->id )
            return;
        ?>
        <style>
            .inline-edit-col-left .clear {
                display: none;
            }
            #wpbody-content .inline-edit-row-page .inline-edit-col-right {
                margin-top: 0;
            }
            #wpbody-content .bulk-edit-row-post .inline-edit-col-right, 
            #wpbody-content .quick-edit-row-page .inline-edit-col-right {
                width: 100%;
            }
        </style>
        <script type="text/javascript">         
            jQuery(document).ready( function($) {
                $('input[name="post_password"]').each(function(i) {
                    $(this).closest('.inline-edit-group').remove();
                });
                $('select[name="_status"]').each(function(i) {
                    $(this).closest('.inline-edit-group').remove();
                });
                $('fieldset.inline-edit-date').remove();
            });    
        </script>
    <?php
    }

    function edit_field_title_placeholder( $title, $post ) {
        if ( !whcb()->is_current_screen( 'whcb-field-group' ) ) {
            return;
        }
        return __( 'Add Field Group Title', 'whcb' );
    }

    /**
     * Hide private post state on field group listing
     */
    function hide_post_state( $post_state, $post ) {
        if ( $post->post_type == WHCBFields::POST_TYPE_FIELD_GROUP ) {
            $post_state = '';
        }
        return $post_state;
    }

    /**
     * The html for the field box.
     */
    function field_box_html() {
        whcb()->get_template_part( 'field-group', array( 'whcb' => whcb() ) );
    }

}

new WHCBAdminFields();
