<?php
/**
 * Add term meta field to CSS
 */
add_action( 'admin_init', function() {

	foreach ( tcs_get_taxonomies() as $taxonomy ) {
		add_action( "{$taxonomy}_edit_form_fields", function( $term, $taxonomy ) {
			?>
			<tr>
				<th>
					<label for="tcs-css"><?php esc_html_e( 'Custom CSS', 'tcs' ); ?></label>
				</th>
				<td>
					<?php
						wp_nonce_field( 'tcs-term-css', '_tcsnonce', false );
						tcs_enqueue_editor( 'tcs-css' );
					?>

					<textarea name="tcs-css" id="tcs-css"><?php echo esc_textarea( get_term_meta( $term->term_id, '_css', true ) ); ?></textarea>

					<p>
						<button id="tcs-term-preview" data-term-id="<?php echo esc_attr( $term->term_id ); ?>" data-end-point="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>"><?php _e( 'Preview' ); ?></button>
					</p>

					<p class="description">
						<?php esc_html_e( 'This CSS will be applied to the taxonomy archive page.', 'tcs' ); ?>
					</p>

					<script>
						jQuery(document).ready(function($){
							$( '#tcs-term-preview' ).click( function ( e ) {
								e.preventDefault();
								var nonce = $( this ).parents( 'td' ).find( 'input[name=_tcsnonce]' ).val();
								var term_id = $( this ).attr( 'data-term-id' );
								$.post( $( this ).attr( 'data-end-point' ), {
									action: 'tcs_term_preview',
									term_id: term_id,
									_wpnonce: nonce,
									css: $( '#tcs-css' ).val(),
									taxonomy: '<?php echo esc_js( $term->taxonomy ); ?>'
								} ).done( function ( response ) {
									window.open( response.data.url, '_preview' );
								} ).fail( function ( response ) {
									window.alert( response.data.text );
								} );
							} );
						} );
					</script>
				</td>
			</tr>
			<?php
		}, 10, 2 );
	}
} );

/**
 * Save term
 */
add_action( 'edited_terms', function( $term_id, $taxonomy ) {
	if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_tcsnonce' ), 'tcs-term-css' ) ) {
		return;
	}
	if ( ! tcs_taxonomy_supported( $taxonomy ) ) {
		return;
	}
	$key = '_css';
	update_term_meta( $term_id, $key, filter_input( INPUT_POST, 'tcs-css' ) );
}, 10, 2 );

/**
 * Save preview and returns id
 */
add_action( 'wp_ajax_tcs_term_preview', function() {
	try {
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce' ), 'tcs-term-css' ) ) {
			throw new Exception( __( 'Wrong access.', 'tcs' ), 401 );
		}
		$term = get_term( filter_input( INPUT_POST, 'term_id' ), filter_input( INPUT_POST, 'taxonomy' ) );
		if ( ! $term || is_wp_error( $term ) || ! tcs_taxonomy_supported( $term->taxonomy ) || ! current_user_can( 'edit_term', $term->term_id ) ) {
			throw new Exception( __( 'You have no permission or Term is not allowed to customize.', 'tcs' ), 400 );
		}
		// Save as preview
		update_term_meta( $term->term_id, '_css_preview', $_POST['css'] );
		// Returns URL
		wp_send_json_success( [
			'url' => add_query_arg( [
				'preview' => 'true',
			], get_term_link( $term ) ),
		] );
	} catch ( Exception $e ) {
		wp_send_json_error( [
			'message' => $e->getMessage(),
		], $e->getCode() );
	}

} );

