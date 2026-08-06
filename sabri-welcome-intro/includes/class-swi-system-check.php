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
			'shell_registry'    => has_filter( 'sabri_shell_module_registry' ),
			'shell_slot'        => has_action( 'sabri_shell_welcome_intro' ),
			'status'            => in_array( false, $assets, true ) ? 'degraded' : 'healthy',
		);
	}
}
