<?php
/**
 * Standalone File 01 registry DTO compatibility regression.
 * No WordPress database or live/staging state is implied.
 */
define( 'ABSPATH', __DIR__ );
define( 'SWI_VERSION', '1.0.1' );
function absint( $value ) { return abs( (int) $value ); }
final class SPF_Registry {
    public static $module;
    public static $routes = array();
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
echo "File 13 / File 01 runtime registry DTO contract: PASS\n";
