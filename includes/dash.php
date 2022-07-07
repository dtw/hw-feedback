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
		// save the hw-feedback-form-inspection-category from POST
		$primary_inspection_category = isset($_POST['hw-feedback-form-inspection-category']) ? $_POST['hw-feedback-form-inspection-category'] : false;
		$show_matches = isset($_POST['hw-feedback-show-matches']) ? $_POST['hw-feedback-show-matches'] : false;
		$preview_only = isset($_POST['hw-feedback-preview-only']) ? $_POST['hw-feedback-preview-only'] : false;
		// default to 10
		$import_number = isset($_POST['hw-feedback-form-import-number']) ? $_POST['hw-feedback-form-import-number'] : 10;
    // create a simple form
    ?>
    <div id="hwbucks-data-import-tool">
      <h1>CQC Data Import Tool</h1>
      <div id="hwbucks-data-import-tool-instructions">
        <p>This tool adds Locations from the CQC API that are not currently listed as a Local Service in hw-feedback. Note that it ignores Locations that are not marked as "Registered".</p>
        <p>To reduce system overhead, by default, this tool is limited to adding 10 Locations at a time. This should be plenty if the tool is used regularly. To further reduce overheads, the tool will only check for new Locations for one Inspection Category at a time.</p>
				<p>You can choose that category using the drop-down list.</p>
      </div>
      <div id="hw-feedback-cqc-import-form" class="hw-feedback-cqc-form">
        <form action="tools.php?page=cqc-data-import" method="post">
					<div class="hw-feedback-cqc-import-form-row">
	          <label for="hw-feedback-form-inspection-category">Select Inspection Category</label>
	          <select class="hw-feedback-select widefat" name="hw-feedback-form-inspection-category" id="hw-feedback-form-inspection-category">
	          <?php foreach (get_terms('cqc_inspection_category', array('hide_empty' => false)) as $key => $term) {
							if ($primary_inspection_category && $primary_inspection_category == $term->name ) {
								echo '<option value="'.$term->name.'" id="hw-feedback-'.$term->name.'" selected>'.$term->name.' - '.$term->description.'</option>';
							} else {
	            	echo '<option value="'.$term->name.'" id="hw-feedback-'.$term->name.'">'.$term->name.' - '.$term->description.'</option>';
							}
	          }
	          ?>
	          </select>
					</div>
					<div class="hw-feedback-cqc-import-form-row">
	          <label for="hw-feedback-form-import-number">Select number of Locations to check/import</label>
	          <select class="hw-feedback-select" name="hw-feedback-form-import-number" id="hw-feedback-form-import-number">
	          <?php foreach (array(1, 5, 10, 20, 30, 40, 50) as $option) {
							if ($import_number && $import_number == $option ) {
								echo '<option value=' . $option . ' id="hw-feedback-import-number-' . $option . '" selected>' . $option . '</option>';
							} else {
								echo '<option value=' . $option . ' id="hw-feedback-import-number-' . $option . '">' . $option . '</option>';
							}
						}
	          ?>
	          </select>
					</div>
					<div class="hw-feedback-cqc-import-form-row">
						<label for="hw-feedback-show-matches">Show matched services?</label>
						<?php if ( $show_matches ) {
							echo '<input type="checkbox" id="hw-feedback-show-matches" class="hw-feedback-checkbox" name="hw-feedback-show-matches" value="true" checked>';
						} else {
							echo '<input type="checkbox" id="hw-feedback-show-matches" class="hw-feedback-checkbox" name="hw-feedback-show-matches" value="true">';
						} ?>
					</div>
					<div id="hw-feedback-preview-only-row" class="hw-feedback-cqc-import-form-row">
						<label for="hw-feedback-preview-only">Preview ONLY!</label>
						<input type="checkbox" id="hw-feedback-preview-only" class="hw-feedback-checkbox" name="hw-feedback-preview-only" value="true" checked>
						<div id="hw-feedback-import-alert" role="alert">New locations will be imported to the database!</div>
					</div>
          <div class="hw-feedback-cqc-import-form-row">
						<input type="submit" class="btn btn-primary hw-feedback-form-submit" id="hw-feedback-form-submit" value="Preview">
					</div>
        </form>
      </div>
    </div>
    <?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Get start time
      $executionStartTime = microtime(true);

      // Query CQC API
      $api_response = json_decode(hw_feedback_cqc_api_query_locations(array(
            'localAuthority' => 'Buckinghamshire',
            'page' => '1',
            'perPage' => '700',
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
      //echo '<h2>Matched in hw-feedback</h2>';
      // loop the posts
      foreach($services as $hw_feedback_service) : setup_postdata($hw_feedback_service);
        // get the CQC Location ID from post_meta
        $our_location_id = get_post_meta( $hw_feedback_service->ID, 'hw_services_cqc_location', true );
        // search for the location_id in $locations
        $result = array_search($our_location_id, array_column($locations, 'locationId'));
        // $result can return empty which PHP can read as [0] - so check it is not empty
        if ($result !== false){
					if ( $show_matches === "true" ) {
	          $current_location_name = $locations[$result]->locationName;
	          $current_location_id = $locations[$result]->locationId;
	          echo '<p>'. $current_location_name . ' (<a href="https://www.cqc.org.uk/location/' . $current_location_id . '" target="_blank">' . $current_location_id . '</a>)</p>';
					}
          // count the match
          $matched_count ++;
          // remove the service from $locations
          unset($locations[$result]);
        }
      endforeach;
      echo '<h3>Matched: ' . $matched_count . '/' . $registered_counter . '</h3>';
      // Reindex array - THIS IS CRITICAL!
      $locations = array_values($locations);

      //echo '<h2>Un-matched / To be added</h2>';
			echo '<h3>Un-matched: ' . count($locations) . '/' . $registered_counter . '</h3>';

			// limit the number of results to $import_number
			$locations = array_slice($locations,0,$import_number,true);
			echo '<h3>Showing: ' . count($locations) . ' results</h3>';

      // loop the remaning $locations
      foreach ($locations as $location) {
        //
				$location_api_response = json_decode(hw_feedback_cqc_api_query_by_id('locations',$location->locationId));
				$cqc_gac_service_types = $location_api_response->gacServiceTypes[0]->description;
				// we know what the primary category code is because we chose it
				$service_types_term = (hw_feedback_inspection_category_to_service_type($primary_inspection_category) !== false) ? hw_feedback_inspection_category_to_service_type($primary_inspection_category) : hw_feedback_gac_category_to_service_type($cqc_gac_service_types);
				// do something different if this is just a preview
				if ( $preview_only ) {
					echo '<p>'. $location->locationName . ' (' . $service_types_term . ' - <a href="https://www.cqc.org.uk/location/' . $location->locationId . '" target="_blank">' . $location->locationId . '</a>)</p>';
				} else {
					// build an array of these
					$cqc_inspection_category_terms = array();
					foreach ($location_api_response->inspectionCategories as $inspection_category) {
						array_push($cqc_inspection_category_terms,$inspection_category->code);
					}
					// get the registration date from API
					$date_stamp = strtotime($location_api_response->registrationDate);
					// I don't want it to be midnight so - yeah really - this is the easiest way to add eight hours
					$date_stamp = $date_stamp + (8*(60*60));

					$registration_date = date("Y-m-d H:i:s", $date_stamp);
					// returns: string(19) "2001-09-11 00:00:00"

					$post_arr = array(
					    'post_title'   => $location_api_response->name,
					    'post_content' => '',
					    'post_status'  => 'draft',
					    'post_author'  => get_current_user_id(),
							'post_date' => $registration_date,
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
					echo '<p>';
					print_r($post_arr);
					echo '</p>';
				}
			}
      // Get finish time
      $executionEndTime = microtime(true);
      // The result will be in seconds and milliseconds.
      $seconds = round($executionEndTime - $executionStartTime,2);
      // Print it out
      echo "<p>This script took $seconds seconds to execute.</p>";
    }
  }
?>
