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
 * Get object to display style in current page.
 *
 * @return WP_Term|WP_Post|null
 */
function tcs_style_for_object() {
	$object = null;
	if ( is_single() || is_page() || is_singular() ) {
		$post_type = get_queried_object()->post_type;
		if ( tcs_post_type_supported( $post_type ) ) {
			$object = get_queried_object();
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( ! tcs_taxonomy_supported( $term->taxonomy ) ) {
			$object = $term;
		}
	}
	return apply_filters( 'tcs_object_for_style', $object );
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

/**
 * Sanitize CSS
 *
 * @deprecated
 * @param string $css
 * @return string
 */
function tcs_sanitize_css( $css ) {
	// Remove script tag.
	$css = preg_replace( '/<script[^>]*?>/u', '', $css );
	$css = str_replace( '</script>', '', $css );
	return $css;
}

/**
 * Render style tag if no error.
 *
 * @param string|WP_Error $style      Style tag contents.
 * @param string          $id         ID attribute for style tag.
 * @param bool            $deprecated Deprecated option.
 *
 * @return void
 */
function tcs_display_style( $style, $id, $deprecated = false ) {
	if ( is_wp_error( $style ) ) {
		// No style tag output.
		// Display error messages as HTML comment and quit.
		echo '<!-- Taro Custom Style Error: ' . esc_html( $id ) . "\n";
		foreach ( $style->get_error_messages() as $message ) {
			echo esc_html( $message ) . "\n";
		}
		echo '-->';
		return;
	}
	?>
	<style id="<?php echo esc_attr( $id ); ?>">
		<?php
		// Output sanitized style.
		echo $style;
		?>
	</style>

	<?php
}

/**
 * Get style group to render.
 *
 * @return WP_Term[]
 */
function tcs_get_style_group() {
	$styles     = [];
	$post_types = tcs_get_post_types();
	if ( $post_types && is_singular( $post_types ) ) {
		$terms = get_the_terms( get_queried_object(), 'style-group' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$styles = $terms;
		}
	}
	return apply_filters( 'tcs_style_groups', $styles );
}
