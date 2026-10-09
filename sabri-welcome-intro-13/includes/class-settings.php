<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Settings {
	const OPTION = 'swi_intro_config';
	const AUDIT_OPTION = 'swi_intro_audit';
	const SCHEMA_OPTION = 'swi_intro_schema_version';

	public static function register() {
		add_filter( 'pre_update_option_' . self::OPTION, array( __CLASS__, 'guard_option_write' ), 99, 3 );
		add_action( 'init', array( __CLASS__, 'maybe_upgrade' ), 2 );
	}

	public static function defaults() {
		return array(
			'enabled' => false,
			'status' => 'disabled',
			'config_version' => 1,
			'heading' => 'Sabri Homeopathy',
			'claim' => 'The Tridimensional Healing System of Soul, Vital Force, and Matter',
			'duration_ms' => 3200,
			'recurrence_days' => 30,
			'eligible_paths' => array( '/' ),
			'analytics_enabled' => false,
			'start_at' => '',
			'end_at' => '',
		);
	}

	public static function activate() {
		// Never advertise a migrated schema if the suppressive config write
		// failed or a competing filter changed the persisted result.
		if ( ! self::persist_suppressed_config() || ! self::persist_schema_version() ) {
			do_action( 'swi_intro_schema_upgrade_blocked', SWI_SCHEMA_VERSION );
		}
	}

	public static function maybe_upgrade() {
		if ( SWI_SCHEMA_VERSION === self::schema_version() ) { return; }
		if ( ! self::persist_suppressed_config() || ! self::persist_schema_version() ) {
			do_action( 'swi_intro_schema_upgrade_blocked', SWI_SCHEMA_VERSION );
			return;
		}
		// Retention remains active while public analytics collection stays disabled.
		do_action( 'swi_intro_schema_upgraded', SWI_SCHEMA_VERSION );
	}

	/**
	 * Persist and read back the exact disabled compatibility configuration.
	 *
	 * update_option() returns false for both no-op and failure. Therefore
	 * success is determined by the stored value, not the write return value.
	 * Never promote the schema while an old enabled/analytics row survives.
	 */
	private static function persist_suppressed_config() {
		$existing = get_option( self::OPTION, false );
		$current = is_array( $existing ) ? array_replace( self::defaults(), $existing ) : self::defaults();
		$expected = self::sanitize( $current, $current );
		if ( false === $existing ) {
			add_option( self::OPTION, $expected, '', false );
		} else {
			update_option( self::OPTION, $expected, false );
		}
		$persisted = get_option( self::OPTION, false );
		return is_array( $persisted ) && $persisted === $expected;
	}

	/** Verify the persisted schema row before claiming migration success. */
	private static function persist_schema_version() {
		update_option( self::SCHEMA_OPTION, SWI_SCHEMA_VERSION, false );
		return SWI_SCHEMA_VERSION === self::schema_version();
	}

	public static function stored() {
		$stored = get_option( self::OPTION, array() );
		return array_replace( self::defaults(), is_array( $stored ) ? $stored : array() );
	}

	public static function schema_version() {
		$value = get_option( self::SCHEMA_OPTION, '' );
		return is_string( $value ) ? $value : '';
	}

	public static function get() {
		$config = self::stored();
		// Even a schema-current option row may be corrupted after migration.
		$config = self::sanitize( $config, $config );
		$config['enabled'] = false;
		$config['status'] = 'disabled';
		$config['analytics_enabled'] = false;
		return $config;
	}

	public static function sanitize( array $input, array $base = array() ) {
		$base = array_replace( self::defaults(), $base );
		$out = $base;
		if ( array_key_exists( 'enabled', $input ) ) { $out['enabled'] = self::to_bool( $input['enabled'] ); }
		if ( isset( $input['status'] ) ) {
			$status = is_string( $input['status'] ) ? sanitize_key( $input['status'] ) : 'disabled';
			$out['status'] = in_array( $status, array( 'active', 'disabled' ), true ) ? $status : $base['status'];
		}
		if ( isset( $input['heading'] ) ) { $out['heading'] = sanitize_text_field( is_string( $input['heading'] ) ? $input['heading'] : self::defaults()['heading'] ); }
		if ( isset( $input['claim'] ) ) { $out['claim'] = sanitize_textarea_field( is_string( $input['claim'] ) ? $input['claim'] : self::defaults()['claim'] ); }
		if ( isset( $input['duration_ms'] ) ) {
			$value = $input['duration_ms'];
			$out['duration_ms'] = is_int( $value ) || ( is_string( $value ) && ctype_digit( $value ) )
				? min( 12000, max( 800, absint( $value ) ) ) : self::defaults()['duration_ms'];
		}
		if ( isset( $input['recurrence_days'] ) ) {
			$value = $input['recurrence_days'];
			$out['recurrence_days'] = is_int( $value ) || ( is_string( $value ) && ctype_digit( $value ) )
				? min( 365, max( 30, absint( $value ) ) ) : self::defaults()['recurrence_days'];
		}
		if ( isset( $input['eligible_paths'] ) ) {
			$paths = is_array( $input['eligible_paths'] ) ? $input['eligible_paths']
				: ( is_string( $input['eligible_paths'] ) ? preg_split( '/[\r\n,]+/', $input['eligible_paths'] ) : array() );
			$clean = array();
			foreach ( $paths as $path ) {
				$path = is_string( $path ) ? self::normalize_path( $path ) : '';
				if ( '' !== $path ) { $clean[] = $path; }
			}
			$out['eligible_paths'] = array_values( array_unique( $clean ) );
			if ( empty( $out['eligible_paths'] ) ) { $out['eligible_paths'] = array( '/' ); }
		}
		if ( array_key_exists( 'analytics_enabled', $input ) ) { $out['analytics_enabled'] = self::to_bool( $input['analytics_enabled'] ); }
		foreach ( array( 'start_at', 'end_at' ) as $date_key ) {
			if ( isset( $input[ $date_key ] ) ) { $out[ $date_key ] = self::sanitize_datetime( $input[ $date_key ] ); }
		}
		/*
		 * Current cross-file governance classifies File 13 as historical
		 * compatibility only. File 20 owns invocation/frequency and File 25 owns
		 * presentation. Legacy public activation and analytics therefore remain
		 * fail-closed even when stale callers submit old settings.
		 */
		$out['enabled'] = false;
		$out['status'] = 'disabled';
		$out['analytics_enabled'] = false;
		$revision = $base['config_version'] ?? 1;
		$out['config_version'] = is_int( $revision ) || ( is_string( $revision ) && ctype_digit( $revision ) )
			? max( 1, absint( $revision ) ) : 1;
		return $out;
	}

	public static function update( array $input, $expected_revision, $actor_id = 0 ) {
		// Protect the persistence boundary even if a future internal caller bypasses admin/REST gates.
		$current_actor = get_current_user_id();
		if ( ! is_user_logged_in() || ! Authorization::can_manage( 'manage_intro' ) || ! is_int( $current_actor ) || $current_actor <= 0 ) {
			return new \WP_Error( 'swi_forbidden', __( 'You are not authorized to change the welcome intro.', SWI_TEXT_DOMAIN ), array( 'status' => 403 ) );
		}
		if ( 0 !== $actor_id && ( ! is_int( $actor_id ) || $actor_id !== $current_actor ) ) {
			return new \WP_Error( 'swi_actor_mismatch', __( 'The audit actor does not match the current operator.', SWI_TEXT_DOMAIN ), array( 'status' => 403 ) );
		}
		if ( ! ( is_int( $expected_revision ) && $expected_revision > 0 )
			&& ! ( is_string( $expected_revision ) && ctype_digit( $expected_revision ) && '0' !== $expected_revision && (int) $expected_revision > 0 ) ) {
			return new \WP_Error( 'swi_invalid_revision', __( 'A valid configuration revision is required.', SWI_TEXT_DOMAIN ), array( 'status' => 400 ) );
		}
		$actor_id = $current_actor;
		global $wpdb;

		// Compare-and-swap against the exact serialized row so two administrators
		// cannot both save the same revision and silently overwrite each other.
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT option_id, option_value FROM {$wpdb->options} WHERE option_name=%s LIMIT 1",
				self::OPTION
			),
			ARRAY_A
		); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

		if ( ! is_array( $row ) || ! array_key_exists( 'option_value', $row ) ) {
			return new \WP_Error( 'swi_config_missing', __( 'The welcome-intro configuration is missing.', SWI_TEXT_DOMAIN ), array( 'status' => 409 ) );
		}

		$stored = maybe_unserialize( $row['option_value'] );
		$stored = is_array( $stored ) ? $stored : array();
		$current = array_replace( self::defaults(), $stored );
		$revision_value = $current['config_version'];
		if ( ! ( is_int( $revision_value ) && $revision_value > 0 )
			&& ! ( is_string( $revision_value ) && ctype_digit( $revision_value ) && (int) $revision_value > 0 ) ) {
			return new \WP_Error( 'swi_corrupt_revision', __( 'The stored configuration revision is invalid.', SWI_TEXT_DOMAIN ), array( 'status' => 409 ) );
		}
		$expected_revision = absint( $expected_revision );
		$current_revision = absint( $revision_value );
		if ( $expected_revision !== $current_revision ) {
			return new \WP_Error( 'swi_stale_config', __( 'The configuration changed since you opened it. Reload before saving.', SWI_TEXT_DOMAIN ), array( 'status' => 409, 'current_revision' => $current_revision ) );
		}

		$next = self::sanitize( $input, $current );
		$next['config_version'] = $current_revision + 1;
		$serialized_next = maybe_serialize( $next );
		$updated = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->options} SET option_value=%s WHERE option_id=%d AND option_name=%s AND option_value=%s",
				$serialized_next,
				absint( $row['option_id'] ),
				self::OPTION,
				(string) $row['option_value']
			)
		); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

		if ( 1 !== $updated ) {
			wp_cache_delete( self::OPTION, 'options' );
			wp_cache_delete( 'alloptions', 'options' ); // Direct SQL may race an autoloaded legacy row.
			return new \WP_Error( 'swi_stale_config', __( 'The configuration changed before your save completed. Reload before saving.', SWI_TEXT_DOMAIN ), array( 'status' => 409 ) );
		}

		wp_cache_delete( self::OPTION, 'options' );
		wp_cache_delete( 'alloptions', 'options' ); // WordPress stores autoloaded options under this key.
		self::append_audit( $current, $next, absint( $actor_id ) );
		do_action( 'swi_intro_config_updated', $next, $current );
		return $next;
	}

	public static function guard_option_write( $new_value, $old_value, $option ) {
		unset( $option );
		if ( ! is_array( $new_value ) ) { return is_array( $old_value ) ? $old_value : self::defaults(); }
		$old_value = is_array( $old_value ) ? array_replace( self::defaults(), $old_value ) : self::defaults();
		$clean = self::sanitize( $new_value, $old_value );
		if ( isset( $new_value['config_version'] ) ) {
			$revision = $new_value['config_version'];
			$clean['config_version'] = is_int( $revision ) || ( is_string( $revision ) && ctype_digit( $revision ) )
				? max( 1, absint( $revision ) ) : 1;
		}
		return $clean;
	}

	private static function append_audit( array $before, array $after, $actor_id ) {
		global $wpdb;

		$changed = array();
		foreach ( $after as $key => $value ) {
			if ( ! array_key_exists( $key, $before ) || $before[ $key ] !== $value ) { $changed[] = sanitize_key( $key ); }
		}
		$record = array(
			'at' => gmdate( 'c' ),
			'actor_id' => absint( $actor_id ),
			'from' => absint( $before['config_version'] ),
			'to' => absint( $after['config_version'] ),
			'changed' => array_values( array_unique( $changed ) ),
		);

		if ( false === get_option( self::AUDIT_OPTION, false ) ) {
			add_option( self::AUDIT_OPTION, array(), '', false );
		}

		for ( $attempt = 0; $attempt < 3; $attempt++ ) {
			$db_row = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT option_id, option_value FROM {$wpdb->options} WHERE option_name=%s LIMIT 1",
					self::AUDIT_OPTION
				),
				ARRAY_A
			); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			if ( ! is_array( $db_row ) || ! array_key_exists( 'option_value', $db_row ) ) { continue; }

			$audit = maybe_unserialize( $db_row['option_value'] );
			$audit = is_array( $audit ) ? $audit : array();
			$audit[] = $record;
			if ( count( $audit ) > 100 ) { $audit = array_slice( $audit, -100 ); }

			$updated = $wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->options} SET option_value=%s WHERE option_id=%d AND option_name=%s AND option_value=%s",
					maybe_serialize( $audit ),
					absint( $db_row['option_id'] ),
					self::AUDIT_OPTION,
					(string) $db_row['option_value']
				)
			); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			if ( 1 === $updated ) {
				wp_cache_delete( self::AUDIT_OPTION, 'options' );
				wp_cache_delete( 'alloptions', 'options' ); // Legacy audit option may be autoloaded.
				return;
			}
		}

		$gap = array( 'at' => gmdate( 'c' ), 'from' => $record['from'], 'to' => $record['to'] );
		update_option( 'swi_intro_audit_gap', $gap, false );
		do_action( 'swi_intro_audit_contention', $record );
	}

	public static function active_now( array $config = null ) {
		unset( $config );
		return false;
	}

	public static function normalize_path( $path ) {
		if ( ! is_string( $path ) ) { return ''; }
		$path = trim( $path );
		if ( '' === $path ) { return ''; }
		$parsed = wp_parse_url( $path, PHP_URL_PATH );
		$path = is_string( $parsed ) ? $parsed : $path;
		$path = '/' . ltrim( preg_replace( '#/+#', '/', $path ), '/' );
		return '/' === $path ? '/' : untrailingslashit( $path );
	}

	private static function sanitize_datetime( $value ) {
		if ( ! is_string( $value ) ) { return ''; }
		$value = trim( sanitize_text_field( $value ) );
		if ( '' === $value ) { return ''; }
		$ts = strtotime( $value );
		return false === $ts ? '' : gmdate( 'c', $ts );
	}

	private static function timestamp( $value ) {
		if ( ! is_string( $value ) || '' === trim( $value ) ) { return 0; }
		$ts = strtotime( $value );
		return false === $ts ? 0 : $ts;
	}

	private static function to_bool( $value ) {
		return in_array( $value, array( true, 1, '1', 'yes', 'on', 'true' ), true );
	}
}
