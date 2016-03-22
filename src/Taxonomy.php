<?php

namespace Torounit\WPLibrary;

use Torounit\WPLibrary\Admin\Taxonomy_Filter;

Class Taxonomy {

	/** @var array  */
	private $post_type = array();

	/** @var string */
	private $taxonomy;

	/** @var string */
	private $taxonomy_name;

	/** @var array */
	private $args;

	/** @var array  */
	private $default_terms = array();


	/**
	 * @param string $taxonomy
	 * @param string $taxonomy_name
	 * @param array|string $post_type
	 * @param array $args
	 */
	public function __construct( $taxonomy, $taxonomy_name, $post_type = array( 'post' ), $args = array() ) {
		$this->taxonomy      = $taxonomy;
		$this->taxonomy_name = $taxonomy_name;
		if ( is_array( $post_type ) ) {
			$this->post_type = $post_type;
		} else {
			$this->post_type = array( $post_type );
		}

		$this->set_options( $args );

		$this->init();
	}


	/**
	 * @param array $args
	 */
	private function set_options( Array $args ) {
		$this->args = $this->create_options( $args );
	}


	/**
	 * add hooks.
	 */
	private function init() {

		add_action( 'init', array( $this, 'register' ), 10 );
		if( did_action('init') ) {
			$this->register();
		}

		add_action( 'wp_loaded', array( $this,'initialize_taxonomy'), 10 );

		if( !empty($this->args[ 'show_admin_column' ]) ) {
			new Admin\Taxonomy_Filter( $this->taxonomy, $this->post_type );
		}



	}


	/**
	 * @return array
	 */
	private function create_labels() {
		return array(
			'name'                => $this->taxonomy_name,
			'singular_name'       => $this->taxonomy_name,
			'search_items'        => $this->taxonomy_name . 'を検索',
			'popular_items'       => 'よく使う' . $this->taxonomy_name,
			'all_items'           => '全ての' . $this->taxonomy_name,
			'edit_item'           => $this->taxonomy_name . 'を編集',
			'update_item'         => $this->taxonomy_name . 'を更新',
			'add_new_item'        => $this->taxonomy_name . 'を追加',
			'new_item_name'       => '新しい' . $this->taxonomy_name,
			'add_or_remove_items' => $this->taxonomy_name . 'を追加もしくは削除',
			'menu_name'           => $this->taxonomy_name
		);
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	private function create_options( Array $args ) {
		$defaults = array(
			'labels'            => $this->create_labels(),
			'show_admin_column' => true,
			'rewrite'           => array( "with_front" => false ),
		);
		return array_merge( $defaults, $args );
	}


	/**
	 * register
	 */
	public function register() {
		register_taxonomy( $this->taxonomy, $this->post_type, $this->args );
	}


	/**
	 * add default terms.
	 */
	public function initialize_taxonomy() {
		$self = $this;
		if ( ! empty( $this->default_terms ) ) {
			array_walk( $this->default_terms, array( $this, 'set_default_term') );
		}
	}

	public function set_default_term( $term ) {
		if ( ! term_exists( $term["name"], $this->taxonomy ) ) {
			wp_insert_term( $term["name"], $this->taxonomy, $term );
		}
	}

	/**
	 * @param string $name
	 * @param string $slug
	 * @param array $args
	 */
	public function add_term( $name, $slug = '', $args = array() ) {
		if ( ! $slug ) {
			$slug = $name;
		}
		$term                  = array_merge( array( 'name' => $name, 'slug' => $slug ), $args );
		$this->default_terms[] = $term;
	}

	public function add_terms( Array $terms ) {

		foreach( $terms as $term ) {
			if( is_string( $term ) ) {
				$this->add_term( $term );
			}
			else {
				$this->add_term( $term['name'], $term['slug'], $term['args'] );
			}
		}
	}


	/**
	 * @return string
	 */
	public function get_taxonomy() {
		return $this->taxonomy;
	}





}













