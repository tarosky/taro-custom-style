<?php
/**
 * Asset related hooks
 * 
 * @package tcs
 */

/**
 * Register action
 */
add_action( 'init', function() {
	$assets_dir = plugin_dir_url( __DIR__ ) . 'assets';
	wp_register_script( 'ace-editor', $assets_dir . '/lib/ace/ace.js', ['jquery'], '1.2.8', true );
} );

