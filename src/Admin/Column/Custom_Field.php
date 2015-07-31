<?php

namespace Torounit\WPLibrary\Admin\Column;

Class Custom_Field {

	/** @var string */
	private $field_key;

	/** @var string */
	private $field_label;

	/** @var string */
	private $post_type;

	/** @var bool */
	private $is_num;


	/**
	 * @param string $post_type
	 * @param string $field_key
	 * @param string $field_label
	 * @param int $position
	 * @param bool $is_num
	 */
	public function __construct( $post_type, $field_key, $field_label = '', $position = -1, $is_num = false ) {

		$this->post_type   = $post_type;
		$this->field_key   = $field_key;
		$this->field_label = $field_label;
		$this->position = $position;

		if ( ! $field_label ) {
			$this->field_label = $field_key;
		}

		$this->is_num = $is_num;

		$this->add_hooks();
	}


	/**
	 *
	 */
	protected function add_hooks() {

		add_filter( 'manage_posts_columns', array( $this, 'manage_posts_columns' ) );
		add_filter( 'manage_edit-' . $this->post_type . '_sortable_columns', array( $this, 'sortable_columns' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'manage_posts_custom_column' ) );
		add_action( 'load-edit.php', array( $this, 'load_edit_init' ) );
	}

	public function load_edit_init() {
		add_filter( 'request', array( $this, 'sort_edit' ) );
	}

	/**
	 * @param callable $callback
	 *
	 * @param int $priority
	 */
	public function add_filter( callable $callback, $priority = 10 ) {
		add_filter( 'admin_custom_column_' . $this->post_type . '_' . $this->field_key, $callback, $priority );
	}


	/**
	 * @param array $columns
	 *
	 * @return array
	 */
	public function manage_posts_columns( $columns ) {

		global $post;

		if ( $post->post_type == $this->post_type ) {

			$keys = array_keys( $columns );
			$values = array_values( $columns );
			array_splice( $keys, $this->position, 0, $this->field_key );
			array_splice( $values, $this->position, 0, $this->field_label );

			return array_combine( $keys, $values );

		}

		return $columns;
	}

	/**
	 * @param string $column_name
	 */
	public function manage_posts_custom_column( $column_name ) {
		global $post;

		if ( $post->post_type == $this->post_type ) {

			if ( $column_name == $this->field_key ) {
				$column = get_post_meta( $post->ID, $this->field_key, true );
				$column = apply_filters( 'admin_custom_column_' . $this->post_type . '_' . $this->field_key, $column );
				echo  $column;
			}

		}

	}

	/**
	 * @param array $columns
	 *
	 * @return array
	 */
	public function sortable_columns( $columns ) {
		$columns[ $this->field_key ] = $this->field_key;

		return $columns;
	}


	/**
	 * @param array $vars $wp_query->query_vars.
	 *
	 * @return array
	 */
	public function sort_edit( $vars ) {
		if ( isset( $vars['post_type'] ) && $this->post_type == $vars['post_type'] ) {

			if ( isset( $vars['orderby'] ) && $vars['orderby'] == $this->field_key ) {

				$vars = array_merge(
					$vars,
					array(
						'meta_key' => $this->field_key,
						'orderby'  => ( $this->is_num ) ? 'meta_value_num' : 'meta_value',
					)
				);
			}
		}

		return $vars;

	}
}