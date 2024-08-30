<?php
/**
 * Metabox
 *
 * @package tcs
 */

/**
 * Add meta box
 */
add_action( 'add_meta_boxes', function( $post_type ) {
	if ( ! tcs_post_type_supported( $post_type ) ) {
		return;
	}
	add_meta_box( 'tcs-style-editor', __( 'Custom CSS', 'tcs' ), function( $post ) {
		wp_nonce_field( 'tcs_post_css', '_tcsnonce', false );
		tcs_enqueue_editor( 'tcs-css', get_current_screen()->is_block_editor() );
		?>
		<textarea name="tcs-css" id="tcs-css"><?php echo esc_textarea( get_post_meta( $post->ID, '_css', true ) ); ?></textarea>
		<p class="description">
			<?php esc_html_e( 'CSS will be displayed only on this page.', 'tcs' ); ?>
		</p>

		<?php
	}, $post_type, 'advanced', 'low' );
} );

/**
 * Save css
 */
add_action( 'save_post', function( $post_id, $post, $update ) {
	if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_tcsnonce' ), 'tcs_post_css' ) ) {
		return;
	}

	if ( wp_is_post_revision( $post ) && wp_is_post_autosave( $post ) ) {
		// Post already published, when viewing update preview.
		$keys    = [ '_css_preview' ];
		$post_id = $post->post_parent;
	} elseif ( tcs_post_type_supported( $post->post_type ) ) {
		// Post not yet published
		$keys = [ '_css_preview', '_css' ];
		//}
	} else {
		// Do nothing.
		return;
	}

	// Save CSS
	foreach ( $keys as $key ) {
		update_post_meta( $post_id, $key, filter_input( INPUT_POST, 'tcs-css' ) );
	}
}, 10, 3 );
