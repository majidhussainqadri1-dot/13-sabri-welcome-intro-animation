<?php
require __DIR__ . '/wp-stubs.php';
if ( ! function_exists( 'wp_timezone' ) ) { function wp_timezone() { return new DateTimeZone( 'Asia/Karachi' ); } }
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-config.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-eligibility.php';

$passed = 0; $failed = 0;
function f13_governance_check( $condition, $message ) {
	global $passed, $failed;
	if ( $condition ) { ++$passed; echo "PASS: $message\n"; }
	else { ++$failed; echo "FAIL: $message\n"; }
}

swi_test_reset();
$stored = SWI_Config::defaults();
$stored['frequency_days'] = 45;
$stored['eligible_routes'] = array( '/', '/about' );
$stored['starts_at'] = '2026-08-10T00:00:00+00:00';
$stored['ends_at'] = '2026-08-20T00:00:00+00:00';
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = $stored;
add_filter( 'swi_runtime_config', static function ( $config ) {
	$config['frequency_days'] = 30;
	$config['eligible_routes'] = array( '*' );
	$config['suppressed_prefixes'] = array( '/custom-suppression' );
	$config['starts_at'] = '2026-08-01T00:00:00+00:00';
	$config['ends_at'] = '2026-08-30T00:00:00+00:00';
	$config['analytics_enabled'] = 1;
	$config['brand_claim'] = 'Companion-owned copy is forbidden';
	return $config;
} );
$config = SWI_Config::get();
f13_governance_check( 45 === $config['frequency_days'], 'runtime integration cannot shorten the locally governed recurrence' );
f13_governance_check( array( '/', '/about' ) === $config['eligible_routes'], 'runtime integration cannot broaden locally eligible routes' );
f13_governance_check( in_array( '/clinical', $config['suppressed_prefixes'], true ) && in_array( '/custom-suppression', $config['suppressed_prefixes'], true ), 'runtime suppression is additive and cannot remove protected route suppressions' );
f13_governance_check( '2026-08-10T00:00:00+00:00' === $config['starts_at'] && '2026-08-20T00:00:00+00:00' === $config['ends_at'], 'runtime integration cannot widen an approved schedule window' );
f13_governance_check( 0 === $config['analytics_enabled'], 'runtime integration cannot opt the site into analytics' );
f13_governance_check( $stored['brand_claim'] === $config['brand_claim'], 'runtime integration cannot replace File 13 governed brand copy' );

swi_test_reset();
$stored = SWI_Config::defaults();
$stored['frequency_days'] = 30;
$stored['eligible_routes'] = array( '/', '/about' );
$stored['starts_at'] = '2026-08-10T00:00:00+00:00';
$stored['ends_at'] = '2026-08-20T00:00:00+00:00';
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = $stored;
add_filter( 'swi_runtime_config', static function ( $config ) {
	$config['frequency_days'] = 60;
	$config['eligible_routes'] = array( '/about' );
	$config['suppressed_prefixes'][] = '/private-task';
	$config['starts_at'] = '2026-08-15T00:00:00+00:00';
	$config['ends_at'] = '2026-08-18T00:00:00+00:00';
	return $config;
} );
$config = SWI_Config::get();
f13_governance_check( 60 === $config['frequency_days'] && array( '/about' ) === $config['eligible_routes'], 'runtime integration may impose stricter recurrence and route eligibility' );
f13_governance_check( '2026-08-15T00:00:00+00:00' === $config['starts_at'] && '2026-08-18T00:00:00+00:00' === $config['ends_at'], 'runtime integration may narrow the approved schedule window' );

swi_test_reset();
$stored = SWI_Config::defaults();
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = $stored;
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_WRITE_LOCK ] = array( 'token' => 'other-writer', 'expires_at' => time() + 30 );
$raw = $stored; $raw['frequency_days'] = 60;
$result = SWI_Config::save( $raw, 1 );
f13_governance_check( is_wp_error( $result ) && 'swi_config_busy' === $result->get_error_code(), 'concurrent configuration write is rejected while the serialization lock is active' );
f13_governance_check( 30 === $GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ]['frequency_days'], 'busy write has no configuration side effect' );

swi_test_reset();
$stored = SWI_Config::defaults();
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = $stored;
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_WRITE_LOCK ] = array( 'token' => 'stale-writer', 'expires_at' => time() - 1 );
$raw = $stored; $raw['frequency_days'] = 60;
$result = SWI_Config::save( $raw, 1 );
f13_governance_check( ! is_wp_error( $result ) && 2 === $result['config_version'] && 60 === $result['frequency_days'], 'expired write lock is recovered and the governed write succeeds' );
f13_governance_check( ! array_key_exists( SWI_Config::OPTION_WRITE_LOCK, $GLOBALS['swi_test_options'] ), 'configuration lock is released after a successful write' );

swi_test_reset();
$stored = SWI_Config::defaults();
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = $stored;
add_filter( 'swi_runtime_config', static function ( $config ) { $config['frequency_days'] = 90; return $config; } );
$raw = $stored; $raw['frequency_days'] = 45;
$result = SWI_Config::save( $raw, 1 );
f13_governance_check( ! is_wp_error( $result ) && 45 === $GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ]['frequency_days'], 'canonical settings save is based on stored governance, not a companion runtime projection' );

printf( "GOVERNANCE MONOTONIC REGRESSIONS: %d passed, %d failed\n", $passed, $failed );
exit( $failed ? 1 : 0 );
