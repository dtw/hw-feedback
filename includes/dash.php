<?php
/* 9. Add Management Page
-------------------------------------------------------- */



function hwbucks_cqc_data_import_tool() {
		add_management_page(
			'CQC Data Import',
			'CQC Data Import',
			'read_private_posts',
			'cqc-data-import',
			'hwbucks_cqc_data_import_contents',
		);
	}

	add_action( 'admin_menu', 'hwbucks_cqc_data_import_tool' );

	function hwbucks_cqc_data_import_contents() {
    // create a simple form
    ?>
    <div id="hwbucks_data_import_tool">
      <h1>CQC Data Import Tool</h1>
      <div id="hwbucks_data_import_tool_instructions">
        <p>This tool adds Locations from the CQC API that are not currently listed as a Local Service in hw-feedback. Note that it ignores Locations that are not marked as "Registered".</p>
        <p>To reduce system overhead, this tool is limited to adding 10 Locations at a time. This should be plenty if the tool is used regularly. To further reduce overheads, the tool will only check for new Locations for one Inspection Category at a time.</p>
				<p>You can choose that category using the drop-down list.</p>
      </div>
      <div id="hw-feedback-cqc-form" class="hw-feedback-form-row">
        <form action="tools.php?page=cqc-data-import" method="post">
          <label for="hw-feedback-form-inspection_category" class="hw-feedback-form-select">Select Inspection Category</label>
          <select class="hw-feedback-select widefat" name="hw-feedback-form-inspection-category" id="hw-feedback-form-inspection-category">
          <?php foreach (get_terms('cqc_inspection_category', array('hide_empty' => false)) as $key => $term) {
            echo '<option value="'.$term->name.'" id="hw-feedback-'.$term->name.'">'.$term->name.' - '.$term->description.'</option>';
          }
          ?>
          </select>
          <input type="submit" class="btn btn-primary hw-feedback-form-submit" id="hw-feedback-form-submit" value="Submit">
        </form>
      </div>
    </div>
    <?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $primary_inspection_category = $_POST["hw-feedback-form-inspection-category"];
      // Get start time
      $executionStartTime = microtime(true);

      // Query CQC API
      $api_response = json_decode(hw_feedback_cqc_api_query_locations(array(
            'localAuthority' => 'Buckinghamshire',
            'page' => '1',
            'perPage' => '10',
            'primaryInspectionCategoryCode' => $primary_inspection_category,
            'partnerCode' => 'HW_BUCKS'
          )));

      // Convert "JSON object" to array
      $locations = array_values($api_response->locations);

      // get the total count of results
      $total = $api_response->total;
      $registered_counter = 0;
      $matched_count = 0;

      // Clean out anything not Registered
      foreach ($locations as $key => $current_location) {
        $current_location_id = $current_location->locationId;
        // get the location details
        $current_location_detail = json_decode(hw_feedback_cqc_api_query_by_id('locations',$current_location->locationId));
        if ($current_location_detail->registrationStatus != 'Registered') {
          //echo '<p>'. $current_location->locationName . ' (<a href="https://www.cqc.org.uk/location/' . $current_location->locationId . '" target="_blank">' . $current_location->locationId . '</a>)</p>';
          unset($locations[$key]);
          continue;
        }
        $registered_counter++;
      }
      // Reindex array - THIS IS CRITICAL!
      $locations = array_values($locations);

      echo '<h1>'.$primary_inspection_category.' Locations</h1>';
      echo '<p>API Query: <a href="https://api.cqc.org.uk/public/v1' . $api_response->firstPageUri . '" target="_blank">https://api.cqc.org.uk/public/v1' . $api_response->firstPageUri . '</a>)</p>';

      // query all local_services posts regardless of status
      $args = array(
        'posts_per_page' => -1,
        'post_type' => 'local_services',
        'meta_key' => 'hw_services_cqc_location',
        'orderby' => 'meta_value'
      );

      $services = get_posts( $args );
      echo '<h2>Matched in hw-feedback</h2>';
      // loop the posts
      foreach($services as $hw_feedback_service) : setup_postdata($hw_feedback_service);
        // get the CQC Location ID from post_meta
        $our_location_id = get_post_meta( $hw_feedback_service->ID, 'hw_services_cqc_location', true );
        // search for the location_id in $locations
        $result = array_search($our_location_id, array_column($locations, 'locationId'));
        // $result can return empty which PHP can read as [0] - so check it is not empty
        if ($result){
          //$current_location_name = $locations[$result]->locationName;
          //$current_location_id = $locations[$result]->locationId;
          //echo '<p>'. $current_location_name . ' (<a href="https://www.cqc.org.uk/location/' . $current_location_id . '" target="_blank">' . $current_location_id . '</a>)</p>';
          // count the match
          $matched_count ++;
          // remove the service from $locations
          unset($locations[$result]);
        }
      endforeach;
      echo '<p>Matched: ' . $matched_count . '/' . $registered_counter . '</p>';
      // Reindex array - THIS IS CRITICAL!
      $locations = array_values($locations);

      echo '<h2>Un-matched / To be added</h2>';
      // loop the remaning $locations
      foreach ($locations as $location) {
        echo '<p>'. $location->locationName . ' (<a href="https://www.cqc.org.uk/location/' . $location->locationId . '" target="_blank">' . $location->locationId . '</a>)</p>';

				$location_api_response = json_decode(hw_feedback_cqc_api_query_by_id('locations',$location->locationId));

				// we know what the primary category code is because we chose it
				$service_types_term = hw_feedback_inspection_category_to_service_type($primary_inspection_category) || "Other";
				// build an array of these
				$cqc_inspection_category_terms = array();

				foreach ($location_api_response->inspectionCategories as $inspection_category) {
					array_push($cqc_inspection_category_terms,$inspection_category->code);
				}

				$post_arr = array(
				    'post_title'   => $location_api_response->name,
				    'post_content' => '',
				    'post_status'  => 'draft',
				    'post_author'  => get_current_user_id(),
				    'tax_input'    => array(
				        'service_types'     => $service_types_term,
								// we removed everything that was not Registered
				        'cqc_reg_status' => 'Registered',
								'cqc_inspection_category' => $cqc_inspection_category_terms
				    ),
				    'meta_input'   => array(
				        'hw_services_cqc_location' => $location->locationId,
				    ),
				);
				print_r($post_arr);
			}
/*

We need to use this:

post-new.php?post_title=My+Title&post_type=local_services&excerpt=Short+excerpt

// this adds an action when post-new loads
add_action( 'load-post-new.php', 'myplugin_post_new' );

// this action
function myplugin_post_new(){
    add_action( 'wp_insert_post', 'myplugin_wp_insert_post_default' );
}
// which runs this callback
function myplugin_wp_insert_post_default( $post_id ){
    //set category
    $cat_id = $_REQUEST['tax-input']['event-category'];
    wp_set_post_terms( $post_id, $cat_id, 'event-category' );

    //set tags
    wp_set_post_tags( $post_id, $_REQUEST['tags']);

    //set custom field
    add_post_meta( $post_id, 'myplugin_meta_key', $_REQUEST['meta_value'] );

    //set thumbnail and use the one from parent post
    if (!empty( $_GET['post_parent'] )) {
        $parent_id = $_GET['post_parent'];
        $attached_image = get_children( "post_parent=".$parent_id."&post_type=attachment&post_mime_type=image&numberposts=1" );
        if ($attached_image) {
            foreach ($attached_image as $attachment_id => $attachment) {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }
    }
}


*/

      // Get finish time
      $executionEndTime = microtime(true);
      // The result will be in seconds and milliseconds.
      $seconds = $executionEndTime - $executionStartTime;
      // Print it out
      echo "<p>This script took $seconds seconds to execute.</p>";
    }
  }
?>
