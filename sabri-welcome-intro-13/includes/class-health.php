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
		$file20_hook = has_action( 'sabri_shell_welcome_intro_invoke' );
		$visual = Renderer::visual_tokens();
		$issues = array();

		if ( ! $file20_hook ) { $issues[] = 'file20_welcome_hook_missing'; }
		if ( version_compare( PHP_VERSION, '8.1', '<' ) ) { $issues[] = 'php_below_declared_minimum'; }
		if ( version_compare( get_bloginfo( 'version' ), '6.0', '<' ) ) { $issues[] = 'wordpress_below_declared_minimum'; }
		if ( absint( $config['recurrence_days'] ) < 30 ) { $issues[] = 'recurrence_below_governing_floor'; }

		return array(
			'plugin_version' => SWI_VERSION,
			'schema_version' => (string) get_option( Settings::SCHEMA_OPTION, '' ),
			'config_version' => absint( $config['config_version'] ),
			'configured_active' => Settings::active_now( $config ),
			'safe_mode' => Eligibility::safe_mode_active(),
			'file20_hook_present' => (bool) $file20_hook,
			'visual_primary' => $visual['primary'],
			'status' => empty( $issues ) ? 'healthy' : 'degraded',
			'issues' => $issues,
			'live_deployment' => 'unverified',
		);
	}
}
