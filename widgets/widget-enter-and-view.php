<?php // Lists ENTER & VIEW

// Register ENTER AND VIEW widget
add_action( 'widgets_init', 'init_hw_enter_and_view' );
function init_hw_enter_and_view() { return register_widget('hw_enter_and_view'); }

class hw_enter_and_view extends WP_Widget {
	/** constructor */
	function hw_enter_and_view() {
		parent::WP_Widget(

			'hw_enter_and_view',
			$name = 'HW Enter and View',
				array(
					'classname'   => 'scaffold_widget_enter_and_view widget_enter_and_view',
					'description' => 'Lists the most recent Enter and View reports, with star rating'
					)

			);
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
		$mlq = new WP_Query(array(
			'post_type' => 'Local_services',
			// 'orderby' => 'rand',
			'showposts' => $number,
			'meta_query' => array(
				array(
					'key'     => 'hw_services_overall_rating',
					'value'   => array( 1, 2, 3, 4, 5 ),
					'compare' => 'IN',
						),
					),
			)
		);

		if( $mlq->have_posts() ) :
		?>

<div class='row'>


<?php while($mlq->have_posts()) : $mlq->the_post(); ?>


<div class="col-md-3 col-sm-6 col-xs-12">

<h3 style="margin:0; padding-bottom: .5rem;"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>

			<?php $rating = get_post_meta( $post->ID, 'hw_services_overall_rating', true );
				echo feedbackstarrating($rating,'green');
			?>

<?php // get_template_part("elements/comments-rating-average"); ?>

<?php the_excerpt(); ?>

</div><!-- end of column -->




		<?php wp_reset_query();
		endwhile; ?>

</div><!-- end of row -->

		<?php endif; ?>
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

} // class hw_enter_and_view

?>
