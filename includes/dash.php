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
    <div class="hw-feedback-cqc-update hw-feedback-form">
        <div class="hw-feedback-form-row">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="hw-feedback-form-inspection_category" class="hw-feedback-form-select">Select Inspection Category</label>
            <select class="hw-feedback-select widefat" name="hw-feedback-form-inspection-category" id="hw-feedback-form-inspection-category">
            <?php foreach (get_terms('cqc_inspection_category', array('hide_empty' => false)) as $key => $term) {
              echo '<option value="'.$term->name.'" id="hw-feedback-'.$term->name.'">'.$term->description.'</option>';
            }
            ?>
            </select>
            <input type="submit" class="btn btn-primary hw-feedback-form-submit" id="hw-feedback-form-submit" value="Submit">
          </form>
        </div>
    </div>
    <?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      echo ( empty($_POST["hw-feedback-form-inspection-category"]) ) ? 'Win!' : 'Balls up!';
      // Get start time
      $executionStartTime = microtime(true);

      // get the total count of results
      $total = $api_response->total;
      $registered_counter = 0;
      $matched_count = 0;

      // Query CQC API
      $api_response = json_decode(hw_feedback_cqc_api_query_locations(array(
            'localAuthority' => 'Buckinghamshire',
            'page' => '1',
            'perPage' => '550',
            'primaryInspectionCategoryCode' => 'P1',
            'partnerCode' => 'HW_BUCKS'
          )));

      // Convert "JSON object" to array
      $locations = array_values($api_response->locations);

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

      echo '<h1>'.$term->name.' Locations</h1>';
      echo '<p>API Query: <a href="https://api.cqc.org.uk/public/v1' . $api_response->firstPageUri . '" target="_blank">https://api.cqc.org.uk/public/v1' . $api_response->firstPageUri . '</a>)</p>';

      // query all local_services posts regardless of status
      $my_query = new WP_Query( array(
        'posts_per_page' => -1,
        'post_type' => 'local_services',
        'meta_key' => 'hw_services_cqc_location',
        'orderby' => 'meta_value'
        )
      );

      echo '<h2>Matched in hw-feedback</h2>';
      // loop the posts
      while ($my_query->have_posts()) {
        $my_query->the_post();
        // get the CQC Location ID from post_meta
        $our_location_id = get_post_meta( $post->ID, 'hw_services_cqc_location', true );
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
      }
      echo '<p>Matched: ' . $matched_count . '/' . $registered_counter . '</p>';
      // Reindex array - THIS IS CRITICAL!
      $locations = array_values($locations);

      echo '<h2>Un-matched / To be added</h2>';
      // loop the remaning $locations
      foreach ($locations as $location) {
        echo '<p>'. $location->locationName . ' (<a href="https://www.cqc.org.uk/location/' . $location->locationId . '" target="_blank">' . $location->locationId . '</a>)</p>';
      }

      // Get finish time
      $executionEndTime = microtime(true);
      // The result will be in seconds and milliseconds.
      $seconds = $executionEndTime - $executionStartTime;
      // Print it out
      echo "<p>This script took $seconds seconds to execute.</p>";
    }
  }
?>
