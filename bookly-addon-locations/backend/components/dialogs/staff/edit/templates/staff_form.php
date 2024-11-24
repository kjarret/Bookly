<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="form-group bookly-js-locations-row">
    <label><?php form_option( 'bookly_l10n_label_location' ) ?></label>
    <ul id="bookly-js-locations"
        data-icon-class="far fa-flag"
        data-txt-select-all="<?php esc_attr_e( 'All locations', 'bookly' ) ?>"
        data-txt-all-selected="<?php esc_attr_e( 'All locations', 'bookly' ) ?>"
        data-txt-nothing-selected="<?php esc_attr_e( 'No locations selected', 'bookly' ) ?>"
    >
        <?php foreach ( $locations as $location ): ?>
            <li data-input-name="location_ids[]" data-value="<?php echo $location['id'] ?>" data-selected="<?php echo (int) in_array( $location['id'], $staff_locations ) ?>">
                <?php echo esc_html( $location['name'] ) ?>
            </li>
        <?php endforeach ?>
    </ul>
    <small class="form-text text-muted"><?php esc_html_e( 'Select locations where the services are provided.', 'bookly' ) ?></small>
</div>