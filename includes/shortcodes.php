<?php

/* Healthwatch review (Dignity in Care) star rating block */
function hw_feedback_shortcode_ratings_block()
{
	ob_start();
	$id = get_the_id();
	if (get_post_meta($id, 'hw_services_overall_rating', true)) { ?>
		<div class="rate-and-review-scores">
			<!-- start row -->
			<div class="row">
				<span class="screen-reader-text">Out of 5 stars</span>
				<div class="col-md-6 col-sm-6 col-xs-7 rating-label">
					<p>How people are treated:</p>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-5 star-rating">
					<?php $rating = get_post_meta($id, 'hw_services_how_people_treated', true);
					echo hw_feedback_star_rating($rating, array('colour' => 'green', 'size' => ''));
					if ($rating == 1) echo '<span class="screen-reader-text">' . $rating . ' star</span>';
					else echo '<span class="screen-reader-text">' . $rating . ' stars</span>';
					?>
				</div>
			</div>
			<!-- start row -->
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-7 rating-label">
					<p>Personal choice:</p>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-5 star-rating">
					<?php $rating = get_post_meta($id, 'hw_services_personal_choice', true);
					echo hw_feedback_star_rating($rating, array('colour' => 'green', 'size' => ''));
					if ($rating == 1) echo '<span class="screen-reader-text">' . $rating . ' star</span>';
					else echo '<span class="screen-reader-text">' . $rating . ' stars</span>';
					?>
				</div>
			</div>
			<!-- start row -->
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-7 rating-label">
					<p>Just like being at home:</p>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-5 star-rating">
					<?php $rating = get_post_meta($id, 'hw_services_being_home', true);
					echo hw_feedback_star_rating($rating, array('colour' => 'green', 'size' => ''));
					if ($rating == 1) echo '<span class="screen-reader-text">' . $rating . ' star</span>';
					else echo '<span class="screen-reader-text">' . $rating . ' stars</span>';
					?>
				</div>
			</div>
			<!-- start row -->
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-7 rating-label">
					<p>Privacy:</p>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-5 star-rating">
					<?php $rating = get_post_meta($id, 'hw_services_privacy', true);
					echo hw_feedback_star_rating($rating, array('colour' => 'green', 'size' => ''));
					if ($rating == 1) echo '<span class="screen-reader-text">' . $rating . ' star</span>';
					else echo '<span class="screen-reader-text">' . $rating . ' stars</span>';
					?>
				</div>
			</div>
			<!-- start row -->
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-7 rating-label">
					<p>Quality of life:</p>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-5 star-rating">
					<?php $rating = get_post_meta($id, 'hw_services_quality_life', true);
					echo hw_feedback_star_rating($rating, array('colour' => 'green', 'size' => ''));
					if ($rating == 1) echo '<span class="screen-reader-text">' . $rating . ' star</span>';
					else echo '<span class="screen-reader-text">' . $rating . ' stars</span>';
					?>
				</div>
			</div>
		</div><!-- end of rate and review panel -->
	<?php } ?>
	<?php return ob_get_clean(); ?>
<? }

add_shortcode('ratings', 'hw_feedback_shortcode_ratings_block');

/* Media object review

This inserts the content of a review as a callout

------------------------ */

function hwbucks_shortcode_review_callout( $atts, $content = null ) {
	$a = shortcode_atts( array(
		'service_id' => (int)'52449', // this is 228 Wendover Road
		'hide_city'  =>  'true', // set to true by default
	), $atts, 'embed_review' );

	// fetch the signpost
	$content_post = get_post($a['service_id']);
	// get and clean up the content and title
	$content = apply_filters('the_content', $content_post->post_content);
	$title = apply_filters('the_title', $content_post->post_title);
	$content = do_shortcode($content);
	// get meta items
	$visit_date = get_post_meta( $content_post->ID, 'hw_services_date_visited', true );
	$rating = get_post_meta( $content_post->ID, 'hw_services_overall_rating', true );
	$city = get_post_meta( $content_post->ID, 'hw_services_city', true );
	$report_link = get_post_meta( $content_post->ID, 'hw_services_full_report', true );

	// typecast
	$a['hide_city'] = filter_var( $a['hide_city'], FILTER_VALIDATE_BOOLEAN );
	$label = 'Review';
	$review_object = '
	<div class="media callout callout-review">
		<div class="media-left callout">
				<i class="media-object fas fa-star fa-2x shortcode-icon" aria-hidden="true" title="' . $label . '"></i>
		</div>
		<div class="media-body callout">';
		$review_object .= '<h3>' . $title . '</h3>';
		if ( ! $a['hide_city'] ) {
			$review_object .= '<p><span class="city">' . $city . '</span></p>';
		}
		$review_object .= '<p>' . hw_feedback_star_rating($rating,array('colour' => 'green','size' => 'fa-lg'));
		if ($rating == 1) $review_object .= '<span class="screen-reader-text">'.$rating.' star</span>';
		else $review_object .= '<span class="screen-reader-text">'.$rating.' stars</span>';
		$review_object .= ' (<a class="review-link" href="' . get_the_permalink($content_post->ID) . '" rel="bookmark">Write a review</a>)</p>';
		$review_object .= '<p class="visit-date">Visited on <strong>' . $visit_date . '</strong></p>';
		$review_object .= '<p><em>"' . get_the_excerpt($content_post->ID) . '"</em></p>';
		$review_object .= '<p><a class="report-link" href="' . $report_link . '" rel="bookmark">Read our full report</a></p></div>
	</div>';

	return $review_object;
}

