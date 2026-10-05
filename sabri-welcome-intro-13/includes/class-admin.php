<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Admin {
	const PAGE = 'sabri-welcome-intro';
	const SAVE_ACTION = 'swi_save_intro_config';

	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_post_' . self::SAVE_ACTION, array( __CLASS__, 'save' ) );
	}

	public static function menu() {
		add_options_page( __( 'Sabri Welcome Intro', SWI_TEXT_DOMAIN ), __( 'Sabri Welcome Intro', SWI_TEXT_DOMAIN ), 'manage_options', self::PAGE, array( __CLASS__, 'render' ) );
	}

	public static function save() {
		Authorization::verify_post( self::SAVE_ACTION );
		$config = Settings::get();
		$input = array(
			'enabled' => isset( $_POST['enabled'] ) ? '1' : '0',
			'status' => isset( $_POST['status'] ) ? wp_unslash( $_POST['status'] ) : 'disabled',
			'heading' => isset( $_POST['heading'] ) ? wp_unslash( $_POST['heading'] ) : '',
			'claim' => isset( $_POST['claim'] ) ? wp_unslash( $_POST['claim'] ) : '',
			'duration_ms' => isset( $_POST['duration_ms'] ) ? wp_unslash( $_POST['duration_ms'] ) : $config['duration_ms'],
			'recurrence_days' => isset( $_POST['recurrence_days'] ) ? wp_unslash( $_POST['recurrence_days'] ) : $config['recurrence_days'],
			'eligible_paths' => isset( $_POST['eligible_paths'] ) ? wp_unslash( $_POST['eligible_paths'] ) : '/',
			'analytics_enabled' => isset( $_POST['analytics_enabled'] ) ? '1' : '0',
			'start_at' => isset( $_POST['start_at'] ) ? wp_unslash( $_POST['start_at'] ) : '',
			'end_at' => isset( $_POST['end_at'] ) ? wp_unslash( $_POST['end_at'] ) : '',
		);
		$expected = isset( $_POST['expected_revision'] ) ? absint( $_POST['expected_revision'] ) : 0;
		$result = Settings::update( $input, $expected, get_current_user_id() );
		$args = array( 'page' => self::PAGE );
		if ( is_wp_error( $result ) ) { $args['swi_error'] = $result->get_error_code(); } else { $args['swi_saved'] = 1; }
		wp_safe_redirect( add_query_arg( $args, admin_url( 'options-general.php' ) ) );
		exit;
	}

	public static function render() {
		Authorization::require_manage( 'manage_intro' );
		$config = Settings::get();
		$health = Health::status();
		$preview = home_url( '/welcome-intro-preview/' );
		echo '<div class="wrap"><h1>' . esc_html__( 'Sabri Welcome Intro', SWI_TEXT_DOMAIN ) . '</h1>';
		if ( isset( $_GET['swi_saved'] ) ) { echo '<div class="notice notice-success"><p>' . esc_html__( 'Configuration saved.', SWI_TEXT_DOMAIN ) . '</p></div>'; }
		if ( isset( $_GET['swi_error'] ) ) { echo '<div class="notice notice-error"><p>' . esc_html__( 'Configuration was not saved. Reload and try again.', SWI_TEXT_DOMAIN ) . '</p></div>'; }
		echo '<p><strong>' . esc_html__( 'Health:', SWI_TEXT_DOMAIN ) . '</strong> ' . esc_html( $health['status'] ) . ' — File 20 hook: ' . esc_html( $health['file20_hook_present'] ? 'present' : 'missing' ) . '</p>';
		echo '<p><a class="button" href="' . esc_url( $preview ) . '" target="_blank" rel="noopener">' . esc_html__( 'Preview', SWI_TEXT_DOMAIN ) . '</a> ';
		echo '<a class="button" href="' . esc_url( add_query_arg( 'state', 'reduced', $preview ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Reduced-motion preview', SWI_TEXT_DOMAIN ) . '</a> ';
		echo '<a class="button" href="' . esc_url( add_query_arg( 'state', 'error', $preview ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Failure preview', SWI_TEXT_DOMAIN ) . '</a></p>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="' . esc_attr( self::SAVE_ACTION ) . '"><input type="hidden" name="expected_revision" value="' . esc_attr( absint( $config['config_version'] ) ) . '">';
		wp_nonce_field( self::SAVE_ACTION, '_swi_nonce' );
		echo '<table class="form-table" role="presentation">';
		self::checkbox_row( 'enabled', __( 'Enabled', SWI_TEXT_DOMAIN ), ! empty( $config['enabled'] ) );
		self::text_row( 'heading', __( 'Heading', SWI_TEXT_DOMAIN ), $config['heading'] );
		self::textarea_row( 'claim', __( 'Brand claim', SWI_TEXT_DOMAIN ), $config['claim'] );
		self::number_row( 'duration_ms', __( 'Display duration (ms)', SWI_TEXT_DOMAIN ), $config['duration_ms'], 800, 12000 );
		self::number_row( 'recurrence_days', __( 'Recurrence after dismissal (days)', SWI_TEXT_DOMAIN ), $config['recurrence_days'], 30, 365 );
		self::textarea_row( 'eligible_paths', __( 'Eligible paths', SWI_TEXT_DOMAIN ), implode( "\n", (array) $config['eligible_paths'] ) );
		self::checkbox_row( 'analytics_enabled', __( 'Aggregate analytics', SWI_TEXT_DOMAIN ), ! empty( $config['analytics_enabled'] ) );
		self::text_row( 'start_at', __( 'Start (optional)', SWI_TEXT_DOMAIN ), $config['start_at'] );
		self::text_row( 'end_at', __( 'End (optional)', SWI_TEXT_DOMAIN ), $config['end_at'] );
		echo '<tr><th scope="row">' . esc_html__( 'Status', SWI_TEXT_DOMAIN ) . '</th><td><select name="status"><option value="active"' . selected( $config['status'], 'active', false ) . '>' . esc_html__( 'Active', SWI_TEXT_DOMAIN ) . '</option><option value="disabled"' . selected( $config['status'], 'disabled', false ) . '>' . esc_html__( 'Disabled', SWI_TEXT_DOMAIN ) . '</option></select></td></tr></table>';
		submit_button();
		echo '</form></div>';
	}

	private static function checkbox_row( $name, $label, $checked ) {
		echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td><label><input type="checkbox" name="' . esc_attr( $name ) . '" value="1" ' . checked( $checked, true, false ) . '> ' . esc_html__( 'Yes', SWI_TEXT_DOMAIN ) . '</label></td></tr>';
	}
	private static function text_row( $name, $label, $value ) {
		echo '<tr><th scope="row"><label for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label></th><td><input class="regular-text" id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '"></td></tr>';
	}
	private static function textarea_row( $name, $label, $value ) {
		echo '<tr><th scope="row"><label for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label></th><td><textarea class="large-text" rows="4" id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '">' . esc_textarea( $value ) . '</textarea></td></tr>';
	}
	private static function number_row( $name, $label, $value, $min, $max ) {
		echo '<tr><th scope="row"><label for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label></th><td><input type="number" id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( absint( $value ) ) . '" min="' . esc_attr( $min ) . '" max="' . esc_attr( $max ) . '"></td></tr>';
	}
}
