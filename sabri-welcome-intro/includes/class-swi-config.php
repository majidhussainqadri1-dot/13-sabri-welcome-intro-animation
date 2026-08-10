<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Config {
	const OPTION_CONFIG      = 'swi_config';
	const OPTION_SCHEMA      = 'swi_schema_version';
	const OPTION_AUDIT       = 'swi_audit_log';
	const OPTION_METRICS     = 'swi_aggregate_metrics';
	const OPTION_WRITE_LOCK  = 'swi_config_write_lock';
	const OPTION_SNAPSHOTS   = 'swi_config_snapshots';
	const USER_META_LAST     = 'swi_last_dismissed_at';
	const USER_META_VER      = 'swi_last_config_version';
	const USER_META_NEVER    = 'swi_never_show';
	const USER_META_EXPERIENCE = 'swi_experience_version';
	const USER_META_PROFILE  = 'swi_accessibility_profile';
	const COOKIE_NAME        = 'swi_seen_at_v1';
	const SESSION_KEY        = 'swi_seen_session_v1';
	const LOCAL_KEY          = 'swi_seen_at_v1';
	const CLAIM_KEY          = 'swi_claim_v1';
	const NEVER_KEY          = 'swi_never_show_v1';
	const PROFILE_KEY        = 'swi_a11y_profile_v1';

	/** @return array<string,mixed> */
	public static function defaults() {
		return array(
			'enabled'                       => 1,
			'config_version'                => 1,
			'experience_version'            => '1.1.0',
			'frequency_days'                => 30,
			'duration_ms'                   => 0,
			'reduced_duration_ms'           => 900,
			'brand_name'                    => 'Sabri Social Homeopathy Platform',
			'brand_claim'                   => 'The Tridimensional Healing System of Soul, Vital Force, and Matter',
			'brand_language'                => 'en-US',
			'localized_copy'                => array(),
			'eligible_routes'               => array( '/' ),
			'suppressed_prefixes'           => array(
				'/wp-login.php', '/wp-admin', '/login', '/register', '/signup', '/account', '/settings', '/support',
				'/appointments/book', '/appointment', '/clinical', '/emergency', '/checkout', '/cart',
			),
			'starts_at'                     => '',
			'ends_at'                       => '',
			'analytics_enabled'             => 0,
			'adaptive_mode'                 => 1,
			'never_show_enabled'            => 1,
			'replay_enabled'                => 1,
			'version_replay_enabled'        => 1,
			'guest_reconcile_enabled'       => 1,
			'instant_exit_enabled'          => 1,
			'data_saver_static'             => 1,
			'performance_circuit_breaker'   => 1,
			'performance_budget_ms'         => 120,
			'accessibility_profiles'        => 1,
			'default_accessibility_profile' => 'auto',
			'pwa_precache_enabled'          => 1,
			'rollback_snapshots_enabled'    => 1,
			'approval_state'                => 'pending',
			'approval_hash'                 => '',
			'approved_at'                   => '',
			'approved_by'                   => 0,
			'updated_at'                    => '',
			'updated_by'                    => 0,
		);
	}

	/** @return array<string,mixed> */
	public static function get() {
		$config   = self::stored_config();
		$filtered = apply_filters( 'swi_runtime_config', $config );
		if ( ! is_array( $filtered ) ) {
			return $config;
		}
		$runtime = self::sanitize( array_merge( $config, $filtered ), false );
		return self::restrict_runtime_config( $config, $runtime );
	}

	/** @return array<string,mixed> */
	private static function stored_config() {
		$stored = get_option( self::OPTION_CONFIG, array() );
		$stored = is_array( $stored ) ? $stored : array();
		return self::sanitize( array_merge( self::defaults(), $stored ), false );
	}

	/**
	 * Companion runtime configuration is restriction-only. File 20/24 may suppress
	 * or narrow eligibility, but cannot replace File 13 copy/visual/preferences,
	 * shorten recurrence, remove protected suppressions, widen schedule/routes,
	 * opt analytics in, re-enable a kill decision, or relax accessibility/data-saver safeguards.
	 *
	 * @param array<string,mixed> $local
	 * @param array<string,mixed> $runtime
	 * @return array<string,mixed>
	 */
	private static function restrict_runtime_config( array $local, array $runtime ) {
		$effective = $local;
		$effective['enabled'] = ! empty( $local['enabled'] ) && ! empty( $runtime['enabled'] ) ? 1 : 0;
		$effective['analytics_enabled'] = ! empty( $local['analytics_enabled'] ) && ! empty( $runtime['analytics_enabled'] ) ? 1 : 0;
		$effective['frequency_days'] = max( absint( $local['frequency_days'] ), absint( $runtime['frequency_days'] ) );
		$effective['performance_budget_ms'] = min( absint( $local['performance_budget_ms'] ), absint( $runtime['performance_budget_ms'] ) ?: absint( $local['performance_budget_ms'] ) );

		$local_routes   = array_values( array_unique( (array) $local['eligible_routes'] ) );
		$runtime_routes = array_values( array_unique( (array) $runtime['eligible_routes'] ) );
		if ( in_array( '*', $local_routes, true ) ) {
			$effective['eligible_routes'] = $runtime_routes;
		} elseif ( in_array( '*', $runtime_routes, true ) ) {
			$effective['eligible_routes'] = $local_routes;
		} else {
			$effective['eligible_routes'] = array_values( array_intersect( $local_routes, $runtime_routes ) );
		}
		$effective['suppressed_prefixes'] = array_values( array_unique( array_merge( (array) $local['suppressed_prefixes'], (array) $runtime['suppressed_prefixes'] ) ) );

		$local_start = (string) $local['starts_at']; $runtime_start = (string) $runtime['starts_at'];
		if ( '' === $local_start ) { $effective['starts_at'] = $runtime_start; }
		elseif ( '' !== $runtime_start && strtotime( $runtime_start ) > strtotime( $local_start ) ) { $effective['starts_at'] = $runtime_start; }
		$local_end = (string) $local['ends_at']; $runtime_end = (string) $runtime['ends_at'];
		if ( '' === $local_end ) { $effective['ends_at'] = $runtime_end; }
		elseif ( '' !== $runtime_end && strtotime( $runtime_end ) < strtotime( $local_end ) ) { $effective['ends_at'] = $runtime_end; }
		if ( '' !== $effective['starts_at'] && '' !== $effective['ends_at'] && strtotime( $effective['starts_at'] ) > strtotime( $effective['ends_at'] ) ) { $effective['enabled'] = 0; }
		return $effective;
	}

	/**
	 * @param array<string,mixed> $raw
	 * @param bool $increment_version
	 * @return array<string,mixed>
	 */
	public static function sanitize( array $raw, $increment_version = true ) {
		$defaults = self::defaults();
		$current  = get_option( self::OPTION_CONFIG, array() );
		$current  = is_array( $current ) ? $current : array();
		$version  = isset( $current['config_version'] ) ? absint( $current['config_version'] ) : 0;
		$config = array();
		$config['enabled'] = empty( $raw['enabled'] ) ? 0 : 1;
		$requested_version = isset( $raw['config_version'] ) ? absint( $raw['config_version'] ) : $version;
		$config['config_version'] = $increment_version ? max( 1, $version + 1 ) : max( 1, $requested_version ?: 1 );
		$config['experience_version'] = self::sanitize_semver( $raw['experience_version'] ?? $defaults['experience_version'], $defaults['experience_version'] );
		$config['frequency_days'] = min( 365, max( 30, absint( $raw['frequency_days'] ?? $defaults['frequency_days'] ) ) );
		$requested_duration = absint( $raw['duration_ms'] ?? $defaults['duration_ms'] );
		$config['duration_ms'] = 0 === $requested_duration ? 0 : min( 30000, max( 1200, $requested_duration ) );
		$config['reduced_duration_ms'] = min( 1500, max( 250, absint( $raw['reduced_duration_ms'] ?? $defaults['reduced_duration_ms'] ) ) );
		$config['brand_name'] = self::sanitize_copy( $raw['brand_name'] ?? $defaults['brand_name'], 120, $defaults['brand_name'] );
		$config['brand_claim'] = self::sanitize_copy( $raw['brand_claim'] ?? $defaults['brand_claim'], 280, $defaults['brand_claim'] );
		$config['brand_language'] = self::sanitize_language( $raw['brand_language'] ?? $defaults['brand_language'] );
		$config['localized_copy'] = self::sanitize_localized_copy( $raw['localized_copy'] ?? array() );
		$config['eligible_routes'] = self::sanitize_routes( $raw['eligible_routes'] ?? $defaults['eligible_routes'], array( '/' ) );
		$config['suppressed_prefixes'] = self::sanitize_routes( $raw['suppressed_prefixes'] ?? $defaults['suppressed_prefixes'], $defaults['suppressed_prefixes'] );
		$config['starts_at'] = self::sanitize_datetime( $raw['starts_at'] ?? '' );
		$config['ends_at'] = self::sanitize_datetime( $raw['ends_at'] ?? '' );
		$config['analytics_enabled'] = empty( $raw['analytics_enabled'] ) ? 0 : 1;
		foreach ( array( 'adaptive_mode','never_show_enabled','replay_enabled','version_replay_enabled','guest_reconcile_enabled','instant_exit_enabled','data_saver_static','performance_circuit_breaker','accessibility_profiles','pwa_precache_enabled','rollback_snapshots_enabled' ) as $flag ) {
			$config[ $flag ] = empty( $raw[ $flag ] ) ? 0 : 1;
		}
		$config['performance_budget_ms'] = min( 1000, max( 40, absint( $raw['performance_budget_ms'] ?? $defaults['performance_budget_ms'] ) ) );
		$config['default_accessibility_profile'] = SWI_Experience::sanitize_profile( $raw['default_accessibility_profile'] ?? $defaults['default_accessibility_profile'] );
		$approval = sanitize_key( (string) ( $raw['approval_state'] ?? $defaults['approval_state'] ) );
		$config['approval_state'] = in_array( $approval, array( 'pending','approved','rejected' ), true ) ? $approval : 'pending';
		$config['approval_hash'] = preg_match( '/^[a-f0-9]{64}$/', (string) ( $raw['approval_hash'] ?? '' ) ) ? (string) $raw['approval_hash'] : '';
		$config['approved_at'] = self::sanitize_datetime( $raw['approved_at'] ?? '' );
		$config['approved_by'] = absint( $raw['approved_by'] ?? 0 );
		if ( $increment_version ) {
			$config['updated_at'] = gmdate( 'c' );
			$config['updated_by'] = function_exists( 'get_current_user_id' ) ? absint( get_current_user_id() ) : 0;
		} else {
			$config['updated_at'] = self::sanitize_datetime( $raw['updated_at'] ?? '' );
			$config['updated_by'] = absint( $raw['updated_by'] ?? 0 );
		}
		if ( '' !== $config['starts_at'] && '' !== $config['ends_at'] && strtotime( $config['starts_at'] ) > strtotime( $config['ends_at'] ) ) { $config['starts_at'] = ''; $config['ends_at'] = ''; }
		return $config;
	}

	/** @param array<string,mixed> $raw @return array<string,mixed>|WP_Error */
	public static function save( array $raw, $expected_version ) {
		$lock_token = self::acquire_write_lock();
		if ( false === $lock_token ) { return new WP_Error( 'swi_config_busy', __( 'Another settings update is in progress. Reload and try again.', 'sabri-welcome-intro' ), array( 'status' => 409 ) ); }
		try {
			$current = self::stored_config();
			if ( absint( $expected_version ) !== absint( $current['config_version'] ) ) { return new WP_Error( 'swi_config_conflict', __( 'The settings changed in another session. Reload and try again.', 'sabri-welcome-intro' ), array( 'status' => 409 ) ); }
			$next = self::sanitize( array_merge( $current, $raw ), true );
			if ( ! empty( $current['rollback_snapshots_enabled'] ) ) { self::snapshot_config( $current, 'pre_update' ); }
			$ok = update_option( self::OPTION_CONFIG, $next, false );
			if ( ! $ok && $next !== $current ) { return new WP_Error( 'swi_config_write_failed', __( 'The settings could not be saved.', 'sabri-welcome-intro' ) ); }
			self::record_audit( 'config_updated', self::changed_keys( $current, $next ), $next['config_version'] );
			do_action( 'swi_config_updated', $next, $current );
			return $next;
		} finally { self::release_write_lock( $lock_token ); }
	}

	/** @return array<string,mixed>|WP_Error */
	public static function approve_visual( $expected_version ) {
		if ( function_exists( 'swi_current_user_can_approve' ) && ! swi_current_user_can_approve() ) { return new WP_Error( 'swi_approval_forbidden', __( 'You are not allowed to approve the visual baseline.', 'sabri-welcome-intro' ), array( 'status' => 403 ) ); }
		$current = self::stored_config();
		if ( absint( $expected_version ) !== absint( $current['config_version'] ) ) { return new WP_Error( 'swi_config_conflict', __( 'The settings changed in another session. Reload and try again.', 'sabri-welcome-intro' ), array( 'status' => 409 ) ); }
		$baseline = SWI_Experience::visual_baseline(); ksort( $baseline );
		$current['approval_state'] = 'approved';
		$current['approval_hash'] = hash( 'sha256', wp_json_encode( $baseline ) );
		$current['approved_at'] = gmdate( 'c' );
		$current['approved_by'] = function_exists( 'get_current_user_id' ) ? absint( get_current_user_id() ) : 0;
		$result = self::save( $current, $expected_version );
		if ( ! is_wp_error( $result ) ) { self::ensure_approved_snapshot( $result ); self::record_audit( 'visual_approved', array( 'approval_state','approval_hash' ), $result['config_version'] ); }
		return $result;
	}

	/** @return string|false */
	private static function acquire_write_lock() {
		$existing = get_option( self::OPTION_WRITE_LOCK, null );
		if ( is_array( $existing ) && isset( $existing['expires_at'] ) && absint( $existing['expires_at'] ) < time() ) { delete_option( self::OPTION_WRITE_LOCK ); }
		$token = function_exists( 'wp_generate_uuid4' ) ? wp_generate_uuid4() : uniqid( 'swi-', true );
		$lock = array( 'token' => $token, 'expires_at' => time() + 15 );
		return add_option( self::OPTION_WRITE_LOCK, $lock, '', false ) ? $token : false;
	}
	private static function release_write_lock( $token ) { $lock = get_option( self::OPTION_WRITE_LOCK, null ); if ( is_array( $lock ) && isset( $lock['token'] ) && hash_equals( (string) $lock['token'], (string) $token ) ) { delete_option( self::OPTION_WRITE_LOCK ); } }

	/** @param array<string,mixed> $config */
	public static function ensure_approved_snapshot( array $config ) {
		$snapshots = self::approved_snapshots();
		if ( empty( $snapshots ) ) { self::snapshot_config( $config, 'activation_baseline' ); }
		if ( 'approved' === (string) ( $config['approval_state'] ?? '' ) ) { self::snapshot_config( $config, 'founder_approved' ); }
	}
	/** @return array<int,array<string,mixed>> */
	public static function approved_snapshots() { $snapshots = get_option( self::OPTION_SNAPSHOTS, array() ); return is_array( $snapshots ) ? array_values( array_slice( $snapshots, -5 ) ) : array(); }
	/** @param array<string,mixed> $config */
	public static function snapshot_config( array $config, $reason ) {
		$safe = self::sanitize( array_merge( self::defaults(), $config ), false ); unset( $safe['updated_by'], $safe['approved_by'] );
		$entry = array( 'reason' => sanitize_key( $reason ), 'created_at' => gmdate( 'c' ), 'config_version' => absint( $safe['config_version'] ), 'hash' => hash( 'sha256', wp_json_encode( $safe ) ), 'config' => $safe );
		$snapshots = self::approved_snapshots(); $last = end( $snapshots ); if ( is_array( $last ) && isset( $last['hash'] ) && hash_equals( (string) $last['hash'], $entry['hash'] ) ) { return; }
		$snapshots[] = $entry; update_option( self::OPTION_SNAPSHOTS, array_values( array_slice( $snapshots, -5 ) ), false );
	}
	/** @return array<string,mixed>|WP_Error */
	public static function restore_snapshot( $index, $expected_version ) {
		$snapshots = self::approved_snapshots(); $index = absint( $index );
		if ( ! isset( $snapshots[ $index ]['config'] ) || ! is_array( $snapshots[ $index ]['config'] ) ) { return new WP_Error( 'swi_snapshot_missing', __( 'The selected rollback snapshot is unavailable.', 'sabri-welcome-intro' ), array( 'status' => 404 ) ); }
		$current = self::stored_config(); if ( absint( $expected_version ) !== absint( $current['config_version'] ) ) { return new WP_Error( 'swi_config_conflict', __( 'The settings changed in another session. Reload and try again.', 'sabri-welcome-intro' ), array( 'status' => 409 ) ); }
		$result = self::save( $snapshots[ $index ]['config'], $expected_version ); if ( ! is_wp_error( $result ) ) { self::record_audit( 'snapshot_restored', array( 'config' ), $result['config_version'] ); } return $result;
	}

	public static function record_audit( $event, array $changed_keys = array(), $version = 0 ) {
		$log = get_option( self::OPTION_AUDIT, array() ); $log = is_array( $log ) ? $log : array();
		$log[] = array( 'event' => sanitize_key( $event ), 'changed_keys' => array_values( array_map( 'sanitize_key', array_slice( $changed_keys, 0, 30 ) ) ), 'version' => absint( $version ), 'actor_id' => function_exists( 'get_current_user_id' ) ? absint( get_current_user_id() ) : 0, 'occurred_at' => gmdate( 'c' ) );
		update_option( self::OPTION_AUDIT, array_slice( $log, -100 ), false );
	}
	/** @return array<int,array<string,mixed>> */
	public static function audit_log() { $log = get_option( self::OPTION_AUDIT, array() ); return is_array( $log ) ? array_reverse( array_slice( $log, -100 ) ) : array(); }
	/** @return string[] */
	private static function changed_keys( array $before, array $after ) { $changed = array(); foreach ( $after as $key => $value ) { if ( in_array( $key, array( 'updated_at','updated_by','config_version' ), true ) ) { continue; } if ( ! array_key_exists( $key, $before ) || $before[ $key ] !== $value ) { $changed[] = $key; } } return $changed; }
	private static function sanitize_copy( $value, $max, $fallback ) { $value = trim( wp_strip_all_tags( (string) $value, true ) ); if ( '' === $value ) { return $fallback; } return function_exists( 'mb_substr' ) ? mb_substr( $value, 0, $max ) : substr( $value, 0, $max ); }
	private static function sanitize_language( $value ) { $value = preg_replace( '/[^A-Za-z0-9-]/', '', (string) $value ); return '' === $value ? 'en-US' : substr( $value, 0, 35 ); }
	private static function sanitize_semver( $value, $fallback ) { $value = trim( (string) $value ); return preg_match( '/^\d+\.\d+\.\d+(?:[-+][A-Za-z0-9.-]+)?$/', $value ) ? $value : $fallback; }
	/** @return array<string,array<string,string>> */
	private static function sanitize_localized_copy( $value ) {
		if ( ! is_array( $value ) ) { return array(); } $out = array();
		foreach ( array_slice( $value, 0, 20, true ) as $locale => $copy ) { if ( ! is_array( $copy ) ) { continue; } $locale_clean = self::sanitize_language( $locale ); $name = self::sanitize_copy( $copy['name'] ?? '', 120, '' ); $claim = self::sanitize_copy( $copy['claim'] ?? '', 280, '' ); if ( '' === $name || '' === $claim ) { continue; } $out[ $locale_clean ] = array( 'name' => $name, 'claim' => $claim, 'language' => self::sanitize_language( $copy['language'] ?? $locale_clean ) ); }
		return $out;
	}
	/** @param mixed $value @param string[] $fallback @return string[] */
	public static function sanitize_routes( $value, array $fallback = array() ) { if ( is_string( $value ) ) { $value = preg_split( '/\r\n|\r|\n|,/', $value ); } if ( ! is_array( $value ) ) { return $fallback; } $routes = array(); foreach ( array_slice( $value, 0, 100 ) as $route ) { if ( '*' === trim( (string) $route ) ) { $routes[] = '*'; continue; } $route = self::normalize_path( (string) $route ); if ( '' !== $route ) { $routes[] = $route; } } $routes = array_values( array_unique( $routes ) ); return empty( $routes ) ? $fallback : $routes; }
	public static function normalize_path( $path ) { $path = trim( wp_strip_all_tags( (string) $path, true ) ); if ( '' === $path ) { return ''; } $parsed = wp_parse_url( $path, PHP_URL_PATH ); if ( false === $parsed || null === $parsed ) { return ''; } $parsed = '/' . ltrim( rawurldecode( (string) $parsed ), '/' ); $parsed = preg_replace( '#/+#', '/', $parsed ); if ( strlen( $parsed ) > 200 || false !== strpos( $parsed, '..' ) ) { return ''; } return '/' === $parsed ? '/' : untrailingslashit( $parsed ); }
	private static function sanitize_datetime( $value ) { $value = trim( (string) $value ); if ( '' === $value ) { return ''; } try { $site_timezone = function_exists( 'wp_timezone' ) ? wp_timezone() : new DateTimeZone( 'UTC' ); $utc_timezone = new DateTimeZone( 'UTC' ); if ( preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value ) ) { $date = DateTimeImmutable::createFromFormat( '!Y-m-d\TH:i', $value, $site_timezone ); $errors = DateTimeImmutable::getLastErrors(); if ( false === $date || ( is_array( $errors ) && ( $errors['warning_count'] > 0 || $errors['error_count'] > 0 ) ) ) { return ''; } } else { $date = new DateTimeImmutable( $value, $site_timezone ); } return $date->setTimezone( $utc_timezone )->format( DATE_ATOM ); } catch ( Exception $exception ) { return ''; } }
}
