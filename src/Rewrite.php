<?php
namespace Torounit\WPLibrary;

Class Rewrite {

	/** @var Rewrite instance cache. */
	private static $instance;

	/** @var String */
	public static $file;

	/** @var String */
	public static $activated_option = 'my_plugin_activated';


	/**
	 * @return Rewrite Singleton::get_instance()
	 */
	public static function get_instance() {

		if ( empty( self::$file ) ) {
			throw new \LogicException( 'Rewrite::$file is not allowed empty.' );
		}

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Rewrite;
		}

		return self::$instance;
	}


	/** @var array */
	private $endpoints = [ ];

	private $rewrite_rules = [];


	private function __construct() {
		$this->init();
	}

	public function init() {

		add_action( 'delete_option', [ $this, 'delete_option' ], 10 );
		add_action( 'init', [ $this, 'add_rewrite' ], 9999 );

		register_activation_hook( self::$file, [ __CLASS__, 'activation' ] );
		register_deactivation_hook( self::$file, [ __CLASS__, 'deactivation' ] );

	}

	/**
	 * @param $name
	 * @param $places
	 * @param null $query_var
	 */
	public function add_endpoint( $name, $places, $query_var = null ) {

		$this->endpoints[] = [
			'name'      => $name,
			'places'    => $places,
			'query_var' => $query_var
		];
	}


	/**
	 * @param $regex
	 * @param $redirect
	 * @param string $after
	 */
	public function add_rewrite_rule( $regex, $redirect, $after = 'bottom' ) {

		$this->rewrite_rules[] = [
			'regex'      => $regex,
			'redirect'    => $redirect,
			'after' => $after
		];
	}



	public function add_rewrite() {
		array_walk( $this->endpoints, function ( $endpoint ) {
			add_rewrite_endpoint( $endpoint['name'], $endpoint['places'], $endpoint['query_var'] );
		} );

		array_walk( $this->rewrite_rules, function ( $rewrite_rule ) {
			add_rewrite_rule( $rewrite_rule['regex'], $rewrite_rule['redirect'], $rewrite_rule['after'] );
		} );

	}


	public function delete_option( $option ) {
		/*
		 * flush_rewrite_rules()が発火&プラグインが有効化されている場合に限りrewrite ruleを再登録
		 * register_activation_hook()発火時にはまだis_plugin_active()の戻り値はtrueのままなのでget_option()の値で評価する必要がある。
		 */
		if ( 'rewrite_rules' === $option && get_option( self::$activated_option ) ) {
			$this->add_rewrite();
		}
	}

	public static function activation() {
		update_option( self::$activated_option, true );
		flush_rewrite_rules();
	}

	public static function deactivation() {
		delete_option( self::$activated_option );
		flush_rewrite_rules();
	}

}