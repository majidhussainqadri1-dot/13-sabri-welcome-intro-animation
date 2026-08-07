<?php

defined( 'ABSPATH' ) || exit;

final class SWI_System_Check {
	/** @return array<string,mixed> */
	public static function snapshot() {
		$config = SWI_Config::get();
		$eligibility = new SWI_Eligibility();
		$assets = array(
			'css'  => is_readable( SWI_DIR . 'assets/css/welcome-intro.css' ),
			'js'   => is_readable( SWI_DIR . 'assets/js/welcome-intro.js' ),
			'logo' => is_readable( SWI_DIR . 'assets/images/sabri-sh-logo.svg' ),
		);
		$shell_version = (string) apply_filters( 'swi_shell_contract_version', '' );
		$visual_version = (string) apply_filters( 'swi_visual_contract_version', '' );
		$assurance_version = (string) apply_filters( 'swi_assurance_contract_version', '' );
		return array(
			'plugin_version'    => SWI_VERSION,
			'schema_version'    => (string) get_option( SWI_Config::OPTION_SCHEMA, '' ),
			'config_version'    => absint( $config['config_version'] ),
			'enabled'           => ! empty( $config['enabled'] ),
			'safe_mode'         => $eligibility->safe_mode_active(),
			'frequency_days'    => absint( $config['frequency_days'] ),
			'eligible_routes'   => count( (array) $config['eligible_routes'] ),
			'suppressed_routes' => count( (array) $config['suppressed_prefixes'] ),
			'analytics_enabled' => ! empty( $config['analytics_enabled'] ),
			'assets'            => $assets,
			'shell_registry_callback' => false !== has_filter( 'sabri_shell_module_registry' ),
			'shell_slot_callback'     => false !== has_action( 'sabri_shell_welcome_intro' ),
			'shell_contract_version'  => $shell_version,
			'visual_contract_version' => $visual_version,
			'assurance_contract_version' => $assurance_version,
			'integration_status' => '' !== $shell_version ? 'connected' : 'fallback',
			'status'            => in_array( false, $assets, true ) ? 'degraded' : 'healthy',
		);
	}
}
