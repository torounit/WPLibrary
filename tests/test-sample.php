<?php
use Torounit\WPLibrary\Post_Type;
use Torounit\WPLibrary\Rewrite;
use Torounit\WPLibrary\Taxonomy;
use Torounit\WPLibrary\Walker_CategoryDropdown_Slug;


class SampleTest extends WP_UnitTestCase {
	function testSample() {

		new Post_Type('hoge','hoge');
		new Taxonomy('piyo', 'piyo');
		Rewrite::$file = __FILE__;
		Rewrite::get_instance();
		new Walker_CategoryDropdown_Slug();

		$this->assertTrue( true );
	}
}