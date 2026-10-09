<?php
/** Standalone rewrite lifecycle regression; no staging/live state implied. */
define( 'ABSPATH', __DIR__ );
$GLOBALS['wp_rewrite'] = (object) array( 'extra_rules_top' => array() );
$GLOBALS['flushed_rules'] = array();
$GLOBALS['cleared_hook'] = '';
$GLOBALS['stored_rewrite_rules'] = array();
$GLOBALS['rewrite_conflicts'] = array();
$GLOBALS['flush_count'] = 0;
function get_option( $name, $default = false ) {
    return 'rewrite_rules' === $name ? $GLOBALS['stored_rewrite_rules'] : $default;
}
function do_action( $hook, ...$args ) {
    if ( 'swi_intro_preview_rewrite_conflict' === $hook ) {
        $GLOBALS['rewrite_conflicts'][] = array( $hook, ...$args );
    }
}
function wp_next_scheduled( $hook ) { return true; }
class File13SettingsActivationStub {
    public static function activate() { $GLOBALS['settings_activated'] = true; }
}
class_alias( 'File13SettingsActivationStub', 'Sabri\\WelcomeIntro\\Settings' );
function add_rewrite_rule( $pattern, $target, $priority ) {
    global $wp_rewrite;
    if ( 'top' !== $priority ) { throw new RuntimeException( 'Unexpected rewrite priority.' ); }
    $wp_rewrite->extra_rules_top[ $pattern ] = $target;
}
function wp_clear_scheduled_hook( $hook ) { $GLOBALS['cleared_hook'] = $hook; }
function flush_rewrite_rules( $hard ) {
    global $wp_rewrite;
    if ( false !== $hard ) { throw new RuntimeException( 'Hard flush is forbidden.' ); }
    $GLOBALS['flushed_rules'] = $wp_rewrite->extra_rules_top;
    $GLOBALS['flush_count']++;
}
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-renderer.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-analytics.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-plugin.php';
use Sabri\WelcomeIntro\Renderer;
use Sabri\WelcomeIntro\Plugin;
use Sabri\WelcomeIntro\Analytics;

Renderer::register_rewrite();
$GLOBALS['wp_rewrite']->extra_rules_top['^unrelated/?$'] = 'index.php?other=1';
Plugin::deactivate();
if ( isset( $GLOBALS['flushed_rules'][ Renderer::PREVIEW_REWRITE_PATTERN ] )
    || 'index.php?other=1' !== ( $GLOBALS['flushed_rules']['^unrelated/?$'] ?? '' )
    || Analytics::CLEANUP_HOOK !== $GLOBALS['cleared_hook'] ) {
    fwrite( STDERR, "Deactivation retained the File 13 route or disturbed another owner.\n" );
    exit( 1 );
}
// Do not remove a colliding rewrite pattern after another owner replaced it.
$GLOBALS['wp_rewrite']->extra_rules_top[ Renderer::PREVIEW_REWRITE_PATTERN ] = 'index.php?another_owner=1';
Plugin::deactivate();
if ( 'index.php?another_owner=1' !== ( $GLOBALS['flushed_rules'][ Renderer::PREVIEW_REWRITE_PATTERN ] ?? '' ) ) {
    fwrite( STDERR, "Deactivation removed a foreign rewrite mapping.\n" );
    exit( 1 );
}
function file13_reset_rewrite_state() {
    $GLOBALS['wp_rewrite']->extra_rules_top = array();
    $GLOBALS['wp_rewrite']->extra_rules = array();
    $GLOBALS['stored_rewrite_rules'] = array();
    $GLOBALS['rewrite_conflicts'] = array();
}
function file13_check( $condition, $message ) {
    if ( ! $condition ) { fwrite( STDERR, $message . "\n" ); exit( 1 ); }
}
$pattern = Renderer::PREVIEW_REWRITE_PATTERN;
$target = Renderer::PREVIEW_REWRITE_TARGET;

file13_reset_rewrite_state();
$GLOBALS['wp_rewrite']->extra_rules_top[ $pattern ] = 'index.php?foreign_top=1';
file13_check( false === Renderer::register_rewrite(), 'Foreign top route accepted.' );
file13_check( 'index.php?foreign_top=1' === $GLOBALS['wp_rewrite']->extra_rules_top[ $pattern ], 'Foreign top route overwritten.' );
file13_check( 'extra_rules_top' === ( $GLOBALS['rewrite_conflicts'][0][2] ?? '' ), 'Top collision evidence missing.' );

file13_reset_rewrite_state();
$GLOBALS['wp_rewrite']->extra_rules[ $pattern ] = 'index.php?foreign_lower=1';
file13_check( false === Renderer::register_rewrite(), 'Foreign lower route accepted.' );
file13_check( ! isset( $GLOBALS['wp_rewrite']->extra_rules_top[ $pattern ] ), 'Foreign lower route shadowed.' );

