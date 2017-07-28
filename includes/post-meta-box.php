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
		tcs_enqueue_editor( 'tcs-css-editor', 'tcs-css' );
		?>
		<div id="tcs-css-editor" style="min-height: 300px;"></div>
		<textarea name="tcs-css" style="display: none;" id="tcs-css"><?= esc_textarea( get_post_meta( $post->ID, '_css', true ) ) ?></textarea>
		<p class="description">
			<?php esc_html_e( 'CSS will be displayed only on this page.', 'tcs' ) ?>
		</p>

		<?php
	}, $post_type, 'advanced', 'low' );
} );

/**
 * Save css
 */
add_action( 'save_post', function( $post_id, $post ) {
	if ( ! isset( $_POST['_tcsnonce'], $_POST['tcs-css'] ) || ! wp_verify_nonce( $_POST['_tcsnonce'], 'tcs_post_css' ) ) {
		return;
	}
	if ( wp_is_post_revision( $post ) && wp_is_post_autosave( $post ) ) {
		$key = '_css_preview';
		$post_id = $post->post_parent;
	} elseif ( tcs_post_type_supported( $post->post_type ) ) {
		$key = '_css';
	} else {
		return;
	}
	// Save CSS
	update_post_meta( $post_id, $key, $_POST['tcs-css'] );
}, 10, 2 );
