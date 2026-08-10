<?php

defined( 'ABSPATH' ) || exit;

final class SWI_REST {
	const NAMESPACE = 'sabri-welcome-intro/v1';
	/** @var SWI_Analytics */ private $analytics;
	public function __construct( SWI_Analytics $analytics ) { $this->analytics = $analytics; }
	public function hooks() { add_action( 'rest_api_init', array( $this, 'register_routes' ) ); }

	public function register_routes() {
		register_rest_route( self::NAMESPACE, '/dismiss', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this, 'dismiss' ), 'permission_callback' => static function () { return is_user_logged_in(); }, 'args' => $this->event_args() ) );
		register_rest_route( self::NAMESPACE, '/event', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this, 'event' ), 'permission_callback' => '__return_true', 'args' => $this->event_args() ) );
		register_rest_route( self::NAMESPACE, '/preference', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this, 'preference' ), 'permission_callback' => static function () { return is_user_logged_in(); }, 'args' => $this->preference_args() ) );
		register_rest_route( self::NAMESPACE, '/status', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( $this, 'status' ), 'permission_callback' => static function () { return swi_current_user_can_manage(); } ) );
	}

	/** @return array<string,array<string,mixed>> */
	private function event_args() {
		return array(
			'event' => array( 'required' => true, 'sanitize_callback' => 'sanitize_key', 'validate_callback' => static function ( $value ) { return in_array( $value, SWI_Analytics::ALLOWED_EVENTS, true ); } ),
			'config_version' => array( 'required' => true, 'sanitize_callback' => 'absint', 'validate_callback' => static function ( $value ) { return is_numeric( $value ) && (int) $value > 0; } ),
			'experience_version' => array( 'required' => false, 'sanitize_callback' => static function ( $value ) { return substr( preg_replace( '/[^A-Za-z0-9.+-]/', '', (string) $value ), 0, 32 ); } ),
			'idempotency_key' => array( 'required' => true, 'sanitize_callback' => static function ( $value ) { return substr( preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $value ), 0, 64 ); }, 'validate_callback' => static function ( $value ) { return '' !== substr( preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $value ), 0, 64 ); } ),
		);
	}
	/** @return array<string,array<string,mixed>> */
	private function preference_args() {
		return array(
			'event' => array( 'required' => false, 'sanitize_callback' => 'sanitize_key' ),
			'config_version' => array( 'required' => true, 'sanitize_callback' => 'absint' ),
			'last_seen' => array( 'required' => false, 'sanitize_callback' => 'absint', 'validate_callback' => static function ( $value ) { return is_numeric( $value ) && (int) $value >= 0 && (int) $value <= time() + 300; } ),
			'never_show' => array( 'required' => false, 'sanitize_callback' => static function ( $value ) { return empty( $value ) ? 0 : 1; } ),
			'accessibility_profile' => array( 'required' => false, 'sanitize_callback' => array( 'SWI_Experience', 'sanitize_profile' ) ),
			'experience_version' => array( 'required' => false, 'sanitize_callback' => static function ( $value ) { return substr( preg_replace( '/[^A-Za-z0-9.+-]/', '', (string) $value ), 0, 32 ); } ),
			'idempotency_key' => array( 'required' => true, 'sanitize_callback' => static function ( $value ) { return substr( preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $value ), 0, 64 ); }, 'validate_callback' => static function ( $value ) { return '' !== substr( preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $value ), 0, 64 ); } ),
		);
	}

	public function dismiss( WP_REST_Request $request ) {
		$user_id = get_current_user_id(); if ( $this->rate_limited( 'dismiss', $user_id, 20, MINUTE_IN_SECONDS ) ) { return new WP_Error( 'swi_rate_limited', __( 'Too many requests. Try again shortly.', 'sabri-welcome-intro' ), array( 'status' => 429 ) ); }
		$event = (string) $request->get_param( 'event' ); if ( ! in_array( $event, array( 'skipped','completed','closed' ), true ) ) { return new WP_Error( 'swi_invalid_dismissal_event', __( 'This event cannot change the welcome preference.', 'sabri-welcome-intro' ), array( 'status' => 400 ) ); }
		$version = absint( $request->get_param( 'config_version' ) ); $key = (string) $request->get_param( 'idempotency_key' ); if ( '' === $key ) { return new WP_Error( 'swi_missing_idempotency', __( 'Missing event identifier.', 'sabri-welcome-intro' ), array( 'status' => 400 ) ); }
		$dedupe = 'swi_dismiss_' . md5( $user_id . '|' . $key ); if ( get_transient( $dedupe ) ) { return rest_ensure_response( array( 'ok' => true, 'duplicate' => true ) ); }
		$config = SWI_Config::get(); $experience = (string) $request->get_param( 'experience_version' ); if ( '' === $experience ) { $experience = (string) $config['experience_version']; }
		$external = (bool) apply_filters( 'swi_external_preference_store_active', false, $user_id );
		if ( ! $external ) { update_user_meta( $user_id, SWI_Config::USER_META_LAST, time() ); update_user_meta( $user_id, SWI_Config::USER_META_VER, $version ); update_user_meta( $user_id, SWI_Config::USER_META_EXPERIENCE, $experience ); }
		do_action( 'swi_user_dismissed', $user_id, $event, $version ); $this->analytics->record( $event, $version ); set_transient( $dedupe, 1, DAY_IN_SECONDS );
		return rest_ensure_response( array( 'ok' => true, 'duplicate' => false ) );
	}

	public function preference( WP_REST_Request $request ) {
		$user_id = get_current_user_id(); if ( $this->rate_limited( 'preference', $user_id, 30, MINUTE_IN_SECONDS ) ) { return new WP_Error( 'swi_rate_limited', __( 'Too many requests. Try again shortly.', 'sabri-welcome-intro' ), array( 'status' => 429 ) ); }
		$key = (string) $request->get_param( 'idempotency_key' ); if ( '' === $key ) { return new WP_Error( 'swi_missing_idempotency', __( 'Missing event identifier.', 'sabri-welcome-intro' ), array( 'status' => 400 ) ); }
		$dedupe = 'swi_pref_' . md5( $user_id . '|' . $key ); if ( get_transient( $dedupe ) ) { return rest_ensure_response( array( 'ok' => true, 'duplicate' => true ) ); }
		$config = SWI_Config::get(); $external = (bool) apply_filters( 'swi_external_preference_store_active', false, $user_id ); $changed = array();
		$last_seen = absint( $request->get_param( 'last_seen' ) ); $current_last = absint( get_user_meta( $user_id, SWI_Config::USER_META_LAST, true ) );
		$never = ! empty( $request->get_param( 'never_show' ) ); $profile = SWI_Experience::sanitize_profile( $request->get_param( 'accessibility_profile' ) ); $experience = (string) $request->get_param( 'experience_version' ); if ( '' === $experience ) { $experience = (string) $config['experience_version']; }
		if ( ! $external ) {
			if ( $last_seen > $current_last && $last_seen <= time() + 300 ) { update_user_meta( $user_id, SWI_Config::USER_META_LAST, $last_seen ); $changed[] = 'last_seen'; }
			if ( $never && ! get_user_meta( $user_id, SWI_Config::USER_META_NEVER, true ) ) { update_user_meta( $user_id, SWI_Config::USER_META_NEVER, 1 ); $changed[] = 'never_show'; }
			if ( 'auto' !== $profile || '' !== (string) $request->get_param( 'accessibility_profile' ) ) { update_user_meta( $user_id, SWI_Config::USER_META_PROFILE, $profile ); $changed[] = 'accessibility_profile'; }
			if ( '' !== $experience ) { update_user_meta( $user_id, SWI_Config::USER_META_EXPERIENCE, $experience ); $changed[] = 'experience_version'; }
			update_user_meta( $user_id, SWI_Config::USER_META_VER, absint( $request->get_param( 'config_version' ) ) );
		}
		do_action( 'swi_user_preference_changed', $user_id, 'reconciled', $changed ); if ( $never ) { $this->analytics->record( 'never_show', absint( $request->get_param( 'config_version' ) ) ); }
		set_transient( $dedupe, 1, DAY_IN_SECONDS ); return rest_ensure_response( array( 'ok' => true, 'duplicate' => false, 'changed' => array_values( array_unique( $changed ) ) ) );
	}

	public function event( WP_REST_Request $request ) {
		$config = SWI_Config::get(); if ( empty( $config['analytics_enabled'] ) ) { return rest_ensure_response( array( 'ok' => false, 'disabled' => true ) ); }
		$nonce = $request->get_header( 'X-SWI-Nonce' ); if ( ! is_string( $nonce ) || ! wp_verify_nonce( $nonce, 'swi_public_event' ) ) { return new WP_Error( 'swi_invalid_nonce', __( 'Invalid event token.', 'sabri-welcome-intro' ), array( 'status' => 403 ) ); }
		if ( $this->rate_limited( 'event', 'global', 300, MINUTE_IN_SECONDS ) ) { return new WP_Error( 'swi_rate_limited', __( 'Too many requests. Try again shortly.', 'sabri-welcome-intro' ), array( 'status' => 429 ) ); }
		$key = (string) $request->get_param( 'idempotency_key' ); if ( '' === $key ) { return new WP_Error( 'swi_missing_idempotency', __( 'Missing event identifier.', 'sabri-welcome-intro' ), array( 'status' => 400 ) ); }
		$dedupe = 'swi_event_' . md5( $key ); if ( get_transient( $dedupe ) ) { return rest_ensure_response( array( 'ok' => true, 'duplicate' => true ) ); } set_transient( $dedupe, 1, DAY_IN_SECONDS );
		$ok = $this->analytics->record( (string) $request->get_param( 'event' ), absint( $request->get_param( 'config_version' ) ) ); return rest_ensure_response( array( 'ok' => $ok, 'duplicate' => false ) );
	}
	public function status() { return rest_ensure_response( SWI_System_Check::snapshot() ); }
	private function rate_limited( $scope, $subject, $limit, $window ) { $key = 'swi_rl_' . md5( $scope . '|' . $subject . '|' . floor( time() / $window ) ); $count = absint( get_transient( $key ) ); if ( $count >= $limit ) { return true; } set_transient( $key, $count + 1, $window + 60 ); return false; }
}
