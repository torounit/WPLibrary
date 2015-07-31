<?php

namespace Torounit\WPLibrary\Admin;

use Torounit\WPLibrary\Walker_CategoryDropdown_Slug;


Class Taxonomy_Filter {

	/** @var Array|string */
	private $post_type;

	/** @var string */
	private $taxonomy;

	public function __construct( $taxonomy, $post_type ) {

		$this->taxonomy  = $taxonomy;
		if( is_array( $post_type ) ) {
			$this->post_type = $post_type;
		}
		else {
			$this->post_type = array( $post_type );
		}

		add_action( 'restrict_manage_posts', array( $this, 'add_post_taxonomy_restrict_filter' ) );
	}

	public function add_post_taxonomy_restrict_filter() {

		global $post_type;

		if ( in_array( $post_type, $this->post_type ) ) {
			$dropdown_options = array(
				'show_option_all' => __( 'All categories' ),
				'hide_empty'      => 0,
				'hierarchical'    => 1,
				'name'            => $this->taxonomy,
				'show_count'      => 0,
				'orderby'         => 'name',
				'taxonomy'        => $this->taxonomy,
				'selected'        => get_query_var( $this->taxonomy ),
				'walker'          => new Walker_CategoryDropdown_Slug()
			);

			echo '<label class="screen-reader-text" for="' . $this->taxonomy . '">' . __( 'Filter by category' ) . '</label>';
			wp_dropdown_categories( $dropdown_options );

		}
	}

}