<?php
/** Isolated source-level regression; not a live analytics or DB test. */
namespace {
define( 'ABSPATH', __DIR__ );
define( 'ARRAY_A', 'ARRAY_A' );
$GLOBALS['swi_test_config'] = array( 'analytics_enabled' => false, 'config_version' => 1 );
$GLOBALS['swi_option_reads'] = 0;
$GLOBALS['swi_db_reads'] = 0;
$GLOBALS['swi_db_writes'] = 0;
$GLOBALS['swi_cache_deletes'] = array();
$GLOBALS['swi_actions'] = array();
class SWI_Analytics_Test_DB {
    public $options = 'wp_options';
    public function prepare( $sql, ...$args ) { return $sql; }
    public function get_row( $sql, $mode ) {
        $GLOBALS['swi_db_reads']++;
        return array( 'option_id' => 7, 'option_value' => serialize( array( 'shown' => 0, 'skipped' => 0, 'completed' => 0 ) ) );
    }
    public function query( $sql ) { $GLOBALS['swi_db_writes']++; return 1; }
}
$GLOBALS['wpdb'] = new SWI_Analytics_Test_DB();
function get_option( $key, $default = false ) { $GLOBALS['swi_option_reads']++; return $default; }
function add_option( $key, $value, $deprecated = '', $autoload = false ) { return true; }
function maybe_unserialize( $value ) { return unserialize( $value ); }
function maybe_serialize( $value ) { return serialize( $value ); }
function absint( $value ) { return abs( (int) $value ); }
function wp_cache_delete( $key, $group ) { $GLOBALS['swi_cache_deletes'][] = $key . ':' . $group; return true; }
function do_action( $name, ...$args ) { $GLOBALS['swi_actions'][] = $name; }
}
namespace Sabri\WelcomeIntro {
final class Settings {
    public static function get() { return $GLOBALS['swi_test_config']; }
}
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-analytics.php';
function check( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, $message . "\n" ); exit( 1 ); } }
check( false === Analytics::record( 'shown', 1 ), 'Disabled analytics accepted a direct write.' );
check( 0 === $GLOBALS['swi_option_reads'] && 0 === $GLOBALS['swi_db_reads'], 'Disabled analytics accessed storage.' );
$GLOBALS['swi_test_config']['analytics_enabled'] = true;
foreach ( array( array( array( 'shown' ), 1 ), array( 'unknown', 1 ), array( 'shown', array( 1 ) ), array( 'shown', 2 ) ) as $case ) {
    check( false === Analytics::record( $case[0], $case[1] ), 'Malformed direct event/version accepted.' );
}
check( 0 === $GLOBALS['swi_option_reads'] && 0 === $GLOBALS['swi_db_reads'], 'Malformed event accessed storage.' );
check( true === Analytics::record( 'shown', 1 ), 'Valid enabled test event was rejected.' );
check( 1 === $GLOBALS['swi_db_reads'] && 1 === $GLOBALS['swi_db_writes'], 'Valid event did not reach single DB CAS.' );
check( in_array( 'alloptions:options', $GLOBALS['swi_cache_deletes'], true ), 'Autoloaded analytics option cache not invalidated.' );
check( in_array( 'swi_intro_event_published', $GLOBALS['swi_actions'], true ), 'Successful event was not signaled.' );
echo "File 13 analytics privacy and event-input regression: PASS\n";
}
