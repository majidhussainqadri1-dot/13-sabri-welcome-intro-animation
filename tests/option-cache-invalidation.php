<?php
/** Isolated source-level cache regression for legacy autoloaded option rows. */
namespace {
define( 'ABSPATH', __DIR__ );
define( 'ARRAY_A', 'ARRAY_A' );
define( 'SWI_TEXT_DOMAIN', 'sabri-welcome-intro' );
$GLOBALS['swi_cache_deletes'] = array();
$GLOBALS['swi_db_reads'] = 0;
$GLOBALS['swi_fail_cas'] = false;
class SWI_Option_CAS_Test_DB {
    public $options = 'wp_options';
    public function prepare( $sql, ...$args ) { return $sql; }
    public function get_row( $sql, $mode ) {
        $GLOBALS['swi_db_reads']++;
        if ( 1 === $GLOBALS['swi_db_reads'] ) {
            return array( 'option_id' => 7, 'option_value' => serialize( \Sabri\WelcomeIntro\Settings::defaults() ) );
        }
        return array( 'option_id' => 8, 'option_value' => serialize( array() ) );
    }
    public function query( $sql ) { return $GLOBALS['swi_fail_cas'] ? 0 : 1; }
}
$GLOBALS['wpdb'] = new SWI_Option_CAS_Test_DB();
class WP_Error {
    private $code;
    public function __construct( $code, $message, $data = array() ) { $this->code = $code; }
    public function get_error_code() { return $this->code; }
}
function __( $text, $domain ) { return $text; }
function get_option( $key, $default = false ) { return $default; }
function add_option( $key, $value, $deprecated = '', $autoload = false ) { return true; }
function maybe_unserialize( $value ) { return unserialize( $value ); }
function maybe_serialize( $value ) { return serialize( $value ); }
function absint( $value ) { return abs( (int) $value ); }
function sanitize_key( $value ) { return strtolower( (string) $value ); }
function sanitize_text_field( $value ) { return trim( (string) $value ); }
function sanitize_textarea_field( $value ) { return trim( (string) $value ); }
function wp_parse_url( $value, $component = -1 ) { return parse_url( $value, $component ); }
function untrailingslashit( $value ) { return rtrim( $value, '/' ); }
function wp_cache_delete( $key, $group ) { $GLOBALS['swi_cache_deletes'][] = $key . ':' . $group; return true; }
function do_action( $name, ...$args ) {}
}
namespace Sabri\WelcomeIntro {
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-settings.php';
function check( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, $message . "\n" ); exit( 1 ); } }
$result = Settings::update( array( 'heading' => 'Changed' ), 1, 5 );
check( is_array( $result ) && 2 === $result['config_version'], 'CAS update failed.' );
check( count( array_filter( $GLOBALS['swi_cache_deletes'], static fn( $item ) => 'alloptions:options' === $item ) ) >= 2, 'Config/audit autoload cache was not invalidated.' );
$GLOBALS['swi_db_reads'] = 0;
$GLOBALS['swi_cache_deletes'] = array();
$GLOBALS['swi_fail_cas'] = true;
$result = Settings::update( array( 'heading' => 'Changed' ), 1, 5 );
check( $result instanceof \WP_Error && 'swi_stale_config' === $result->get_error_code(), 'Failed CAS did not return conflict.' );
check( in_array( 'alloptions:options', $GLOBALS['swi_cache_deletes'], true ), 'Failed CAS left stale autoload cache.' );
echo "File 13 legacy autoloaded option cache invalidation: PASS\n";
}
