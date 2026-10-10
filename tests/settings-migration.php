<?php
/** Source-only migration failure regression; not a live database test. */
define( 'ABSPATH', __DIR__ );
define( 'SWI_SCHEMA_VERSION', '1.0.1' );
define( 'SWI_TEXT_DOMAIN', 'sabri-welcome-intro' );
$GLOBALS['swi_config'] = array( 'enabled' => true, 'status' => 'active', 'analytics_enabled' => true );
$GLOBALS['swi_schema'] = '1.0.0';
$GLOBALS['deny_config_write'] = true;
$GLOBALS['deny_add'] = false;
$GLOBALS['tamper_config'] = false;
$GLOBALS['events'] = array();
function get_option( $key, $default = false ) {
    if ( 'swi_intro_config' === $key ) { return $GLOBALS['swi_config']; }
    if ( 'swi_intro_schema_version' === $key ) { return $GLOBALS['swi_schema']; }
    return $default;
}
function update_option( $key, $value, $autoload = false ) {
    if ( 'swi_intro_config' === $key ) {
        if ( $GLOBALS['deny_config_write'] ) { return false; }
        if ( $GLOBALS['tamper_config'] ) { $value['enabled'] = true; }
        $GLOBALS['swi_config'] = $value;
        return true;
    }
    if ( 'swi_intro_schema_version' === $key ) { $GLOBALS['swi_schema'] = $value; return true; }
    return false;
}
function add_option( $key, $value, $deprecated = '', $autoload = false ) {
    if ( 'swi_intro_config' !== $key || $GLOBALS['deny_add'] ) { return false; }
    $GLOBALS['swi_config'] = $value;
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
function verify_migration( $condition, $message ) {
    if ( ! $condition ) { fwrite( STDERR, $message . "\n" ); exit( 1 ); }
}
// A failed write must not advance schema or claim migration success.
Settings::maybe_upgrade();
verify_migration( '1.0.0' === $GLOBALS['swi_schema'], 'Failed config write promoted schema.' );
verify_migration( true === $GLOBALS['swi_config']['enabled'], 'Failed-write fixture was unexpectedly changed.' );
verify_migration( in_array( 'swi_intro_schema_upgrade_blocked', $GLOBALS['events'], true ), 'Missing blocked-migration signal.' );
// An external filter that restores enabled=true must also block promotion.
$GLOBALS['deny_config_write'] = false;
$GLOBALS['tamper_config'] = true;
Settings::maybe_upgrade();
verify_migration( '1.0.0' === $GLOBALS['swi_schema'], 'Tampered config promoted schema.' );
// Retry with valid persistence and verify the public and analytics kill switches.
$GLOBALS['tamper_config'] = false;
Settings::maybe_upgrade();
verify_migration( '1.0.1' === $GLOBALS['swi_schema'], 'Valid migration did not promote schema.' );
verify_migration( false === $GLOBALS['swi_config']['enabled']
    && 'disabled' === $GLOBALS['swi_config']['status']
    && false === $GLOBALS['swi_config']['analytics_enabled'], 'Legacy runtime not disabled on disk.' );
// Existing correct config is a valid no-op migration.
$GLOBALS['swi_schema'] = '1.0.0';
$GLOBALS['deny_config_write'] = true;
Settings::maybe_upgrade();
verify_migration( '1.0.1' === $GLOBALS['swi_schema'], 'Already-safe config did not migrate.' );
// Fresh activation cannot claim schema success if the initial option insert fails.
$GLOBALS['swi_config'] = false;
$GLOBALS['swi_schema'] = '1.0.0';
$GLOBALS['deny_add'] = true;
Settings::activate();
verify_migration( '1.0.0' === $GLOBALS['swi_schema'], 'Failed initial insert promoted schema.' );
$GLOBALS['deny_add'] = false;
Settings::activate();
verify_migration( '1.0.1' === $GLOBALS['swi_schema'] && is_array( $GLOBALS['swi_config'] )
    && false === $GLOBALS['swi_config']['enabled'], 'Fresh activation failed safe migration.' );
// Historical option rows are untrusted: arrays/objects must never reach WordPress scalar sanitizers.
$GLOBALS['deny_config_write'] = false;
$GLOBALS['swi_schema'] = '1.0.0';
$GLOBALS['swi_config'] = array(
    'enabled' => true,
    'status' => array( 'active' ),
    'heading' => array( 'malformed' ),
    'claim' => array( 'malformed' ),
    'duration_ms' => array( 999 ),
    'recurrence_days' => new stdClass(),
    'eligible_paths' => array( '/safe', array( '/bad' ), new stdClass() ),
    'analytics_enabled' => true,
    'start_at' => array( 'tomorrow' ),
    'end_at' => new stdClass(),
    'config_version' => array( 10 ),
);
set_error_handler( static function ( $severity, $message ) { throw new ErrorException( $message, 0, $severity ); } );
try {
    Settings::maybe_upgrade();
    verify_migration( '' === Settings::normalize_path( array( '/unsafe' ) ), 'Array path was accepted.' );
} finally {
    restore_error_handler();
}
$clean = $GLOBALS['swi_config'];
verify_migration( '1.0.1' === $GLOBALS['swi_schema'], 'Malformed legacy config blocked safe migration.' );
verify_migration( false === $clean['enabled'] && 'disabled' === $clean['status']
    && false === $clean['analytics_enabled'], 'Malformed legacy row reactivated public runtime.' );
verify_migration( 'Sabri Homeopathy' === $clean['heading']
    && 3200 === $clean['duration_ms'] && 30 === $clean['recurrence_days']
    && 1 === $clean['config_version'], 'Malformed scalar values not normalized.' );
verify_migration( array( '/safe' ) === $clean['eligible_paths']
    && '' === $clean['start_at'] && '' === $clean['end_at'], 'Malformed paths/dates not normalized.' );
// Schema-current reads remain safe even if a third-party later corrupts the option.
$GLOBALS['swi_config']['heading'] = array( 'invalid' );
$GLOBALS['swi_config']['claim'] = new stdClass();
$GLOBALS['swi_config']['config_version'] = array( 99 );
$GLOBALS['swi_schema'] = array( 'corrupt-schema' );
set_error_handler( static function ( $severity, $message ) { throw new ErrorException( $message, 0, $severity ); } );
try {
    $view = Settings::get();
    verify_migration( '' === Settings::schema_version(), 'Malformed schema version was cast.' );
} finally { restore_error_handler(); }
verify_migration( 'Sabri Homeopathy' === $view['heading']
    && 1 === $view['config_version'] && false === $view['enabled'], 'Malformed post-migration view not normalized.' );
echo "File 13 fail-closed schema migration persistence and malformed legacy input: PASS\n";
