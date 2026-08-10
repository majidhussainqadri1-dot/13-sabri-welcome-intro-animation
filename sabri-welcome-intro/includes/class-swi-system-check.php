<?php

defined( 'ABSPATH' ) || exit;

final class SWI_System_Check {
	/** @return array<string,mixed> */
	public static function snapshot() {
		$config = SWI_Config::get(); $eligibility = new SWI_Eligibility(); $assets = array( 'css' => is_readable( SWI_DIR . 'assets/css/welcome-intro.css' ), 'js' => is_readable( SWI_DIR . 'assets/js/welcome-intro.js' ), 'logo' => is_readable( SWI_DIR . 'assets/images/sabri-sh-logo.svg' ) );
		$shell_version = (string) apply_filters( 'swi_shell_contract_version', '' ); $visual_version = (string) apply_filters( 'swi_visual_contract_version', '' ); $assurance_version = (string) apply_filters( 'swi_assurance_contract_version', '' );
		return array(
			'plugin_version' => SWI_VERSION, 'schema_version' => (string) get_option( SWI_Config::OPTION_SCHEMA, '' ), 'experience_contract' => SWI_Experience::EXPERIENCE_CONTRACT, 'experience_version' => (string) $config['experience_version'], 'config_version' => absint( $config['config_version'] ), 'approval_state' => (string) $config['approval_state'], 'enabled' => ! empty( $config['enabled'] ), 'safe_mode' => $eligibility->safe_mode_active(), 'frequency_days' => absint( $config['frequency_days'] ), 'performance_budget_ms' => absint( $config['performance_budget_ms'] ),
			'adaptive_mode' => ! empty( $config['adaptive_mode'] ), 'never_show_enabled' => ! empty( $config['never_show_enabled'] ), 'guest_reconcile_enabled' => ! empty( $config['guest_reconcile_enabled'] ), 'accessibility_profiles' => ! empty( $config['accessibility_profiles'] ), 'localized_copy_variants' => count( (array) $config['localized_copy'] ), 'approved_snapshots' => count( SWI_Config::approved_snapshots() ), 'eligible_routes' => count( (array) $config['eligible_routes'] ), 'suppressed_routes' => count( (array) $config['suppressed_prefixes'] ), 'analytics_enabled' => ! empty( $config['analytics_enabled'] ), 'assets' => $assets, 'visual_baseline' => SWI_Experience::visual_baseline(), 'pwa_precache_assets' => ! empty( $config['pwa_precache_enabled'] ) ? SWI_Experience::pwa_precache_assets() : array(),
			'shell_registry_callback' => false !== has_filter( 'sabri_shell_module_registry' ), 'shell_slot_callback' => false !== has_action( 'sabri_shell_welcome_intro' ), 'shell_contract_version' => $shell_version, 'visual_contract_version' => $visual_version, 'assurance_contract_version' => $assurance_version, 'integration_status' => '' !== $shell_version ? 'connected' : 'fallback', 'status' => in_array( false, $assets, true ) ? 'degraded' : 'healthy',
		);
	}
}
