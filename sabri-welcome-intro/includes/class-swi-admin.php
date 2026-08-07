<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Admin {
	/** @var SWI_Analytics */
	private $analytics;

	public function __construct( SWI_Analytics $analytics ) {
		$this->analytics = $analytics;
	}

	public function hooks() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_post_swi_save_settings', array( $this, 'save_settings' ) );
		add_action( 'admin_post_swi_reset_settings', array( $this, 'reset_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'plugin_action_links_' . SWI_BASENAME, array( $this, 'action_links' ) );
	}

	public function add_page() {
		add_options_page(
			__( 'Sabri Welcome Intro', 'sabri-welcome-intro' ),
			__( 'Sabri Welcome Intro', 'sabri-welcome-intro' ),
			swi_manage_capability(),
			'sabri-welcome-intro',
			array( $this, 'render_page' )
		);
	}

	public function enqueue_assets( $hook ) {
		if ( 'settings_page_sabri-welcome-intro' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'swi-admin', SWI_URL . 'assets/css/admin.css', array(), SWI_VERSION );
		wp_enqueue_script( 'swi-admin', SWI_URL . 'assets/js/admin.js', array(), SWI_VERSION, true );
		wp_localize_script( 'swi-admin', 'swiAdmin', array( 'confirmReset' => __( 'Restore the safe default Welcome Intro settings?', 'sabri-welcome-intro' ) ) );
	}

	public function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=sabri-welcome-intro' ) ) . '">' . esc_html__( 'Settings', 'sabri-welcome-intro' ) . '</a>' );
		return $links;
	}

	public function save_settings() {
		if ( ! swi_current_user_can_manage() ) {
			wp_die( esc_html__( 'You are not allowed to manage these settings.', 'sabri-welcome-intro' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'swi_save_settings' );
		$raw = array(
			'enabled'             => isset( $_POST['enabled'] ) ? 1 : 0,
			'frequency_days'      => isset( $_POST['frequency_days'] ) ? wp_unslash( $_POST['frequency_days'] ) : 30,
			'duration_ms'         => isset( $_POST['duration_ms'] ) ? wp_unslash( $_POST['duration_ms'] ) : 8000,
			'reduced_duration_ms' => isset( $_POST['reduced_duration_ms'] ) ? wp_unslash( $_POST['reduced_duration_ms'] ) : 900,
			'brand_name'          => isset( $_POST['brand_name'] ) ? wp_unslash( $_POST['brand_name'] ) : '',
			'brand_claim'         => isset( $_POST['brand_claim'] ) ? wp_unslash( $_POST['brand_claim'] ) : '',
			'brand_language'      => isset( $_POST['brand_language'] ) ? wp_unslash( $_POST['brand_language'] ) : 'en-US',
			'eligible_routes'     => isset( $_POST['eligible_routes'] ) ? wp_unslash( $_POST['eligible_routes'] ) : '/',
			'suppressed_prefixes' => isset( $_POST['suppressed_prefixes'] ) ? wp_unslash( $_POST['suppressed_prefixes'] ) : '',
			'starts_at'           => isset( $_POST['starts_at'] ) ? wp_unslash( $_POST['starts_at'] ) : '',
			'ends_at'             => isset( $_POST['ends_at'] ) ? wp_unslash( $_POST['ends_at'] ) : '',
			'analytics_enabled'   => isset( $_POST['analytics_enabled'] ) ? 1 : 0,
		);
		$expected = isset( $_POST['config_version'] ) ? absint( $_POST['config_version'] ) : 0;
		$result   = SWI_Config::save( $raw, $expected );
		if ( is_wp_error( $result ) ) {
			$this->redirect_notice( 'error', $result->get_error_code() );
		}
		$this->redirect_notice( 'success', 'saved' );
	}

	public function reset_settings() {
		if ( ! swi_current_user_can_manage() ) {
			wp_die( esc_html__( 'You are not allowed to manage these settings.', 'sabri-welcome-intro' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'swi_reset_settings' );
		$current = SWI_Config::get();
		$result  = SWI_Config::save( SWI_Config::defaults(), (int) $current['config_version'] );
		if ( is_wp_error( $result ) ) {
			$this->redirect_notice( 'error', $result->get_error_code() );
		}
		$this->redirect_notice( 'success', 'reset' );
	}

	private function redirect_notice( $type, $code ) {
		$url = add_query_arg( array( 'page' => 'sabri-welcome-intro', 'swi_notice' => sanitize_key( $type ), 'swi_code' => sanitize_key( $code ) ), admin_url( 'options-general.php' ) );
		wp_safe_redirect( $url );
		exit;
	}

	public function render_page() {
		if ( ! swi_current_user_can_manage() ) {
			return;
		}
		$config = SWI_Config::get();
		$check  = SWI_System_Check::snapshot();
		$audit  = array_slice( SWI_Config::audit_log(), 0, 20 );
		$notice = isset( $_GET['swi_notice'] ) ? sanitize_key( wp_unslash( $_GET['swi_notice'] ) ) : '';
		$code   = isset( $_GET['swi_code'] ) ? sanitize_key( wp_unslash( $_GET['swi_code'] ) ) : '';
		?>
		<div class="wrap swi-admin-wrap">
			<h1><?php esc_html_e( 'Sabri Welcome Intro', 'sabri-welcome-intro' ); ?></h1>
			<p><?php esc_html_e( 'File 13 controls the accessible welcome experience. It is hidden by default, never blocks the underlying page on failure, and is suppressed for at least 30 days after dismissal.', 'sabri-welcome-intro' ); ?></p>
			<?php if ( $notice ) : ?><div class="notice <?php echo 'success' === $notice ? 'notice-success' : 'notice-error'; ?> is-dismissible"><p><?php echo esc_html( 'success' === $notice ? __( 'Settings saved.', 'sabri-welcome-intro' ) : sprintf( __( 'Settings were not saved: %s', 'sabri-welcome-intro' ), $code ) ); ?></p></div><?php endif; ?>

			<div class="swi-admin-grid">
				<section class="swi-admin-card">
					<h2><?php esc_html_e( 'Configuration', 'sabri-welcome-intro' ); ?></h2>
					<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
						<input type="hidden" name="action" value="swi_save_settings">
						<input type="hidden" name="config_version" value="<?php echo esc_attr( $config['config_version'] ); ?>">
						<?php wp_nonce_field( 'swi_save_settings' ); ?>
						<table class="form-table" role="presentation">
							<tr><th scope="row"><?php esc_html_e( 'Enabled', 'sabri-welcome-intro' ); ?></th><td><label><input type="checkbox" name="enabled" value="1" <?php checked( 1, $config['enabled'] ); ?>> <?php esc_html_e( 'Enable on eligible routes', 'sabri-welcome-intro' ); ?></label></td></tr>
							<tr><th scope="row"><label for="swi-frequency"><?php esc_html_e( 'Frequency', 'sabri-welcome-intro' ); ?></label></th><td><input id="swi-frequency" class="small-text" type="number" min="30" max="365" name="frequency_days" value="<?php echo esc_attr( $config['frequency_days'] ); ?>"> <?php esc_html_e( 'days minimum', 'sabri-welcome-intro' ); ?></td></tr>
							<tr><th scope="row"><label for="swi-duration"><?php esc_html_e( 'Maximum duration', 'sabri-welcome-intro' ); ?></label></th><td><input id="swi-duration" class="small-text" type="number" min="1200" max="8000" step="100" name="duration_ms" value="<?php echo esc_attr( $config['duration_ms'] ); ?>"> ms</td></tr>
							<tr><th scope="row"><label for="swi-reduced"><?php esc_html_e( 'Reduced-motion duration', 'sabri-welcome-intro' ); ?></label></th><td><input id="swi-reduced" class="small-text" type="number" min="250" max="1500" step="50" name="reduced_duration_ms" value="<?php echo esc_attr( $config['reduced_duration_ms'] ); ?>"> ms</td></tr>
							<tr><th scope="row"><label for="swi-name"><?php esc_html_e( 'Brand name', 'sabri-welcome-intro' ); ?></label></th><td><input id="swi-name" class="regular-text" type="text" maxlength="120" name="brand_name" value="<?php echo esc_attr( $config['brand_name'] ); ?>"></td></tr>
							<tr><th scope="row"><label for="swi-claim"><?php esc_html_e( 'Brand claim', 'sabri-welcome-intro' ); ?></label></th><td><textarea id="swi-claim" class="large-text" rows="3" maxlength="280" name="brand_claim"><?php echo esc_textarea( $config['brand_claim'] ); ?></textarea></td></tr>
							<tr><th scope="row"><label for="swi-lang"><?php esc_html_e( 'Copy language', 'sabri-welcome-intro' ); ?></label></th><td><input id="swi-lang" class="regular-text" type="text" maxlength="35" name="brand_language" value="<?php echo esc_attr( $config['brand_language'] ); ?>"></td></tr>
							<tr><th scope="row"><label for="swi-eligible"><?php esc_html_e( 'Eligible routes', 'sabri-welcome-intro' ); ?></label></th><td><textarea id="swi-eligible" class="large-text code" rows="4" name="eligible_routes"><?php echo esc_textarea( implode( "\n", (array) $config['eligible_routes'] ) ); ?></textarea><p class="description"><?php esc_html_e( 'One same-origin path per line. Safe default is only /. Use * only after explicit review.', 'sabri-welcome-intro' ); ?></p></td></tr>
							<tr><th scope="row"><label for="swi-suppressed"><?php esc_html_e( 'Suppressed route prefixes', 'sabri-welcome-intro' ); ?></label></th><td><textarea id="swi-suppressed" class="large-text code" rows="8" name="suppressed_prefixes"><?php echo esc_textarea( implode( "\n", (array) $config['suppressed_prefixes'] ) ); ?></textarea></td></tr>
							<tr><th scope="row"><label for="swi-start"><?php esc_html_e( 'Start (optional)', 'sabri-welcome-intro' ); ?></label></th><td><input id="swi-start" class="regular-text" type="datetime-local" name="starts_at" value="<?php echo esc_attr( $this->datetime_local( $config['starts_at'] ) ); ?>"></td></tr>
							<tr><th scope="row"><label for="swi-end"><?php esc_html_e( 'End (optional)', 'sabri-welcome-intro' ); ?></label></th><td><input id="swi-end" class="regular-text" type="datetime-local" name="ends_at" value="<?php echo esc_attr( $this->datetime_local( $config['ends_at'] ) ); ?>"></td></tr>
							<tr><th scope="row"><?php esc_html_e( 'Aggregate analytics', 'sabri-welcome-intro' ); ?></th><td><label><input type="checkbox" name="analytics_enabled" value="1" <?php checked( 1, $config['analytics_enabled'] ); ?>> <?php esc_html_e( 'Count only shown/skipped/completed/error totals; no IP address, user agent or fingerprint.', 'sabri-welcome-intro' ); ?></label></td></tr>
						</table>
						<?php submit_button(); ?>
					</form>
					<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" data-swi-confirm-reset>
						<input type="hidden" name="action" value="swi_reset_settings"><?php wp_nonce_field( 'swi_reset_settings' ); ?>
						<?php submit_button( __( 'Restore Safe Defaults', 'sabri-welcome-intro' ), 'secondary', 'submit', false ); ?>
					</form>
				</section>

				<section class="swi-admin-card">
					<h2><?php esc_html_e( 'Preview states', 'sabri-welcome-intro' ); ?></h2>
					<p><?php esc_html_e( 'Preview is administrator-only, signed, noindex and does not alter public frequency state.', 'sabri-welcome-intro' ); ?></p>
					<div class="swi-preview-links">
					<?php foreach ( array( 'default' => __( 'Default', 'sabri-welcome-intro' ), 'reduced' => __( 'Reduced motion', 'sabri-welcome-intro' ), 'skipped' => __( 'Skipped', 'sabri-welcome-intro' ), 'disabled' => __( 'Disabled', 'sabri-welcome-intro' ), 'error' => __( 'Failure / fail-open', 'sabri-welcome-intro' ) ) as $state => $label ) :
						$url = wp_nonce_url( add_query_arg( array( 'swi_state' => $state ), home_url( '/welcome-intro-preview/' ) ), 'swi_preview' ); ?>
						<a class="button" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $label ); ?></a>
					<?php endforeach; ?>
					</div>
					<h2><?php esc_html_e( 'System check', 'sabri-welcome-intro' ); ?></h2>
					<table class="widefat striped"><tbody>
					<?php foreach ( $check as $key => $value ) : ?><tr><th><?php echo esc_html( $key ); ?></th><td><code><?php echo esc_html( is_array( $value ) ? wp_json_encode( $value ) : ( is_bool( $value ) ? ( $value ? 'true' : 'false' ) : (string) $value ) ); ?></code></td></tr><?php endforeach; ?>
					</tbody></table>
				</section>
			</div>

			<section class="swi-admin-card swi-admin-full">
				<h2><?php esc_html_e( 'Recent configuration audit', 'sabri-welcome-intro' ); ?></h2>
				<table class="widefat striped"><thead><tr><th><?php esc_html_e( 'Time (UTC)', 'sabri-welcome-intro' ); ?></th><th><?php esc_html_e( 'Event', 'sabri-welcome-intro' ); ?></th><th><?php esc_html_e( 'Version', 'sabri-welcome-intro' ); ?></th><th><?php esc_html_e( 'Changed keys', 'sabri-welcome-intro' ); ?></th></tr></thead><tbody>
				<?php if ( empty( $audit ) ) : ?><tr><td colspan="4"><?php esc_html_e( 'No audit records yet.', 'sabri-welcome-intro' ); ?></td></tr><?php else : foreach ( $audit as $row ) : ?><tr><td><?php echo esc_html( $row['occurred_at'] ?? '' ); ?></td><td><?php echo esc_html( $row['event'] ?? '' ); ?></td><td><?php echo esc_html( $row['version'] ?? 0 ); ?></td><td><?php echo esc_html( implode( ', ', (array) ( $row['changed_keys'] ?? array() ) ) ); ?></td></tr><?php endforeach; endif; ?>
				</tbody></table>
			</section>
		</div>
		<?php
	}

	private function datetime_local( $value ) {
		if ( ! $value ) {
			return '';
		}
		try {
			$date = new DateTimeImmutable( (string) $value );
			$timezone = function_exists( 'wp_timezone' ) ? wp_timezone() : new DateTimeZone( 'UTC' );
			return $date->setTimezone( $timezone )->format( 'Y-m-d\TH:i' );
		} catch ( Exception $exception ) {
			return '';
		}
	}
}
