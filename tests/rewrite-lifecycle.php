<?php
/** Standalone rewrite lifecycle regression; no staging/live state implied. */
define( 'ABSPATH', __DIR__ );
$GLOBALS['wp_rewrite'] = (object) array( 'extra_rules_top' => array() );
$GLOBALS['flushed_rules'] = array();
$GLOBALS['cleared_hook'] = '';
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
}
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-renderer.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-analytics.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-plugin.php';
use Sabri\WelcomeIntro\Renderer;
use Sabri\WelcomeIntro\Plugin;
use Sabri\WelcomeIntro\Analytics;

$GLOBALS['wp_rewrite']->extra_rules_top[ Renderer::PREVIEW_REWRITE_PATTERN ] = 'index.php?foreign_owner=1';
Renderer::register_rewrite();
if ( 'index.php?foreign_owner=1' !== $GLOBALS['wp_rewrite']->extra_rules_top[ Renderer::PREVIEW_REWRITE_PATTERN ] ) {
    fwrite( STDERR, "Registration overwrote a foreign preview route.\n" );
    exit( 1 );
}
unset( $GLOBALS['wp_rewrite']->extra_rules_top[ Renderer::PREVIEW_REWRITE_PATTERN ] );
Renderer::register_rewrite();
$GLOBALS['wp_rewrite']->extra_rules_top['^unrelated/? = 'index.php?other=1';
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
echo "File 13 preview rewrite deactivation: PASS\n";
] = 'index.php?other=1';
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
echo "File 13 preview rewrite deactivation: PASS\n";
