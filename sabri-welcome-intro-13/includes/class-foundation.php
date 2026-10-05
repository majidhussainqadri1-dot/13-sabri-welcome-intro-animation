<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * File 01 registry adapter.
 *
 * Registry writes are never performed silently during front-end requests or
 * plugin activation. An authorized operator must explicitly run the sync.
 */
final class Foundation {
	const SYNC_ACTION = 'swi_sync_foundation_registry';
	const MODULE_KEY = 'file-13';
	const ROUTE_KEY = 'file13-welcome-intro-preview';

	public static function register() {
		add_action( 'admin_post_' . self::SYNC_ACTION, array( __CLASS__, 'handle_sync' ) );
	}

	public static function manifest() {
		return array(
			'module_key' => self::MODULE_KEY,
			'owner_file' => '13',
			'owner_name' => 'Sabri Welcome Intro Animation',
			'slug' => 'sabri-welcome-intro',
			'namespace_prefix' => 'Sabri\\WelcomeIntro',
			'software_version' => SWI_VERSION,
			'contract_version' => '1.0.0',
			'state' => 'compatible',
			'required' => array(
				array(
					'module_key' => 'file-20',
					'minimum_version' => '1.2.0',
					'maximum_version' => '',
					'purpose' => 'Canonical shell placement and route eligibility context.',
					'fail_mode' => 'Intro suppressed; ordinary content remains available.',
				),
			),
			'optional' => array(
				array(
					'module_key' => 'file-00',
					'minimum_version' => '1.1.2',
					'maximum_version' => '',
					'purpose' => 'Stricter institutional authorization assertions when provided.',
					'fail_mode' => 'WordPress native privileged capability remains authoritative for plugin administration.',
				),
				array(
					'module_key' => 'file-24',
					'minimum_version' => '1.0.0',
					'maximum_version' => '',
					'purpose' => 'Cross-cutting security and resilience assurance without native-control takeover.',
					'fail_mode' => 'File 13 native validation, authorization and fail-open-to-content controls remain active.',
				),
				array(
					'module_key' => 'file-25',
					'minimum_version' => '1.0.0',
					'maximum_version' => '',
					'purpose' => 'Canonical public visual token contract.',
					'fail_mode' => 'Continuity-safe governed green fallback tokens.',
				),
			),
			'capabilities' => array(
				'manage-welcome-intro',
				'preview-welcome-intro',
				'render-welcome-intro',
			),
			'commands' => array(
				'SetIntroConfig.v1',
				'DisableWelcomeIntro.v1',
				'DismissWelcomeIntro.v1',
			),
			'queries' => array(
				'GetWelcomeIntroEligibility.v1',
				'GetWelcomeIntroStatus.v1',
			),
			'events' => array(
				'WelcomeIntroShown.v1',
				'WelcomeIntroSkipped.v1',
				'WelcomeIntroCompleted.v1',
			),
			'routes' => array( '/welcome-intro-preview/' ),
			'data_classes' => array(
				'intro-config',
				'intro-session-state',
				'intro-event-aggregate',
			),
			'health' => array(
				'filter' => 'swi_intro_health_status',
				'fail_mode' => 'intro-suppressed',
			),
			'canonical_entities' => array( 'intro-config' ),
			'writes' => array(
				array(
					'owner_module' => self::MODULE_KEY,
					'operation' => 'config-write',
					'purpose' => 'File 13 owned intro configuration only.',
				),
			),
			'global_shell_owner' => false,
			'application_shell_owner' => false,
		);
	}

	public static function route() {
		return array(
			'route_key' => self::ROUTE_KEY,
			'route_path' => '/welcome-intro-preview/',
			'owner_module' => self::MODULE_KEY,
			'page_id' => null,
			'layout_context' => 'minimal',
			'status' => 'active',
			'destination' => '',
			'redirects' => array(),
		);
	}

