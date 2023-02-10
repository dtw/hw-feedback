<?php

/*

1. Create Local Services CUSTOM POST TYPE
2. Put Local Services on the AT A GLANCE section of the dashboard
3. Register CUSTOM TAXONOMIES
		- service types

4. Add ADDRESS & CONTACT DETAILS  META BOX to LOCAL SERVICES edit screen
5. Add ENTER & VIEW  META BOX to LOCAL SERVICES edit screen
6. Create ADMIN COLUMNS for local services

*/

/* 1. Create Local Services CUSTOM POST TYPE
-------------------------------------------------- */

add_action( 'init', 'hw_feedback_create_post_type' );
function hw_feedback_create_post_type() {
  register_post_type( 'local_services',
    array(

      'labels' => array(
        'name' => 'Local Services',
        'singular_name' => 'Local Service',
        'menu_name' => 'Local Services',
		'edit_item' => 'Edit Local Service',
		'view_item' => 'View Local Service',
		'search_items' => 'Search Local Services',
		'all_items' => 'All Local Services',
		'not_found' => 'No Local Services found',
		'add_new_item' => 'Add New Local Service'
	      ),
		'public' => true,
		'has_archive' => true,
		'exclude_from_search' => false,
		'menu_position' => 4,
		'show_in_nav_menus' => false,
		'menu_icon' => 'dashicons-star-half',
		'rewrite' => array('slug' => 'services'),
		'supports' => array('title','editor','excerpt','comments','thumbnail','custom-fields'),
		'can_export' => 'true',
		'taxonomies' => array('service_types'),

    )
  );
}




/* 2. Put Local Services on the AT A GLANCE section of the dashboard
--------------------------------------------------------------------------- */

add_action( 'dashboard_glance_items', 'hw_feedback_at_glance_content_table_end' );
function hw_feedback_at_glance_content_table_end() {
    $args = array(
        'public' => true,
        '_builtin' => false
    );
    $output = 'object';
    $operator = 'and';

    $post_types = get_post_types( $args, $output, $operator );
    foreach ( $post_types as $post_type ) {
        $num_posts = wp_count_posts( $post_type->name );
        $num = number_format_i18n( $num_posts->publish );
        $text = _n( $post_type->labels->singular_name, $post_type->labels->name, intval( $num_posts->publish ) );
        if ( current_user_can( 'edit_posts' ) ) {
            $output = '<a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a>';
            echo '<li class="post-count ' . $post_type->name . '-count">' . $output . '</li>';
        }
    }
}




/* 3. Register CUSTOM TAXONOMIES
(Note that must add taxonomy to custom post type too)
		- service types
------------------------------------------------------------------ */

function hw_feedback_taxonomies_init() {
	// for SERVICE TYPES
	register_taxonomy(
		'service_types',
		'local_services',
		array(
			'label' => 'Service Types',
      'labels' => array(
        'singular_name' => 'Service Type',
        'all_items' => 'All Service Types',
        'edit_item' => 'Edit Service Type',
        'view_item' => 'View Service Type',
        'update_item' => 'Update Service Type',
        'add_new_item' => 'Add new Service Type',
        'new_item_name' => 'New Service Type',
        'search_items' => 'Search Service Types'
      ),
			'rewrite' => array( 'slug' => 'type' ),
			'show_in_nav_menus' => true,
			'show_in_quick_edit' => true,
			'show_admin_column' => true,
			'hierarchical' => true,
      'capabilities' => array (
        'manage_terms' => 'activate_plugins',
        'edit_terms' => 'activate_plugins',
        'delete_terms' => 'activate_plugins',
        'assign_terms' => 'edit_posts',
      ),
		)
	);

/* This will create a "hidden" taxonomy that cannot be edited by anyone except Admin (activate_plugins) capabilities */
  // for REG STATUS
	register_taxonomy(
		'cqc_reg_status',
		'local_services',
		array(
			'label' => 'CQC Registration Status',
      'labels' => array(
        'singular_name' => 'Registration Status',
        'all_items' => 'All Registration Status',
        'edit_item' => 'Edit Registration Status',
        'view_item' => 'View Registration Status',
        'update_item' => 'Update Registration Status',
        'add_new_item' => 'Add new Registration Status',
        'new_item_name' => 'New Registration Status',
        'search_items' => 'Search Registration Status'
      ),
			'rewrite' => array( 'slug' => 'status' ),
			'edit_item' => 'Edit CQC Registration Status',
			'show_in_nav_menus' => true,
			'show_in_quick_edit' => false,
			'show_admin_column' => true,
      'meta_box_cb' => 'hw_feedback_cqc_reg_status_meta_box_callback',
      'show_tagcloud' => false,
      'capabilities' => array (
        'manage_terms' => 'activate_plugins',
        'edit_terms' => 'activate_plugins',
        'delete_terms' => 'activate_plugins',
        'assign_terms' => 'activate_plugins',
      ),
		)
	);

  /* This will create a "hidden" taxonomy that cannot be edited by anyone except Admin (activate_plugins) capabilities */
    // for REG STATUS
  	register_taxonomy(
  		'cqc_inspection_category',
  		'local_services',
  		array(
  			'label' => 'CQC Inspection Category',
        'labels' => array(
          'singular_name' => 'Inspection Category',
          'all_items' => 'All Inspection Categories',
          'edit_item' => 'Edit Inspection Category',
          'view_item' => 'View Inspection Category',
          'update_item' => 'Update Inspection Category',
          'add_new_item' => 'Add new Inspection Category',
          'new_item_name' => 'New Inspection Category',
          'search_items' => 'Search Inspection Category'
        ),
  			'edit_item' => 'Edit CQC Inspection Category',
  			'show_in_nav_menus' => true,
  			'show_in_quick_edit' => false,
        'meta_box_cb' => false,
  			'show_admin_column' => true,
        'show_tagcloud' => false,
        'capabilities' => array (
          'manage_terms' => 'activate_plugins',
          'edit_terms' => 'activate_plugins',
          'delete_terms' => 'activate_plugins',
          'assign_terms' => 'activate_plugins',
        ),
  		)
  	);
}
add_action( 'init', 'hw_feedback_taxonomies_init' );

