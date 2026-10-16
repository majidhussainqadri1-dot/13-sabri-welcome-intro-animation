<?php
/** Historical-only responsive preview regression; no WordPress/live assertion. */
define( 'ABSPATH', __DIR__ );
define( 'SWI_TEXT_DOMAIN', 'sabri-welcome-intro' );
function __( $message, $domain = '' ) { return $domain . ':' . $message; }
function sanitize_key( $value ) { return strtolower( (string) $value ); }
function wp_unslash( $value ) { return $value; }
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-renderer.php';
use Sabri\WelcomeIntro\Renderer;

function verify_preview_viewport( $ok, $message ) {
    if ( ! $ok ) { fwrite( STDERR, $message . PHP_EOL ); exit( 1 ); }
}
unset( $_GET['viewport'], $_GET['embed'] );
verify_preview_viewport( 'auto' === Renderer::normalized_preview_viewport(), 'Default viewport must be auto.' );
verify_preview_viewport( false === Renderer::preview_embed_requested(), 'Default must not embed.' );
foreach ( array( 'auto', 'mobile', 'tablet', 'desktop' ) as $valid ) {
    $_GET['viewport'] = $valid;
    verify_preview_viewport( $valid === Renderer::normalized_preview_viewport(), 'Approved viewport rejected: ' . $valid );
}
foreach ( array( 'evil', '../admin', '<script>', array( 'mobile' ), new stdClass(), 360, null ) as $invalid ) {
    $_GET['viewport'] = $invalid;
    verify_preview_viewport( 'auto' === Renderer::normalized_preview_viewport(), 'Invalid viewport accepted.' );
}
$_GET['embed'] = '1';
verify_preview_viewport( true === Renderer::preview_embed_requested(), 'Explicit nested-frame mode unavailable.' );
foreach ( array( '', 'true', '2', array( '1' ), 1, null ) as $invalid ) {
    $_GET['embed'] = $invalid;
    verify_preview_viewport( false === Renderer::preview_embed_requested(), 'Malformed nested-frame mode accepted.' );
}
$translated = Renderer::preview_viewport_labels();
verify_preview_viewport( array_keys( $translated ) === array( 'auto', 'mobile', 'tablet', 'desktop' ), 'Viewport label keys drifted.' );
foreach ( array(
    'auto' => 'Automatic',
    'mobile' => 'Mobile',
    'tablet' => 'Tablet',
    'desktop' => 'Desktop',
) as $key => $english ) {
    verify_preview_viewport( SWI_TEXT_DOMAIN . ':' . $english === $translated[ $key ], 'Viewport label bypassed WordPress textdomain: ' . $key );
}
$source = file_get_contents( dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-renderer.php' );
foreach ( array(
    'if ( ! $embedded )', "'mobile' => 360", "'tablet' => 768", "'desktop' => 1280",
    "'embed' => '1'", 'sandbox="allow-scripts"', 'referrerpolicy="same-origin"',
    'swi-preview-viewport-', 'aria-current="page"', 'esc_url( $frame_url )',
    'esc_attr( $widths[ $viewport ] )', "Authorization::require_manage( 'preview_intro' )",
    'nocache_headers()', 'X-Robots-Tag: noindex',
    'class="swi-preview-viewports"', 'min-block-size:44px', 'min-inline-size:44px',
    '.swi-preview-viewports a:focus-visible', 'esc_html( $label )',
) as $marker ) {
    verify_preview_viewport( str_contains( $source, $marker ), 'Missing preview safety/viewport contract: ' . $marker );
}
echo "File13 allowlisted isolated responsive preview states: PASS\n";
