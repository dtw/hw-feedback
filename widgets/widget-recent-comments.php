<?php


// Register 'Recently Rated Services' widget
add_action( 'widgets_init', 'init_hw_recent_comments' );
function init_hw_recent_comments() { return register_widget('hw_recent_comments'); }

class hw_recent_comments extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::__construct( 'rcp_recent_comments', $name = 'HW recent comments' );
	}

	function hw_recent_comments () {
		self::__construct();
	}

	/**
	* This is our Widget
	**/
	function widget( $args, $instance ) {
		global $post;
		extract($args);

		// Widget options
		$title 	 = apply_filters('widget_title', $instance['title'] ); // Title
		$number	 = $instance['number']; // Number of posts to show

        // Output
		echo $before_widget;

	    if ( $title ) echo $before_title . $title . $after_title;


$args = array(
	'status' => 'approve',
	'post_type' => 'local_services',
	'number' => $number,
);

// The Query
$comments_query = new WP_Comment_Query;
$comments = $comments_query->query( $args );


// Comment Loop
if ( $comments ) {

echo "<div class='row'>";
	foreach ( $comments as $comment ) { ?>
	<div class="review-container col-md-3 col-sm-6 col-xs-12">
		<?php												// Display icon for taxonomy term
			$term_ids = get_the_terms( $comment->comment_post_ID, 'service_types' );	// Find taxonomies
			$term_id = $term_ids[0]->term_id;											// Get taxonomy ID
			$term_name = $term_ids[0]->name;											// Get taxonomy name
			$term_icon = get_term_meta( $term_id, 'icon', true );						// Get meta
		?>

		<div class="text-center">
			<a class="img-anchor" href="<?php echo get_the_permalink($comment->comment_post_ID); ?>" aria-hidden="true">
				<img width="80" height="80" src="<?php echo $term_icon; ?>" alt="<?php echo $term_name?>"  />
			</a>
		</div>
		<h3>
			<a href="<?php echo get_the_permalink($comment->comment_post_ID); ?>"><?php echo get_the_title($comment->comment_post_ID); ?></a>
		</h3>
		<?php // Display star rating
		$individual_rating = get_comment_meta( $comment->comment_ID, 'feedback_rating', true );
		if ($individual_rating) { ?>
	  <p class="star-rating p-rating">
	  	<?php echo hw_feedback_star_rating($individual_rating,array('size' => 'fa-lg'));
				if ($individual_rating == 1) echo '<span class="screen-reader-text">'.$individual_rating.' star</span>';
				else echo '<span class="screen-reader-text">'.$individual_rating.' stars</span>';
			?>
	  </p>
		<p>
			<strong>
				<?php echo human_time_diff( strtotime($comment->comment_date), current_time( 'timestamp' ) ); ?> ago
			</strong>
		</p>
		<?php } // end of if there is a rating ?>
		<p><?php // mb_strimwidth trims comment to 300 (if needed) and adds an ellipsis
			// wpautop converts double line breaks to <p></p>
			// i.e. this keeps line breaks in the comment
			echo wpautop(wp_strip_all_tags(mb_strimwidth($comment->comment_content,0,300," ...")), true); ?>
		</p>
		<?php if (get_comment_meta( $comment->comment_ID, 'feedback_response', true )) { ?>
			<div class="feedback-response">
				<img width="100" height="100" class="alignright" src="<?php bloginfo('url') ?>/wp-content/themes/scaffold/images/icons/colour/response-small.png" alt="Response" />
				<p><?php echo get_the_title($comment->comment_post_ID); ?> has responded to this feedback:</p>
				<blockquote><em><?php echo mb_strimwidth ( get_comment_meta( $comment->comment_ID, 'feedback_response', true ),0,180," ..." ); ?></em>	<a href="<?php echo get_the_permalink($comment->comment_post_ID); ?>">read more</a></blockquote>
			</div><!-- end of response -->
		<?php } ?>
	</div><!-- end of review-container -->

<?php	 } // end of loop?

echo "</div><!-- end of row -->";

} else {
	echo 'No comments found.';
}


?>



		<?php
		// echo widget closing tag
		echo $after_widget;
	}

	/** Widget control update */
	function update( $new_instance, $old_instance ) {
		$instance    = $old_instance;

		//Let's turn that array into something the Wordpress database can store
		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		return $instance;
	}

	/**
	* Widget settings
	**/
	function form( $instance ) {

		    // instance exist? if not set defaults
		    if ( $instance ) {
				$title  = $instance['title'];
		        $number = $instance['number'];
		    } else {
			    //These are our defaults
				$title  = '';
		        $number = '4';
		    }

			// The widget form
			?>
			<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __( 'Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat" />
			</p>
			<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php echo __( 'How many services?' ); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</p>
	<?php
	}

} // class hw_recent_comments

?>
