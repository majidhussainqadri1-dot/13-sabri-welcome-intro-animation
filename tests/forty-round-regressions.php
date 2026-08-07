<?php
require __DIR__ . '/wp-stubs.php';
if ( ! function_exists( 'wp_timezone' ) ) {
	function wp_timezone() { return new DateTimeZone( 'Asia/Karachi' ); }
}
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-config.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-eligibility.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-analytics.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-rest.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-activator.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-system-check.php';

$passed = 0; $failed = 0;
function f13_check( $condition, $message ) { global $passed, $failed; if ( $condition ) { ++$passed; echo "PASS: $message\n"; } else { ++$failed; echo "FAIL: $message\n"; } }

swi_test_reset();
$stored = SWI_Config::defaults();
$stored['config_version'] = 7;
$stored['updated_at'] = '2026-08-07T00:00:00+00:00';
$stored['updated_by'] = 19;
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = $stored;
add_filter( 'swi_runtime_config', static function ( $config ) { $config['frequency_days'] = 45; $config['config_version'] = 999; $config['updated_by'] = 999; return $config; } );
$config = SWI_Config::get();
f13_check( 45 === $config['frequency_days'], 'runtime contract may override an allowed runtime field' );
f13_check( 7 === $config['config_version'], 'runtime contract cannot falsify governed config version' );
f13_check( 19 === $config['updated_by'], 'runtime contract cannot falsify governed updater identity' );

swi_test_reset();
$raw = SWI_Config::defaults();
$raw['starts_at'] = '2026-08-07T09:00';
$scheduled = SWI_Config::sanitize( $raw, false );
f13_check( '2026-08-07T04:00:00+00:00' === $scheduled['starts_at'], 'datetime-local input is interpreted in the WordPress site timezone' );
$raw['starts_at'] = '2026-02-31T09:00';
$invalid = SWI_Config::sanitize( $raw, false );
f13_check( '' === $invalid['starts_at'], 'invalid calendar datetime is rejected rather than normalized silently' );

swi_test_reset();
$GLOBALS['swi_test_current_user'] = 9;
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = SWI_Config::defaults();
$rest = new SWI_REST( new SWI_Analytics() );
$missing = $rest->dismiss( new WP_REST_Request( array( 'event' => 'skipped', 'config_version' => 1, 'idempotency_key' => '' ) ) );
f13_check( is_wp_error( $missing ) && 'swi_missing_idempotency' === $missing->get_error_code(), 'dismissal rejects an empty idempotency key' );

swi_test_reset();
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = array_merge( SWI_Config::defaults(), array( 'analytics_enabled' => 1 ) );
$rest = new SWI_REST( new SWI_Analytics() );
for ( $i = 0; $i < 300; ++$i ) {
	$rest->event( new WP_REST_Request( array( 'event' => 'shown', 'config_version' => 1, 'idempotency_key' => 'k' . $i ), array( 'X-SWI-Nonce' => 'valid-swi_public_event' ) ) );
}
$limited = $rest->event( new WP_REST_Request( array( 'event' => 'shown', 'config_version' => 1, 'idempotency_key' => 'overflow' ), array( 'X-SWI-Nonce' => 'valid-swi_public_event' ) ) );
f13_check( is_wp_error( $limited ) && 'swi_rate_limited' === $limited->get_error_code(), 'public aggregate event endpoint has a privacy-safe global abuse ceiling' );

swi_test_reset();
$GLOBALS['wp_rewrite'] = (object) array( 'extra_rules_top' => array( '^welcome-intro-preview/?$' => 'index.php?swi_preview_route=1' ) );
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = SWI_Config::defaults();
SWI_Activator::deactivate();
f13_check( ! isset( $GLOBALS['wp_rewrite']->extra_rules_top['^welcome-intro-preview/?$'] ), 'deactivation removes the preview rewrite before flushing' );

swi_test_reset();
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = SWI_Config::defaults();
$check = SWI_System_Check::snapshot();
f13_check( 'fallback' === $check['integration_status'], 'system check does not misreport self-registered hooks as a connected File 20 host' );

printf( "FORTY-ROUND PHP REGRESSIONS: %d passed, %d failed\n", $passed, $failed );
exit( $failed ? 1 : 0 );
