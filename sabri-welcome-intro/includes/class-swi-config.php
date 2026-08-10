<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Config {
	const OPTION_CONFIG  = 'swi_config';
	const OPTION_SCHEMA  = 'swi_schema_version';
	const OPTION_AUDIT   = 'swi_audit_log';
	const OPTION_METRICS = 'swi_aggregate_metrics';
	const USER_META_LAST = 'swi_last_dismissed_at';
	const USER_META_VER  = 'swi_last_config_version';
	const COOKIE_NAME    = 'swi_seen_at_v1';
	const SESSION_KEY    = 'swi_seen_session_v1';
	const LOCAL_KEY      = 'swi_seen_at_v1';
	const CLAIM_KEY      = 'swi_claim_v1';

	/**
	 * Return the complete safe default configuration.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults() {
		return array(
			'enabled'              => 1,
			'config_version'       => 1,
			'frequency_days'       => 30,
			'duration_ms'          => 0,
			'reduced_duration_ms'  => 900,
			'brand_name'           => 'Sabri Social Homeopathy Platform',
			'brand_claim'          => 'The Tridimensional Healing System of Soul, Vital Force, and Matter',
			'brand_language'       => 'en-US',
			'eligible_routes'      => array( '/' ),
			'suppressed_prefixes'  => array(
				'/wp-login.php',
				'/wp-admin',
				'/login',
				'/register',
				'/signup',
				'/account',
				'/settings',
				'/support',
				'/appointments/book',
				'/appointment',
				'/clinical',
				'/emergency',
				'/checkout',
				'/cart',
			),
			'starts_at'            => '',
			'ends_at'              => '',
			'analytics_enabled'    => 0,
			'updated_at'           => '',
			'updated_by'           => 0,
		);
	}

	/**
	 * Return the normalized current config.
	 *
	 * @return array<string,mixed>
	 */
	public static function get() {
		$stored = get_option( self::OPTION_CONFIG, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$config = self::sanitize( array_merge( self::defaults(), $stored ), false );
		$filtered = apply_filters( 'swi_runtime_config', $config );
		if ( ! is_array( $filtered ) ) {
			return $config;
		}

		$runtime = self::sanitize( array_merge( $config, $filtered ), false );
		foreach ( array( 'config_version', 'updated_at', 'updated_by' ) as $governed_key ) {
			$runtime[ $governed_key ] = $config[ $governed_key ];
		}

		// Runtime integrations may further restrict local governance, but may never
		// re-enable an administrator kill switch or opt a site into analytics.
		$runtime['enabled'] = ! empty( $config['enabled'] ) && ! empty( $runtime['enabled'] ) ? 1 : 0;
		$runtime['analytics_enabled'] = ! empty( $config['analytics_enabled'] ) && ! empty( $runtime['analytics_enabled'] ) ? 1 : 0;
		return $runtime;
	}

	/**
	 * Sanitize a configuration payload.
	 *
	 * @param array<string,mixed> $raw Raw values.
	 * @param bool                $increment_version Whether to increment config version.
	 * @return array<string,mixed>
	 */
	public static function sanitize( array $raw, $increment_version = true ) {
		$defaults = self::defaults();
		$current  = get_option( self::OPTION_CONFIG, array() );
		$current  = is_array( $current ) ? $current : array();
		$version  = isset( $current['config_version'] ) ? absint( $current['config_version'] ) : 0;

		$config = array();
		$config['enabled']             = empty( $raw['enabled'] ) ? 0 : 1;
		$requested_version = isset( $raw['config_version'] ) ? absint( $raw['config_version'] ) : $version;
		$config['config_version']      = $increment_version ? max( 1, $version + 1 ) : max( 1, $requested_version ?: 1 );
		$config['frequency_days']      = min( 365, max( 30, absint( $raw['frequency_days'] ?? $defaults['frequency_days'] ) ) );
		$requested_duration            = absint( $raw['duration_ms'] ?? $defaults['duration_ms'] );
		$config['duration_ms']         = 0 === $requested_duration ? 0 : min( 30000, max( 1200, $requested_duration ) );
		$config['reduced_duration_ms'] = min( 1500, max( 250, absint( $raw['reduced_duration_ms'] ?? $defaults['reduced_duration_ms'] ) ) );
		$config['brand_name']          = self::sanitize_copy( $raw['brand_name'] ?? $defaults['brand_name'], 120, $defaults['brand_name'] );
		$config['brand_claim']         = self::sanitize_copy( $raw['brand_claim'] ?? $defaults['brand_claim'], 280, $defaults['brand_claim'] );
		$config['brand_language']      = self::sanitize_language( $raw['brand_language'] ?? $defaults['brand_language'] );
		$config['eligible_routes']     = self::sanitize_routes( $raw['eligible_routes'] ?? $defaults['eligible_routes'], array( '/' ) );
		$config['suppressed_prefixes'] = self::sanitize_routes( $raw['suppressed_prefixes'] ?? $defaults['suppressed_prefixes'], $defaults['suppressed_prefixes'] );
		$config['starts_at']           = self::sanitize_datetime( $raw['starts_at'] ?? '' );
		$config['ends_at']             = self::sanitize_datetime( $raw['ends_at'] ?? '' );
		$config['analytics_enabled']   = empty( $raw['analytics_enabled'] ) ? 0 : 1;
		if ( $increment_version ) {
			$config['updated_at'] = gmdate( 'c' );
			$config['updated_by'] = function_exists( 'get_current_user_id' ) ? absint( get_current_user_id() ) : 0;
		} else {
			$config['updated_at'] = self::sanitize_datetime( $raw['updated_at'] ?? '' );
			$config['updated_by'] = absint( $raw['updated_by'] ?? 0 );
		}

		if ( '' !== $config['starts_at'] && '' !== $config['ends_at'] && strtotime( $config['starts_at'] ) > strtotime( $config['ends_at'] ) ) {
			$config['starts_at'] = '';
			$config['ends_at']   = '';
		}

		return $config;
	}

	/**
	 * Save config with optimistic version checking and a minimized audit trail.
	 *
	 * @param array<string,mixed> $raw Raw submitted config.
	 * @param int                 $expected_version Expected current version.
	 * @return array<string,mixed>|WP_Error
	 */
	public static function save( array $raw, $expected_version ) {
		$current = self::get();
		if ( absint( $expected_version ) !== absint( $current['config_version'] ) ) {
			return new WP_Error( 'swi_config_conflict', __( 'The settings changed in another session. Reload and try again.', 'sabri-welcome-intro' ) );
		}

		$next = self::sanitize( $raw, true );
		$ok   = update_option( self::OPTION_CONFIG, $next, false );
		if ( ! $ok && $next !== $current ) {
			return new WP_Error( 'swi_config_write_failed', __( 'The settings could not be saved.', 'sabri-welcome-intro' ) );
		}

		self::record_audit( 'config_updated', self::changed_keys( $current, $next ), $next['config_version'] );
		do_action( 'swi_config_updated', $next, $current );
		return $next;
	}

	/**
	 * Bounded, minimized audit record. No copy values or network identifiers are stored.
	 *
	 * @param string   $event Event name.
	 * @param string[] $changed_keys Changed keys.
	 * @param int      $version Config version.
	 * @return void
	 */
	public static function record_audit( $event, array $changed_keys = array(), $version = 0 ) {
		$log = get_option( self::OPTION_AUDIT, array() );
		$log = is_array( $log ) ? $log : array();
		$log[] = array(
			'event'        => sanitize_key( $event ),
			'changed_keys' => array_values( array_map( 'sanitize_key', array_slice( $changed_keys, 0, 30 ) ) ),
			'version'      => absint( $version ),
			'actor_id'     => function_exists( 'get_current_user_id' ) ? absint( get_current_user_id() ) : 0,
			'occurred_at'  => gmdate( 'c' ),
		);
		$log = array_slice( $log, -100 );
		update_option( self::OPTION_AUDIT, $log, false );
	}

	/** @return array<int,array<string,mixed>> */
	public static function audit_log() {
		$log = get_option( self::OPTION_AUDIT, array() );
		return is_array( $log ) ? array_reverse( array_slice( $log, -100 ) ) : array();
	}

	/** @return string[] */
	private static function changed_keys( array $before, array $after ) {
		$changed = array();
		foreach ( $after as $key => $value ) {
			if ( in_array( $key, array( 'updated_at', 'updated_by', 'config_version' ), true ) ) {
				continue;
			}
			if ( ! array_key_exists( $key, $before ) || $before[ $key ] !== $value ) {
				$changed[] = $key;
			}
		}
		return $changed;
	}

	private static function sanitize_copy( $value, $max, $fallback ) {
		$value = trim( wp_strip_all_tags( (string) $value, true ) );
		if ( '' === $value ) {
			return $fallback;
		}
		if ( function_exists( 'mb_substr' ) ) {
			return mb_substr( $value, 0, $max );
		}
		return substr( $value, 0, $max );
	}

	private static function sanitize_language( $value ) {
		$value = preg_replace( '/[^A-Za-z0-9-]/', '', (string) $value );
		return '' === $value ? 'en-US' : substr( $value, 0, 35 );
	}

	/**
	 * @param mixed    $value Raw list or newline string.
	 * @param string[] $fallback Fallback routes.
	 * @return string[]
	 */
	public static function sanitize_routes( $value, array $fallback = array() ) {
		if ( is_string( $value ) ) {
			$value = preg_split( '/\r\n|\r|\n|,/', $value );
		}
		if ( ! is_array( $value ) ) {
			return $fallback;
		}
		$routes = array();
		foreach ( array_slice( $value, 0, 100 ) as $route ) {
			if ( '*' === trim( (string) $route ) ) {
				$routes[] = '*';
				continue;
			}
			$route = self::normalize_path( (string) $route );
			if ( '' !== $route ) {
				$routes[] = $route;
			}
		}
		$routes = array_values( array_unique( $routes ) );
		return empty( $routes ) ? $fallback : $routes;
	}

	public static function normalize_path( $path ) {
		$path = trim( wp_strip_all_tags( (string) $path, true ) );
		if ( '' === $path ) {
			return '';
		}
		$parsed = wp_parse_url( $path, PHP_URL_PATH );
		if ( false === $parsed || null === $parsed ) {
			return '';
		}
		$parsed = '/' . ltrim( rawurldecode( (string) $parsed ), '/' );
		$parsed = preg_replace( '#/+#', '/', $parsed );
		if ( strlen( $parsed ) > 200 || false !== strpos( $parsed, '..' ) ) {
			return '';
		}
		return '/' === $parsed ? '/' : untrailingslashit( $parsed );
	}

	private static function sanitize_datetime( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}

		try {
			$site_timezone = function_exists( 'wp_timezone' ) ? wp_timezone() : new DateTimeZone( 'UTC' );
			$utc_timezone  = new DateTimeZone( 'UTC' );
			if ( preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value ) ) {
				$date = DateTimeImmutable::createFromFormat( '!Y-m-d\TH:i', $value, $site_timezone );
				$errors = DateTimeImmutable::getLastErrors();
				if ( false === $date || ( is_array( $errors ) && ( $errors['warning_count'] > 0 || $errors['error_count'] > 0 ) ) ) {
					return '';
				}
			} else {
				$date = new DateTimeImmutable( $value, $site_timezone );
			}
			return $date->setTimezone( $utc_timezone )->format( DATE_ATOM );
		} catch ( Exception $exception ) {
			return '';
		}
	}
}
