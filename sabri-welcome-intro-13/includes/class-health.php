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
		$stored = Settings::stored();
		// Settings::stored() normalizes a scalar/missing row into safe defaults.
		// Assurance must inspect the raw persisted shape, not certify those defaults.
		$raw_stored = get_option( Settings::OPTION, false );
		$stored_status = is_string( $stored['status'] ?? null ) ? $stored['status'] : '';
		$stored_flags_valid = is_array( $raw_stored )
			&& is_bool( $raw_stored['enabled'] ?? null )
			&& is_string( $raw_stored['status'] ?? null )
			&& is_bool( $raw_stored['analytics_enabled'] ?? null );
		$file20_class = 'Sabri\\UnifiedShell\\FourPlanHarmonization';
		$file20_available = class_exists( $file20_class ) && defined( 'SABRI_SHELL_VERSION' );
		$legacy_renderer = false !== has_action( 'sabri_shell_welcome_intro_invoke', array( Renderer::class, 'invoke' ) );
		$legacy_analytics = false !== has_action( 'wp_ajax_swi_intro_event', array( Analytics::class, 'ajax_event' ) )
			|| false !== has_action( 'wp_ajax_nopriv_swi_intro_event', array( Analytics::class, 'ajax_event' ) );
		$legacy_public_disabled = empty( $config['enabled'] )
			&& 'disabled' === (string) $config['status']
			&& empty( $config['analytics_enabled'] )
			&& ! $legacy_renderer
			&& ! $legacy_analytics;
		$visual = Renderer::visual_tokens();
		$visual_contract = Renderer::visual_contract_status();
		$foundation = Foundation::status();
		$issues = array();

		if ( version_compare( PHP_VERSION, '8.1', '<' ) ) { $issues[] = 'php_below_declared_minimum'; }
		if ( version_compare( get_bloginfo( 'version' ), '6.0', '<' ) ) { $issues[] = 'wordpress_below_declared_minimum'; }
		if ( SWI_SCHEMA_VERSION !== Settings::schema_version() ) { $issues[] = 'schema_migration_pending'; }
		if ( ! $stored_flags_valid ) { $issues[] = 'legacy_stored_malformed'; }
		if ( ! empty( $config['enabled'] ) || 'disabled' !== (string) $config['status'] || ! empty( $config['analytics_enabled'] ) ) { $issues[] = 'legacy_runtime_not_disabled'; }
		if ( ! empty( $stored['enabled'] ) || 'disabled' !== $stored_status || ! empty( $stored['analytics_enabled'] ) ) { $issues[] = 'legacy_stored_activation_detected'; }
		if ( $legacy_renderer ) { $issues[] = 'legacy_public_renderer_registered'; }
		if ( $legacy_analytics ) { $issues[] = 'legacy_public_analytics_registered'; }
		if ( empty( $foundation['available'] ) ) { $issues[] = 'file01_registry_unavailable'; }
		elseif ( 'synced' !== (string) ( $foundation['state'] ?? '' ) ) { $issues[] = 'file01_registry_unsynced'; }
		if ( false !== get_option( 'swi_intro_audit_gap', false ) ) { $issues[] = 'configuration_audit_gap'; }

		return array(
			'plugin_version' => SWI_VERSION,
			'schema_version' => Settings::schema_version(),
			'config_version' => absint( $config['config_version'] ),
			'configured_active' => ! empty( $stored['enabled'] ) || 'active' === $stored_status,
			'legacy_public_disabled' => $legacy_public_disabled,
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
		$stored = Settings::stored();
		// Settings::stored() normalizes a scalar/missing row into safe defaults.
		// Assurance must inspect the raw persisted shape, not certify those defaults.
		$raw_stored = get_option( Settings::OPTION, false );
		$stored_status = is_string( $stored['status'] ?? null ) ? $stored['status'] : '';
		$stored_flags_valid = is_array( $raw_stored )
			&& is_bool( $raw_stored['enabled'] ?? null )
			&& is_string( $raw_stored['status'] ?? null )
			&& is_bool( $raw_stored['analytics_enabled'] ?? null );
		$renderer_registered = false !== has_action( 'sabri_shell_welcome_intro_invoke', array( Renderer::class, 'invoke' ) );
		$analytics_registered = false !== has_action( 'wp_ajax_swi_intro_event', array( Analytics::class, 'ajax_event' ) )
			|| false !== has_action( 'wp_ajax_nopriv_swi_intro_event', array( Analytics::class, 'ajax_event' ) );
		// File 24 assurance must not certify a suppressed legacy runtime while
		// schema migration, the File 01 registry, or its audit trail is unresolved.
		$registry = Foundation::status();
		$assurance_ready = SWI_SCHEMA_VERSION === Settings::schema_version()
			&& 'synced' === (string) ( $registry['state'] ?? '' )
			&& false === get_option( 'swi_intro_audit_gap', false );
		return $assurance_ready && empty( $config['enabled'] )
			&& 'disabled' === (string) $config['status']
			&& empty( $config['analytics_enabled'] )
			&& $stored_flags_valid
			&& empty( $stored['enabled'] )
			&& 'disabled' === $stored_status
			&& empty( $stored['analytics_enabled'] )
			&& ! $renderer_registered
			&& ! $analytics_registered
			? 'compatible'
			: 'blocked';
	}
}
