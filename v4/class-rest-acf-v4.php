<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'REST_ACF_V4' ) ) {
	class REST_ACF_V4 {
		public static function includes() {
			require_once dirname( __FILE__ ) . '/lib/class-rest-acf-api.php';
			require_once dirname( __FILE__ ) . '/lib/class-rest-acf-field-settings.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-acf-controller.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-acf-posts-controller.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-acf-terms-controller.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-acf-comments-controller.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-acf-attachments-controller.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-acf-options-controller.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-acf-users-controller.php';
		}

		public static function create_rest_routes() {
			foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
				if ( 'attachment' == $post_type->name ) {
					$controller = new REST_ACF_Attachments_Controller( $post_type );
				} else {
					$controller = new REST_ACF_Posts_Controller( $post_type );
				}
				$controller->register();
			}

			foreach ( get_taxonomies( array( 'show_in_rest' => true ), 'objects' ) as $taxonomy ) {
				$controller = new REST_ACF_Terms_Controller( $taxonomy );
				$controller->register();
			}

			$controller = new REST_ACF_Comments_Controller;
			$controller->register();

			$controller = new REST_ACF_Options_Controller;
			$controller->register();

			$controller = new REST_ACF_Users_Controller;
			$controller->register();
		}

		public static function missing_notice() {
			if ( ! REST_ACF::is_plugin_active( 'rest-api' ) ) {
				include dirname( __FILE__ ) . '/../shared/includes/admin/views/html-notice-missing-rest-api.php';
			}

			if ( ! REST_ACF::is_plugin_active( 'acf' ) ) {
				include dirname( __FILE__ ) . '/../shared/includes/admin/views/html-notice-missing-acf.php';
			}
		}
	}
}
