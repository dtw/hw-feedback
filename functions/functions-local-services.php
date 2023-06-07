<?php
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