<?php

namespace Torounit\WPLibrary;


Class Post_Type {

	/** @var string */
	private $post_type;

	/** @var string */
	private $post_type_name;


	/** @var array */
	private $args;

	/**
	 * @param string $post_type
	 * @param string $post_type_name
	 * @param array $args
	 */
	public function __construct( $post_type, $post_type_name, $args = [ ]  ) {
		$this->post_type      = $post_type;
		$this->post_type_name = $post_type_name;
		$this->set_options( $args );

		$this->init();
	}

	/**
	 * add hooks.
	 */
	public function init() {

		add_action( 'init', [ $this, 'register_post_type' ], 10 );
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ] );
	}

	/**
	 * @return string
	 */
	public function get_post_type() {
		return $this->post_type;
	}

	/**
	 * Set Option.
	 *
	 * @param $args
	 */
	public function set_options( $args ) {
		$this->args = $this->create_options( $args );
	}


	/**
	 * Create Labels.
	 * @return array
	 */
	public function create_labels() {
		return array(
			'name'               => $this->post_type_name,
			'singular_name'      => $this->post_type_name,
			'add_new'            => '新規追加',
			'add_new_item'       => $this->post_type_name . 'を追加',
			'edit_item'          => $this->post_type_name . 'を編集',
			'new_item'           => '新しい' . $this->post_type_name,
			'view_item'          => $this->post_type_name . 'を表示',
			'search_items'       => $this->post_type_name . 'を検索',
			'not_found'          => $this->post_type_name . 'が見つかりませんでした。',
			'not_found_in_trash' => 'ゴミ箱の中から、' . $this->post_type_name . 'が見つかりませんでした。',
			'menu_name'          => $this->post_type_name
		);
	}

	/**
	 *
	 * Create Options.
	 *
	 * @param $args
	 *
	 * @return array
	 */
	public function create_options( $args ) {
		$defaults = array(
			'labels'            => $this->create_labels(),
			'public'            => true,
			'show_ui'           => true,
			'show_in_admin_bar' => true,
			'menu_position'     => null,
			'show_in_nav_menus' => true,
			'has_archive'       => true,
			'rewrite'           => [
				'with_front' => false,
				'slug'       => $this->post_type,
				'walk_dirs'  => false
			],
			'supports'          => array(
				'title',
				'author',
				"editor",
				'excerpt',
				'revisions',
			)
		);


		if ( empty( $args['rewrite']['walk_dirs'] ) ) {
			$args['rewrite']['walk_dirs'] = false;
		}

		return array_merge( $defaults, $args );
	}

	public function register_post_type() {
		register_post_type( $this->post_type, $this->args );
	}


	/**
	 *
	 * Default order to menu_order in admin.
	 *
	 * @param \WP_Query $query
	 *
	 */
	public function pre_get_posts( \WP_Query $query ) {

		if ( $query->is_main_query() and is_admin() ) {
			if ( $query->get( 'post_type' ) == $this->get_post_type() ) {


				if ( post_type_supports( $this->get_post_type(), 'page-attributes' ) ) {

					if ( empty( $query->query['order'] ) ) {
						$query->set( 'order', 'ASC' );
					}

					if ( empty( $query->query['orderby'] ) ) {
						$query->set( 'orderby', 'menu_order' );
					}
				}
			}
		}
	}


}










