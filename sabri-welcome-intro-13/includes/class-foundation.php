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
			'owner_name' => 'Sabri Welcome Intro Historical Compatibility',
			'slug' => 'sabri-welcome-intro',
			'namespace_prefix' => 'Sabri\\WelcomeIntro',
			'software_version' => SWI_VERSION,
			'contract_version' => '1.1.0',
			'state' => 'compatible',
			'required' => array(),
			'optional' => array(
				array(
					'module_key' => 'file-20',
					'minimum_version' => '1.4.12',
					'maximum_version' => '',
					'purpose' => 'Canonical owner of welcome invocation and frequency.',
					'fail_mode' => 'Legacy File 13 intro remains disabled.',
				),
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
					'purpose' => 'Canonical owner of welcome presentation and accessibility.',
					'fail_mode' => 'Legacy File 13 intro remains disabled.',
				),
			),
			'capabilities' => array(
				'inspect-legacy-welcome-intro',
				'migrate-legacy-welcome-intro',
			),
			'commands' => array(
				'DisableLegacyWelcomeIntro.v1',
				'SyncLegacyCompatibilityRegistry.v1',
			),
			'queries' => array(
				'GetLegacyWelcomeIntroStatus.v1',
			),
			'events' => array(
				'LegacyWelcomeIntroSuppressed.v1',
				'LegacyWelcomeIntroRegistrySynced.v1',
			),
			'routes' => array( '/welcome-intro-preview/' ),
			'data_classes' => array(
				'legacy-intro-config',
				'legacy-intro-audit',
			),
			'health' => array(
				'filter' => 'swi_intro_health_status',
				'fail_mode' => 'legacy-intro-disabled',
			),
			'canonical_entities' => array(),
			'writes' => array(
				array(
					'owner_module' => self::MODULE_KEY,
					'operation' => 'legacy-suppression',
					'purpose' => 'Disable historical File 13 runtime during compatibility migration.',
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
		$routes = \SPF_Registry::list_routes();
		// File 01 list_routes() is capped at 200 with no paging or exact-key read.
		// A full page cannot prove there are no later collisions: fail closed.
		$route_inventory_complete = is_array( $routes ) && count( $routes ) < 200;
		$route = null;
		foreach ( $route_inventory_complete ? $routes : array() as $candidate ) {
			if ( is_array( $candidate ) && self::ROUTE_KEY === (string) ( $candidate['route_key'] ?? '' ) ) { $route = $candidate; break; }
		}
		$manifest = self::manifest();
		// File 01 normalizes dependency order before persistence; compare the same
		// canonical order, while checking all dependencies and the health contract.
		foreach ( array( 'required', 'optional' ) as $dependency_field ) {
			usort( $manifest[ $dependency_field ], static function ( $a, $b ) {
				return strcmp( $a['module_key'], $b['module_key'] );
			} );
		}
		$module_ok = is_array( $module ) && in_array( (string) ( $module['state'] ?? '' ), array( 'compatible', 'degraded' ), true );
		if ( $module_ok ) {
			foreach ( array( 'module_key', 'owner_file', 'owner_name', 'slug', 'namespace_prefix', 'software_version', 'contract_version', 'required', 'optional', 'health', 'capabilities', 'commands', 'queries', 'events', 'routes', 'data_classes', 'canonical_entities', 'writes', 'global_shell_owner', 'application_shell_owner' ) as $field ) {
				if ( ! array_key_exists( $field, $module ) || $module[ $field ] != $manifest[ $field ] ) { $module_ok = false; break; }
			}
		}
		$expected_route = self::route();
		$route_ok = $route_inventory_complete && is_array( $route );
		if ( $route_ok ) {
			foreach ( array( 'route_key', 'route_path', 'owner_module', 'page_id', 'layout_context', 'status', 'destination', 'redirects' ) as $field ) {
				if ( ! array_key_exists( $field, $route ) || $route[ $field ] != $expected_route[ $field ] ) { $route_ok = false; break; }
			}
		}
		return array(
			'available' => true, 'module_registered' => $module_ok, 'route_registered' => $route_ok,
			'route_inventory_complete' => $route_inventory_complete,
			'state' => $module_ok && $route_ok ? 'synced' : 'unsynced',
			'module_state' => is_array( $module ) ? (string) ( $module['state'] ?? '' ) : '',
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
		// An exact, complete registry contract is already synchronized. File 01
		// increments record versions and emits audit events on every write, so
		// avoid duplicate registration when both module and route match.
		$current_status = self::status();
		if ( 'synced' === (string) ( $current_status['state'] ?? '' ) ) {
			return array(
				'already_synced' => true,
				'module' => array(
					'module_key' => self::MODULE_KEY,
					'record_version' => $current_status['module_record_version'],
					'state' => $current_status['module_state'],
				),
				'route' => array(
					'route_key' => self::ROUTE_KEY,
					'record_version' => $current_status['route_record_version'],
					'status' => 'active',
				),
			);
		}
		$existing = \SPF_Registry::get_module( self::MODULE_KEY );
		$existing_state = is_array( $existing ) ? (string) ( $existing['state'] ?? '' ) : '';
		if ( in_array( $existing_state, array( 'retired', 'suspended' ), true ) ) {
			return new \WP_Error( 'swi_foundation_module_protected', __( 'A suspended or retired module cannot be silently revived.', SWI_TEXT_DOMAIN ), array( 'status' => 409 ) );
		}
		if ( is_array( $existing ) && '13' !== (string) ( $existing['owner_file'] ?? '' ) ) {
			return new \WP_Error( 'swi_foundation_owner_conflict', __( 'The existing registry module has a different owner.', SWI_TEXT_DOMAIN ), array( 'status' => 409 ) );
		}
		// Preflight route BEFORE module mutation. File 01 offers separate writes.
		$routes = \SPF_Registry::list_routes();
		if ( ! is_array( $routes ) || count( $routes ) >= 200 ) {
			return new \WP_Error( 'swi_foundation_route_inventory_incomplete', __( 'File 01 route inventory is bounded or unavailable; no registry writes were attempted.', SWI_TEXT_DOMAIN ), array( 'status' => 409 ) );
		}
		$current_route = null;
		foreach ( $routes as $candidate ) {
			if ( ! is_array( $candidate ) ) { continue; }
			if ( self::ROUTE_KEY === (string) ( $candidate['route_key'] ?? '' ) ) {
				$current_route = $candidate;
			} elseif ( '/welcome-intro-preview/' === (string) ( $candidate['route_path'] ?? '' ) ) {
				return new \WP_Error( 'swi_foundation_route_collision', __( 'The preview route is owned by another registry entry.', SWI_TEXT_DOMAIN ), array( 'status' => 409 ) );
			}
		}
		if ( is_array( $current_route ) && 'retired' === (string) ( $current_route['status'] ?? '' ) ) {
			return new \WP_Error( 'swi_foundation_route_retired', __( 'The preview route is retired and cannot be silently revived.', SWI_TEXT_DOMAIN ), array( 'status' => 409 ) );
		}
		if ( is_array( $current_route ) && self::MODULE_KEY !== (string) ( $current_route['owner_module'] ?? '' ) ) {
			return new \WP_Error( 'swi_foundation_route_owner_conflict', __( 'The preview route has a different owner.', SWI_TEXT_DOMAIN ), array( 'status' => 409 ) );
		}
		$manifest = self::manifest();
		if ( is_array( $existing ) ) {
			$manifest['state'] = in_array( $existing_state, array( 'active', 'degraded' ), true ) ? 'degraded' : 'compatible';
		}
		$context = array( 'purpose' => 'file13_registry_sync' );
		if ( is_array( $existing ) ) { $context['expected_version'] = absint( $existing['record_version'] ?? 0 ); }
		$registered = \SPF_Registry::register_manifest( $manifest, $context );
		if ( is_wp_error( $registered ) ) { return $registered; }
		$route_context = array( 'purpose' => 'file13_preview_route_sync' );
		if ( is_array( $current_route ) ) { $route_context['expected_version'] = absint( $current_route['record_version'] ?? 0 ); }
		$mapped = \SPF_Registry::map_route( self::route(), $route_context );
		if ( is_wp_error( $mapped ) ) {
			return new \WP_Error( 'swi_foundation_partial_sync', __( 'Module updated but route mapping failed. Reconcile registry before retrying.', SWI_TEXT_DOMAIN ), array( 'status' => 409, 'route_error' => $mapped->get_error_code(), 'module_record_version' => $registered['record_version'] ?? 0 ) );
		}
		do_action( 'swi_intro_foundation_registry_synced', $registered, $mapped );
		return array( 'module' => $registered, 'route' => $mapped );
	}

}
