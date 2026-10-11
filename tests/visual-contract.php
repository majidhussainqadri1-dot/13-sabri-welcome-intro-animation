<?php
/** Source-only File 25 token contract regression. No staging/live assertion. */
define( 'ABSPATH', __DIR__ );
function apply_filters( $name, $fallback ) { return $GLOBALS['visual_contract'] ?? $fallback; }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) ); }
function sanitize_text_field( $value ) { return trim( (string) $value ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-renderer.php';
use Sabri\WelcomeIntro\Renderer;
$GLOBALS['visual_contract'] = array(
    'owner' => 'file-25', 'version' => '1.0.0',
    'tokens' => array( 'unrelated' => '#123456' ),
);
if ( Renderer::visual_contract_status()['valid'] ) { fwrite( STDERR, "unrelated token falsely accepted\n" ); exit( 1 ); }
$GLOBALS['visual_contract']['tokens'] = array(
    'primary_color' => '#123456', 'text' => 'javascript:alert(1)', 'surface_strong' => '#abcdef',
);
if ( Renderer::visual_contract_status()['valid'] ) { fwrite( STDERR, "malformed token falsely accepted\n" ); exit( 1 ); }
$GLOBALS['visual_contract']['tokens']['text'] = '#123abc';
if ( ! Renderer::visual_contract_status()['valid'] ) { fwrite( STDERR, "valid File 25 tokens rejected\n" ); exit( 1 ); }
$colors = Renderer::visual_tokens();
if ( '#123456' !== $colors['primary'] || '#123abc' !== $colors['dark'] || '#abcdef' !== $colors['light'] ) {
    fwrite( STDERR, "valid token mapping incorrect\n" ); exit( 1 );
}
$GLOBALS['visual_contract']['owner'] = array( 'file-25' );
$GLOBALS['visual_contract']['version'] = array( '1.0.0' );
set_error_handler( static function ( $severity, $message ) { throw new ErrorException( $message, 0, $severity ); } );
try { $malformed = Renderer::visual_contract_status(); }
finally { restore_error_handler(); }
if ( $malformed['valid'] ) { fwrite( STDERR, "Malformed visual contract accepted\n" ); exit( 1 ); }
// The authenticated legacy preview's forced reduced-motion state must
// suppress CSS entrance/exit animations even if the OS does not request it.
$style = new ReflectionMethod( Renderer::class, 'style_block' );
$css = $style->invoke( null, $colors );
if ( false === strpos( $css, '.swi-intro.swi-intro--reduced *{animation:none!important;transition:none!important;scroll-behavior:auto!important}' )
    || false === strpos( $css, '.swi-intro.swi-intro--reduced .swi-intro__line{inline-size:100%}' ) ) {
    fwrite( STDERR, "Forced reduced-motion preview CSS override missing\n" ); exit( 1 );
}
echo "File 13 / File 25 visual-token integrity and historical preview reduced-motion CSS: PASS\n";
