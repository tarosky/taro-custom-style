<?php
/**
 * Output style
 */

add_action( 'wp_head', function() {
	if ( is_single() || is_page() || is_singular() ) {
		$post_type = get_queried_object()->post_type;
		if ( ! tcs_post_type_supported( $post_type ) ) {
			return;
		}
		$key = is_preview() ? '_css_preview' : '_css';
		$css = get_post_meta( get_queried_object_id(), $key, true );
		$id  = 'tcs-post-style-' . get_queried_object_id();
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( ! tcs_taxonomy_supported( $term->taxonomy ) ) {
			return;
		}
		$key = '_css';
		if ( is_preview() ) {
			$key .= '_preview';
		}
		$css = get_term_meta( $term->term_id, $key, true );
		$id  = 'tcs-taxonomy-style-' . get_queried_object_id();
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
