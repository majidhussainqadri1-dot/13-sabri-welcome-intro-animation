<?php

defined( 'ABSPATH' ) || exit;

final class SWI_System_Check {
	/** @return array<string,mixed> */
	public static function snapshot() {
		$config = SWI_Config::get(); $eligibility = new SWI_Eligibility();
		$assets = array( 'css' => is_readable( SWI_DIR . 'assets/css/welcome-intro.css' ), 'js' => is_readable( SWI_DIR . 'assets/js/welcome-intro.js' ), 'logo' => is_readable( SWI_DIR . 'assets/images/sabri-sh-logo.svg' ) );
		$baseline = SWI_Experience::visual_baseline(); $baseline_hash = SWI_Experience::visual_baseline_hash();
		$approval_hash = (string) $config['approval_hash']; $approval_current = 'approved' === (string) $config['approval_state'] && '' !== $approval_hash && hash_equals( $approval_hash, $baseline_hash );
		$shell_version = (string) apply_filters( 'swi_shell_contract_version', '' ); $visual_version = (string) apply_filters( 'swi_visual_contract_version', '' ); $assurance_version = (string) apply_filters( 'swi_assurance_contract_version', '' );
		$status = in_array( false, $assets, true ) ? 'degraded' : 'healthy';
		return array(
			'plugin_version' => SWI_VERSION, 'schema_version' => (string) get_option( SWI_Config::OPTION_SCHEMA, '' ), 'experience_contract' => SWI_Experience::EXPERIENCE_CONTRACT, 'experience_version' => (string) $config['experience_version'], 'config_version' => absint( $config['config_version'] ),
			'approval_state' => (string) $config['approval_state'], 'approval_current' => $approval_current, 'approval_baseline_hash' => $approval_hash, 'current_visual_baseline_hash' => $baseline_hash, 'approval_requires_founder' => ! $approval_current,
			'enabled' => ! empty( $config['enabled'] ), 'safe_mode' => $eligibility->safe_mode_active(), 'frequency_days' => absint( $config['frequency_days'] ), 'performance_budget_ms' => absint( $config['performance_budget_ms'] ),
			'adaptive_mode' => ! empty( $config['adaptive_mode'] ), 'never_show_enabled' => ! empty( $config['never_show_enabled'] ), 'signed_replay_enabled' => ! empty( $config['replay_enabled'] ), 'version_replay_enabled' => ! empty( $config['version_replay_enabled'] ), 'guest_reconcile_enabled' => ! empty( $config['guest_reconcile_enabled'] ), 'instant_exit_enabled' => ! empty( $config['instant_exit_enabled'] ), 'data_saver_static' => ! empty( $config['data_saver_static'] ), 'performance_circuit_breaker' => ! empty( $config['performance_circuit_breaker'] ), 'accessibility_profiles' => ! empty( $config['accessibility_profiles'] ),
			'localized_copy_variants' => count( (array) $config['localized_copy'] ), 'rollback_snapshots' => count( SWI_Config::approved_snapshots() ), 'eligible_routes' => count( (array) $config['eligible_routes'] ), 'suppressed_routes' => count( (array) $config['suppressed_prefixes'] ), 'analytics_enabled' => ! empty( $config['analytics_enabled'] ),
			'assets' => $assets, 'visual_baseline' => $baseline, 'pwa_precache_enabled' => ! empty( $config['pwa_precache_enabled'] ), 'pwa_precache_assets' => ! empty( $config['pwa_precache_enabled'] ) ? SWI_Experience::pwa_precache_assets() : array(),
			'shell_registry_callback' => false !== has_filter( 'sabri_shell_module_registry' ), 'shell_slot_callback' => false !== has_action( 'sabri_shell_welcome_intro' ), 'shell_contract_version' => $shell_version, 'visual_contract_version' => $visual_version, 'assurance_contract_version' => $assurance_version,
			'integration_status' => '' !== $shell_version ? 'connected' : 'fallback', 'status' => $status,
			'production_truth' => 'Repository health only; staging/live/operational status requires independent environment evidence.',
		);
	}
}
