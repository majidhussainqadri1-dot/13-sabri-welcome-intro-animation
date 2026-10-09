<?php
/** Standalone source contract regression, not staging or production evidence. */
namespace {
define( 'ABSPATH', __DIR__ );
define( 'SWI_VERSION', '1.0.1' );
define( 'SWI_SCHEMA_VERSION', '1.0.1' );
$GLOBALS['swi_schema_version'] = '1.0.1';
define( 'SWI_TEXT_DOMAIN', 'sabri-welcome-intro' );
function has_action( $hook, $callback = false ) { return isset( $GLOBALS['swi_test_hooks'][ $hook ] ) ? 10 : false; }
function get_bloginfo( $field ) { return 'version' === $field ? '6.8' : ''; }
function get_option( $key, $default = false ) {
    if ( 'swi_intro_schema_version' === $key ) { return $GLOBALS['swi_schema_version']; }
    if ( 'swi_intro_config' === $key ) { return array_key_exists( 'swi_stored_override', $GLOBALS ) ? $GLOBALS['swi_stored_override'] : array( 'enabled' => false, 'status' => 'disabled', 'analytics_enabled' => false ); }
    if ( 'swi_intro_audit_gap' === $key ) { return $GLOBALS['swi_test_audit_gap'] ?? $default; }
    return $default;
}
function absint( $value ) { return abs( (int) $value ); }
}
namespace Sabri\WelcomeIntro {
    final class Settings {
        public const SCHEMA_OPTION = 'swi_intro_schema_version';
        public const OPTION = 'swi_intro_config';
        public static function get() { return array( 'enabled' => false, 'status' => 'disabled', 'analytics_enabled' => false, 'config_version' => 1 ); }
        public static function stored() { $raw = $GLOBALS['swi_stored_override'] ?? self::get(); return is_array( $raw ) ? $raw : self::get(); }
        public static function schema_version() { return is_string( $GLOBALS['swi_schema_version'] ) ? $GLOBALS['swi_schema_version'] : ''; }
    }
    final class Renderer {
        public static function invoke() {}
        public static function visual_tokens() { return array( 'primary' => '#087a4e' ); }
        public static function visual_contract_status() { return array( 'valid' => true, 'owner' => 'file-25', 'version' => '1.0.0' ); }
    }
    final class Analytics { public static function ajax_event() {} }
    final class Eligibility { public static function safe_mode_active() { return false; } }
    final class Foundation { public static function status() { return array( 'available' => true, 'state' => $GLOBALS['swi_test_registry_state'] ?? 'synced' ); } }
    require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-health.php';
    $GLOBALS['swi_test_hooks'] = array();
    $clean = Health::status();
    if ( true !== $clean['legacy_public_disabled'] || 'healthy' !== $clean['status'] ) { fwrite( STDERR, "disabled baseline invalid\n" ); exit( 1 ); }
    $GLOBALS['swi_test_hooks']['sabri_shell_welcome_intro_invoke'] = true;
    $unsafe = Health::status();
    if ( false !== $unsafe['legacy_public_disabled'] || 'degraded' !== $unsafe['status']
        || ! in_array( 'legacy_public_renderer_registered', $unsafe['issues'], true ) ) {
        fwrite( STDERR, "renderer hook concealed by health status\n" ); exit( 1 );
    }
    unset( $GLOBALS['swi_test_hooks']['sabri_shell_welcome_intro_invoke'] );
    $GLOBALS['swi_test_hooks']['wp_ajax_nopriv_swi_intro_event'] = true;
    $unsafe = Health::status();
    if ( false !== $unsafe['legacy_public_disabled'] || 'degraded' !== $unsafe['status'] ) {
        fwrite( STDERR, "analytics hook concealed by health status\n" ); exit( 1 );
    }
    unset( $GLOBALS['swi_test_hooks']['wp_ajax_nopriv_swi_intro_event'] );
    $GLOBALS['swi_schema_version'] = '1.0.0';
    $pending = Health::status();
    if ( 'degraded' !== $pending['status'] || ! in_array( 'schema_migration_pending', $pending['issues'], true ) ) {
        fwrite( STDERR, "Unmigrated schema was reported healthy\n" ); exit( 1 );
    }
    if ( 'blocked' !== Health::file24_contract_state( 'unassessed' ) ) {
        fwrite( STDERR, "Pending schema migration falsely certified by File 24 adapter\n" ); exit( 1 );
    }
    $GLOBALS['swi_schema_version'] = '1.0.1';
    $GLOBALS['swi_test_audit_gap'] = array( 'from' => 1, 'to' => 2 );
    if ( 'blocked' !== Health::file24_contract_state( 'unassessed' ) ) {
        fwrite( STDERR, "Audit gap falsely certified by File 24 adapter\n" ); exit( 1 );
    }
    unset( $GLOBALS['swi_test_audit_gap'] );
    $GLOBALS['swi_test_registry_state'] = 'unsynced';
    if ( 'blocked' !== Health::file24_contract_state( 'unassessed' ) ) {
        fwrite( STDERR, "Unsynced File 01 registry falsely certified by File 24 adapter\n" ); exit( 1 );
    }
    unset( $GLOBALS['swi_test_registry_state'] );
    if ( 'compatible' !== Health::file24_contract_state( 'unassessed' ) ) {
        fwrite( STDERR, "Healthy File 24 adapter was incorrectly blocked\n" ); exit( 1 );
    }
    $GLOBALS['swi_stored_override'] = array( 'enabled' => array(), 'status' => array( 'disabled' ), 'analytics_enabled' => false );
    set_error_handler( static function ( $severity, $message ) { throw new \ErrorException( $message, 0, $severity ); } );
    try { $malformed = Health::status(); $file24 = Health::file24_contract_state( 'unassessed' ); }
    finally { restore_error_handler(); }
    if ( 'degraded' !== $malformed['status'] || ! in_array( 'legacy_stored_malformed', $malformed['issues'], true )
        || 'blocked' !== $file24 ) { fwrite( STDERR, "Malformed persisted flags were concealed\n" ); exit( 1 ); }
    // A scalar persisted row is masked by Settings::stored() safe defaults;
    // source assurance must nevertheless fail closed on the raw DB shape.
    $GLOBALS['swi_stored_override'] = 'corrupt-serialized-option';
    set_error_handler( static function ( $severity, $message ) { throw new \ErrorException( $message, 0, $severity ); } );
    try { $scalar = Health::status(); $scalar_contract = Health::file24_contract_state( 'unassessed' ); }
    finally { restore_error_handler(); }
    if ( 'degraded' !== $scalar['status'] || ! in_array( 'legacy_stored_malformed', $scalar['issues'], true )
        || 'blocked' !== $scalar_contract ) { fwrite( STDERR, "Scalar persisted config falsely certified\n" ); exit( 1 ); }
    echo "File 13 legacy effective-public-disable health regression: PASS\n";
}
