<?php

class WHCBFields {

    const POST_META_KEY_FIELD = '_whcb_field_meta';
    const POST_META_KEY_GROUP = '_whcb_field_group_meta';

    const POST_TYPE_FIELD_GROUP = 'whcb-field-group';

    /**
     * Get all fields 
     */
    public static function get_simple_fields_for_editor() {
        // get all field groups
        $field_groups = get_posts( array(
            'post_status'    => 'private',
            'post_type'      => WHCBFields::POST_TYPE_FIELD_GROUP,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'no_found_rows'  => true,
            'posts_per_page' => -1
        ) );

        $ret = array();
        foreach ( $field_groups as $field_group_post ) {
            $wp_formatted_fields = get_post_meta( $field_group_post->ID, WHCBFields::POST_META_KEY_FIELD, true );
            $group_options = get_post_meta( $field_group_post->ID, WHCBFields::POST_META_KEY_GROUP, true );
            if ( empty( $group_options ) ) {
                $group_options = array();
            }
            $group = array(
                'key'         => $field_group_post->post_name,
                'fields'      => array(),
                'blockTypes'  => !empty( $group_options['block_types'] ) ? $group_options['block_types'] : array(),
                'panel_name'  => $field_group_post->post_title
            );
            foreach( $wp_formatted_fields as $wp_formatted_field ) {
                $field = array(
                    'key'         => $wp_formatted_field['key'],
                    'label'       => $wp_formatted_field['field_label'],
                    'help'        => $wp_formatted_field['help'],
                    'options'     => $wp_formatted_field['_class_names'],
                    'isMultiple'  => !empty( $wp_formatted_field['is_multiple'] ) ? $wp_formatted_field['is_multiple'] : false,
                    'blockTypes'  => !empty( $wp_formatted_field['_block_types'] ) ? $wp_formatted_field['_block_types'] : array()
                );
                $group['fields'][] = $field;
            }
            if ( !empty( $group['fields'] ) ) {
                $ret[] = $group;   
            }
        }
        return $ret;
    }

    /**
     * Convert simplified field definitions to what we need.
     */
    public static function convert_simple_fields( $field_groups ) {
        for ( $i = 0; $i < count( $field_groups ); $i++ ) {
            $fields = $field_groups[$i]['fields'];
            for ( $j = 0; $j < count( $fields ); $j++ ) { 
                if ( !empty( $fields[$j]['control'] ) ) {
                    continue;
                }
                $simple_field = $fields[$j];
                $is_multiple = !empty( $simple_field['isMultiple'] ) ? $simple_field['isMultiple'] : false;
                $att_type = $is_multiple ? 'array' : 'object';
                $fields[$j] = array(
                    'key'         => $simple_field['key'],
                    'label'       => $simple_field['label'],
                    'help'        => !empty( $simple_field['help'] ) ? $simple_field['help'] : '',
                    'isAttribute' => true,
                    'attributeArgs' => array(
                        'type' => $att_type
                    ),
                    'control'     => 'SimpleCSS',
                    'controlArgs' => array(
                        'options'     => $simple_field['options'],
                        'isMultiple'  => $is_multiple
                    ),
                    'blockTypes' => empty( $simple_field['blockTypes'] ) ? array() : $simple_field['blockTypes']
                );
            }
            $field_groups[$i]['fields'] = $fields;
        }

        return $field_groups;
    }

    public static function filter_post_data( $post, $postarr ) {
        if ( $post['post_type'] != WHCBFields::POST_TYPE_FIELD_GROUP ) {
            return $post;
        }

        if ($post['post_status'] != 'trash' && 
            $post['post_status'] != 'draft' && 
            $post['post_status'] != 'auto-draft' ) {

            $post['post_status'] = 'private';
        }
        return $post;
    }