/* Add default terms to cqc_reg_status taxonomy */
function hw_feedback_register_default_terms () {
  // service_types
  wp_insert_term('Ambulance service','service_types', array('description'=>'Provide medical care outside of hospital and patient transport services', 'slug' => 'ambulance'));
  wp_insert_term('Care home','service_types', array('description'=>'Care homes which offer a variety of personal care and accommodation', 'slug' => 'care-home'));
  wp_insert_term('Care home with nursing','service_types', array('description'=>'Care homes which offer a variety of personal care and accommodation with nursing', 'slug' => 'care-home-with-nursing'));
  wp_insert_term('Community healthcare service','service_types', array('description'=>'Provide healthcare support to people in their own home or in community settings ', 'slug' => 'community-healthcare'));
  wp_insert_term('Dentist','service_types', array('description'=>'Includes registered dentists and dental care professionals', 'slug' => 'dentist'));
  wp_insert_term('Diagnostic / screening service','service_types', array('description'=>'Provide individual health assessment and/or screening to people using X-rays, Magnetic resonance imaging (MRI), etc.', 'slug' => 'diagnostic-screening-service'));
  wp_insert_term('General practice','service_types', array('description'=>'General practices', 'slug' => 'gp'));
  wp_insert_term('Home care agency','service_types', array('description'=>'Provide personal care for people living in their own homes', 'slug' => 'home-care-agency'));
  wp_insert_term('Hospice service','service_types', array('description'=>'Provide a range of services for conditions where curative treatment is no longer an option, and people are approaching the end of their life', 'slug' => 'hospice'));
  wp_insert_term('Hospital','service_types', array('description'=>'NHS and independent hospitals including acute and non-acute, and community hospitals', 'slug' => 'hospital'));
  wp_insert_term('Independent consulting doctor','service_types', array('description'=>'Private doctor services', 'slug' => 'private-doctor'));
  wp_insert_term('Mental health','service_types', array('description'=>'Provide care, treatment and support for people with mental health needs, including community and hospital settings', 'slug' => 'mental-health'));
  wp_insert_term('Optician','service_types', array('description'=>'Provide optician services', 'slug' => 'optician'));
  wp_insert_term('Other','service_types', array('description'=>'Other services', 'slug' => 'other'));
  wp_insert_term('Pharmacy','service_types', array('description'=>'Provide community pharmacy services including dispensing prescriptions and advice on treating minor illnesses', 'slug' => 'pharmacy'));
  wp_insert_term('Prison healthcare','service_types', array('description'=>'Provide prison healthcare services', 'slug' => 'prison-healthcare'));
  wp_insert_term('Supported living service','service_types', array('description'=>'Provide care and/or support services to persons living in their own home to promote their independence', 'slug' => 'supported-living-service'));
  wp_insert_term('Urgent & emergency care','service_types', array('description'=>'Provide urgent or emergency medical help', 'slug' => 'urgent-care'));
  // cqc_reg_status
  wp_insert_term('Registered','cqc_reg_status', array('description'=>'Registered with CQC', 'slug' => 'registered'));
  wp_insert_term('Deregistered','cqc_reg_status', array('description'=>'Deregistered with CQC', 'slug' => 'deregistered'));
  wp_insert_term('Not registered','cqc_reg_status', array('description'=>'Not registered with CQC', 'slug' => 'not-registered'));
  wp_insert_term('Not applicable','cqc_reg_status', array('description'=>'Not required to register with CQC', 'slug' => 'not-applicable'));
  wp_insert_term('Archived','cqc_reg_status', array('description'=>'No longer registered with CQC', 'slug' => 'archived'));
  // cqc_inspection_category
  wp_insert_term('P1','cqc_inspection_category', array('description'=>'Dentists', 'slug' => 'p1'));
  wp_insert_term('P2','cqc_inspection_category', array('description'=>'General Practices', 'slug' => 'p2'));
  wp_insert_term('P3','cqc_inspection_category', array('description'=>'Out of hours', 'slug' => 'p3'));
  wp_insert_term('P4','cqc_inspection_category', array('description'=>'Prison healthcare', 'slug' => 'p4'));
  wp_insert_term('P5','cqc_inspection_category', array('description'=>'Remote clinical advice', 'slug' => 'p5'));
  wp_insert_term('P6','cqc_inspection_category', array('description'=>'Urgent care services & mobile doctors', 'slug' => 'p6'));
  wp_insert_term('P7','cqc_inspection_category', array('description'=>'Independent consulting doctors', 'slug' => 'p7'));
  wp_insert_term('P8','cqc_inspection_category', array('description'=>'Slimming clinics', 'slug' => 'p8'));
  wp_insert_term('S1','cqc_inspection_category', array('description'=>'Residential social care', 'slug' => 's1'));
  wp_insert_term('S2','cqc_inspection_category', array('description'=>'Community based adult social care services', 'slug' => 's2'));
  wp_insert_term('S3','cqc_inspection_category', array('description'=>'Hospice services', 'slug' => 's3'));
  wp_insert_term('H1','cqc_inspection_category', array('description'=>'Acute hospital - NHS non-specialist', 'slug' => 'h1'));
  wp_insert_term('H2','cqc_inspection_category', array('description'=>'Acute hospital - NHS specialist', 'slug' => 'h2'));
  wp_insert_term('H3','cqc_inspection_category', array('description'=>'Acute hospital - Independent non-specialist', 'slug' => 'h3'));
  wp_insert_term('H4','cqc_inspection_category', array('description'=>'Acute hospital - Independent specialist', 'slug' => 'h4'));
  wp_insert_term('H5','cqc_inspection_category', array('description'=>'Ambulance service', 'slug' => 'h5'));
  wp_insert_term('H6','cqc_inspection_category', array('description'=>'Community health - NHS & Independent', 'slug' => 'h6'));
  wp_insert_term('H7','cqc_inspection_category', array('description'=>'Community substance misuse', 'slug' => 'h7'));
  wp_insert_term('H8','cqc_inspection_category', array('description'=>'Mental health - community & hospital - independent', 'slug' => 'h8'));
  wp_insert_term('H9','cqc_inspection_category', array('description'=>'Mental health - community & residential - NHS', 'slug' => 'h9'));
  wp_insert_term('H10','cqc_inspection_category', array('description'=>'Residential substance misuse', 'slug' => 'h10'));
  wp_insert_term('H11','cqc_inspection_category', array('description'=>'Acute services - non-hospital', 'slug' => 'h11'));
  wp_insert_term('H12','cqc_inspection_category', array('description'=>'Hospice services', 'slug' => 'h12'));
}

