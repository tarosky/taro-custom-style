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
	return false !== array_search( $post_type, tcs_get_post_types() );
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
 * Get taxonomies
 *
 * @param array $taxonomy
 *
 * @return bool
 */
function tcs_taxonomy_supported( $taxonomy ) {
	return false !== array_search( $taxonomy, tcs_get_taxonomies() );
}

/**
 * Enqueue CSS editor
 *
 * @param string $editor_id
 * @param string $textarea_id
 */
function tcs_enqueue_editor( $editor_id, $textarea_id ) {
	$editor_id = esc_js( $editor_id );
	$textarea_id = esc_js( $textarea_id );
	$js = <<<JS
		(function(){
			var editor = ace.edit("{$editor_id}");
    		editor.setTheme("ace/theme/xcode");
    		editor.getSession().setMode("ace/mode/css");
    		jQuery(document).ready(function($){
    			editor.getSession().setValue($('#{$textarea_id}').val());
				editor.getSession().on('change', function(){
					$('#{$textarea_id}').val(editor.getSession().getValue());
				});
    		});
		})();
JS;
	wp_add_inline_script( 'ace-editor', $js );
}
