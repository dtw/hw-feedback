<?php

/* Functions for working with local_services */

function hw_feedback_update_inspection_categories ($post_id, $inspection_categories) {
  // update the cqc_inspection_category
  foreach ($inspection_categories as $inspection_category) {
    wp_set_post_terms( $post_id, sanitize_text_field($inspection_category->code) , 'cqc_inspection_category', true );
    if ( isset($inspection_category->primary) && $inspection_category->primary === 'true') {
      $primary_inspection_category = sanitize_text_field($inspection_category->code);
      error_log('hw-feedback: '. $primary_inspection_category);
    }
  }
  return $primary_inspection_category;
}

function hw_feedback_generate_local_service_excerpt ($primary_inspection_category, $api_response) {
  if ($primary_inspection_category == "P2") {
    $post_excerpt = "General practice";
  } else if ($primary_inspection_category == "P7") {
    $post_excerpt = $api_response->gacServiceTypes[0]->description . " - specialism(s): ";
    $provider_specialisms = array_column((array)$api_response->specialisms, 'name');
    $post_excerpt .= implode(', ', $provider_specialisms);
  } else {
    $post_excerpt = $api_response->gacServiceTypes[0]->description;
    if ( isset($api_response->numberOfBeds) ) {
      if ($api_response->numberOfBeds !== 0) {
        $post_excerpt .= ' - ' . $api_response->numberOfBeds . ' beds';
      }
    }
  }
  return $post_excerpt;
}

?>