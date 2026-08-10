<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Analytics {
	const ALLOWED_EVENTS = array( 'shown', 'skipped', 'completed', 'closed', 'error', 'never_show', 'replay', 'variant_full', 'variant_light', 'variant_static' );
	const DAILY_CAP = 5000;
	public function record( $event, $version ) {
		$config = SWI_Config::get();
		if ( empty( $config['analytics_enabled'] ) || ! in_array( $event, self::ALLOWED_EVENTS, true ) ) { return false; }
		$day = gmdate( 'Y-m-d' ); $metrics = get_option( SWI_Config::OPTION_METRICS, array() ); $metrics = is_array( $metrics ) ? $metrics : array(); $bucket = isset( $metrics[ $day ] ) && is_array( $metrics[ $day ] ) ? $metrics[ $day ] : array(); $total = isset( $bucket['_total'] ) ? absint( $bucket['_total'] ) : 0;
		if ( $total >= self::DAILY_CAP ) { return false; }
		$key = 'v' . absint( $version ) . '_' . sanitize_key( $event ); $bucket[ $key ] = min( self::DAILY_CAP, absint( $bucket[ $key ] ?? 0 ) + 1 ); $bucket['_total'] = $total + 1; $metrics[ $day ] = $bucket; ksort( $metrics ); $metrics = array_slice( $metrics, -90, null, true );
		$updated = update_option( SWI_Config::OPTION_METRICS, $metrics, false ); if ( $updated ) { return true; } $persisted = get_option( SWI_Config::OPTION_METRICS, array() ); return is_array( $persisted ) && $persisted === $metrics;
	}
	/** @return array<string,mixed> */ public function all() { $metrics = get_option( SWI_Config::OPTION_METRICS, array() ); return is_array( $metrics ) ? $metrics : array(); }
}