add_action('wp_loaded', 'hw_feedback_register_default_terms');



/**
 * Prints the hw_cqc_reg_status_meta_box content.
 *
 * @param WP_Post $post
 * @param array $box
 *
 * https://codebriefly.com/display-wordpress-custom-taxonomy-dropdown/
 * I don't fully understand how this works but it does.
 */
function hw_feedback_cqc_reg_status_meta_box_callback($post, $box) {
  $defaults = array('taxonomy' => 'category');

  if (!isset($box['args']) || !is_array($box['args']))
      $args = array();
  else
      $args = $box['args'];

  extract(wp_parse_args($args, $defaults), EXTR_SKIP);

  $tax = get_taxonomy($taxonomy);
  $selected = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'ids'));

  // https://codebriefly.com/display-wordpress-custom-taxonomy-dropdown/

  //$tax = get_taxonomy('cqc_reg_status');
  if (current_user_can($tax->cap->edit_terms)) {
    ?>
    <select name="<?php echo "tax_input[$taxonomy][]"; ?>" class="widefat">
      <option value="0"></option>
      <?php foreach (get_terms($taxonomy, array('hide_empty' => false)) as $term): ?>
        <option value="<?php echo esc_attr($term->slug); ?>" <?php echo selected($term->term_id, count($selected) >= 1 ? $selected[0] : ''); ?>><?php echo esc_html($term->name); ?></option>
      <?php endforeach; ?>
    </select>
  <?php
  } else {
    echo "You don't have permission to edit this";
  }
}


/* 4. Add ADDRESS AND CONTACT DETAILS META BOX to LOCAL SERVICES edit screen
--------------------------------------------------------------- */

function hw_feedback_add_cpt_fields_meta_box() {

		add_meta_box(
			'hw_services_meta_box',		// Unique ID
			'Additional fields',		// Title
			'hw_feedback_cpt_fields_meta_box_callback',		// Callback function
			'local_services',	// Which custom post type?
			'moved',	// Placement on editing page
			'high'		// Priority
		);
}
add_action( 'add_meta_boxes', 'hw_feedback_add_cpt_fields_meta_box' );



// MOVES THE META BOX above the Post Editor
function hw_feedback_move_meta_box() {
        # Get the globals:
        global $post, $wp_meta_boxes;

        # Output the "advanced" meta boxes:
        do_meta_boxes( get_current_screen(), 'moved', $post );

        # Remove the initial "advanced" meta boxes:
        unset($wp_meta_boxes['post']['hw_services_meta_box']);
    }

add_action('edit_form_after_title', 'hw_feedback_move_meta_box');




