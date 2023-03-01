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

/**
 * Sanitize CSS
 *
 * @param string $css
 * @return string|WP_Error
 */
function tcs_sanitize_css( $css ) {
	// Strip tags. This will mal-affect to css sanitizer.
	//$css           = wp_kses( $css, [] );
	$sanitized_css = '';
	// Start parsing.
	$parser     = \Wikimedia\CSS\Parser\Parser::newFromString( $css );
	$stylesheet = $parser->parseStylesheet();

	// Check syntax error.
	$errors = new WP_Error();
	foreach ( $parser->getParseErrors() as list( $code, $line, $pos ) ) {
		// translators: %1$s is error code, %2$d is line number, %3$d is char position.
		$errors->add( 'css_parse_error', sprintf( __( 'CSS Parse Error: %1$s at line %2$d char %3$d', 'tcs' ), $code, $line, $pos ) );
	}
	if ( ! $errors->get_error_messages() ) {
		// Sanitize stylesheet.
		$sanitizer     = \Wikimedia\CSS\Sanitizer\StylesheetSanitizer::newDefault();
		$sanitized_css = $sanitizer->sanitize( $stylesheet );
		/** Report any sanitizer errors **/
		foreach ( $sanitizer->getSanitizationErrors() as list( $code, $line, $pos ) ) {
			// translators: %1$s is error code, %2$d is line number, %3$d is char position.
			$errors->add( 'css_sanitize_error', sprintf( __( 'CSS Sanitize Error: %1$s at line %2$d char %3$d', 'tcs' ), $code, $line, $pos ) );
		}
	}

	if ( $errors->get_error_messages() ) {
		return $errors;
	}

	// No error.
	return (string) $sanitized_css;
}

/**
 * Render style tag if no error.
 *
 * @param string $style          Style tag contents.
 * @param string $id             ID attribute for style tag.
 * @param bool   $skip_sanitizer If set true, skip sanitizer.
 *
 * @return void
 */
function tcs_display_style( $style, $id, $skip_sanitizer = false ) {
	if ( ! $skip_sanitizer ) {
		$style = tcs_sanitize_css( $style );
	}
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
