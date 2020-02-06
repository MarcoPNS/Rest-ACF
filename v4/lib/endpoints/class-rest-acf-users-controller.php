<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'REST_ACF_Users_Controller' ) ) {
	class REST_ACF_Users_Controller extends REST_ACF_Controller {
		public function __construct() {
			$this->type      = 'user';
			$this->rest_base = 'users';
			parent::__construct();
		}

		public function get_items( $request ) {
			$this->controller = new WP_REST_Users_Controller;
			return parent::get_items( $request );
		}
	}
}
