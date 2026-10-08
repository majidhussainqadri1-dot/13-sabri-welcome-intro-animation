<?php
/** Standalone source contract regression, not staging or production evidence. */
namespace {
define( 'ABSPATH', __DIR__ );
define( 'SWI_VERSION', '1.0.1' );
define( 'SWI_TEXT_DOMAIN', 'sabri-welcome-intro' );
function has_action( $hook, $callback = false ) { return isset( $GLOBALS['swi_test_hooks'][ $hook ] ) ? 10 : false; }
function get_bloginfo( $field ) { return 'version' === $field ? '6.8' : ''; }
function get_option( $key, $default = false ) { return 'swi_intro_schema_version' === $key ? '1.0.1' : $default; }
function absint( $value ) { return abs( (int) $value ); }
}
namespace Sabri\WelcomeIntro {
    final class Settings {
        public const SCHEMA_OPTION = 'swi_intro_schema_version';
        public static function get() { return array( 'enabled' => false, 'status' => 'disabled', 'analytics_enabled' => false, 'config_version' => 1 ); }
        public static function stored() { return self::get(); }
    }
    final class Renderer {
        public static function invoke() {}
        public static function visual_tokens() { return array( 'primary' => '#087a4e' ); }
        public static function visual_contract_status() { return array( 'valid' => true, 'owner' => 'file-25', 'version' => '1.0.0' ); }
    }
    final class Analytics { public static function ajax_event() {} }
    final class Eligibility { public static function safe_mode_active() { return false; } }
    final class Foundation { public static function status() { return array( 'available' => true, 'state' => 'synced' ); } }
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
    echo "File 13 legacy effective-public-disable health regression: PASS\n";
}