file13_reset_rewrite_state();
$GLOBALS['stored_rewrite_rules'][ $pattern ] = 'index.php?foreign_persisted=1';
file13_check( false === Renderer::register_rewrite(), 'Foreign persisted route accepted.' );
file13_check( ! isset( $GLOBALS['wp_rewrite']->extra_rules_top[ $pattern ] ), 'Foreign persisted route shadowed.' );
file13_check( 'persisted' === ( $GLOBALS['rewrite_conflicts'][0][2] ?? '' ), 'Persisted collision evidence missing.' );
$flush_before = $GLOBALS['flush_count'];
Plugin::activate();
file13_check( $flush_before === $GLOBALS['flush_count'], 'Activation flushed foreign route.' );

file13_reset_rewrite_state();
$GLOBALS['wp_rewrite']->extra_rules_top[ $pattern ] = $target;
$GLOBALS['stored_rewrite_rules'][ $pattern ] = $target;
file13_check( true === Renderer::register_rewrite(), 'Matching own mapping rejected.' );

file13_reset_rewrite_state();
$flush_before = $GLOBALS['flush_count'];
Plugin::activate();
file13_check( $flush_before + 1 === $GLOBALS['flush_count'], 'Clean activation did not flush.' );
file13_check( $target === ( $GLOBALS['flushed_rules'][ $pattern ] ?? '' ), 'Clean activation missing own route.' );
file13_check( empty( $GLOBALS['rewrite_conflicts'] ), 'False collision on clean activation.' );

// Preview must be bound to the actual canonical rewrite, not a query parameter.
function get_query_var( $key ) { return 'swi_intro_preview' === $key ? ( $GLOBALS['preview_query_var'] ?? 0 ) : 0; }
function home_url( $path = '/' ) { return 'https://example.test/subsite' . $path; }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function wp_unslash( $value ) { return stripslashes( $value ); }
function untrailingslashit( $value ) { return rtrim( $value, '/' ); }
$GLOBALS['preview_query_var'] = 1;
$GLOBALS['wp'] = (object) array( 'matched_rule' => $pattern );
$_SERVER['REQUEST_URI'] = '/subsite/?swi_intro_preview=1';
file13_check( false === Renderer::is_preview_request(), 'Public home query spoof reached admin preview.' );
$_SERVER['REQUEST_URI'] = '/subsite/account/?swi_intro_preview=1';
file13_check( false === Renderer::is_preview_request(), 'Public account query spoof reached admin preview.' );
$_SERVER['REQUEST_URI'] = '/subsite/welcome-intro-preview/?state=reduced';
file13_check( true === Renderer::is_preview_request(), 'Canonical subdirectory preview was rejected.' );
$_SERVER['REQUEST_URI'] = '/subsite/welcome-intro-preview';
file13_check( true === Renderer::is_preview_request(), 'Canonical preview without trailing slash was rejected.' );
$_SERVER['REQUEST_URI'] = '/subsite/welcome-intro-preview-other/?swi_intro_preview=1';
file13_check( false === Renderer::is_preview_request(), 'Lookalike route reached admin preview.' );
$GLOBALS['preview_query_var'] = 0;
$_SERVER['REQUEST_URI'] = '/subsite/welcome-intro-preview/';
file13_check( false === Renderer::is_preview_request(), 'Missing rewrite query var was accepted.' );
$GLOBALS['preview_query_var'] = 1;
$GLOBALS['wp']->matched_rule = '^other/?$';
file13_check( false === Renderer::is_preview_request(), 'GET spoof on canonical path bypassed matched-rule ownership.' );
$GLOBALS['wp']->matched_rule = $pattern;
unset( $_SERVER['REQUEST_URI'] );
file13_check( false === Renderer::is_preview_request(), 'Missing request URI was accepted.' );
$_SERVER['REQUEST_URI'] = '/subsite/welcome-intro-preview/';
file13_reset_rewrite_state();
$GLOBALS['wp_rewrite']->extra_rules_top[ $pattern ] = 'index.php?foreign=1';
file13_check( false === Renderer::register_rewrite(), 'Foreign preview route was accepted.' );
file13_check( false === Renderer::is_preview_request(), 'Foreign-owned route with query spoof reached admin preview.' );
file13_reset_rewrite_state();
file13_check( true === Renderer::register_rewrite(), 'Clean route registration rejected after collision.' );
file13_check( true === Renderer::is_preview_request(), 'Clean route registration did not restore canonical preview.' );
// PHP 8 rejects arrays passed to sanitize_key(); the preview must not fatal.
function sanitize_key( $value ) {
    if ( ! is_string( $value ) ) { throw new TypeError( 'Expected string preview state.' ); }
    return strtolower( preg_replace( '/[^a-z0-9_-]/', '', $value ) );
}
$_GET['state'] = array( 'reduced' );
file13_check( 'default' === Renderer::normalized_preview_state(), 'Array state caused an unsafe preview parse.' );
$_GET['state'] = 'reduced';
file13_check( 'reduced' === Renderer::normalized_preview_state(), 'Valid reduced state was rejected.' );
$_GET['state'] = 'unknown';
file13_check( 'default' === Renderer::normalized_preview_state(), 'Unknown state did not fail closed.' );
unset( $_GET['state'] );
file13_check( 'default' === Renderer::normalized_preview_state(), 'Missing state did not use safe default.' );
echo "File 13 preview rewrite ownership and deactivation: PASS\n";
