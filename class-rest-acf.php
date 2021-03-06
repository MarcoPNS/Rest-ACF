<?php
/**
 * Plugin Name: Rest ACF
 * Description: Rest ACF is a updated version of acf-to-res-api by Aires Gonçalves. Because the old git got his last update a couple of years ago, I decided to fork it and start a new one.
 * Author: Marco Sadowski
 * Author URI: https://github.com/MarcoPNS
 * Version: 4.0.0
 * Plugin URI: https://github.com/MarcoPNS/Rest-ACF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'REST_ACF' ) ) {

	class REST_ACF {

		const VERSION = '4.0.0';
		private static $default_request_version = 4;
		private static $request_version;

		private static $instance = null;

		public static function init() {
			self::includes();
			self::hooks();
		}

		protected static function instance() {
			if ( is_null( self::$instance ) ) {
				$class = 'REST_ACF_V' . self::handle_request_version();
				if ( class_exists( $class ) ) {
					self::$instance = new $class;
				}
			}
			return self::$instance;
		}

		private static function includes() {
            require_once dirname( __FILE__ ) . '/v4/class-rest-acf-v4.php';

			if ( self::is_plugin_active( 'all' ) ) {
				if ( is_admin() ) {
					require_once dirname( __FILE__ ) . '/shared/lib/class-rest-acf-settings.php';
				}
			}
            self::instance()->includes();
		}

		public static function handle_request_version() {
			if ( is_null( self::$request_version ) ) {
				if ( defined( 'REST_ACF_REQUEST_VERSION' ) ) {
					self::$request_version = (int) REST_ACF_REQUEST_VERSION;
				} else {
					self::$request_version = (int) get_option( 'REST_ACF_request_version', self::$default_request_version );
				}
			}
			return self::$request_version;
		}

		private static function hooks() {
			add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ) );

			if ( self::is_plugin_active( 'all' ) ) {
				add_action( 'rest_api_init', array( __CLASS__, 'create_rest_routes' ), 10 );
				if ( self::$default_request_version == self::handle_request_version() ) {
					REST_ACF_Field_Settings::hooks();
				}
			} else {
				add_action( 'admin_notices', array( __CLASS__, 'missing_notice' ) );
			}

		}

		public static function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'rest-acf' );
			load_textdomain( 'rest-acf', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/languages/' . $locale . '.mo' );
		}

		public static function create_rest_routes() {
			self::instance()->create_rest_routes();
		}

		public static function is_plugin_active( $plugin ) {
			if ( 'rest-api' == $plugin ) {
				return class_exists( 'WP_REST_Controller' );
			} elseif ( 'acf' == $plugin ) {
				return class_exists( 'acf' );
			} elseif ( 'all' == $plugin ) {
				return class_exists( 'WP_REST_Controller' ) && class_exists( 'acf' );
			}

			return false;
		}

		public static function is_plugin_installed( $plugin ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$paths = false;
			if ( 'rest-api' == $plugin ) {
				$paths = array( 'rest-api/plugin.php' );
			} elseif ( 'acf' == $plugin ) {
				$paths = array( 'advanced-custom-fields-pro/acf.php', 'acf-pro/acf.php', 'advanced-custom-fields/acf.php' );
			}

			if ( $paths ) {
				$plugins = get_plugins();
				if ( is_array( $plugins ) && count( $plugins ) > 0 ) {
					foreach ( $paths as $path ) {
						if ( isset( $plugins[ $path ] ) && ! empty( $plugins[ $path ] ) ) {
							return $path;
						}
					}
				}
			}

			return false;
		}

		public static function missing_notice() {
			self::instance()->missing_notice();
		}
	}

	add_action( 'plugins_loaded', array( 'REST_ACF', 'init' ) );

}
