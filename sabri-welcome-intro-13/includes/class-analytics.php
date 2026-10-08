<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Analytics {
	const NONCE_ACTION = 'swi_intro_event';
	const OPTION_PREFIX = 'swi_intro_agg_';
	const CLEANUP_HOOK = 'swi_intro_cleanup_aggregates';

	public static function register_retention() {
		add_action( self::CLEANUP_HOOK, array( __CLASS__, 'cleanup' ) );
		add_action( 'init', array( __CLASS__, 'schedule_cleanup' ), 20 );
	}

	public static function schedule_cleanup() {
		if ( ! wp_next_scheduled( self::CLEANUP_HOOK ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', self::CLEANUP_HOOK );
		}
	}

	public static function ajax_event() {
		$config = Settings::get();
		if ( empty( $config['analytics_enabled'] ) ) {
			wp_send_json_error( array( 'code' => 'analytics_disabled' ), 404 );
		}
		check_ajax_referer( self::NONCE_ACTION );
		if ( ! self::same_origin_request() ) {
			wp_send_json_error( array( 'code' => 'origin_rejected' ), 403 );
		}

		$event = isset( $_POST['event'] ) ? sanitize_key( wp_unslash( $_POST['event'] ) ) : '';
		$version = isset( $_POST['version'] ) ? absint( $_POST['version'] ) : 0;
		if ( ! in_array( $event, array( 'shown', 'skipped', 'completed' ), true )
			|| $version < 1
			|| $version !== absint( $config['config_version'] ) ) {
			wp_send_json_error( array( 'code' => 'invalid_event' ), 400 );
		}

		self::record( $event, $version );
		wp_send_json_success( array( 'accepted' => true ) );
	}

	public static function record( $event, $version ) {
		global $wpdb;

		// Aggregate only: no IP, account, cookie ID, URL history, user-agent, or fingerprint is stored.
		$key = self::OPTION_PREFIX . gmdate( 'Ymd' ) . '_v' . absint( $version );
		$empty = array( 'shown' => 0, 'skipped' => 0, 'completed' => 0 );
		if ( false === get_option( $key, false ) ) {
			add_option( $key, $empty, '', false );
		}

		$recorded = false;
		for ( $attempt = 0; $attempt < 3; $attempt++ ) {
			$db_row = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT option_id, option_value FROM {$wpdb->options} WHERE option_name=%s LIMIT 1",
					$key
				),
				ARRAY_A
			); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

			if ( ! is_array( $db_row ) || ! array_key_exists( 'option_value', $db_row ) ) { continue; }
			$row = maybe_unserialize( $db_row['option_value'] );
			$row = is_array( $row ) ? array_replace( $empty, $row ) : $empty;
			$row[ $event ] = min( PHP_INT_MAX, absint( $row[ $event ] ) + 1 );

			$updated = $wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->options} SET option_value=%s WHERE option_id=%d AND option_name=%s AND option_value=%s",
					maybe_serialize( $row ),
					absint( $db_row['option_id'] ),
					$key,
					(string) $db_row['option_value']
				)
			); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

			if ( 1 === $updated ) {
				wp_cache_delete( $key, 'options' );
				$recorded = true;
				break;
			}
		}
		if ( ! $recorded ) {
			do_action( 'swi_intro_analytics_contention', $event, absint( $version ) );
			return;
		}

		$event_name = array(
			'shown' => 'WelcomeIntroShown.v1',
			'skipped' => 'WelcomeIntroSkipped.v1',
			'completed' => 'WelcomeIntroCompleted.v1',
		)[ $event ];
		do_action( 'swi_intro_event_published', $event_name, array( 'version' => absint( $version ), 'date' => gmdate( 'Y-m-d' ) ) );
	}

	public static function cleanup() {
		global $wpdb;
		$like = $wpdb->esc_like( self::OPTION_PREFIX ) . '%';
		$cutoff = (int) gmdate( 'Ymd', time() - ( 90 * DAY_IN_SECONDS ) );
		$cursor = 0;
		do {
			$rows = $wpdb->get_results(
				$wpdb->prepare( "SELECT option_id, option_name FROM {$wpdb->options} WHERE option_name LIKE %s AND option_id > %d ORDER BY option_id ASC LIMIT 500", $like, $cursor ),
				ARRAY_A
			);
			if ( ! is_array( $rows ) || ! $rows ) { break; }
			foreach ( $rows as $row ) {
				$cursor = max( $cursor, absint( $row['option_id'] ) );
				$name = (string) $row['option_name'];
				if ( preg_match( '/^' . preg_quote( self::OPTION_PREFIX, '/' ) . '(\\d{8})_v\\d+$/', $name, $m ) && absint( $m[1] ) < $cutoff ) {
					delete_option( $name );
				}
			}
		} while ( count( $rows ) === 500 );
	}

	private static function same_origin_request() {
		$source = isset( $_SERVER['HTTP_ORIGIN'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_ORIGIN'] ) ) : '';
		if ( '' === $source && isset( $_SERVER['HTTP_REFERER'] ) ) {
			$source = esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) );
		}
		if ( '' === $source ) { return false; }

		$home = wp_parse_url( home_url( '/' ) );
		$src = wp_parse_url( $source );
		if ( ! is_array( $home ) || ! is_array( $src ) || empty( $home['host'] ) || empty( $src['host'] ) ) { return false; }

		$home_scheme = strtolower( (string) ( $home['scheme'] ?? '' ) );
		$src_scheme = strtolower( (string) ( $src['scheme'] ?? '' ) );
		$home_host = strtolower( rtrim( (string) $home['host'], '.' ) );
		$src_host = strtolower( rtrim( (string) $src['host'], '.' ) );
		$home_port = isset( $home['port'] ) ? absint( $home['port'] ) : ( 'https' === $home_scheme ? 443 : 80 );
		$src_port = isset( $src['port'] ) ? absint( $src['port'] ) : ( 'https' === $src_scheme ? 443 : 80 );

		return hash_equals( $home_scheme, $src_scheme )
			&& hash_equals( $home_host, $src_host )
			&& $home_port === $src_port;
	}
}
