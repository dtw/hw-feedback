<?php

// Register 'Most Rated Services' widget
add_action( 'widgets_init', 'init_hw_most_rated' );
function init_hw_most_rated() { return register_widget('hw_most_rated'); }

class hw_most_rated extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::__construct(

			'hw_most_rated',
			$name = 'HW Most Rated',
				array(
					'classname'   => 'scaffold_widget_most_rated widget_most_rated',
					'description' => 'Lists the most rated services, with star rating'
					)

			);
	}

	function hw_most_rated () {
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

		$mlq = new WP_Query(array(
			'post_type' => 'local_services',
			'orderby' => 'comment_count',
			'showposts' => $number
			)
			);
		if( $mlq->have_posts() ) :
		?>


		<?php while($mlq->have_posts()) : $mlq->the_post(); ?>


<h3><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
<?php include (TEMPLATEPATH . '/elements/comments-rating-average.php'); ?>


		<?php wp_reset_query();
		endwhile; ?>




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
		        $number = '5';
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

} // class hw_most_rated

?>
