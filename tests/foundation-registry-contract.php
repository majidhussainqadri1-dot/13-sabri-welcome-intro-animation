<?php
/**
 * Standalone File 01 registry DTO compatibility regression.
 * No WordPress database or live/staging state is implied.
 */
define( 'ABSPATH', __DIR__ );
define( 'SWI_VERSION', '1.0.1' );
define( 'SWI_TEXT_DOMAIN', 'sabri-welcome-intro' );
function __( $message, $domain = '' ) { return $message; }
class WP_Error {
    private $code;
    public function __construct( $code, $message = '', $data = array() ) { $this->code = $code; }
    public function get_error_code() { return $this->code; }
}
function absint( $value ) { return abs( (int) $value ); }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function do_action( $hook, ...$args ) { /* Standalone event stub. */ }
final class SPF_Registry {
    public static $module;
    public static $routes = array();
    public static $module_writes = 0;
    public static $route_writes = 0;
    public static $route_error = false;
    public static function register_manifest( $manifest, $context = array() ) { self::$module_writes++; return array( 'record_version' => 2 ); }
    public static function map_route( $route, $context = array() ) { self::$route_writes++; return self::$route_error ? new WP_Error( 'spf_route_failed' ) : array( 'record_version' => 2 ); }
    public static function get_module( $key ) { return 'file-13' === $key ? self::$module : null; }
    public static function list_routes() { return self::$routes; }
}
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-foundation.php';

use Sabri\WelcomeIntro\Foundation;

$expected = Foundation::manifest();
foreach ( array( 'required', 'optional' ) as $field ) {
    usort( $expected[ $field ], static function ( $a, $b ) {
        return strcmp( $a['module_key'], $b['module_key'] );
    } );
}
$expected['record_version'] = 1;
$route = Foundation::route();
$route['record_version'] = 1;
SPF_Registry::$routes = array( $route );

$assert_status = static function ( $module, $want, $label ) {
    SPF_Registry::$module = $module;
    $actual = Foundation::status()['state'];
    if ( $want !== $actual ) {
        fwrite( STDERR, $label . ': expected ' . $want . ', got ' . $actual . PHP_EOL );
        exit( 1 );
    }
};
$assert_status( $expected, 'synced', 'canonical File 01 normalized DTO' );
// Exact repeated sync is a true no-op, including record versions and audit writes.
SPF_Registry::$module = $expected;
$already = Foundation::sync();
if ( ! is_array( $already ) || empty( $already['already_synced'] )
    || 1 !== $already['module']['record_version']
    || 1 !== $already['route']['record_version']
    || SPF_Registry::$module_writes || SPF_Registry::$route_writes ) {
    fwrite( STDERR, "Already-synced registry caused duplicate writes or lost its version.\\n" );
    exit( 1 );
}
// A degraded-but-contract-matching compatibility record must also be idempotent.
$degraded = $expected;
$degraded['state'] = 'degraded';
SPF_Registry::$module = $degraded;
$again = Foundation::sync();
if ( ! is_array( $again ) || empty( $again['already_synced'] )
    || 'degraded' !== $again['module']['state']
    || SPF_Registry::$module_writes || SPF_Registry::$route_writes ) {
    fwrite( STDERR, "Degraded compatible registry unexpectedly mutated.\\n" );
    exit( 1 );
}
// Repair a stale route without rewriting a matching module.
SPF_Registry::$module = $expected;
$route_drift = $route;
$route_drift['layout_context'] = 'shell';
SPF_Registry::$routes = array( $route_drift );
SPF_Registry::$module_writes = SPF_Registry::$route_writes = 0;
$route_only = Foundation::sync();
if ( ! is_array( $route_only ) || 0 !== SPF_Registry::$module_writes
    || 1 !== SPF_Registry::$route_writes || 1 !== $route_only['module']['record_version'] ) {
    fwrite( STDERR, "Route-only repair rewrote a matching module.\n" ); exit( 1 );
}
// Report the underlying route error if no module mutation occurred.
SPF_Registry::$module_writes = SPF_Registry::$route_writes = 0;
SPF_Registry::$route_error = true;
$route_failure = Foundation::sync();
if ( ! ( $route_failure instanceof WP_Error ) || 'spf_route_failed' !== $route_failure->get_error_code()
    || 0 !== SPF_Registry::$module_writes || 1 !== SPF_Registry::$route_writes ) {
    fwrite( STDERR, "Route-only failure misreported or mutated module.\n" ); exit( 1 );
}
SPF_Registry::$route_error = false;
// Repair a stale module without rewriting a matching route.
$module_drift = $expected;
$module_drift['owner_name'] = 'Old historical label';
SPF_Registry::$module = $module_drift;
SPF_Registry::$routes = array( $route );
SPF_Registry::$module_writes = SPF_Registry::$route_writes = 0;
$module_only = Foundation::sync();
if ( ! is_array( $module_only ) || 1 !== SPF_Registry::$module_writes
    || 0 !== SPF_Registry::$route_writes || 1 !== $module_only['route']['record_version'] ) {
    fwrite( STDERR, "Module-only repair rewrote a matching route.\n" ); exit( 1 );
}
// Two-resource drift plus route failure must report a genuine partial update.
SPF_Registry::$module = $module_drift;
SPF_Registry::$routes = array( $route_drift );
SPF_Registry::$module_writes = SPF_Registry::$route_writes = 0;
SPF_Registry::$route_error = true;
$partial = Foundation::sync();
if ( ! ( $partial instanceof WP_Error ) || 'swi_foundation_partial_sync' !== $partial->get_error_code()
    || 1 !== SPF_Registry::$module_writes || 1 !== SPF_Registry::$route_writes ) {
    fwrite( STDERR, "Two-resource partial failure not reported correctly.\n" ); exit( 1 );
}
SPF_Registry::$route_error = false;
SPF_Registry::$module_writes = SPF_Registry::$route_writes = 0;
SPF_Registry::$routes = array( $route );

SPF_Registry::$module = $expected;
foreach ( array( 'required', 'optional', 'health' ) as $field ) {
    $drift = $expected;
    if ( 'optional' === $field ) {
        $drift['optional'][0]['minimum_version'] = '99.0.0';
    } elseif ( 'required' === $field ) {
        $drift['required'][] = array( 'module_key' => 'file-99' );
    } else {
        $drift['health']['fail_mode'] = 'unsafe';
    }
    $assert_status( $drift, 'unsynced', 'drift:' . $field );
}
$missing = $expected;
unset( $missing['health'] );
$assert_status( $missing, 'unsynced', 'missing health' );
// The File 01 route API returns at most 200 entries, with no pagination.
// Never infer completeness from a full page or mutate the module first.
SPF_Registry::$routes = array();
for ( $i = 0; $i < 200; $i++ ) {
    SPF_Registry::$routes[] = array( 'route_key' => 'unrelated-' . $i, 'route_path' => '/unrelated-' . $i . '/' );
}
$assert_status( $expected, 'unsynced', 'bounded route inventory' );
if ( true === Foundation::status()['route_inventory_complete'] ) { fwrite( STDERR, "full route page marked complete\n" ); exit( 1 ); }
$sync = Foundation::sync();
if ( ! ( $sync instanceof WP_Error ) || 'swi_foundation_route_inventory_incomplete' !== $sync->get_error_code()
    || SPF_Registry::$module_writes || SPF_Registry::$route_writes ) {
    fwrite( STDERR, "bounded route inventory did not fail before writes\n" ); exit( 1 );
}
echo "File 13 / File 01 runtime registry DTO and bounded-route contract: PASS\n";
