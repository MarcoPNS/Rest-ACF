<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="rest-acf-settings">
	<code><?php echo esc_url( home_url( 'wp-json/acf/' ) ); ?></code>
	<select name="REST_ACF_settings[request_version]">
		<option value="4"<?php selected( 4, $request_version ); ?>>v4</option>
	</select>
    <p>You are using Rest-ACF. If you want to access older API Endpoints then you need to download the `ACF to REST API` Plugin.</p>
</div>
