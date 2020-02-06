<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_installed = REST_ACF::is_plugin_installed( 'rest-api' );

$target = false;
$action = __( 'Install', 'rest-acf' );
if ( current_user_can( 'install_plugins' ) ) {
	if ( $is_installed ) {
		$action = __( 'Active', 'rest-acf' );
		$url    = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=rest-api/plugin.php&plugin_status=active' ), 'activate-plugin_rest-api/plugin.php' );
	} else {
		$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=rest-api' ), 'install-plugin_rest-api' );
	}
} else {
	$target = true;
	$url    = 'http://wordpress.org/plugins/rest-api/';
}

?>

<div class="notice error is-dismissible">
	<p><strong><?php esc_html_e( 'ACF to REST API', 'act-to-rest-api' ); ?></strong> <?php esc_html_e( 'depends on the last version of WordPress REST API to work!', 'rest-acf' ); ?></p>
	<p><a href="<?php echo esc_url( $url ); ?>" class="button button-primary"<?php if ( $target ) : ?> target="_blank"<?php endif; ?>><?php esc_html_e( $action . ' WordPress REST API', 'rest-acf' ); ?></a></p>
</div>
