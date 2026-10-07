<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Health {
	public static function register() {
		add_filter( 'swi_intro_health_status', array( __CLASS__, 'filter_status' ) );
		add_filter( 'spcrc/file13_contract_state', array( __CLASS__, 'file24_contract_state' ), 20, 2 );
	}

	public static function filter_status( $status ) {
		return array_replace( is_array( $status ) ? $status : array(), self::status() );
	}

	public static function status() {
		$config = Settings::get();
		$file20_class = 'Sabri\\UnifiedShell\\FourPlanHarmonization';
		$file20_available = class_exists( $file20_class ) && defined( 'SABRI_SHELL_VERSION' );
		$legacy_renderer = false !== has_action( 'sabri_shell_welcome_intro_invoke', array( Renderer::class, 'invoke' ) );
		$legacy_analytics = false !== has_action( 'wp_ajax_swi_intro_event', array( Analytics::class, 'ajax_event' ) )
			|| false !== has_action( 'wp_ajax_nopriv_swi_intro_event', array( Analytics::class, 'ajax_event' ) );
		$visual = Renderer::visual_tokens();
		$visual_contract = Renderer::visual_contract_status();
		$foundation = Foundation::status();
		$issues = array();

		if ( version_compare( PHP_VERSION, '8.1', '<' ) ) { $issues[] = 'php_below_declared_minimum'; }
		if ( version_compare( get_bloginfo( 'version' ), '6.0', '<' ) ) { $issues[] = 'wordpress_below_declared_minimum'; }
		if ( ! empty( $config['enabled'] ) || 'disabled' !== (string) $config['status'] || ! empty( $config['analytics_enabled'] ) ) { $issues[] = 'legacy_runtime_not_disabled'; }
		if ( $legacy_renderer ) { $issues[] = 'legacy_public_renderer_registered'; }
		if ( $legacy_analytics ) { $issues[] = 'legacy_public_analytics_registered'; }
		if ( empty( $foundation['available'] ) ) { $issues[] = 'file01_registry_unavailable'; }
		elseif ( 'synced' !== (string) ( $foundation['state'] ?? '' ) ) { $issues[] = 'file01_registry_unsynced'; }
		if ( false !== get_option( 'swi_intro_audit_gap', false ) ) { $issues[] = 'configuration_audit_gap'; }

		return array(
			'plugin_version' => SWI_VERSION,
			'schema_version' => (string) get_option( Settings::SCHEMA_OPTION, '' ),
			'config_version' => absint( $config['config_version'] ),
			'configured_active' => false,
			'legacy_public_disabled' => true,
			'safe_mode' => Eligibility::safe_mode_active(),
			'file20_available' => (bool) $file20_available,
			'file20_version' => defined( 'SABRI_SHELL_VERSION' ) ? (string) SABRI_SHELL_VERSION : '',
			'visual_primary' => $visual['primary'],
			'file25_visual_contract' => array(
				'valid' => ! empty( $visual_contract['valid'] ),
				'owner' => (string) ( $visual_contract['owner'] ?? '' ),
				'version' => (string) ( $visual_contract['version'] ?? '' ),
			),
			'file01_registry' => $foundation,
			'file24_contract_state' => self::file24_contract_state( 'unassessed' ),
			'ownership' => array( 'invocation_frequency' => 'file-20', 'presentation' => 'file-25', 'file-13' => 'legacy-compatibility-only' ),
			'status' => empty( $issues ) ? 'healthy' : 'degraded',
			'issues' => $issues,
			'live_deployment' => 'unverified',
		);
	}

	public static function file24_contract_state( $state, $definition = array() ) {
		unset( $state, $definition );
		$config = Settings::get();
		$renderer_registered = false !== has_action( 'sabri_shell_welcome_intro_invoke', array( Renderer::class, 'invoke' ) );
		$analytics_registered = false !== has_action( 'wp_ajax_swi_intro_event', array( Analytics::class, 'ajax_event' ) )
			|| false !== has_action( 'wp_ajax_nopriv_swi_intro_event', array( Analytics::class, 'ajax_event' ) );
		return empty( $config['enabled'] )
			&& 'disabled' === (string) $config['status']
			&& empty( $config['analytics_enabled'] )
			&& ! $renderer_registered
			&& ! $analytics_registered
			? 'compatible'
			: 'blocked';
	}
}
