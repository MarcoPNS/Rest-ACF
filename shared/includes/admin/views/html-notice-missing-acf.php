<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_installed = REST_ACF::is_plugin_installed( 'acf' );

$target = false;
$action = __( 'Install', 'rest-acf' );
if ( current_user_can( 'install_plugins' ) ) {
	if ( $is_installed ) {
		$action = __( 'Active', 'rest-acf' );
		$url    = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $is_installed . '&plugin_status=active' ), 'activate-plugin_' . $is_installed );
	} else {
		$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=advanced-custom-fields' ), 'install-plugin_advanced-custom-fields' );
	}
} else {
	$target = true;
	$url    = 'http://wordpress.org/plugins/advanced-custom-fields/';
}

?>

<div class="notice error is-dismissible">
	<p><strong><?php esc_html_e( 'ACF to REST API', 'act-to-rest-api' ); ?></strong> <?php esc_html_e( 'depends on the last version of Advanced Custom Fields to work!', 'rest-acf' ); ?></p>
	<p><a href="<?php echo esc_url( $url ); ?>" class="button button-primary"<?php if ( $target ) : ?> target="_blank"<?php endif; ?>><?php esc_html_e( $action . ' Advanced Custom Fields', 'rest-acf' ); ?></a></p>
</div>
