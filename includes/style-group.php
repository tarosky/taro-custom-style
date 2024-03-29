<?php
/**
 * Style groupds.
 *
 *
 */

/**
 * Register style group.
 */
add_action( 'init', function() {
	$post_types = tcs_get_post_types();
	if ( empty( $post_types ) ) {
		return;
	}
	register_taxonomy( 'style-group', $post_types, [
		'public'             => false,
		'show_ui'            => true,
		'label'              => __( 'Style Group', 'tcs' ),
		'hierarchical'       => true,
		'show_in_rest'       => true,
		'show_admin_column'  => true,
		'show_in_nav_menus'  => false,
		'show_tagcloud'      => false,
		'show_in_quick_edit' => false,
		'labels'             => [
			'parent_field_description' => esc_html__( 'Parent styles will be loaded with this style.', 'tcs' ),
		],
	] );
} );

/**
 * Render supported styles.
 */
add_action( 'style-group_term_edit_form_top', function( $tag, $taxonomy ) {
	?>
	<style>
		.term-description-wrap {
			display: none;
		}
		.style-editor-wrapper {
			margin: 40px 0;
		}
	</style>
	<?php
}, 10, 2 );

/**
 * Save stylesheet.
 */
add_action( 'edit_terms', function( $term_id, $taxonomy ) {
	if ( 'style-group' !== $taxonomy ) {
		return;
	}
	if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_tcsnonce' ), 'update_style' ) ) {
		return;
	}
	update_term_meta( $term_id, 'tcs_style', filter_input( INPUT_POST, 'tcs_style' ) );
}, 10, 2 );

/**
 * Render editor.
 */
add_action( 'style-group_edit_form', function( $tag, $taxonomy ) {
	// Enqueue editor.
	tcs_enqueue_editor( 'tcs-editor' );
	// Nonce.
	wp_nonce_field( 'update_style', '_tcsnonce', false );
	// CSS.
	$css = get_term_meta( $tag->term_id, 'tcs_style', true );
	?>
	<div class="style-editor-wrapper">
		<textarea class="style-editor" id="tcs-editor" name="tcs_style"><?php echo esc_textarea( $css ); ?></textarea>
	</div>
	<?php
}, 10, 2 );

/**
 * Render styles.
 */
add_action( 'wp_head', function() {
	$styles     = tcs_get_style_group();
	$all_styles = [];
	foreach ( $styles as $style ) {
		$ancestors = get_ancestors( $style->term_id, $style->taxonomy, 'taxonomy' );
		if ( $ancestors ) {
			for ( $i = count( $ancestors ); 0 < $i; $i-- ) {
				$term_id                = $ancestors[ $i - 1 ];
				$all_styles[ $term_id ] = get_term( $term_id, 'style-group' );
			}
		}
		$all_styles[ $style->term_id ] = $style;
	}
	foreach ( $all_styles as $term_id => $style ) {
		$style = get_term_meta( $style->term_id, 'tcs_style', true );
		if ( ! $style ) {
			continue;
		}
		$skip_sanitizer = (bool) get_term_meta( $term_id, '_ignore_sanitizer', true );
		tcs_display_style( $style, 'tcs-style-group-' . $term_id, $skip_sanitizer );
	}
}, 9999 );
