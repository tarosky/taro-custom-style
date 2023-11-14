<?php
/**
 * Output style
 */
add_action( 'wp_head', function() {
	$object = tcs_style_for_object();
	$key    = is_preview() ? '_css_preview' : '_css';
	if ( is_a( $object, 'WP_Post' ) ) {
		$css = get_post_meta( $object->ID, $key, true );
		$id  = 'tcs-post-style-' . $object->ID;
	} elseif ( is_a( $object, 'WP_Term' ) ) {
		$css = get_term_meta( $object->term_id, $key, true );
		$id  = 'tcs-taxonomy-style-' . $object->term_id;
	} else {
		return;
	}
	/**
	 * tcs_css_output
	 *
	 * @param string $css Stylesheet.
	 * @param WP_Term|WP_Post $queried_object Current page's object. TErm or Post.
	 */
	$css = apply_filters( 'tcs_css_output', $css, get_queried_object() );
	if ( empty( $css ) ) {
		// No CSS.
		return;
	}
	tcs_display_style( $css, $id );
}, 100 );
