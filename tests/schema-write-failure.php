<?php
/** Source-only regression; this does not attest to live database state. */
define( 'ABSPATH', __DIR__ );
define( 'SWI_SCHEMA_VERSION', '1.0.1' );
define( 'SWI_TEXT_DOMAIN', 'sabri-welcome-intro' );
$GLOBALS['config'] = array( 'enabled' => false, 'status' => 'disabled', 'analytics_enabled' => false );
$GLOBALS['schema'] = '1.0.0';
$GLOBALS['schema_write_mode'] = 'fail';
$GLOBALS['events'] = array();
function get_option( $key, $default = false ) {
    if ( 'swi_intro_config' === $key ) { return $GLOBALS['config']; }
    if ( 'swi_intro_schema_version' === $key ) { return $GLOBALS['schema']; }
    return $default;
}
function update_option( $key, $value, $autoload = false ) {
    if ( 'swi_intro_config' === $key ) { $GLOBALS['config'] = $value; return true; }
    if ( 'swi_intro_schema_version' === $key ) {
        if ( 'fail' === $GLOBALS['schema_write_mode'] ) { return false; }
        $GLOBALS['schema'] = 'tamper' === $GLOBALS['schema_write_mode'] ? '0.9.9' : $value;
        return true;
    }
    return false;
}
function add_option( $key, $value, $deprecated = '', $autoload = false ) {
    if ( 'swi_intro_config' !== $key ) { return false; }
    $GLOBALS['config'] = $value;
    return true;
}
function absint( $value ) { return abs( (int) $value ); }
function sanitize_key( $value ) { return strtolower( (string) $value ); }
function sanitize_text_field( $value ) { return trim( (string) $value ); }
function sanitize_textarea_field( $value ) { return trim( (string) $value ); }
function wp_parse_url( $value, $component = -1 ) { return parse_url( $value, $component ); }
function untrailingslashit( $value ) { return rtrim( $value, '/' ); }
function do_action( $name, ...$args ) { $GLOBALS['events'][] = $name; }
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-settings.php';
use Sabri\WelcomeIntro\Settings;
function verify_schema( $condition, $message ) {
    if ( ! $condition ) { fwrite( STDERR, $message . "\n" ); exit( 1 ); }
}
Settings::maybe_upgrade();
verify_schema( '1.0.0' === $GLOBALS['schema'], 'Failed schema write changed the version.' );
verify_schema( ! in_array( 'swi_intro_schema_upgraded', $GLOBALS['events'], true ), 'Failed schema write claimed success.' );
verify_schema( in_array( 'swi_intro_schema_upgrade_blocked', $GLOBALS['events'], true ), 'Failed schema write lacked blocked event.' );
$GLOBALS['events'] = array();
$GLOBALS['schema_write_mode'] = 'tamper';
Settings::maybe_upgrade();
verify_schema( '0.9.9' === $GLOBALS['schema'], 'Tamper fixture was not applied.' );
verify_schema( ! in_array( 'swi_intro_schema_upgraded', $GLOBALS['events'], true ), 'Tampered schema write claimed success.' );
$GLOBALS['events'] = array();
$GLOBALS['schema_write_mode'] = 'ok';
Settings::maybe_upgrade();
verify_schema( SWI_SCHEMA_VERSION === $GLOBALS['schema'], 'Valid retry did not persist schema.' );
verify_schema( in_array( 'swi_intro_schema_upgraded', $GLOBALS['events'], true ), 'Successful retry lacked success event.' );
$GLOBALS['schema'] = '1.0.0';
$GLOBALS['schema_write_mode'] = 'fail';
$GLOBALS['events'] = array();
Settings::activate();
verify_schema( '1.0.0' === $GLOBALS['schema'], 'Activation promoted failed schema write.' );
verify_schema( in_array( 'swi_intro_schema_upgrade_blocked', $GLOBALS['events'], true ), 'Activation failed to signal blocked schema.' );
echo "File 13 schema write read-back regression: PASS\n";
