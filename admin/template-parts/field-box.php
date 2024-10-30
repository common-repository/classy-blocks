<?php
    $field = empty( $field ) ? array() : $field;
?>
<div class="<?php echo esc_attr( $whcb_field_classes ); ?> row" id="<?php if ( !empty( $field['key'] ) ) echo $field['key']; ?>">
    <input type="hidden" data-target="menu_order" name="menu_order" value="<?php echo !empty( $field['menu_order'] ) ? (int)$field['menu_order'] : 1; ?>">
    <div class="whcb-field-summary col-12 mb-4">
        <div class="whcb-field-summary-header row">
            <div class="col-2">
                <span class="order-handle" data-target="order"><?php echo !empty( $field['menu_order'] ) ? (int)$field['menu_order'] : 1; ?></span>
            </div>
            <div class="col-9">
                <a href="#" class="whcb-name toggle-field-button" data-target="field-label-display"><?php echo !empty( $field['field_label'] ) ? $field['field_label'] : 'New Field'; ?></a>
                <div class="row-actions">
                    <span class="edit"><a href="#" class="toggle-field-button">Edit</a></span>
                    |
                    <span class="delete"><a href="#" class="delete-field">Delete</a></span>
                </div>
            </div>
            <div class="col-1">
                <a href="#" class="toggle-field-button">
                    <span class="toggle-indicator" aria-hidden="true"></span>
                </a>
            </div>
        </div>
    </div>
    <div class="whcb-settings col-12 pb-3">
        <div class="form-group row">
            <div class="col-2">
                <label for="field-name">Field Label</label>
                <small>The name of your field.</small>
            </div>
            <div class="col-10">
                <input type="text" class="form-control field-label" name="field_label" value="<?php if ( !empty( $field['field_label'] ) ) echo esc_attr( $field['field_label'] ); ?>">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-2">
                <label for="field-name">Instructions</label>
                <small>Some helpful info on what this field does.</small>
            </div>
            <div class="col-10">
                <input type="text" class="form-control" name="help" value="<?php if ( !empty( $field['help'] ) ) echo esc_attr( $field['help'] ); ?>">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-2">
                <label for="styles">CSS Class Names</label>
                <small>A user friendly label followed by a CSS class name.</small>
            </div>
            <div class="col-10">
                <textarea class="styles-input form-control" name="class_names" placeholder="My Nice Name | my-css-class-name"><?php if (!empty( $field['class_names'] ) ) echo esc_textarea( $field['class_names'] ); ?></textarea>
                <small>Enter one "label | class-name" per line.  CSS classes can be added to the <a href="https://webheadcoder.com/wp-admin/themes.php?page=editcss-customizer-redirect">WordPress Customizer</a> or directly in your theme's stylesheets.</small>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-2">
                <label for="allow-multiple">Allow Multiple Selection?</label>
            </div>
            <div class="col-10">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is-multiple" name="is_multiple" value="1" <?php checked( !empty( $field['is_multiple'] ), true ); ?>>
                    <label class="form-check-label" for="is-multiple">Yes</label>
                </div>
                
            </div>
        </div>
        <div class="form-group row">
            <div class="col-2">
                <label for="allow-multiple">Show for Block Types</label>
                <small>Limit where this field will show.</small>
            </div>
            <div class="col-10">
                <select class="select2 block-types" name="block_types[]" multiple="multiple"></select>
            </div>
        </div>
        <div class="form-group row">
            <div class="offset-2 col-10">
                <a href="#" class="button toggle-field-button">Minimize Field</a>
            </div>
        </div>
    </div>
</div>