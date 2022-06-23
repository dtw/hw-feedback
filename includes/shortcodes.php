<?php

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
		$review_object .= ' (<a class="review-link" href="' . get_the_permalink($content_post->ID) . '" rel="bookmark">Leave your own rating</a>)</p>';
		$review_object .= '<p class="visit-date">Visited on <strong>' . $visit_date . '</strong></p>';
		$review_object .= '<p><em>"' . get_the_excerpt($content_post->ID) . '"</em></p>';
		$review_object .= '<p><a class="report-link" href="' . $report_link . '" rel="bookmark">Read our full report</a></p></div>
	</div>';

	return $review_object;
}

add_shortcode( 'embed_review', 'hwbucks_shortcode_review_callout' );

?>