/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function hw_feedback_cpt_fields_meta_box_callback( $post ) {

	// Add a NONCE field so we can check for it later.
	wp_nonce_field( 'hw_meta_box', 'hw_meta_box_nonce' );

  echo "<h2><strong>CQC Information</strong></h2><br />";
  // CQC LOCATION CODE
	$value = get_post_meta( $post->ID, 'hw_services_cqc_location', true );
	echo '<label for="hw-services-cqc-location">Location ID </label>';
	echo '<input type="text" id="hw-services-cqc-location" name="hw_services_cqc_location" value="' . esc_attr( $value ) . '" size="15" /><div id="hw-services-cqc-location-alert" class="hw-feedback-alert" role="alert">Save this Service to see updated values from CQC!</div>';
  // only check API and show fields if there is a location id
  if ($value != '') {
    $objcqcapiquery = json_decode(hw_feedback_cqc_api_query_by_id('locations',esc_attr(get_post_meta( $post->ID, 'hw_services_cqc_location', true ))));
    echo '<br /><h3>API Checks</h3><p id="api-check-help-text"><strong>Reminder:</strong> some services are not provided at the address where they are registered.</p>';
    $apioutputarray = array('Registration Name'=>'name','Registration Status'=>'registrationStatus','Local Authority'=>'localAuthority','Registration Date'=>'registrationDate');
    //'Deregistration Date'=>$objcqcapiquery->deregistrationDate);
    foreach($apioutputarray as $x => $val) {
      if (isset($objcqcapiquery->$val)) {
        echo '<div id="api-output-'.$val.'" class="api-output"><div class="api-output-label">'.$x.':</div><div class="api-output-value">'.$objcqcapiquery->$val.'</div></div>';
      }
    }
    //echo '<strong>Reg Status: </strong><span class="api-output">' . $objcqcapiquery->registrationStatus . '</span><br />';
    //echo '<strong>Reg Date: </strong><span class="api-output">' . $objcqcapiquery->registrationDate . '</span><br />';
    if ( isset($objcqcapiquery->registrationStatus) && $objcqcapiquery->registrationStatus == 'Deregistered'){ ?>
      <div class="api-output-deregistered">
        <div class="api-output-label">Deregistration Date:</div><div class="api-output-value"><?php echo $objcqcapiquery->deregistrationDate?></div>
        <div id="hw_services_cqc_deg_reg_alert" role="alert"><p>This service has been automatically marked as 'Deregistered'. <a href="https://www.cqc.org.uk/location/<?php echo $objcqcapiquery->locationId?>?referer=widget4" target="_blank">Check this registration on the CQC website</a>. If there is a new registration, update the <strong>Location ID</strong> above. If there is no new registration, change the <a href="#tagsdiv-cqc_reg_status">CQC Registration Status</a> to 'Archived'.</p></div>
      </div><?
    }
  }

// ADDRESS FIELDS
echo "<br /><h2><strong>Address</strong></h2><br />";

foreach(
  array(
    array('hw_services_address_line_1','Address line 1', isset($objcqcapiquery->postalAddressLine1) == true ? $objcqcapiquery->postalAddressLine1 : ''),
    array('hw_services_address_line_2','Address line 2', isset($objcqcapiquery->postalAddressLine2) == true ? $objcqcapiquery->postalAddressLine2 : ''),
    array('hw_services_city','City', isset($objcqcapiquery->postalAddressTownCity) == true ? $objcqcapiquery->postalAddressTownCity : ''),
    array('hw_services_county','County', isset($objcqcapiquery->postalAddressCounty) == true ? $objcqcapiquery->postalAddressCounty : ''),
    array('hw_services_postcode','Postcode', isset($objcqcapiquery->postalCode) == true ? $objcqcapiquery->postalCode : ''),
  ) as $row
) {
  hw_feedback_generate_metabox_form_field($row,$post->ID,'60');
}


// CONTACT FIELDS
echo "<br /><h2><strong>Contact details</strong></h2><br />";

hw_feedback_generate_metabox_form_field(array('hw_services_phone','Phone',isset($objcqcapiquery->mainPhoneNumber) == true ? $objcqcapiquery->mainPhoneNumber : ''),$post->ID,'20');
hw_feedback_generate_metabox_form_field(array('hw_services_website','Website',isset($objcqcapiquery->website) == true ? $objcqcapiquery->website : ''),$post->ID,'30');
// no need to check API as email is not provided
hw_feedback_generate_metabox_form_field(array('hw_services_contact_email','Contact email',''),$post->ID,'30');

// RATE AND REVIEW FIELDS
	echo "<br /><br /><h2><strong>How we rated this service</strong></h2><br />";

  // DATE OF VISIT
	$value = get_post_meta( $post->ID, 'hw_services_date_visited', true );
		echo '<label for="hw_services_date_visited">Date of visit </label>';
		echo '<input placeholder="2nd May 2016" type="text" id="hw_services_date_visited" name="hw_services_date_visited" value="' . esc_attr( $value ) . '" size="30" />';

echo "<br /><br />";

	// LINK TO FULL REPORT
	$value = get_post_meta( $post->ID, 'hw_services_full_report', true );
		echo '<label for="hw_services_full_report">Link to full report </label>';
		echo '<input placeholder="Begins http://" type="url" id="hw_services_full_report" name="hw_services_full_report" value="' . esc_attr( $value ) . '" size="90" />';

echo "<br /><br />";

  // list DiC rating fields and labels
  $ratingareas = array (
    'hw_services_overall_rating' => 'Overall rating',
    'hw_services_how_people_treated' => 'How people are treated',
    'hw_services_personal_choice' => 'Personal choice',
    'hw_services_being_home' => 'Just like being at home',
    'hw_services_privacy' => 'Privacy',
    'hw_services_quality_life' => 'Quality of life',
  );

  foreach ($ratingareas as $field => $label) {
  	// get value of field
  	$value = get_post_meta( $post->ID, $field, true );
    // print label (with space after)
  	echo '<label for="'.$field.'">'.$label.' </label>';
    // generate metabox radio field
    hw_feedback_generate_metabox_radio_field(array (
      'No rating' => '',
      '1' => 1,
      '2' => 2,
      '3' => 3,
      '4' => 4,
      '5' => 5
    ), $field, $value);
    // add some space
    echo "<br /><br />";
  }
}



