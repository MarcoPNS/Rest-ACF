<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'REST_ACF_Options_Controller' ) ) {
	class REST_ACF_Options_Controller extends REST_ACF_Controller {
		public function __construct() {
			$this->type      = 'option';
			$this->rest_base = 'options';
			parent::__construct();
		}

		public function register_routes() {
			register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\w\-\_]+)/?(?P<field>[\w\-\_]+)?', array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
			) );
		}

	}
}
