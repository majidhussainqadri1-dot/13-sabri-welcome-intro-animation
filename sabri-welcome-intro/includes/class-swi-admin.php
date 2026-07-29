<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Admin {
	public function hooks() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ) );
	}

	public function register_settings() {
		register_setting(
			'swi_settings',
			'swi_enabled',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => static function ( $value ) {
					return empty( $value ) ? 0 : 1;
				},
				'default'           => 1,
			)
		);
	}

	public function add_page() {
		add_options_page(
			__( 'Sabri Welcome Intro', 'sabri-welcome-intro' ),
			__( 'Sabri Welcome Intro', 'sabri-welcome-intro' ),
			'manage_options',
			'sabri-welcome-intro',
			array( $this, 'render_page' )
		);
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$preview_url = add_query_arg( 'swi_preview', '1', home_url( '/' ) );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Sabri Welcome Intro', 'sabri-welcome-intro' ); ?></h1>
			<p><?php esc_html_e( 'The welcome animation lasts eight seconds and appears once per browser session. It is shortened automatically for visitors who prefer reduced motion.', 'sabri-welcome-intro' ); ?></p>

			<form action="options.php" method="post">
				<?php settings_fields( 'swi_settings' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Welcome animation', 'sabri-welcome-intro' ); ?></th>
						<td>
							<input type="hidden" name="swi_enabled" value="0">
							<label>
								<input type="checkbox" name="swi_enabled" value="1" <?php checked( 1, (int) get_option( 'swi_enabled', 1 ) ); ?>>
								<?php esc_html_e( 'Enable the public welcome intro', 'sabri-welcome-intro' ); ?>
							</label>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>

			<p>
				<a class="button button-secondary" href="<?php echo esc_url( $preview_url ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'Preview Intro', 'sabri-welcome-intro' ); ?>
				</a>
			</p>
		</div>
		<?php
	}
}