/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function hw_feedback_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our NONCE is set.
	if ( ! isset( $_POST['hw_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the NONCE is valid.
	if ( ! wp_verify_nonce( $_POST['hw_meta_box_nonce'], 'hw_meta_box' ) ) {
		return;
	}

	// IF THIS IS AN AUTOSAVE,
	// our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's PERMISSIONS.
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}





/* 5. SAVE THE DATA when page updated
--------------------------------------------------------- */


	/* OK, it's safe for us to save the data now. */
		// For each custom field:
		// Make sure that FIELD is SET.
		// SANITIZE user input.
		// UPDATE the meta field in the database.

		// ADDRESS LINE 1
		if ( ! isset( $_POST['hw_services_address_line_1'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_address_line_1'] );
		update_post_meta( $post_id, 'hw_services_address_line_1', $my_data );

		// ADDRESS LINE 2
		if ( ! isset( $_POST['hw_services_address_line_2'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_address_line_2'] );
		update_post_meta( $post_id, 'hw_services_address_line_2', $my_data );

		// CITY
		if ( ! isset( $_POST['hw_services_city'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_city'] );
		update_post_meta( $post_id, 'hw_services_city', $my_data );

		// COUNTY
		if ( ! isset( $_POST['hw_services_county'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_county'] );
		update_post_meta( $post_id, 'hw_services_county', $my_data );

		// POSTCODE
		if ( ! isset( $_POST['hw_services_postcode'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_postcode'] );
		update_post_meta( $post_id, 'hw_services_postcode', $my_data );

		// PHONE
		if ( ! isset( $_POST['hw_services_phone'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_phone'] );
		update_post_meta( $post_id, 'hw_services_phone', $my_data );

		// WEBSITE
		if ( ! isset( $_POST['hw_services_website'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_website'] );
		update_post_meta( $post_id, 'hw_services_website', $my_data );

		// CONTACT EMAIL
		if ( ! isset( $_POST['hw_services_contact_email'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_contact_email'] );
		update_post_meta( $post_id, 'hw_services_contact_email', $my_data );

		// CQC LOCATION CODE
		if ( ! isset( $_POST['hw_services_cqc_location'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_cqc_location'] );
		update_post_meta( $post_id, 'hw_services_cqc_location', $my_data );

		// OVERALL RATING
		if ( ! isset( $_POST['hw_services_overall_rating'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_overall_rating'] );
		update_post_meta( $post_id, 'hw_services_overall_rating', $my_data );

		// HOW PEOPLE ARE TREATED
		if ( ! isset( $_POST['hw_services_how_people_treated'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_how_people_treated'] );
		update_post_meta( $post_id, 'hw_services_how_people_treated', $my_data );

		// PERSONAL CHOICE
		if ( ! isset( $_POST['hw_services_personal_choice'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_personal_choice'] );
		update_post_meta( $post_id, 'hw_services_personal_choice', $my_data );

		// JUST LIKE BEING AT HOME
		if ( ! isset( $_POST['hw_services_being_home'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_being_home'] );
		update_post_meta( $post_id, 'hw_services_being_home', $my_data );

		// PRIVACY
		if ( ! isset( $_POST['hw_services_privacy'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_privacy'] );
		update_post_meta( $post_id, 'hw_services_privacy', $my_data );

		// QUALITY OF LIFE
		if ( ! isset( $_POST['hw_services_quality_life'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_quality_life'] );
		update_post_meta( $post_id, 'hw_services_quality_life', $my_data );

		// URL OF FULL REPORT
		if ( ! isset( $_POST['hw_services_full_report'] ) ) { return; }
		$my_data = esc_url_raw( $_POST['hw_services_full_report'] );
		update_post_meta( $post_id, 'hw_services_full_report', $my_data );

		// DATE OF VISIT
		if ( ! isset( $_POST['hw_services_date_visited'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_date_visited'] );
		update_post_meta( $post_id, 'hw_services_date_visited', $my_data );

}

add_action( 'save_post', 'hw_feedback_save_meta_box_data' );
























/* 6. Create ADMIN COLUMNS for local services
--------------------------------------------------------- */

add_filter('manage_local_services_posts_columns', 'bs_local_services_table_head');
function bs_local_services_table_head( $columns ) {
    $options = get_option( 'hw_feedback_options' );
    if ( $options['hw_feedback_field_disable_lhw_rating'] != 1 ) {
      $columns['rated']  = 'Rated';
    }
    $columns['contact']  = 'Contact';
    $columns['website']  = 'Website';
    $columns['cqc_location']  = 'CQC Location';
    return $columns;

}
add_action( 'manage_local_services_posts_custom_column', 'bs_local_services_table_content', 10, 2 );

// Fill data into ADMIN COLUMNS for local services
function bs_local_services_table_content( $column_name, $post_id ) {

  if( $column_name == 'contact' ) {

		$address_city = get_post_meta( $post_id, 'hw_services_city', true );

		if ($address_city) { echo $address_city; }

		//echo $col_address;

    $col_phone = get_post_meta( $post_id, 'hw_services_phone', true );
    if ($col_phone) {
      echo "<br /><span class='dashicons dashicons-phone admin-dashicons-phone'></span> <strong>";
      $col_phone = format_telephone(sanitize_telephone($col_phone));
      echo $col_phone; echo "</strong>";
    }
  }



    if( $column_name == 'website' ) {
        $col_website = get_post_meta( $post_id, 'hw_services_website', true );
        echo "<a target='_blank' href='http://". $col_website ."'>";
		echo $col_website;
		echo "</a>";
    }

    if( $column_name == 'cqc_location' ) {
        // get location id
        $location_id = get_post_meta( $post_id, 'hw_services_cqc_location', true );
        // check there is location_id
        if ( ! empty( $location_id ) || $location_id != '') {
          // get post tax terms as names
          $tax_terms = wp_get_post_terms( $post_id, 'cqc_reg_status', array( "fields" => "names" ));
          // some error checks
          if ( ! empty( $tax_terms ) && ! is_wp_error( $tax_terms ) ) {
            if ($tax_terms[0] != 'Registered') {
              echo '<a href="https://www.cqc.org.uk/location/' . $location_id . '?referer=HW_BUCKS" target="_blank">' . $location_id . '</a>';
            } else {
              echo $tax_terms[0];
            }
          } else {
            echo $location_id;
          }
        } else {
          echo '-';
        }
		echo "<br />";

    }



    if( $column_name == 'rated' ) {
		$col_rating = get_post_meta( $post_id, 'hw_services_overall_rating', true );
    if($col_rating > 0){echo '<p>'.hw_feedback_star_rating($col_rating,array('colour' => 'green')).'</p>';}

	}




}


















// Remove date filter from dashboard
add_filter('months_dropdown_results', '__return_empty_array' );








/**
 * Display a custom taxonomy dropdown in admin
 * Thank you to:
 * @author Mike Hemberger
 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
 */
add_action('restrict_manage_posts', 'hw_feedback_filter_post_type_by_taxonomy');
function hw_feedback_filter_post_type_by_taxonomy() {
	global $typenow;
	$post_type = 'local_services'; // change to your post type
	$taxonomy  = 'service_types'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'show_option_all' => __("Show All {$info_taxonomy->label}"),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => true,
		));
	};
}
/**
 * Filter posts by taxonomy in admin
 */
add_filter('parse_query', 'hw_feedback_convert_id_to_term_in_query');
function hw_feedback_convert_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'local_services'; // change to your post type
	$taxonomy  = 'service_types'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}

/* 9. Add a function to query CQC reg status and update service
--------------------------------------------------------- */
function hw_feedback_check_cqc_registration_status() {

  /* unhook the hw_feedback_check_cqc_registration_status_single function
  not sure but this might start an infinite loop otherwise
  error_log('hw_feedback: unhook the hw_feedback_check_cqc_registration_status_single function');
  remove_action( 'updated_post_meta', 'hw_feedback_save_local_services_meta'); */

  // create array of local_services post_ids that have had their reg changed
  $registration_status_changed = array();

  global $post;
  // get local_services
  $args = array(
    'post_type'       => 'local_services',
    'posts_per_page'  => -1,
    'post_status' => array('publish','private')
  );

  $services = get_posts( $args );
    foreach($services as $hw_feedback_post) : setup_postdata($hw_feedback_post);
      // get location id
      $location_id = get_post_meta( $hw_feedback_post->ID, 'hw_services_cqc_location', true );
      // some error checks
      if ( ! empty( $location_id ) || $location_id != '') {
        // call API
        $api_response = json_decode(hw_feedback_cqc_api_query_by_id('locations',$location_id));
        // get post tax terms as names
        $tax_terms = wp_get_post_terms( $hw_feedback_post->ID, 'cqc_reg_status', array( "fields" => "names" ));
        // if service is Archived (which is done manually) bail
        if ( $tax_terms[0] == 'Archived' ) {
          continue;
        }
        // if there is a reg status from the api
        if ( isset($api_response->registrationStatus) ) {
          // is it different from the current status AND NOT Archived
          if ( $tax_terms[0]  != $api_response->registrationStatus) {
            // set new terms - takes names of terms not slugs...
            wp_set_post_terms( $hw_feedback_post->ID, sanitize_text_field($api_response->registrationStatus) , 'cqc_reg_status', false );
            // update array
            array_push($registration_status_changed,$hw_feedback_post->ID);
          }
          // update the cqc_inspection_category
          foreach ($api_response->inspectionCategories as $inspection_category) {
            wp_set_post_terms( $hw_feedback_post->ID, sanitize_text_field($inspection_category->code) , 'cqc_inspection_category', true );
            if ( isset($inspection_category->primary) && $inspection_category->primary === 'true') {
              $primary_inspection_category = sanitize_text_field($inspection_category->code);
            }
          }
          // update the excerpt if blank
          if ( ! has_excerpt($hw_feedback_post->ID) ) {
            if ($primary_inspection_category == "P2") {
              $post_excerpt = "General practice";
            } else {
              $post_excerpt = $api_response->gacServiceTypes[0]->description;
              if ( isset($api_response->numberOfBeds) ) {
                if ($api_response->numberOfBeds !== 0) {
                  $post_excerpt .= ' - ' . $api_response->numberOfBeds . ' beds';
                }
              }
            }
            wp_update_post(array(
              'ID' => $hw_feedback_post->ID,
              'post_excerpt' => $post_excerpt));
          }
        // otherwise, it has a location id locally but that is not listed by CQC
        } else {
          // set new terms - takes names of terms not slugs...
          wp_set_post_terms( $hw_feedback_post->ID, 'Not registered' , 'cqc_reg_status', false );
        }
      } else {
        wp_set_post_terms( $hw_feedback_post->ID, 'Not applicable' , 'cqc_reg_status', false );
      }
      // remove ALL terms
      //wp_remove_object_terms( $post_id, array('registered','deregistered','not-registered'), 'cqc_reg_status' );
    endforeach;
    // restore the hw_feedback_check_cqc_registration_status_single function hook
    //add_action( 'updated_post_meta', 'hw_feedback_save_local_services_meta', 10, 4);

    // set php mailer variables
    $to = get_option('admin_email');
    $subject = "Local Services - registration updates (". parse_url( get_site_url(), PHP_URL_HOST ) .")";
    // set headers to allow HTML
    $headers = array('Content-Type: text/html; charset=UTF-8');
    // build the content
    $formatted_message = '<p>Hi!</p><p>The registration check completed successfully at ' . date('d/m/Y h:i:s a', time()).'</p>';
    // check if there were changes
    if (empty($registration_status_changed)) {
      $formatted_message .= '<p>There were no changes.</p>';
    } else {
    // compose an email contain reg changes
      $formatted_message .= '<p>The registration status of the following services was updated automatically:</p><ul>';
      foreach ($registration_status_changed as $post_id) {
        $location_id = get_post_meta( $post_id, 'hw_services_cqc_location', true );
        $formatted_message .= '<li>' . get_the_title($post_id) . ' - <a href="https://www.cqc.org.uk/location/' . $location_id . '" target="_blank">' . $location_id . '</a> (';
        $formatted_message .= '<a href="'.get_site_url().'/wp-admin/post.php?post='.$post_id.'&action=edit">Edit</a> | <a href="'.get_post_permalink($post_id).'">View</a>)</li>';
      }
      $formatted_message .= '</ul>';
    }
    $formatted_message .= '<p>Hugs and kisses!</p>';
    $sent = wp_mail($to, $subject, stripslashes($formatted_message), $headers);
}

function hw_feedback_check_cqc_registration_status_single($post_id) {
  $single_local_service = get_post($post_id);
  // get location id
  $location_id = get_post_meta( $single_local_service->ID, 'hw_services_cqc_location', true );
  // some error checks
  if ( ! empty( $location_id ) || $location_id != '') {
    // call API
    $api_response = json_decode(hw_feedback_cqc_api_query_by_id('locations',$location_id));
    // get post tax terms as names
    $tax_terms = wp_get_post_terms( $single_local_service->ID, 'cqc_reg_status', array( "fields" => "names" ));
    // if service is Archived (which is done manually) bail
    if ( $tax_terms[0] == 'Archived' ) {
      return;
    }
    // if there is a reg status from the api
    if ( isset($api_response->registrationStatus) ) {
      // is it different from the current status AND NOT Archived
      if ( $tax_terms[0]  != $api_response->registrationStatus ) {
        // set new terms - takes names of terms not slugs...
        wp_set_post_terms( $single_local_service->ID, sanitize_text_field($api_response->registrationStatus) , 'cqc_reg_status', false );
      }
    // otherwise, it has a location id locally but that is not listed by CQC
    } else {
      // set new terms - takes names of terms not slugs...
      wp_set_post_terms( $single_local_service->ID, 'Not registered' , 'cqc_reg_status', false );
    }
  } else {
    wp_set_post_terms( $single_local_service->ID, 'Not applicable' , 'cqc_reg_status', false );
  }
  // try and override Registered local_services to "Allow Comments"
  if ( $api_response->registrationStatus == "Registered" ) {
    wp_update_post(array(
      'ID' => $single_local_service->ID,
      'comment_status' => "open"
    ));
  }
}

/* 10. Link cron job to function check_cqc_registration_status
--------------------------------------------------------- */
add_action( 'hw_feedback_cqc_reg_check_cron_job', 'hw_feedback_check_cqc_registration_status' );

/* Run CQC update when local_services are saved */

function hw_feedback_save_local_services_meta($meta_id, $post_id, $meta_key, $_meta_value) {
  global $pagenow;
  if (( 'post.php' === $pagenow ) && ($meta_key == 'hw_services_cqc_location')) {
    hw_feedback_check_cqc_registration_status_single($post_id);
  }
}

// fires when meta data updated, which is not the same as...
add_action( 'updated_post_meta', 'hw_feedback_save_local_services_meta', 10, 4);
// fires when meta data is added to a post
add_action( 'added_post_meta', 'hw_feedback_save_local_services_meta', 10, 4);

// Send an email to a provider when a new comment is APPROVED
function hw_feedback_approve_comment($new_status, $old_status, $comment) {
  $options = get_option( 'hw_feedback_options' );
  // check notifications enabled
  if ( isset( $options['hw_feedback_field_enable_notifications'] ) ) {
    error_log('hw-feedback: notification check');
  if ( $old_status == "unapproved" && $new_status == "approved" ) {
    error_log('hw-feedback: approve comment fired');
    // get comment details
    $new_comment = get_comment( $comment, OBJECT );
    $title_post = get_the_title( $new_comment->comment_post_ID );
    $link_post = get_permalink( $new_comment->comment_post_ID );
    // check if comment has been withheld
    if ( strpos($new_comment->comment_content,"[Withheld in accordance with our") === 0 ) {
      // comment has been withheld so do nothing
      error_log('hw-feedback: comment withheld '.strpos($new_comment->comment_content,"[Withheld in accordance with our"));
      return;
    }
    // send an email
    // set php mailer variables
    $contact_email = get_post_meta($new_comment->comment_post_ID,'hw_services_contact_email',true);
    // set headers to allow HTML
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: Healthwatch Bucks <info@healthwatchbucks.co.uk>';
    if ( $contact_email != '' ) {
      $to = $contact_email;
      error_log('hw-feedback: contact email: '.$to);
      $subject = "Respond to a new comment about your service";
      // build the content
      $formatted_message = '<p>Hello</p><p>A new comment about <strong>'.$title_post.'</strong> has been published on '. parse_url( get_site_url(), PHP_URL_HOST ) .'.</p>';
      // compose an email containing comment details
      $formatted_message .= '<p>The comment was received on <strong>'.date("l j F Y H:i:s T", strtotime($new_comment->comment_date) ).'</strong></p>';
      $formatted_message .= '<p>Comment:</p><blockquote>';
      $formatted_message .= $new_comment->comment_content;
      $formatted_message .= '</blockquote>';
      $formatted_message .= '<p>The commenter gave a rating of <strong>'.get_comment_meta( $new_comment->comment_ID, 'feedback_rating', true ). ' out of 5 stars</strong>.</p>';
      $formatted_message .= '<p><a href="'.$link_post.'">View '.$title_post.' on our website</a></p>';
      $formatted_message .= '<p>If you would like to respond to this comment, please reply to this email. We will share your response directly with the commenter where possible.</p>';
      $formatted_message .= '<p>Kind regards</p>';
      $email_footer = '<p><img alt="Healthwatch Bucks Logo" src="http://www.healthwatchbucks.co.uk/wp-content/uploads/2016/07/HW_BUCKS.png" style="width: 204px; height: 51px;" /></p><p><span style="font-size:14px;font-family:Trebuchet MS,Helvetica,sans-serif;">Your health and social care champion</span></p><p><span style="font-size:11px;font-family:Trebuchet MS,Helvetica,sans-serif;">PO Box 958, OX1 9ZP</span></p><p><span style="font-size:11px;font-family:Trebuchet MS,Helvetica,sans-serif;">01494 324832</span></p><p><span style="font-size:11px;font-family:Trebuchet MS,Helvetica,sans-serif;"><a href="http://www.healthwatchbucks.co.uk/?utm_source=website&amp;utm_medium=email&amp;utm_campaign=stakeholder" style="color:#a81563;">www.healthwatchbucks.co.uk</a></span></p><p><span style="font-size:11px;font-family:Trebuchet MS,Helvetica,sans-serif;">Company Registration Number 08426201</span></p><p><span style="font-size:11px;font-family:Trebuchet MS,Helvetica,sans-serif;">YOUR PRIVACY: Healthwatch Bucks is the Local Healthwatch for Buckinghamshire, under contract to Buckinghamshire County Council, as defined in <a href="https://www.legislation.gov.uk/ukpga/2007/28/part/14" style="color:#a81563;" target="_blank">Part 14 of the Local Government and Public Involvement in Health Act 2007</a> and amended in <a href="http://www.legislation.gov.uk/ukpga/2012/7/part/5/chapter/1/crossheading/local-healthwatch-organisations/enacted" style="color:#a81563;" target="_blank">Sections 182 through 184 of the Health and Social Care Act 2012</a>. We have collected personal information about you either directly from you; via the organisation that you represent; through another third party; or through the public domain. You can read the <a href="https://healthwatchbucks.sharepoint.com/:w:/g/EZMsORzh41dNvTpRNUaWW3sBZMg0EmPg9f-X-guw0DbNQQ?e=Ofp2gu" style="color:#a81563;" target="_blank">relevant privacy notice</a> online or <a href="mailto:info@healthwatchbucks.co.uk?Subject=Stakeholder%20privacy%20notice" style="color:#a81563;">contact us for a copy</a>. If you would prefer that we do not contact you, please <a href="mailto:info@healthwatchbucks.co.uk?Subject=Stakeholder%20opt-out" style="color:#a81563;">contact us by email</a> or telephone.</span></p><p><span style="font-size:11px;font-family:Trebuchet MS,Helvetica,sans-serif;">CONFIDENTIALITY &amp; DISCLAIMER: This e-mail, including any attachments, is confidential and is intended solely for the use of the recipient(s) to whom it is addressed. If you are not the intended recipient, please contact <a href="mailto:info@healthwatchbucks.co.uk" style="color:#a81563;">info@healthwatchbucks.co.uk</a> and confirm that it has been deleted from your system and any hard copies destroyed. Any views or opinions presented within this e-mail are solely those of the author and do not necessarily represent those of Healthwatch Bucks, unless otherwise specifically stated. Any attachment received in this e-mail should be virus checked by the recipient before being opened. Healthwatch Bucks does not accept liability (for negligence or otherwise) for passing on viruses in e-mails.</span></p>';
      $formatted_message .= $email_footer;
      $sent_provider = wp_mail($to, $subject, stripslashes($formatted_message), $headers);
    } else {
      if ( $options['hw_feedback_field_your_story_email'] != "") {
        $to = $options['hw_feedback_field_your_story_email'];
      } else {
        $to = get_option('admin_email');
      }
      error_log('hw-feedback: no contact email: '.$to);
      $subject = "Missing Contact Email for ". $title_post;
      // build the content
      $formatted_message = '<p>Hello</p><p>'.$title_post.' on '.parse_url( get_site_url(), PHP_URL_HOST ).' is missing a Contact Email address.</p>';
      $formatted_message .= '<a href="'.get_edit_post_link($new_comment->comment_post_ID).'">Add the missing email address here</a>';
      $formatted_message .= '<p>Hugs and kisses!</p>';
      $sent_admin = wp_mail($to, $subject, stripslashes($formatted_message), $headers);
    }
    if ( isset( $sent_provider ) ) {
      // add some hidden meta data to the comment
      add_comment_meta( $comment->comment_ID, 'feedback_provider_notification_sent', true, true);
      error_log('hw-feedback: email sent');
    }
  }
}
// fires when a comment status changes
add_action('transition_comment_status', 'hw_feedback_approve_comment', 10, 3);
?>
