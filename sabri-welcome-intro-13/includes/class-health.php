<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Health {
	public static function register() {
		add_filter( 'swi_intro_health_status', array( __CLASS__, 'filter_status' ) );
	}

	public static function filter_status( $status ) {
		return array_replace( is_array( $status ) ? $status : array(), self::status() );
	}

	public static function status() {
		$config = Settings::get();
		$file20_class = 'Sabri\\UnifiedShell\\FourPlanHarmonization';
		$file20_hook = class_exists( $file20_class )
			&& defined( 'SABRI_SHELL_VERSION' )
			&& false !== has_action( 'wp_body_open', array( $file20_class, 'invoke_welcome_intro' ) );
		$visual = Renderer::visual_tokens();
		$foundation = Foundation::status();
		$issues = array();

		if ( ! $file20_hook ) { $issues[] = 'file20_welcome_contract_missing'; }
		if ( version_compare( PHP_VERSION, '8.1', '<' ) ) { $issues[] = 'php_below_declared_minimum'; }
		if ( version_compare( get_bloginfo( 'version' ), '6.0', '<' ) ) { $issues[] = 'wordpress_below_declared_minimum'; }
		if ( absint( $config['recurrence_days'] ) < 30 ) { $issues[] = 'recurrence_below_governing_floor'; }
		if ( ! wp_next_scheduled( Analytics::CLEANUP_HOOK ) ) { $issues[] = 'analytics_cleanup_not_scheduled'; }
		if ( ! empty( $foundation['available'] ) && 'synced' !== (string) ( $foundation['state'] ?? '' ) ) { $issues[] = 'file01_registry_unsynced'; }

		return array(
			'plugin_version' => SWI_VERSION,
			'schema_version' => (string) get_option( Settings::SCHEMA_OPTION, '' ),
			'config_version' => absint( $config['config_version'] ),
			'configured_active' => Settings::active_now( $config ),
			'safe_mode' => Eligibility::safe_mode_active(),
			'file20_contract_present' => (bool) $file20_hook,
			'file20_version' => defined( 'SABRI_SHELL_VERSION' ) ? (string) SABRI_SHELL_VERSION : '',
			'visual_primary' => $visual['primary'],
			'file01_registry' => $foundation,
			'status' => empty( $issues ) ? 'healthy' : 'degraded',
			'issues' => $issues,
			'live_deployment' => 'unverified',
		);
	}
}
