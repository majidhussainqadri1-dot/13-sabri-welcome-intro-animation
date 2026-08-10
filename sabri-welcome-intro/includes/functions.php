<?php

defined( 'ABSPATH' ) || exit;
if ( ! function_exists( 'swi_manage_capability' ) ) { function swi_manage_capability() { $capability = apply_filters( 'swi_manage_capability', 'manage_options' ); return is_string( $capability ) && '' !== sanitize_key( $capability ) ? sanitize_key( $capability ) : 'manage_options'; } }
if ( ! function_exists( 'swi_approval_capability' ) ) { function swi_approval_capability() { $capability = apply_filters( 'swi_founder_approval_capability', swi_manage_capability() ); return is_string( $capability ) && '' !== sanitize_key( $capability ) ? sanitize_key( $capability ) : swi_manage_capability(); } }
if ( ! function_exists( 'swi_current_user_can_manage' ) ) { function swi_current_user_can_manage() { return current_user_can( swi_manage_capability() ); } }
if ( ! function_exists( 'swi_current_user_can_approve' ) ) { function swi_current_user_can_approve() { return current_user_can( swi_approval_capability() ); } }
if ( ! function_exists( 'swi_render_welcome_intro' ) ) { function swi_render_welcome_intro() { $renderer = SWI_Plugin::renderer(); if ( $renderer ) { $renderer->render(); } } }
if ( ! function_exists( 'swi_get_welcome_intro_replay_url' ) ) {
	function swi_get_welcome_intro_replay_url( $url = '' ) {
		$fallback = home_url( '/' );
		$base = $url ? wp_validate_redirect( esc_url_raw( $url ), $fallback ) : $fallback;
		$home_host = wp_parse_url( $fallback, PHP_URL_HOST );
		$base_host = wp_parse_url( $base, PHP_URL_HOST );
		if ( ! is_string( $home_host ) || ! is_string( $base_host ) || strtolower( $home_host ) !== strtolower( $base_host ) ) { $base = $fallback; }
		return add_query_arg( array( 'swi_replay' => '1', '_swi_nonce' => wp_create_nonce( 'swi_public_replay' ) ), $base );
	}
}
if ( ! function_exists( 'swi_get_welcome_intro_contract' ) ) {
	/** @return array<string,mixed> */
	function swi_get_welcome_intro_contract() {
		return array(
			'contract' => 'sabri.file13.welcome-intro', 'version' => '1.1.0', 'owner' => 'File 13', 'render_callback' => 'swi_render_welcome_intro', 'replay_url_helper' => 'swi_get_welcome_intro_replay_url',
			'eligibility_filter' => 'swi_eligibility_decision', 'safe_mode_filter' => 'sabri_platform_safe_mode', 'preference_filter' => 'swi_user_last_seen_timestamp', 'never_show_filter' => 'swi_user_never_show',
			'capability_filter' => 'swi_manage_capability', 'approval_capability_filter' => 'swi_founder_approval_capability', 'runtime_config_filter' => 'swi_runtime_config',
			'dismissed_action' => 'swi_user_dismissed', 'preference_action' => 'swi_user_preference_changed', 'config_updated_action' => 'swi_config_updated',
			'public_event_names' => array( 'swi:shown','swi:skipped','swi:completed','swi:closed','swi:error','swi:variant','swi:replay' ),
			'privacy_class' => 'device/session + optional account preference timestamp; aggregate telemetry only', 'frequency_min_days' => 30, 'fail_open' => true,
			'future_superset' => array( 'adaptive_intro','never_show','signed_replay','version_awareness','cross_device_sync','guest_account_reconcile','instant_exit','data_saver','performance_circuit_breaker','accessibility_profiles','localized_copy','preview_lab','founder_approval','visual_baseline','privacy_safe_telemetry','health_dashboard','rollback_snapshots','pwa_offline_compatibility' ),
		);
	}
}
