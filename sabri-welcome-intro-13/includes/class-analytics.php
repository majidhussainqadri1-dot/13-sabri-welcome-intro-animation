<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Analytics {
	const NONCE_ACTION = 'swi_intro_event';
	const OPTION_PREFIX = 'swi_intro_agg_';
	const CLEANUP_HOOK = 'swi_intro_cleanup_aggregates';

	public static function register() {
		add_action( 'wp_ajax_swi_intro_event', array( __CLASS__, 'ajax_event' ) );
		add_action( 'wp_ajax_nopriv_swi_intro_event', array( __CLASS__, 'ajax_event' ) );
		add_action( self::CLEANUP_HOOK, array( __CLASS__, 'cleanup' ) );\n\t\tadd_action( 'init', array( __CLASS__, 'schedule_cleanup' ), 20 );
	}

	public static function schedule_cleanup() {
		if ( ! wp_next_scheduled( self::CLEANUP_HOOK ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', self::CLEANUP_HOOK );
		}
	}

	public static function ajax_event() {
		$config = Settings::get();
		if ( empty( $config['analytics_enabled'] ) ) { wp_send_json_error( array( 'code' => 'analytics_disabled' ), 404 ); }
		check_ajax_referer( self::NONCE_ACTION );
		if ( ! self::same_origin_request() ) { wp_send_json_error( array( 'code' => 'origin_rejected' ), 403 ); }

		$event = isset( $_POST['event'] ) ? sanitize_key( wp_unslash( $_POST['event'] ) ) : '';
		$version = isset( $_POST['version'] ) ? absint( $_POST['version'] ) : 0;
		if ( ! in_array( $event, array( 'shown', 'skipped', 'completed' ), true ) || $version < 1 ) {
			wp_send_json_error( array( 'code' => 'invalid_event' ), 400 );
		}
		self::record( $event, $version );
		wp_send_json_success( array( 'accepted' => true ) );
	}

	public static function record( $event, $version ) {
		// Aggregate only: no IP, account, cookie ID, URL history, user-agent, or fingerprint is stored.
		$key = self::OPTION_PREFIX . gmdate( 'Ymd' ) . '_v' . absint( $version );
		$row = get_option( $key, array( 'shown' => 0, 'skipped' => 0, 'completed' => 0 ) );
		$row = is_array( $row ) ? array_replace( array( 'shown' => 0, 'skipped' => 0, 'completed' => 0 ), $row ) : array( 'shown' => 0, 'skipped' => 0, 'completed' => 0 );
		$row[ $event ] = min( PHP_INT_MAX, absint( $row[ $event ] ) + 1 );
		update_option( $key, $row, false );

		$event_name = array( 'shown' => 'WelcomeIntroShown.v1', 'skipped' => 'WelcomeIntroSkipped.v1', 'completed' => 'WelcomeIntroCompleted.v1' )[ $event ];
		do_action( 'swi_intro_event_published', $event_name, array( 'version' => absint( $version ), 'date' => gmdate( 'Y-m-d' ) ) );
	}

	public static function cleanup() {
		global $wpdb;
		$like = $wpdb->esc_like( self::OPTION_PREFIX ) . '%';
		$names = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT 500", $like ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$cutoff = (int) gmdate( 'Ymd', time() - ( 90 * DAY_IN_SECONDS ) );
		foreach ( (array) $names as $name ) {
			if ( preg_match( '/^' . preg_quote( self::OPTION_PREFIX, '/' ) . '(\d{8})_v\d+$/', (string) $name, $m ) && absint( $m[1] ) < $cutoff ) {
				delete_option( $name );
			}
		}
	}

	private static function same_origin_request() {
		$origin = isset( $_SERVER['HTTP_ORIGIN'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_ORIGIN'] ) ) : '';
		if ( '' === $origin ) { return true; }
		$home = wp_parse_url( home_url( '/' ) );
		$src = wp_parse_url( $origin );
		if ( ! is_array( $home ) || ! is_array( $src ) || empty( $home['host'] ) || empty( $src['host'] ) ) { return false; }
		$home_scheme = strtolower( (string) ( $home['scheme'] ?? '' ) );
		$src_scheme = strtolower( (string) ( $src['scheme'] ?? '' ) );
		$home_host = strtolower( rtrim( (string) $home['host'], '.' ) );
		$src_host = strtolower( rtrim( (string) $src['host'], '.' ) );
		return hash_equals( $home_scheme, $src_scheme ) && hash_equals( $home_host, $src_host );
	}
}
