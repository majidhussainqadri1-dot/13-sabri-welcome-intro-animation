<?php
/** Source-only authorization regression. No live database is accessed. */
define( 'ABSPATH', __DIR__ );
define( 'SWI_TEXT_DOMAIN', 'sabri-welcome-intro' );
define( 'ARRAY_A', 'ARRAY_A' );
$GLOBALS['logged_in'] = false;
$GLOBALS['native_cap'] = false;
$GLOBALS['institutional'] = true;
$GLOBALS['actor_id'] = 9;
$GLOBALS['db_reads'] = 0;
function is_user_logged_in() { return $GLOBALS['logged_in']; }
function get_current_user_id() { return $GLOBALS['actor_id']; }
function current_user_can( $cap ) { return $GLOBALS['native_cap'] && 'manage_options' === $cap; }
function apply_filters( $hook, $value, ...$args ) {
    return 'swi_intro_authorization_decision' === $hook ? $GLOBALS['institutional'] && $value : $value;
}
function __( $message, $domain = '' ) { return $message; }
function absint( $value ) { return abs( (int) $value ); }
class WP_Error {
    private $code;
    public function __construct( $code, $message, $data = array() ) { $this->code = $code; }
    public function get_error_code() { return $this->code; }
}
class FakeDB {
    public $options = 'wp_options';
    public function prepare( $sql, ...$args ) { return $sql; }
    public function get_row( $sql, $type ) { $GLOBALS['db_reads']++; return null; }
}
$GLOBALS['wpdb'] = new FakeDB();
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-authorization.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-settings.php';
use Sabri\WelcomeIntro\Settings;
function check_error( $result, $code, $label ) {
    if ( ! $result instanceof WP_Error || $code !== $result->get_error_code() ) {
        fwrite( STDERR, $label . ': unexpected result' . PHP_EOL ); exit( 1 );
    }
}
check_error( Settings::update( array(), 1 ), 'swi_forbidden', 'anonymous' );
$GLOBALS['logged_in'] = true;
check_error( Settings::update( array(), 1 ), 'swi_forbidden', 'no native capability' );
$GLOBALS['native_cap'] = true;
$GLOBALS['institutional'] = false;
check_error( Settings::update( array(), 1 ), 'swi_forbidden', 'institutional denial' );
$GLOBALS['institutional'] = true;
check_error( Settings::update( array(), 1, 8 ), 'swi_actor_mismatch', 'spoofed actor' );
check_error( Settings::update( array(), array( 1 ) ), 'swi_invalid_revision', 'array revision' );
check_error( Settings::update( array(), '0' ), 'swi_invalid_revision', 'zero revision' );
if ( 0 !== $GLOBALS['db_reads'] ) { fwrite( STDERR, 'Denied request reached database' . PHP_EOL ); exit( 1 ); }
check_error( Settings::update( array(), 1, 9 ), 'swi_config_missing', 'authorized path' );
if ( 1 !== $GLOBALS['db_reads'] ) { fwrite( STDERR, 'Authorized request did not reach database' . PHP_EOL ); exit( 1 ); }
echo 'File 13 direct settings authorization: PASS' . PHP_EOL;
