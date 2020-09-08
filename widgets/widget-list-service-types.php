<?php

// Register 'Recent Custom Posts' widget
add_action( 'widgets_init', 'init_hw_list_categories' );
function init_hw_list_categories() { return register_widget('hw_list_categories'); }

class hw_list_categories extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::WP_Widget(
			'hw_list_categories',
			$name = 'HW List Service Types',
			array(
					'classname'   => 'scaffold_widget_list_service_types widget_list_service_types',
					'description' => 'Navigation menu listing types of service'
					)


			);
	}

	function hw_list_categories () {
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

        // Output
		echo $before_widget;


	    if ( $title ) echo $before_title . $title . $after_title;

	get_search_form();


			// List LOCAL SERVICES with link to each
            $terms = get_terms( 'service_types' );
             if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                 echo '<ul id="local-services-selector" class="list-unstyled">';
                 foreach ( $terms as $term ) {

	$term_id = $term->term_taxonomy_id;
    $term_icon = get_term_meta( $term_id, 'icon', true );
                   echo '<li><a class="' . $term->slug . '" href="' . get_term_link( $term ) . '"><img class="alignright" width="35" height="35" src="' . $term_icon . '" alt="' . $term->name . '" />' . $term->name . '</a></li>';

                 }
                 echo '</ul>';
             }

		// echo widget closing tag
		echo $after_widget;
	}

	/** Widget control update */
	function update( $new_instance, $old_instance ) {
		$instance    = $old_instance;

		//Let's turn that array into something the Wordpress database can store
		$instance['title']  = strip_tags( $new_instance['title'] );
		return $instance;
	}

	/**
	* Widget settings
	**/
	function form( $instance ) {

		    // instance exist? if not set defaults
		    if ( $instance ) {
				$title  = $instance['title'];
		    } else {
			    //These are our defaults
				$title  = '';
		    }

			// The widget form
			?>
			<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __( 'Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat" />
			</p>

	<?php
	}

} // class hw_list_categories

?>