add_shortcode( 'embed_review', 'hwbucks_shortcode_review_callout' );

/* Media object NEW SERVICE
------------------------ */

function hwbucks_shortcode_new_service_callout( $atts, $content = null ) {
	$a = shortcode_atts( array(
		'website' => get_site_url(), // website for the callout
	), $atts );

	if ( empty( $content ) ) {
		$cleaned_url = wp_strip_all_tags($a['website']);
	} else {
		$cleaned_url = wp_strip_all_tags($content);
	}
	$display_url = preg_replace("(^https?://)", "", $cleaned_url );
	$label = 'New service';
	$new_service_object = '
	<div class="media callout callout-new-service">
		<div class="media-left callout">
				<i class="media-object fas fa-plus-square fa-2x shortcode-icon" aria-hidden="true" title="' . $label . '"></i>
		</div>
		<div class="media-body callout"><p>There is a new service at this location <a href="' . $cleaned_url . '">' . $display_url . '</a></p></div>
	</div>';

	return $new_service_object;
}

add_shortcode( 'new_service', 'hwbucks_shortcode_new_service_callout' );

/* Media object DUAL REGISTRATION
------------------------ */

function hwbucks_shortcode_dual_registration_callout( $atts, $content = null ) {
	$a = shortcode_atts( array(
		'cqc_location_id' => 'RXQ32', // Location ID for the callout
	), $atts );

	if ( empty( $content ) ) {
		$content = $a['cqc_location_id'];
	}
	$label = 'Dual registration';
	$dual_reg_object = '
	<div class="media callout callout-dual-reg">
		<div class="media-left callout">
				<i class="media-object fas fa-user-friends fa-2x shortcode-icon" aria-hidden="true" title="' . $label . '"></i>
		</div>
		<div class="media-body callout"><p>This location is run by two companies. They have a dual registration and are jointly responsible for the services. You can view the second registration on the CQC website: <a href="https://www.cqc.org.uk/location/' . $content . '" target="_blank">' . $content . '</a></p></div>
	</div>';

	return $dual_reg_object;
}

add_shortcode( 'dual_reg', 'hwbucks_shortcode_dual_registration_callout' );

/* Media object MULTIPLE REGISTRATION
------------------------ */

function hwbucks_shortcode_multi_registration_callout( $atts, $content = null ) {
	$a = shortcode_atts( array(
		'hw_feedback_local_service_id' => 'RXQ32', // Location ID for the callout
	), $atts );

	if ( empty( $content ) ) {
		$content = $a['hw_feedback_local_service_id'];
	}
	$label = 'Multiple registrations';
	$multi_reg_object = '
	<div class="media callout callout-dual-reg">
		<div class="media-left callout">
				<i class="media-object fas fa-users fa-2x shortcode-icon" aria-hidden="true" title="' . $label . '"></i>
		</div>
		<div class="media-body callout"><p>More than one provider is registered to provide services at this address. Please check you are reviewing the correct provider.</p>';
		$local_service_ids = explode(',', $content);
		foreach ( $local_service_ids as $local_service_id ) {
			$multi_reg_object .= '<p><a href="'.apply_filters( 'the_permalink',get_permalink($local_service_id),$local_service_id).'">'.apply_filters( 'the_title',get_the_title($local_service_id),$local_service_id).'</a></p>'; ; 
		}
		$multi_reg_object .= '</div>
	</div>';

	return $multi_reg_object;
}

add_shortcode( 'multi_reg', 'hwbucks_shortcode_multi_registration_callout' );

/* Media object MULTIPLE SERVICES
------------------------ */

function hwbucks_shortcode_multi_services_callout( $atts, $content = null ) {
	$a = shortcode_atts( array(
		'hw_feedback_local_service_id' => 'RXQ32', // Location ID for the callout
	), $atts );

	if ( empty( $content ) ) {
		$content = $a['hw_feedback_local_service_id'];
	}
	$label = 'Multiple services';
	$multi_serv_object = '
	<div class="media callout callout-dual-serv">
		<div class="media-left callout">
				<i class="media-object fas fa-sitemap fa-2x shortcode-icon" aria-hidden="true" title="' . $label . '"></i>
		</div>
		<div class="media-body callout"><p>The registered provider at this location provides multiple services. Please check you are reviewing the correct service.</p>';
		$local_service_ids = explode(',', $content);
		foreach ( $local_service_ids as $local_service_id ) {
			$multi_serv_object .= '<p><a href="'.apply_filters( 'the_permalink',get_permalink($local_service_id),$local_service_id).'">'.apply_filters( 'the_title',get_the_title($local_service_id),$local_service_id).'</a></p>'; ; 
		}
		$multi_serv_object .= '</div>
	</div>';

	return $multi_serv_object;
}

add_shortcode( 'multi_serv', 'hwbucks_shortcode_multi_services_callout' );

?>
