<?php
/** Historical-only responsive preview regression; no WordPress/live assertion. */
define( 'ABSPATH', __DIR__ );
define( 'SWI_TEXT_DOMAIN', 'sabri-welcome-intro' );
function __( $message, $domain = '' ) { return $domain . ':' . $message; }
function sanitize_key( $value ) { return strtolower( (string) $value ); }
function wp_unslash( $value ) { return $value; }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-renderer.php';
use Sabri\WelcomeIntro\Renderer;

function verify_preview_viewport( $ok, $message ) {
    if ( ! $ok ) { fwrite( STDERR, $message . PHP_EOL ); exit( 1 ); }
}
unset( $_GET['viewport'], $_GET['embed'] );
verify_preview_viewport( 'auto' === Renderer::normalized_preview_viewport(), 'Default viewport must be auto.' );
verify_preview_viewport( false === Renderer::preview_embed_requested(), 'Default must not embed.' );
foreach ( array( 'auto', 'mobile-small', 'mobile', 'tablet', 'desktop' ) as $valid ) {
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
verify_preview_viewport( array_keys( $translated ) === array( 'auto', 'mobile-small', 'mobile', 'tablet', 'desktop' ), 'Viewport label keys drifted.' );
foreach ( array(
    'auto' => 'Automatic',
    'mobile-small' => 'Small mobile (320px)',
    'mobile' => 'Mobile',
    'tablet' => 'Tablet',
    'desktop' => 'Desktop',
) as $key => $english ) {
    verify_preview_viewport( SWI_TEXT_DOMAIN . ':' . $english === $translated[ $key ], 'Viewport label bypassed WordPress textdomain: ' . $key );
}
// Exercise the REAL privately rendered stylesheet, not only a static source
// substring. The historical Skip button needs a >=44px target even at
// desktop widths, where the mobile @media rule does not apply.
$css_method = new ReflectionMethod( Renderer::class, 'style_block' );
$css = $css_method->invoke( null, array(
    'primary' => '#087a4e', 'dark' => '#171717', 'light' => '#e7f5ee',
) );
$desktop_css = explode( '@media(max-width:560px)', $css )[0];
verify_preview_viewport(
    1 === preg_match( '/\\.swi-intro__skip\\{[^}]*min-block-size:44px[^}]*min-inline-size:44px/', $desktop_css ),
    'Desktop-width Skip button missing minimum 44px interactive target.'
);
verify_preview_viewport(
    str_contains( $css, '.swi-intro__skip:focus-visible' )
        && str_contains( $css, '@media(max-width:560px)' ),
    'Skip keyboard focus or mobile responsive style disappeared.'
);
$source = file_get_contents( dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-renderer.php' );
foreach ( array(
    'if ( ! $embedded )', "'mobile-small' => 320", "'mobile' => 360", "'tablet' => 768", "'desktop' => 1280",
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
