<?php
/*
Plugin Name: Taro Custom Style
Plugin URI: https://wordpress.org/plugin/taro-custom-style
Description: Add custom css to each posts and taxonomies.
Author: TAROSKY INC.
Author URI: https://tarosky.co.jp
Text Domain: tcs
Domain Path: /languages/
License: GPL v3 or later.
Version: 1.1.11
PHP Version: 5.4.0
*/

add_action( 'plugins_loaded', 'taro_custom_style_init' );

/**
 * Bootstrap
 *
 * @package tcs
 * @since 1.0.0
 * @access private
 */
function taro_custom_style_init() {
	// i18n.
	load_plugin_textdomain( 'tcs', false, basename( dirname( __FILE__ ) ) . '/languages' );
	// Load composer if exists.
	require_once __DIR__ . '/vendor/autoload.php';
	// Load all components.
	foreach ( scandir( dirname( __FILE__ ) . '/includes' ) as $file ) {
		if ( preg_match( '#^[^._].*\.php$#u', $file ) ) {
			require dirname( __FILE__ ) . '/includes/' . $file;
		}
	}
}

/**
 * Get version number
 *
 * @package tcs
 * @since 1.0.0
 * @return string
 */
function taro_custom_style_version() {
	static $version = null;
	if ( is_null( $version ) ) {
		$info    = get_file_data( __FILE__, [
			'version' => 'Version',
		] );
		$version = $info['version'];
	}
	return $version;
}
