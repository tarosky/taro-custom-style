<?php
/**
 * Add setting API
 *
 * @package tcs
 */

/**
 * Add menu's sub page
 */
add_action( 'admin_menu', function() {

	$title = __( 'Taro Custom Style Setting', 'tcs' );
	add_theme_page( $title, __( 'Custom CSS', 'tcs' ), 'manage_options', 'tcs-setting', function() use ( $title ) {
		?>
		<div class="wrap">
			<h2><?php echo esc_html( $title ); ?></h2>
			<form action="<?php echo admin_url( 'options.php' ); ?>" method="post">
				<?php settings_fields( 'tsc-setting' ); ?>
				<?php do_settings_sections( 'tsc-setting' ); ?>
				<?php submit_button( __( 'Update' ) ); ?>
			</form>
		</div>
		<?php
	} );

} );

/**
 * Register settings.
 */
add_action( 'admin_init', function() {

	// Add section
	add_settings_section(
		'tcs_each_section',
		__( 'Customizable Objects', 'tcs' ),
		function() {
			printf( '<p class="description">%s</p>', esc_html__( 'Specified post types or taxonomies have custom css fields.', 'tcs' ) );
		},
		'tsc-setting'
	);

	// Field1: Post type
	add_settings_field(
		'tcs_post_types',
		__( 'Post Types', 'tcs' ),
		function() {
			$post_types = get_post_types( [
				'public' => true,
			], OBJECT );
			foreach ( $post_types as $post_type ) {
				?>
				<label style="display: inline-block; margin-right: 1em;">
					<input type="checkbox" name="tcs_post_types[]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( tcs_post_type_supported( $post_type->name ) ); ?> />
					<?php echo esc_html( $post_type->label ); ?>
				</label>
				<?php
			}
		},
		'tsc-setting',
		'tcs_each_section'
	);

	// Field2: Taxonomies
	add_settings_field(
		'tcs_taxonomies',
		__( 'Taxonomies', 'tcs' ),
		function() {
			$taxonomies = get_taxonomies( [
				'public' => true,
			], OBJECT );
			foreach ( $taxonomies as $taxonomy ) {
				?>
				<label style="display: inline-block; margin-right: 1em;">
					<input type="checkbox" name="tcs_taxonomies[]" value="<?php echo esc_attr( $taxonomy->name ); ?>" <?php checked( tcs_taxonomy_supported( $taxonomy->name ) ); ?> />
					<?php echo esc_html( $taxonomy->label ); ?>
				</label>
				<?php
			}
		},
		'tsc-setting',
		'tcs_each_section'
	);

	// Register as Setting
	register_setting( 'tsc-setting', 'tcs_post_types' );
	register_setting( 'tsc-setting', 'tcs_taxonomies' );

} );

