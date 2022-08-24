<?php

/*

1. Register meta term
2. Add form
3. Edit form
4. Save term meta
5. Add column to admin screen
6. Add output to column

*/


/* 1. Register meta term
------------------------------------ */

add_action( 'init', 'hw_feedback_register_meta' );

function hw_feedback_register_meta() {

    register_meta( 'term', 'taxonomy-icon','' );
}


function hw_feedback_get_term_icon( $term_id, $hash = false ) {

    $icon = get_term_meta( $term_id, 'icon', true );

    return $icon;
}




/* 2. Add form
------------------------------------ */

add_action( 'service_types_add_form_fields', 'hw_feedback_new_term_icon_field' );
add_action( 'signpost_categories_add_form_fields', 'hw_feedback_new_term_icon_field' );

function hw_feedback_new_term_icon_field() {

    wp_nonce_field( basename( __FILE__ ), 'hw_term_icon_nonce' );
    $site_icon_id = get_option( 'site_icon' );
    $image = wp_get_attachment_image( $site_icon_id, 'thumbnail', false, array( 'id' => 'hw-feedback-preview-image' ) );?>

    <div class="form-field hw-term-icon-wrap">
        <label for="hw-term-icon">Icon</label>
        <input type="hidden" name="hw_term_icon" id="hw-term-icon" value="<?php echo $site_icon_id; ?>"/>
        <?php echo $image; ?>
        <input type="button" class="button-primary" value="Select an icon" id="hw_feedback_media_manager"/>
    </div>
<?php }




/* 3. Edit form
------------------------------------ */

add_action( 'service_types_edit_form_fields', 'hw_feedback_edit_term_icon_field' );
add_action( 'signpost_categories_edit_form_fields', 'hw_feedback_edit_term_icon_field' );

function hw_feedback_edit_term_icon_field( $term ) {

  $icon   = hw_feedback_get_term_icon( $term->term_id, true );
  if( intval( $icon ) > 0 ) {
    // Change with the image size you want to use
    $image = wp_get_attachment_image( $icon, 'thumbnail', false, array( 'id' => 'hw-feedback-preview-image' ) );
  } else {
    // Some default image
    $site_icon_id = get_option( 'site_icon' );
    $image = wp_get_attachment_image( $site_icon_id, 'thumbnail', false, array( 'id' => 'hw-feedback-preview-image' ) );
  }

		?>

  <tr class="form-field hw-term-icon-wrap">
    <th scope="row"><label for="hw-term-icon"><?php _e( 'Icon', 'hw-feedback' ); ?></label></th>
    <td>
      <?php wp_nonce_field( basename( __FILE__ ), 'hw_term_icon_nonce' ); ?>
      <input type="hidden" name="hw_term_icon" id="hw-term-icon" value="<?php echo esc_attr( $icon ); ?>"/>
      <?php echo $image; ?>
      <input type="button" class="button-primary" value="Select an icon" id="hw_feedback_media_manager"/>
    </td>
  </tr>
  <?php
}




/* 4. Save term meta
------------------------------------ */

add_action( 'edit_service_types',   'hw_feedback_save_term_icon' );
add_action( 'create_service_types', 'hw_feedback_save_term_icon' );
add_action( 'edit_signpost_categories',   'hw_feedback_save_term_icon' );
add_action( 'create_signpost_categories', 'hw_feedback_save_term_icon' );

function hw_feedback_save_term_icon( $term_id ) {

    if ( ! isset( $_POST['hw_term_icon_nonce'] ) || ! wp_verify_nonce( $_POST['hw_term_icon_nonce'], basename( __FILE__ ) ) )
        return;

    $old_icon = hw_feedback_get_term_icon( $term_id );
    $new_icon = $_POST['hw_term_icon'];

    if ( $old_icon && '' === $new_icon )
        delete_term_meta( $term_id, 'icon' );

    else if ( $old_icon !== $new_icon )
        update_term_meta( $term_id, 'icon', $new_icon );
}

// Ajax action to refresh the user image - https://wordpress.stackexchange.com/questions/235406/how-do-i-select-an-image-from-media-library-in-my-plugin
function hw_feedback_get_image() {
  if(isset($_GET['id']) ){
    $image = wp_get_attachment_image( filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT ), 'thumbnail', false, array( 'id' => 'hw-feedback-preview-image' ) );
    $data = array(
      'image'  => $image,
    );
    wp_send_json_success( $data );
  } else {
    wp_send_json_error();
  }
}
add_action( 'wp_ajax_hw_feedback_get_image', 'hw_feedback_get_image'   );


/* 5. Add column to admin screen
------------------------------------ */

add_filter( 'manage_edit-service_types_columns', 'hw_feedback_edit_term_columns' );
add_filter( 'manage_edit-signpost_categories_columns', 'hw_feedback_edit_term_columns' );

function hw_feedback_edit_term_columns( $columns ) {

    $columns['Icon'] = __( 'Icon', 'hw-feedback' );

    return $columns;
}



/* 6. Add output to column
------------------------------------ */

add_filter( 'manage_signpost_categories_custom_column', 'hw_feedback_manage_term_custom_column', 10, 3 );
add_filter( 'manage_service_types_custom_column', 'hw_feedback_manage_term_custom_column', 10, 3 );

function hw_feedback_manage_term_custom_column( $out, $column, $term_id ) {

    if ( 'Icon' === $column ) {

        $icon = hw_feedback_get_term_icon( $term_id, true );

        if ( $icon > 0 ) {
          $out = wp_get_attachment_image( $icon, array(40,40), true);
} else {
        $out = sprintf( '<img width="40" height="40" src="%s" alt="Icon" />', esc_attr( $icon ) );
			}

    }

    return $out;
}
