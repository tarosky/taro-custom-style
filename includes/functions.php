<?php
/**
 * Utility functions for Taro Custom Style
 *
 * @package tcs
 */

/**
 * Get post types
 *
 * @return array
 */
function tcs_get_post_types() {
	return array_filter( (array) get_option( 'tcs_post_types', [] ), function ( $post_type ) {
		return $post_type && post_type_exists( $post_type );
	} );
}

/**
 * Detect if post type is supported
 *
 * @param string $post_type
 *
 * @return bool
 */
function tcs_post_type_supported( $post_type ) {
	return in_array( $post_type, tcs_get_post_types(), true );
}

/**
 * Get taxonomies
 *
 * @return array
 */
function tcs_get_taxonomies() {
	return array_filter( (array) get_option( 'tcs_taxonomies', [] ), function ( $taxonomy ) {
		return $taxonomy && taxonomy_exists( $taxonomy );
	} );
}

/**
 * Is taxonomy supported?
 *
 * @param string $taxonomy
 *
 * @return bool
 */
function tcs_taxonomy_supported( $taxonomy ) {
	return in_array( $taxonomy, tcs_get_taxonomies(), true );
}

/**
 * Enqueue CSS editor
 *
 * @param string $textarea_id Text area.
 */
function tcs_enqueue_editor( $textarea_id, $in_block_editor = false ) {
	// Enqueue code editor and settings for manipulating HTML.
	$settings = wp_enqueue_code_editor( [
		'type'       => 'text/css',
		'codemirror' => [
			'autoRefresh' => true,
		],
	] );

	// Return if the editor was not enqueued.
	if ( false === $settings ) {
		return;
	}

	// Convert settings to JSON.
	$textarea_id  = esc_js( $textarea_id );
	$block_editor = $in_block_editor ? 'block-editor' : '';
	$json         = wp_json_encode( $settings );
	$js           = <<<JS
jQuery( function() {
	var cm = wp.codeEditor.initialize( '{$textarea_id}', {$json} );
	if ( 'block-editor' === '{$block_editor}' ) {
		cm.codemirror.on( 'change', function( args ) {
			cm.codemirror.save();
		} );
	}
} );
JS;

	wp_add_inline_script( 'code-editor', $js );
}
