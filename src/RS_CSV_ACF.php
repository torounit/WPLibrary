<?php

namespace Torounit\WPLibrary;


Class RS_CSV_ACF {

	public function __construct() {
		add_action( 'really_simple_csv_importer_post_saved', [ $this, 'really_simple_csv_importer_post_saved' ] );
	}



	/**
	 *
	 * ACF Taxonomy Field Converter.
	 * @param \WP_Post $post
	 *
	 */
	public function really_simple_csv_importer_post_saved( \WP_Post $post ) {

		foreach ( get_taxonomies() as $taxonomy ) {
			$terms = get_the_terms( $post->ID, $taxonomy );
			if ( is_array( $terms ) ) {
				foreach ( $terms as $term ) {
					update_post_meta( $post->ID, str_replace( '-', '_', $taxonomy ), $term->term_id );
				}
			}
		}
	}
}
