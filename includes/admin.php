<?php

/*

1. Custom dashboard widget

*/

/* 1. Custom dashboard widget
-------------------------------------------------------- */

add_action('wp_dashboard_setup', 'hw_feedback_custom_dashboard_widgets');

function hw_feedback_custom_dashboard_widgets() {
  global $wp_meta_boxes;

  wp_add_dashboard_widget('hw_feedback_local_services_widget', 'Deregistered Local Services', 'hw_feedback_custom_dashboard_local_services');
}

function hw_feedback_custom_dashboard_local_services() {
  echo '<div id="dashboard_local_services_header" class="dashboard_local_services_flex-container">';
  echo '<div id="count_label_header" class="dashboard_local_services_header count_label">Type</div>';
  echo '<div id="total_value_header" class="dashboard_local_services_header total_value">Total</div>';
  echo '<div id="count_value_header" class="dashboard_local_services_header count_value">Deregistered</div>';
  echo '</div>';
  // get all the terms
  $terms = get_terms('service_types');
  $total_dereg_count = 0;

  foreach ( $terms as $term ) {
    $args = array(
      'post_type' => 'local_services',
      'posts_per_page' => -1,
      'tax_query' => array(
        'relation' => 'AND',
        array(
          'taxonomy' => 'service_types',
          'field'    => 'slug',
          'terms'    => $term->slug,
        ),
        array(
          'taxonomy' => 'cqc_reg_status',
          'field'    => 'slug',
          'terms'    => 'deregistered',
        )
      )
    );
    $query = new WP_Query($args);
    $count = $query->post_count;
    if ($count) {
      echo '<div class="dashboard_local_services_flex-container">';
      echo '<div id="count_label_' . $term->term_id . '" class="count_label">' . $term->name . '</div>';
      echo '<div id="total_value_' . $term->term_id . '" class="total_value">' . $term->count . '</div>';
      $url_query_string = 'edit.php?post_type=local_services&cqc_reg_status=deregistered&service_types=' . $term->slug;
      echo'<div id="count_value_' . $term->term_id . '" class="count_value"><a href="'.admin_url( $url_query_string, 'https' ).'">' . $count . '</a></div>';
      echo '</div>';
    }
    $total_dereg_count += $count;
  }
  if ( $total_dereg_count ) {
    echo '<div id="dashboard_local_services_footer" class="dashboard_local_services_flex-container">';
    echo '<div id="count_label_footer" class="dashboard_local_services_footer count_label">Total</div>';
    echo '<div id="total_value_footer" class="dashboard_local_services_footer total_value"></div>';
    echo '<div id="count_value_footer" class="dashboard_local_services_footer count_value"><a href="'.admin_url( 'edit.php?post_type=local_services&cqc_reg_status=deregistered', 'https' ).'">' . $total_dereg_count . '</a></div>';
    echo '</div>';
  } else {
    echo '<div class="dashboard_local_services_flex-container"><p>No deregistered services found.</p></div>';
  }
}

/* 2. Tidy
------------------------------------------------------------------------------ */

// Remove meta boxes
function hw_feedback_remove_meta_boxes() {
	remove_meta_box( 'postcustom' , 'local_services' , 'normal' ); // custom fields metabox
	//remove_meta_box( 'commentstatusdiv' , 'local_services' , 'normal' ); // discussion metabox
}
add_action( 'admin_menu' , 'hw_feedback_remove_meta_boxes' );

/* 3. Wordpress Media handler
------------------------------------------------------------------------------ */

function load_wp_media_files( $page ) {
  // change to the $page where you want to enqueue the script
  if( $page == 'term.php' || $page == 'edit-tags.php' ) {
    // Enqueue WordPress media scripts
    wp_enqueue_media();
    // Enqueue custom script that will interact with wp.media
    wp_enqueue_script( 'hw_feedback_script', plugins_url( '/js/wordpress_media.js' , __DIR__ ), array('jquery'), '0.1' );
  }
}
add_action( 'admin_enqueue_scripts', 'load_wp_media_files' );

?>
