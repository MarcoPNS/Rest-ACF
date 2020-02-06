<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'REST_ACF_V3' ) ) {
	class REST_ACF_V3 {
		public static function includes() {
			require_once dirname( __FILE__ ) . '/lib/class-rest-api-acf-api.php';
			require_once dirname( __FILE__ ) . '/lib/class-rest-api-acf-field-settings.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-api-controller.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-api-posts-controller.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-api-terms-controller.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-api-comments-controller.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-api-attachments-controller.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-api-options-controller.php';
			require_once dirname( __FILE__ ) . '/lib/endpoints/class-rest-api-users-controller.php';
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
