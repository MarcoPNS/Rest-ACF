<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="acf-to-rest-api-settings">
	<code><?php echo esc_url( home_url( 'wp-json/acf/' ) ); ?></code>
	<select name="REST_ACF_settings[request_version]">
		<option value="3"<?php selected( 4, $request_version ); ?>>v4</option>
	</select>
	<p><a href="<?php echo esc_url( self::$donation_url ); ?>" target="_blank"><?php esc_html_e( 'Click here', 'acf-to-rest-api' ); ?></a> <?php esc_html_e( 'to make a donation and help to improve the plugin.', 'acf-to-rest-api' ); ?></p>
</div>
