<div class="whcb-bootstrap">
    <h2 class="section-heading">Fields</h2>
    <div class="whcb-fields container">
        <?php 
            global $post;
            $fields = get_post_meta( $post->ID, WHCBFields::POST_META_KEY_FIELD, true );
            $whcb->get_template_part( 'field-box', array( 'whcb_field_classes' => 'whcb-field-template closed' ) ); 
        ?>
        <div class="header-row row <?php if ( empty( $fields ) ) echo 'd-none'; ?>">
            <div class="col-12">
                <div class="row py-2">
                    <div class="col-2">Order</div>
                    <div class="col-9">Label</div>
                </div>
            </div>
        </div>
        <div class="row no-fields-found-row <?php if ( !empty( $fields ) ) echo 'd-none'; ?>">
            <div class="col-12">
                <div class="row whcb-field-info py-4">
                    <div class="col-12">
                        <h2 class="heading">No fields found</h2>
                        <strong><?php echo __( 'Click the "Add Field" button below to add a field.', 'whcb' ); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    
        <?php
        if ( !empty( $fields ) ) :
            foreach ( $fields as $field ) :
                $whcb->get_template_part( 'field-box', array( 
                    'whcb_field_classes' => 'whcb-field closed',
                    'field'              => $field
                ) );
            endforeach;
        endif;
        ?>

    </div>
    <div class="row footer-row whcb-add-row my-4 text-right">
        <div class="col-12">
            <a href="#" class="button button-secondary button-large whcb-add-field">Add Field</a>
        </div>
    </div>


    <h2 class="section-heading">Field Group Options</h2>
    <div class="whcb-field-group-options container">
        <div class="form-group row">
            <div class="whcb-settings col-12 pb-3 pt-4">
                <div class="form-group row">
                    <div class="col-2">
                        <label for="field-name">Show for Block Types</label>
                        <small>Limit where this group of fields will show.</small>
                    </div>
                    <div class="col-10">
                        <select class="select2 block-types" name="group_block_types[]" multiple="multiple"></select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>