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

  /* This will create a "hidden" taxonomy that cannot be edited by anyone except Admin (activate_plugins) capabilities */
  // for REG STATUS
  register_taxonomy(
    'ods_status',
    'local_services',
    array(
      'label' => 'ODS Status',
      'labels' => array(
        'singular_name' => 'Status',
        'all_items' => 'All Status',
        'edit_item' => 'Edit Status',
        'view_item' => 'View Status',
        'update_item' => 'Update Status',
        'add_new_item' => 'Add new Status',
        'new_item_name' => 'New Status',
        'search_items' => 'Search Status'
      ),
      'edit_item' => 'Edit ODS Status',
      'show_in_nav_menus' => true,
      'show_in_quick_edit' => false,
      'meta_box_cb' => false,
      'show_admin_column' => true,
      'show_tagcloud' => false,
      'capabilities' => array(
        'manage_terms' => 'activate_plugins',
        'edit_terms' => 'activate_plugins',
        'delete_terms' => 'activate_plugins',
        'assign_terms' => 'activate_plugins',
      ),
    )
  );

  /* This will create a "hidden" taxonomy that cannot be edited by anyone except Admin (activate_plugins) capabilities */
  // for ODS ROLE CODES
  register_taxonomy(
    'ods_role_code',
    'local_services',
    array(
      'label' => 'ODS Role Code',
      'labels' => array(
        'singular_name' => 'Role Code',
        'all_items' => 'Role Codes',
        'edit_item' => 'Edit Role Code',
        'view_item' => 'View Role Code',
        'update_item' => 'Update Role Code',
        'add_new_item' => 'Add new Role Code',
        'new_item_name' => 'New Role Code',
        'search_items' => 'Search Role Code'
      ),
      'edit_item' => 'Edit Role Code',
      'show_in_nav_menus' => true,
      'show_in_quick_edit' => false,
      'meta_box_cb' => false,
      'show_admin_column' => true,
      'show_tagcloud' => false,
      'capabilities' => array(
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
  // ods_status
  wp_insert_term('Active', 'ods_status', array('description' => 'Active', 'slug' => 'active'));
  wp_insert_term('Inactive', 'ods_status', array('description' => 'Inactive', 'slug' => 'inactive'));
  // ods_role_code
  // there are far more than we need for Local Healthwatch
wp_insert_term('101','ods_role_code', array('description' => 'Social Care Site', 'slug' => 'social_care_site'));
wp_insert_term('102','ods_role_code', array('description' => 'Clinical Network', 'slug' => 'clinical_network'));
wp_insert_term('103','ods_role_code', array('description' => 'Cancer Network', 'slug' => 'cancer_network'));
wp_insert_term('104','ods_role_code', array('description' => 'Social Care Provider', 'slug' => 'social_care_provider'));
wp_insert_term('105','ods_role_code', array('description' => 'Cancer Registry', 'slug' => 'cancer_registry'));
wp_insert_term('106','ods_role_code', array('description' => 'Common Service Agency (CSA)', 'slug' => 'common_service_agency_(csa)'));
wp_insert_term('107','ods_role_code', array('description' => 'Care Trust', 'slug' => 'care_trust'));
wp_insert_term('108','ods_role_code', array('description' => 'Care Trust Site', 'slug' => 'care_trust_site'));
wp_insert_term('109','ods_role_code', array('description' => 'District Health Authority (DHA)', 'slug' => 'district_health_authority_(dha)'));
wp_insert_term('11','ods_role_code', array('description' => 'LA - Metropolitan District', 'slug' => 'la_-_metropolitan_district'));
wp_insert_term('110','ods_role_code', array('description' => 'General Dental Practice', 'slug' => 'general_dental_practice'));
wp_insert_term('111','ods_role_code', array('description' => 'Directly Managed Unit (DMU)', 'slug' => 'directly_managed_unit_(dmu)'));
wp_insert_term('114','ods_role_code', array('description' => 'DMU Site', 'slug' => 'dmu_site'));
wp_insert_term('116','ods_role_code', array('description' => 'Executive Agency', 'slug' => 'executive_agency'));
wp_insert_term('117','ods_role_code', array('description' => 'Education', 'slug' => 'education'));
wp_insert_term('119','ods_role_code', array('description' => 'Local Authority - Legacy', 'slug' => 'local_authority_-_legacy'));
wp_insert_term('12','ods_role_code', array('description' => 'LA - Inner London', 'slug' => 'la_-_inner_london'));
wp_insert_term('122','ods_role_code', array('description' => 'Local Authority Site - Legacy', 'slug' => 'local_authority_site_-_legacy'));
wp_insert_term('123','ods_role_code', array('description' => 'Local Authority Department', 'slug' => 'local_authority_department'));
wp_insert_term('126','ods_role_code', array('description' => 'Government Department', 'slug' => 'government_department'));
wp_insert_term('128','ods_role_code', array('description' => 'Government Office Region', 'slug' => 'government_office_region'));
wp_insert_term('131','ods_role_code', array('description' => 'Government Department Site', 'slug' => 'government_department_site'));
wp_insert_term('132','ods_role_code', array('description' => 'Health Authority (HA)', 'slug' => 'health_authority_(ha)'));
wp_insert_term('134','ods_role_code', array('description' => 'Health Observatory', 'slug' => 'health_observatory'));
wp_insert_term('136','ods_role_code', array('description' => 'Strategic Health Authority Site', 'slug' => 'strategic_health_authority_site'));
wp_insert_term('137','ods_role_code', array('description' => 'Isle Of Man Government Directorate', 'slug' => 'isle_of_man_government_directorate'));
wp_insert_term('138','ods_role_code', array('description' => 'Isle Of Man Government Department', 'slug' => 'isle_of_man_government_department'));
wp_insert_term('140','ods_role_code', array('description' => 'Isle Of Man Government Directorate Site', 'slug' => 'isle_of_man_government_directorate_site'));
wp_insert_term('141','ods_role_code', array('description' => 'Local Authority', 'slug' => 'local_authority'));
wp_insert_term('142','ods_role_code', array('description' => 'Local Health Board', 'slug' => 'local_health_board'));
wp_insert_term('144','ods_role_code', array('description' => 'Welsh Local Health Board', 'slug' => 'welsh_local_health_board'));
wp_insert_term('146','ods_role_code', array('description' => 'Local Service Provider (LSP)', 'slug' => 'local_service_provider_(lsp)'));
wp_insert_term('147','ods_role_code', array('description' => 'LSP Site', 'slug' => 'lsp_site'));
wp_insert_term('148','ods_role_code', array('description' => 'Local Health Board Site', 'slug' => 'local_health_board_site'));
wp_insert_term('149','ods_role_code', array('description' => 'Welsh Local Health Board Site', 'slug' => 'welsh_local_health_board_site'));
wp_insert_term('15','ods_role_code', array('description' => 'Reg\'d Under Part 2 Care Stds Act 2000', 'slug' => 'regd_under_part_2_care_stds_act_2000'));
wp_insert_term('150','ods_role_code', array('description' => 'Military Hospital', 'slug' => 'military_hospital'));
wp_insert_term('153','ods_role_code', array('description' => 'Northern Ireland Health & Social Care Board', 'slug' => 'northern_ireland_health_&_social_care_board'));
wp_insert_term('154','ods_role_code', array('description' => 'Northern Ireland Health And Social Care Trust', 'slug' => 'northern_ireland_health_and_social_care_trust'));
wp_insert_term('155','ods_role_code', array('description' => 'Northern Ireland Local Commissioning Group', 'slug' => 'northern_ireland_local_commissioning_group'));
wp_insert_term('157','ods_role_code', array('description' => 'Non-NHS Organisation', 'slug' => 'non-nhs_organisation'));
wp_insert_term('158','ods_role_code', array('description' => 'Non Statutory NHS Organisation', 'slug' => 'non_statutory_nhs_organisation'));
wp_insert_term('159','ods_role_code', array('description' => 'National Application Service Provider', 'slug' => 'national_application_service_provider'));
wp_insert_term('161','ods_role_code', array('description' => 'NHS Support Agency', 'slug' => 'nhs_support_agency'));
wp_insert_term('162','ods_role_code', array('description' => 'Other Statutory Authority (OSA)', 'slug' => 'other_statutory_authority_(osa)'));
wp_insert_term('166','ods_role_code', array('description' => 'Optical Headquarters', 'slug' => 'optical_headquarters'));
wp_insert_term('167','ods_role_code', array('description' => 'Optical Site', 'slug' => 'optical_site'));
wp_insert_term('168','ods_role_code', array('description' => 'Other Statutory Authority Site', 'slug' => 'other_statutory_authority_site'));
wp_insert_term('169','ods_role_code', array('description' => 'Other Unit (in support Of NHS business)', 'slug' => 'other_unit_(in_support_of_nhs_business)'));
wp_insert_term('171','ods_role_code', array('description' => 'Primary Care Group', 'slug' => 'primary_care_group'));
wp_insert_term('172','ods_role_code', array('description' => 'Independent Sector Healthcare Provider', 'slug' => 'independent_sector_healthcare_provider'));
wp_insert_term('173','ods_role_code', array('description' => 'Pathology Lab', 'slug' => 'pathology_lab'));
wp_insert_term('175','ods_role_code', array('description' => 'Prison', 'slug' => 'prison'));
wp_insert_term('176','ods_role_code', array('description' => 'Independent Sector H/C Provider Site', 'slug' => 'independent_sector_h/c_provider_site'));
wp_insert_term('177','ods_role_code', array('description' => 'Prescribing Cost Centre', 'slug' => 'prescribing_cost_centre'));
wp_insert_term('179','ods_role_code', array('description' => 'Primary Care Trust', 'slug' => 'primary_care_trust'));
wp_insert_term('180','ods_role_code', array('description' => 'Primary Care Trust Site', 'slug' => 'primary_care_trust_site'));
wp_insert_term('181','ods_role_code', array('description' => 'Pharmacy Headquarter', 'slug' => 'pharmacy_headquarter'));
wp_insert_term('182','ods_role_code', array('description' => 'Pharmacy', 'slug' => 'pharmacy'));
wp_insert_term('185','ods_role_code', array('description' => 'Regional Office (RO)', 'slug' => 'regional_office_(ro)'));
wp_insert_term('189','ods_role_code', array('description' => 'Special Health Authority (SPHA)', 'slug' => 'special_health_authority_(spha)'));
wp_insert_term('190','ods_role_code', array('description' => 'Scottish Health Board', 'slug' => 'scottish_health_board'));
wp_insert_term('191','ods_role_code', array('description' => 'Special Health Authority Site', 'slug' => 'special_health_authority_site'));
wp_insert_term('197','ods_role_code', array('description' => 'NHS Trust', 'slug' => 'nhs_trust'));
wp_insert_term('198','ods_role_code', array('description' => 'NHS Trust Site', 'slug' => 'nhs_trust_site'));
wp_insert_term('200','ods_role_code', array('description' => 'Welsh Assembly', 'slug' => 'welsh_assembly'));
wp_insert_term('209','ods_role_code', array('description' => 'NHS England (Region)', 'slug' => 'nhs_england_(region)'));
wp_insert_term('21','ods_role_code', array('description' => 'Level 03 PCT', 'slug' => 'level_03_pct'));
wp_insert_term('210','ods_role_code', array('description' => 'NHS England (Region, Local Office)', 'slug' => 'nhs_england_(region,_local_office)'));
wp_insert_term('211','ods_role_code', array('description' => 'Specialised Commissioning Hub', 'slug' => 'specialised_commissioning_hub'));
wp_insert_term('212','ods_role_code', array('description' => 'NHS England (Region, Local Office) Site', 'slug' => 'nhs_england_(region,_local_office)_site'));
wp_insert_term('213','ods_role_code', array('description' => 'Commissioning Support Unit', 'slug' => 'commissioning_support_unit'));
wp_insert_term('214','ods_role_code', array('description' => 'Commissioning Support Unit Site', 'slug' => 'commissioning_support_unit_site'));
wp_insert_term('215','ods_role_code', array('description' => 'Hosts Data Managment Integration Centre (DMIC)', 'slug' => 'hosts_data_managment_integration_centre_(dmic)'));
wp_insert_term('216','ods_role_code', array('description' => 'Data Services For Commissioners Regional Office (DSCRO)', 'slug' => 'data_services_for_commissioners_regional_office_(dscro)'));
wp_insert_term('217','ods_role_code', array('description' => 'DSCRO Site', 'slug' => 'dscro_site'));
wp_insert_term('218','ods_role_code', array('description' => 'Commissioning Hub', 'slug' => 'commissioning_hub'));
wp_insert_term('22','ods_role_code', array('description' => 'Level 04 PCT', 'slug' => 'level_04_PCT'));
wp_insert_term('221','ods_role_code', array('description' => 'School', 'slug' => 'school'));
wp_insert_term('222','ods_role_code', array('description' => 'Local Authority Site', 'slug' => 'local_authority_site'));
wp_insert_term('223','ods_role_code', array('description' => 'London Borough', 'slug' => 'london_borough'));
wp_insert_term('227','ods_role_code', array('description' => 'Scottish GP Practice', 'slug' => 'scottish_gp_practice'));
wp_insert_term('228','ods_role_code', array('description' => 'Young Offender Institution', 'slug' => 'young_offender_institution'));
wp_insert_term('229','ods_role_code', array('description' => 'Non Residential Institution', 'slug' => 'non_residential_institution'));
wp_insert_term('230','ods_role_code', array('description' => 'Secure Training Centre', 'slug' => 'secure_training_centre'));
wp_insert_term('231','ods_role_code', array('description' => 'Secure Children\'s Home', 'slug' => 'secure_childrens_home'));
wp_insert_term('232','ods_role_code', array('description' => 'Immigration Removal Centre', 'slug' => 'immigration_removal_centre'));
wp_insert_term('233','ods_role_code', array('description' => 'Constabulary', 'slug' => 'constabulary'));
wp_insert_term('234','ods_role_code', array('description' => 'Police Custody Suite', 'slug' => 'police_custody_suite'));
wp_insert_term('235','ods_role_code', array('description' => 'Court', 'slug' => 'court'));
wp_insert_term('236','ods_role_code', array('description' => 'Sexual Assault Referral Centre', 'slug' => 'sexual_assault_referral_centre'));
wp_insert_term('24','ods_role_code', array('description' => 'NHS Trust Derived', 'slug' => 'nhs_trust_derived'));
wp_insert_term('246','ods_role_code', array('description' => 'Care Home/Nursing Home Prescribing Cost Centre', 'slug' => 'care_home/nursing_home_prescribing_cost_centre'));
wp_insert_term('247','ods_role_code', array('description' => 'Community Health Service Prescribing Cost Centre', 'slug' => 'community_health_service_prescribing_cost_centre'));
wp_insert_term('248','ods_role_code', array('description' => 'Court Prescribing Cost Centre', 'slug' => 'court_prescribing_cost_centre'));
wp_insert_term('249','ods_role_code', array('description' => 'Hospice Prescribing Cost Centre', 'slug' => 'hospice_prescribing_cost_centre'));
wp_insert_term('25','ods_role_code', array('description' => 'PCT Derived', 'slug' => 'pct_derived'));
wp_insert_term('250','ods_role_code', array('description' => 'Hospital Service Prescribing Cost Centre', 'slug' => 'hospital_service_prescribing_cost_centre'));
wp_insert_term('251','ods_role_code', array('description' => 'Immigration Removal Centre Prescribing Cost Centre', 'slug' => 'immigration_removal_centre_prescribing_cost_centre'));
wp_insert_term('252','ods_role_code', array('description' => 'Optometry Service Prescribing Cost Centre', 'slug' => 'optometry_service_prescribing_cost_centre'));
wp_insert_term('255','ods_role_code', array('description' => 'Public Health Service Prescribing Cost Centre', 'slug' => 'public_health_service_prescribing_cost_centre'));
wp_insert_term('256','ods_role_code', array('description' => 'Secure Children\'s Home Prescribing Cost Centre', 'slug' => 'secure_childrens_home_prescribing_cost_centre'));
wp_insert_term('257','ods_role_code', array('description' => 'Secure Training Centre Prescribing Cost Centre', 'slug' => 'secure_training_centre_prescribing_cost_centre'));
wp_insert_term('258','ods_role_code', array('description' => 'Sexual Assault Referral Centre Prescribing Cost Centre', 'slug' => 'sexual_assault_referral_centre_prescribing_cost_centre'));
wp_insert_term('259','ods_role_code', array('description' => 'Urgent & Emergency Care Prescribing Cost Centre', 'slug' => 'urgent_&_emergency_care_prescribing_cost_centre'));
wp_insert_term('260','ods_role_code', array('description' => 'Young Offender Institution Prescribing Cost Centre', 'slug' => 'young_offender_institution_prescribing_cost_centre'));
wp_insert_term('261','ods_role_code', array('description' => 'Strategic Partnership', 'slug' => 'strategic_partnership'));
wp_insert_term('262','ods_role_code', array('description' => 'Sustainability Transformation Partnership', 'slug' => 'sustainability_transformation_partnership'));
wp_insert_term('266','ods_role_code', array('description' => 'Extended Access Provider', 'slug' => 'extended_access_provider'));
wp_insert_term('267','ods_role_code', array('description' => 'Extended Access Hub', 'slug' => 'extended_access_hub'));
wp_insert_term('268','ods_role_code', array('description' => 'Medicine Supplier', 'slug' => 'medicine_supplier'));
wp_insert_term('269','ods_role_code', array('description' => 'Care Home', 'slug' => 'care_home'));
wp_insert_term('270','ods_role_code', array('description' => 'Domiciliary Care', 'slug' => 'domiciliary_care'));
wp_insert_term('272','ods_role_code', array('description' => 'Primary Care Network', 'slug' => 'primary_care_network'));
wp_insert_term('273','ods_role_code', array('description' => 'Combined Authority', 'slug' => 'combined_authority'));
wp_insert_term('274','ods_role_code', array('description' => 'Local Health And Care Record Exemplar', 'slug' => 'local_health_and_care_record_exemplar'));
wp_insert_term('275','ods_role_code', array('description' => 'SMHPC - CYPMHS Tier 4', 'slug' => 'smhpc_-_cypmhs_tier_4'));
wp_insert_term('276','ods_role_code', array('description' => 'SMHPC - Adult Eating Disorders', 'slug' => 'smhpc_-_adult_eating_disorders'));
wp_insert_term('277','ods_role_code', array('description' => 'SMHPC - Adult Secure Services', 'slug' => 'smhpc_-_adult_secure_services'));
wp_insert_term('279','ods_role_code', array('description' => 'COVID Vaccination Centre', 'slug' => 'covid_vaccination_centre'));
wp_insert_term('280','ods_role_code', array('description' => 'Pharmacy Site', 'slug' => 'pharmacy_site'));
wp_insert_term('281','ods_role_code', array('description' => 'Community School', 'slug' => 'community_school'));
wp_insert_term('282','ods_role_code', array('description' => 'Voluntary Aided School', 'slug' => 'voluntary_aided_school'));
wp_insert_term('283','ods_role_code', array('description' => 'Voluntary Controlled School', 'slug' => 'voluntary_controlled_school'));
wp_insert_term('284','ods_role_code', array('description' => 'Foundation School', 'slug' => 'foundation_school'));
wp_insert_term('285','ods_role_code', array('description' => 'City Technology College', 'slug' => 'city_technology_college'));
wp_insert_term('286','ods_role_code', array('description' => 'Community Special School', 'slug' => 'community_special_school'));
wp_insert_term('287','ods_role_code', array('description' => 'Non-Maintained Special School', 'slug' => 'non-maintained_special_school'));
wp_insert_term('288','ods_role_code', array('description' => 'Other Independent Special School', 'slug' => 'other_independent_special_school'));
wp_insert_term('289','ods_role_code', array('description' => 'Other Independent School', 'slug' => 'other_independent_school'));
wp_insert_term('29','ods_role_code', array('description' => 'Treatment Centre', 'slug' => 'treatment_centre'));
wp_insert_term('290','ods_role_code', array('description' => 'Foundation Special School', 'slug' => 'foundation_special_school'));
wp_insert_term('291','ods_role_code', array('description' => 'Pupil Referral Unit', 'slug' => 'pupil_referral_unit'));
wp_insert_term('292','ods_role_code', array('description' => 'Local Authority Nursery School', 'slug' => 'local_authority_nursery_school'));
wp_insert_term('293','ods_role_code', array('description' => 'Further Education', 'slug' => 'further_education'));
wp_insert_term('294','ods_role_code', array('description' => 'Secure Units', 'slug' => 'secure_units'));
wp_insert_term('296','ods_role_code', array('description' => 'Miscellaneous', 'slug' => 'miscellaneous'));
wp_insert_term('297','ods_role_code', array('description' => 'Academy Sponsor Led', 'slug' => 'academy_sponsor_led'));
wp_insert_term('298','ods_role_code', array('description' => 'Higher Education Institutions', 'slug' => 'higher_education_institutions'));
wp_insert_term('299','ods_role_code', array('description' => 'Sixth Form Centres', 'slug' => 'sixth_form_centres'));
wp_insert_term('30','ods_role_code', array('description' => 'Research And Development', 'slug' => 'research_and_development'));
wp_insert_term('300','ods_role_code', array('description' => 'Special Post 16 Institution', 'slug' => 'special_post_16_institution'));
wp_insert_term('301','ods_role_code', array('description' => 'Academy Special Sponsor Led', 'slug' => 'academy_special_sponsor_led'));
wp_insert_term('302','ods_role_code', array('description' => 'Academy Converter', 'slug' => 'academy_converter'));
wp_insert_term('303','ods_role_code', array('description' => 'Free Schools', 'slug' => 'free_schools'));
wp_insert_term('304','ods_role_code', array('description' => 'Free Schools Special', 'slug' => 'free_schools_special'));
wp_insert_term('305','ods_role_code', array('description' => 'Free Schools Alternative Provision', 'slug' => 'free_schools_alternative_provision'));
wp_insert_term('306','ods_role_code', array('description' => 'Free Schools 16 to 19', 'slug' => 'free_schools_16_to_19'));
wp_insert_term('307','ods_role_code', array('description' => 'University Technical College', 'slug' => 'university_technical_college'));
wp_insert_term('308','ods_role_code', array('description' => 'Studio Schools', 'slug' => 'studio_schools'));
wp_insert_term('309','ods_role_code', array('description' => 'Academy Alternative Provision Converter', 'slug' => 'academy_alternative_provision_converter'));
wp_insert_term('31','ods_role_code', array('description' => 'PPA Epact System', 'slug' => 'ppa_epact_system'));
wp_insert_term('310','ods_role_code', array('description' => 'Academy Alternative Provision Sponsor Led', 'slug' => 'academy_alternative_provision_sponsor_led'));
wp_insert_term('311','ods_role_code', array('description' => 'Academy Special Converter', 'slug' => 'academy_special_converter'));
wp_insert_term('312','ods_role_code', array('description' => 'Academy 16-19 Converter', 'slug' => 'academy_16-19_converter'));
wp_insert_term('313','ods_role_code', array('description' => 'Academy 16 to 19 Sponsor Led', 'slug' => 'academy_16_to_19_sponsor_led'));
wp_insert_term('314','ods_role_code', array('description' => 'Institution Funded By Other Government Department', 'slug' => 'institution_funded_by_other_government_department'));
wp_insert_term('315','ods_role_code', array('description' => 'Northern Ireland GP Practice', 'slug' => 'northern_ireland_gp_practice'));
wp_insert_term('316','ods_role_code', array('description' => 'Special Schools Eye Care Service Provider', 'slug' => 'special_schools_eye_care_service_provider'));
wp_insert_term('317','ods_role_code', array('description' => 'Community Diagnostic Centre', 'slug' => 'community_diagnostic_centre'));
wp_insert_term('318','ods_role_code', array('description' => 'Integrated Care Board', 'slug' => 'integrated_care_board'));
wp_insert_term('319','ods_role_code', array('description' => 'Sub Icb Location', 'slug' => 'sub_icb_location'));
wp_insert_term('320','ods_role_code', array('description' => 'Elective Surgical Hub', 'slug' => 'elective_surgical_hub'));
wp_insert_term('321','ods_role_code', array('description' => 'Primary Care Network Prescribing Cost Centre', 'slug' => 'primary_care_network_prescribing_cost_centre'));
wp_insert_term('322','ods_role_code', array('description' => 'Northern Ireland Non-Hpss Provider/Organisation', 'slug' => 'northern_ireland_non-hpss_provider/organisation'));
wp_insert_term('324','ods_role_code', array('description' => 'SMHPC - Perinatal Mh', 'slug' => 'smhpc_-_perinatal_mh'));
wp_insert_term('33','ods_role_code', array('description' => 'County Council', 'slug' => 'county_council'));
wp_insert_term('34','ods_role_code', array('description' => 'Borough Council', 'slug' => 'borough_council'));
wp_insert_term('35','ods_role_code', array('description' => 'City Council', 'slug' => 'city_council'));
wp_insert_term('36','ods_role_code', array('description' => 'District Council', 'slug' => 'district_council'));
wp_insert_term('37','ods_role_code', array('description' => 'Metropolitan District', 'slug' => 'metropolitan_district'));
wp_insert_term('38','ods_role_code', array('description' => 'Council', 'slug' => 'council'));
wp_insert_term('39','ods_role_code', array('description' => 'Metropolitan District Council', 'slug' => 'metropolitan_district_council'));
wp_insert_term('40','ods_role_code', array('description' => 'Unitary Authority', 'slug' => 'unitary_authority'));
wp_insert_term('57','ods_role_code', array('description' => 'Foundation Trust', 'slug' => 'foundation_trust'));
wp_insert_term('65','ods_role_code', array('description' => 'Private Dental Practice', 'slug' => 'private_dental_practice'));
wp_insert_term('67','ods_role_code', array('description' => 'Specialised Commissioning Group', 'slug' => 'specialised_commissioning_group'));
wp_insert_term('7','ods_role_code', array('description' => 'Hospice', 'slug' => 'hospice'));
wp_insert_term('71','ods_role_code', array('description' => 'Privately Owned Entity', 'slug' => 'privately_owned_entity'));
wp_insert_term('72','ods_role_code', array('description' => 'Other Prescribing Cost Centre', 'slug' => 'other_prescribing_cost_centre'));
wp_insert_term('76','ods_role_code', array('description' => 'GP Practice', 'slug' => 'gp_practice'));
wp_insert_term('80','ods_role_code', array('description' => 'Out Of Hours Cost Centre', 'slug' => 'out_of_hours_cost_centre'));
wp_insert_term('82','ods_role_code', array('description' => 'Prison Prescribing Cost Centre', 'slug' => 'prison_prescribing_cost_centre'));
wp_insert_term('83','ods_role_code', array('description' => 'Residential Institution', 'slug' => 'residential_institution'));
wp_insert_term('87','ods_role_code', array('description' => 'Walk In Centre', 'slug' => 'walk_in_centre'));
wp_insert_term('88','ods_role_code', array('description' => 'GP Abeyance And Dispersal', 'slug' => 'gp_abeyance_and_dispersal'));
wp_insert_term('89','ods_role_code', array('description' => 'Executive Agency Programme - Department', 'slug' => 'executive_agency_programme_-_department'));
wp_insert_term('90','ods_role_code', array('description' => 'Executive Agency Site', 'slug' => 'executive_agency_site'));
wp_insert_term('91','ods_role_code', array('description' => 'Executive Agency Programme', 'slug' => 'executive_agency_programme'));
wp_insert_term('92','ods_role_code', array('description' => 'Application Service Provider', 'slug' => 'application_service_provider'));
wp_insert_term('93','ods_role_code', array('description' => 'Application Service Provider - Legacy', 'slug' => 'application_service_provider_-_legacy'));
wp_insert_term('94','ods_role_code', array('description' => 'Appliance Contractor', 'slug' => 'appliance_contractor'));
wp_insert_term('96','ods_role_code', array('description' => 'Branch Surgery', 'slug' => 'branch_surgery'));
wp_insert_term('98','ods_role_code', array('description' => 'Clinical Commissioning Group', 'slug' => 'clinical_commissioning_group'));
wp_insert_term('99','ods_role_code', array('description' => 'Clinical Commissioning Group Site', 'slug' => 'clinical_commissioning_group_site'));

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
  // only check CQC API and show fields if there is a location id
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
        <div id="hw_services_cqc_deg_reg_alert" role="alert"><p>This service has been automatically marked as 'Deregistered'. If there is a new registration, update the <strong>Location ID</strong> above. If there is no new registration, change the <a href="#tagsdiv-cqc_reg_status">CQC Registration Status</a> to 'Archived'.</p></div>
      </div><?
    }
    echo '<a href="https://www.cqc.org.uk/location/' . $objcqcapiquery->locationId . '?referer=widget4" target="_blank">Check this registration on the CQC website</a>';
  }

// ODS
echo '<h2><strong>ODS Information</strong></h2><br />';
// ODS CODE
$value = get_post_meta( $post->ID, 'hw_services_ods_code', true );
echo '<label for="hw_services_ods_code">ODS Code </label>';
echo '<input type="text" id="hw_services_ods_code" name="hw_services_ods_code" value="' . esc_attr( $value ) . '" size="6" />';
  // only check API and show fields if there is a ODS Code
  if ($value != '') {
    $objodsapiquery = json_decode(hw_feedback_ods_api_query_by_code(esc_attr($value)));
    $is_active = $objodsapiquery->active ? 'Yes' : 'No';
    echo '<br /><h3>API Checks</h3>';
    echo '<div id="api-output-name" class="api-output"><div class="api-output-label">Organisation Name:</div><div class="api-output-value">'.$objodsapiquery->name.'</div></div>';
    echo '<div id="api-output-active" class="api-output"><div class="api-output-label">Active?</div><div class="api-output-value">'.$is_active.'</div></div>';
    echo '<div id="api-output-start" class="api-output"><div class="api-output-label">Start date:</div><div class="api-output-value">'.$objodsapiquery->extension[0]->valuePeriod->start.'</div></div>'; 
    if ( isset($objodsapiquery->active) && $objodsapiquery->active != true){ ?>
      <div class="api-output-inactive">
        <div class="api-output-label">Last Updated:</div><div class="api-output-value"><?php echo date("F jS, Y", strtotime($objodsapiquery->meta->lastUpdated))?></div>
        <div id="hw_services_cqc_deg_reg_alert" role="alert"><p>This organisation is no longer active.</p></div>
      </div><?
    }
    echo '<a href="https://directory.spineservices.nhs.uk/STU3/Organization/' . $objodsapiquery->id . '" target="_blank">Check this registration in the ODS API</a>';
  }
echo "<br />";

// ADDRESS FIELDS
echo "<h2><strong>Address</strong></h2><br />";

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
$value = get_post_meta( $post->ID, 'hw_services_contact_optout', true );
  echo '<label for="hw_services_contact_optout">Notification opt-out</label>';
  // on first run, a checkbox needs a null value - two solutions
  // if ( ! isset( $options[ $args['label_for'] ] ) ) { $options[ $args['label_for'] ] = false; }
  $value = !empty( $value ) ? $value : 0;
  ?>
  <input type="checkbox"
    id="hw_services_contact_optout"
    name="hw_services_contact_optout"
    <?php // checked() as a WordPress function - compares the first two arguments and if identical marks as checked - last arg control whether to echo or not
    checked( 1, $value, true ) ?>
    >
<?php
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

    // CONTACT OPTOUT
		if ( ! isset( $_POST['hw_services_contact_optout'] ) ) {
      // if $_POST var is not set the box is unchecked, so store an "off" value
      update_post_meta( $post_id, 'hw_services_contact_optout', 0 );
    } else {
		  update_post_meta( $post_id, 'hw_services_contact_optout', 1 );
    }

		// CQC LOCATION CODE
		if ( ! isset( $_POST['hw_services_cqc_location'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_cqc_location'] );
		update_post_meta( $post_id, 'hw_services_cqc_location', $my_data );

		// NHS ODS (Organisation Data Service) code - https://digital.nhs.uk/services/organisation-data-service
		if ( ! isset( $_POST['hw_services_ods_code'] ) ) { return; }
		$my_data = strtoupper(sanitize_text_field( $_POST['hw_services_ods_code'] ));
		update_post_meta( $post_id, 'hw_services_ods_code', $my_data );

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
    $columns['ods_code']  = 'ODS Code';
    return $columns;

}
add_action( 'manage_local_services_posts_custom_column', 'bs_local_services_table_content', 10, 2 );

// Fill data into ADMIN COLUMNS for local services
function bs_local_services_table_content( $column_name, $post_id ) {

    if( $column_name == 'rated' ) {
		$col_rating = get_post_meta( $post_id, 'hw_services_overall_rating', true );
    if($col_rating > 0){echo '<p>'.hw_feedback_star_rating($col_rating,array('colour' => 'green')).'</p>';}

	}

  if( $column_name == 'ods_code' ) {
    $col_ods_code = get_post_meta( $post_id, 'hw_services_ods_code', true );
    echo "<a target='_blank' href='https://directory.spineservices.nhs.uk/STU3/Organization/". $col_ods_code ."'>";
    echo $col_ods_code;
    echo "</a>";
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
      // Update the local_service with CQC data
      // if reg status has been changed, this will return 'changed'
      $reg_status = hw_feedback_check_cqc_registration_status_single($hw_feedback_post->ID);
      if ($reg_status === 'changed') {
        array_push($registration_status_changed,$hw_feedback_post->ID);
      }
      // remove ALL terms
      //wp_remove_object_terms( $post_id, array('registered','deregistered','not-registered'), 'cqc_reg_status' );
    endforeach;
    error_log('hw-feedback: services check complete!');
    // restore the hw_feedback_check_cqc_registration_status_single function hook
    //add_action( 'updated_post_meta', 'hw_feedback_save_local_services_meta', 10, 4);

    // set php mailer variables
    $to = get_option('admin_email');
    $subject = "Local Services - CQC registration updates (". parse_url( get_site_url(), PHP_URL_HOST ) .")";
    // set headers to allow HTML
    $headers = array('Content-Type: text/html; charset=UTF-8');
    // build the content
    $formatted_message = '<p>Hi!</p><p>The CQC registration check completed successfully at ' . date('d/m/Y h:i:s a', time()).'</p>';
    // check if there were changes
    if (empty($registration_status_changed)) {
      $formatted_message .= '<p>There were no changes.</p>';
    } else {
    // compose an email contain reg changes
      $formatted_message .= '<p>The CQC registration status of the following services was updated automatically:</p><ul>';
      foreach ($registration_status_changed as $post_id) {
        $location_id = get_post_meta( $post_id, 'hw_services_cqc_location', true );
        $formatted_message .= '<li>' . get_the_title($post_id) . ' - <a href="https://www.cqc.org.uk/location/' . $location_id . '" target="_blank">' . $location_id . '</a> (';
        $formatted_message .= '<a href="'.get_site_url().'/wp-admin/post.php?post='.$post_id.'&action=edit">Edit</a> | <a href="'.get_post_permalink($post_id).'">View</a>)</li>';
      }
      $formatted_message .= '</ul>';
    }
    $formatted_message .= '<p>Hugs and kisses!</p>';
    $sent = wp_mail($to, $subject, stripslashes($formatted_message), $headers);

    if ( $sent ) {
      error_log('hw-feedback: cqc reg update email sent');
    } else {
      error_log('hw-feedback: cqc reg update email failed');
    }
}

/* 10. Link cron job to function check_cqc_registration_status
--------------------------------------------------------- */
add_action( 'hw_feedback_cqc_reg_check_cron_job', 'hw_feedback_check_cqc_registration_status' );
add_action( 'hw_feedback_ods_check_bootstrap_cron_job', 'hw_feedback_ods_checks_bootstrap');

// Send an email to a provider when a new comment is APPROVED
function hw_feedback_approve_comment($new_status, $old_status, $comment) {
  $options = get_option( 'hw_feedback_options' );
  // check notifications enabled
  if ( isset( $options['hw_feedback_field_enable_notifications'] ) ) {
    error_log('hw-feedback: notification check');
    // get comment details
    $new_comment = get_comment( $comment, OBJECT );
    // check for provider optout
    if ( get_post_meta( $new_comment->comment_post_ID, 'hw_services_contact_optout', true ) ) {
      error_log('hw-feedback: provider opt-out');
      return;
    }
    // check if notification has been sent before
    if ( ! get_comment_meta( $comment->comment_ID, 'feedback_provider_notification_sent', true) ) {
      error_log('hw-feedback: no previous notification');
      // check that comment was unapproved and is now approved (don't send on unapprove or spam)
      if ( $old_status == "unapproved" && $new_status == "approved" ) {
        error_log('hw-feedback: approve comment fired');
        // get post details
        $title_post = get_the_title( $new_comment->comment_post_ID );
        $link_post = get_permalink( $new_comment->comment_post_ID );
        // check if comment has been withheld
        $withheld_comment_text = htmlspecialchars($options['hw_feedback_field_withheld_comment_text']);
        if ( strpos($new_comment->comment_content,$withheld_comment_text) === 0 ) {
          // comment has been withheld so do nothing
          error_log('hw-feedback: comment withheld '.strpos($new_comment->comment_content,$withheld_comment_text));
          return;
        }
        // send an email
        // set php mailer variables
        $contact_email = get_post_meta($new_comment->comment_post_ID,'hw_services_contact_email',true);
        // set headers to allow HTML
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        // set FROM header - complies with RFC 2822
        if ( isset( $options['hw_feedback_field_email_from_address'] ) ) {
          if ( isset( $options['hw_feedback_field_email_from_name'] ) ) {
            $headers[] = 'From: '. $options['hw_feedback_field_email_from_name'] .' <'. $options['hw_feedback_field_email_from_address'] .'>';
          } else {
            $headers[] = 'From: '. $options['hw_feedback_field_email_from_address'];
          }
        }
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
          $formatted_message .= '<p>If you would like to respond to this comment, please reply to this email. All responses will be published alongside the comment on our website. We will also share your response directly with the commenter where possible.</p>';
          $formatted_message .= '<p>Kind regards</p>';
          //$email_footer = htmlspecialchars($options['hw_feedback_field_comment_email_footer']);
          $email_footer = $options['hw_feedback_field_comment_email_footer'];
          $formatted_message .= $email_footer;
          $sent_provider = wp_mail($to, $subject, stripslashes($formatted_message), $headers);
        } else if ( isset ($options['hw_feedback_field_enable_missing_address_reminders']) ) {
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
    } else {
      error_log('hw-feedback: previous notification sent');
    }
  }
}
// fires when a comment status changes
add_action('transition_comment_status', 'hw_feedback_approve_comment', 10, 3);
?>