    /**
     * Save the fields when the field group is saved
     * Will always get all field groups.
     * store everything in post_content since nothing is searched in the DB.  
     */
    public static function save_fields( $field_group_id ) {
        remove_action( 'save_post_'.WHCBFields::POST_TYPE_FIELD_GROUP, 'WHCBFields::save_fields' );
        if ( !isset( $_POST['whcb'] ) ) {
            update_post_meta( $field_group_id, WHCBFields::POST_META_KEY_FIELD, array() );
            return;
        }

        $data = array();
        foreach ( $_POST['whcb'] as $key => $item ) {
            $check_empty = $item;
            unset( $check_empty['menu_order'] );
            if( !array_filter( $check_empty ) ) {
                // everything empty
                continue;
            }
            // parse class names
            $class_names = array();
            if ( !empty( $item['class_names'] ) ) {
                $lines = explode( "\n", $item['class_names'] );
                foreach( $lines as $line ) {
                    $value_key = explode( '|', $line );
                    if ( count( $value_key ) != 2 ) {
                        // no way to throw an error here, just skip it.
                        continue;
                    }
                    $class_label = trim( $value_key[0] );
                    $css_class_name = trim( $value_key[1] );
                    // $css_class_name must be a valid css class, only one class name, no spaces.
                    // https://stackoverflow.com/questions/448981/which-characters-are-valid-in-css-class-names-selectors
                    if ( preg_match( '/^-?[_a-zA-Z]+[_a-zA-Z0-9-]*$/', $css_class_name ) ) {
                        $class_names[] = array( 'label' => $class_label, 'value' => $css_class_name );
                    }
                }
            }

            if ( !empty( $item['block_types'] ) ) {
                // filter out non-valid block type names
                // https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-registration/
                $block_types = array_filter( $item['block_types'], function( $val ) {
                    return preg_match( '/^[a-z][a-z0-9_\/]*$/', $val );
                } );
            }
            else {
                $block_types = array();
            }

            $data[$key] = $item;
            $data[$key]['key'] = $key;
            $data[$key]['_class_names'] = $class_names;
            $data[$key]['_block_types'] = $block_types;
            $data[$key]['field_label'] = !empty( $item['field_label'] ) ? $item['field_label'] : '(untitled)';

        }
        update_post_meta( $field_group_id, WHCBFields::POST_META_KEY_FIELD, $data );

        // save field group options

        $group_options = array();
        if ( !empty( $_POST['group_block_types'] ) ) {
            // filter out non-valid block type names
            // https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-registration/
            $block_types = array_filter( $_POST['group_block_types'], function( $val ) {
                return preg_match( '/^[a-z][a-z0-9_\/]*$/', $val );
            } );
        }
        else {
            $block_types = array();
        }
        $group_options['block_types'] = $block_types;
        update_post_meta( $field_group_id, WHCBFields::POST_META_KEY_GROUP, $group_options );
    }

    /**
     * Register post types to save fields.
     */
    public static function register_post_types() {
        // vars
        $cap = whcb()->options( 'capability' );
        
        register_post_type( WHCBFields::POST_TYPE_FIELD_GROUP, array(
            'labels'            => array(
                'name'                  => __( 'Field Groups', 'whcb' ),
                'singular_name'         => __( 'Field Group', 'whcb' ),
                'add_new'               => __( 'Add New' , 'whcb' ),
                'add_new_item'          => __( 'Add New Field Group' , 'whcb' ),
                'edit_item'             => __( 'Edit Field Group' , 'whcb' ),
                'new_item'              => __( 'New Field Group' , 'whcb' ),
                'view_item'             => __( 'View Field Group', 'whcb' ),
                'search_items'          => __( 'Search Field Groups', 'whcb' ),
                'not_found'             => __( 'No Field Groups found', 'whcb' ),
                'not_found_in_trash'    => __( 'No Field Groups found in Trash', 'whcb' ), 
            ),
            'public'            => false,
            'show_ui'           => true,
            'capability_type'   => 'post',
            'hierarchical'      => false,
            'rewrite'           => false,
            'query_var'         => false,
            'supports'          => array( 'title', 'editor', 'page-attributes' ),
            'show_in_rest'      => true,
            'show_in_menu'      => false,
        ));
        
    }
}