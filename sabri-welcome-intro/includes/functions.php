<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'swi_manage_capability' ) ) {
	function swi_manage_capability() {
		$capability = apply_filters( 'swi_manage_capability', 'manage_options' );
		return is_string( $capability ) && '' !== sanitize_key( $capability ) ? sanitize_key( $capability ) : 'manage_options';
	}
}

if ( ! function_exists( 'swi_current_user_can_manage' ) ) {
	function swi_current_user_can_manage() {
		return current_user_can( swi_manage_capability() );
	}
}

if ( ! function_exists( 'swi_render_welcome_intro' ) ) {
	function swi_render_welcome_intro() {
		$renderer = SWI_Plugin::renderer();
		if ( $renderer ) {
			$renderer->render();
		}
	}
}

if ( ! function_exists( 'swi_get_welcome_intro_contract' ) ) {
	/** @return array<string,mixed> */
	function swi_get_welcome_intro_contract() {
		return array(
			'contract'             => 'sabri.file13.welcome-intro',
			'version'              => '1.0.0',
			'owner'                => 'File 13',
			'render_callback'      => 'swi_render_welcome_intro',
			'eligibility_filter'   => 'swi_eligibility_decision',
			'safe_mode_filter'     => 'sabri_platform_safe_mode',
			'preference_filter'    => 'swi_user_last_seen_timestamp',
			'capability_filter'    => 'swi_manage_capability',
			'runtime_config_filter'=> 'swi_runtime_config',
			'dismissed_action'     => 'swi_user_dismissed',
			'config_updated_action'=> 'swi_config_updated',
			'public_event_names'   => array( 'swi:shown', 'swi:skipped', 'swi:completed', 'swi:closed', 'swi:error' ),
			'privacy_class'        => 'device/session + optional account timestamp',
			'frequency_min_days'   => 30,
			'fail_open'            => true,
		);
	}
}