	public static function status() {
		if ( ! class_exists( 'SPF_Registry' ) ) {
			return array( 'available' => false, 'module_registered' => false, 'route_registered' => false, 'state' => 'unavailable' );
		}

		$module = \SPF_Registry::get_module( self::MODULE_KEY );
		$route = null;
		foreach ( (array) \SPF_Registry::list_routes() as $candidate ) {
			if ( is_array( $candidate ) && self::ROUTE_KEY === (string) ( $candidate['route_key'] ?? '' ) ) {
				$route = $candidate;
				break;
			}
		}

		$module_ok = is_array( $module ) && in_array( (string) ( $module['state'] ?? '' ), array( 'compatible', 'active', 'degraded' ), true );
		$route_ok = is_array( $route )
			&& '/welcome-intro-preview/' === (string) ( $route['route_path'] ?? '' )
			&& self::MODULE_KEY === (string) ( $route['owner_module'] ?? '' )
			&& in_array( (string) ( $route['status'] ?? '' ), array( 'active', 'degraded' ), true );

		return array(
			'available' => true,
			'module_registered' => $module_ok,
			'route_registered' => $route_ok,
			'state' => $module_ok && $route_ok ? 'synced' : 'unsynced',
			'module_record_version' => is_array( $module ) ? absint( $module['record_version'] ?? 0 ) : 0,
			'route_record_version' => is_array( $route ) ? absint( $route['record_version'] ?? 0 ) : 0,
		);
	}

	public static function handle_sync() {
		Authorization::verify_post( self::SYNC_ACTION );
		$result = self::sync();
		$args = array( 'page' => Admin::PAGE );
		if ( is_wp_error( $result ) ) {
			$args['swi_registry_error'] = sanitize_key( $result->get_error_code() );
		} else {
			$args['swi_registry_synced'] = 1;
		}
		wp_safe_redirect( add_query_arg( $args, admin_url( 'options-general.php' ) ) );
		exit;
	}

	public static function sync() {
		if ( ! class_exists( 'SPF_Registry' ) ) {
			return new \WP_Error( 'swi_foundation_unavailable', __( 'File 01 registry is not available.', SWI_TEXT_DOMAIN ), array( 'status' => 503 ) );
		}

		$existing = \SPF_Registry::get_module( self::MODULE_KEY );
		if ( is_array( $existing ) && 'retired' === (string) ( $existing['state'] ?? '' ) ) {
			return new \WP_Error( 'swi_foundation_module_retired', __( 'The File 13 registry record is retired and cannot be silently revived.', SWI_TEXT_DOMAIN ), array( 'status' => 409 ) );
		}

		$manifest = self::manifest();
		if ( is_array( $existing ) ) {
			$current_state = (string) ( $existing['state'] ?? '' );
			$manifest['state'] = in_array( $current_state, array( 'active', 'compatible' ), true ) ? $current_state : 'compatible';
		}
		$context = array( 'purpose' => 'file13_registry_sync' );
		if ( is_array( $existing ) ) {
			$context['expected_version'] = absint( $existing['record_version'] ?? 0 );
		}
		$registered = \SPF_Registry::register_manifest( $manifest, $context );
		if ( is_wp_error( $registered ) ) { return $registered; }

		$current_route = null;
		foreach ( (array) \SPF_Registry::list_routes() as $candidate ) {
			if ( is_array( $candidate ) && self::ROUTE_KEY === (string) ( $candidate['route_key'] ?? '' ) ) {
				$current_route = $candidate;
				break;
			}
		}
		if ( is_array( $current_route ) && 'retired' === (string) ( $current_route['status'] ?? '' ) ) {
			return new \WP_Error( 'swi_foundation_route_retired', __( 'The File 13 preview route is retired and cannot be silently revived.', SWI_TEXT_DOMAIN ), array( 'status' => 409 ) );
		}

		$route_context = array( 'purpose' => 'file13_preview_route_sync' );
		if ( is_array( $current_route ) ) {
			$route_context['expected_version'] = absint( $current_route['record_version'] ?? 0 );
		}
		$mapped = \SPF_Registry::map_route( self::route(), $route_context );
		if ( is_wp_error( $mapped ) ) { return $mapped; }

		do_action( 'swi_intro_foundation_registry_synced', $registered, $mapped );
		return array( 'module' => $registered, 'route' => $mapped );
	}
}
