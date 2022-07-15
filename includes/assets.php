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
} );

