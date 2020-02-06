<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'REST_ACF_Comments_Controller' ) ) {
	class REST_ACF_Comments_Controller extends REST_ACF_Controller {
		public function __construct() {
			$this->type      = 'comment';
			$this->rest_base = 'comments';
			parent::__construct();
		}

		public function get_items( $request ) {
			$this->controller = new WP_REST_Comments_Controller;
			return parent::get_items( $request );
		}
	}
}
