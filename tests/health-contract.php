<?php
/** Standalone source contract regression, not staging or production evidence. */
namespace {
define( 'ABSPATH', __DIR__ );
define( 'SWI_VERSION', '1.0.1' );
define( 'SWI_SCHEMA_VERSION', '1.0.1' );
$GLOBALS['swi_schema_version'] = '1.0.1';
define( 'SWI_TEXT_DOMAIN', 'sabri-welcome-intro' );
define( 'SABRI_SHELL_VERSION', '1.4.17' );
function apply_filters( $name, $fallback ) {
    if ( 'sabri_shell_welcome_frequency_contract' === $name ) {
        return array_key_exists( 'swi_file20_welcome_contract', $GLOBALS ) ? $GLOBALS['swi_file20_welcome_contract'] : $fallback;
    }
    return $fallback;
}
function has_action( $hook, $callback = false ) { return isset( $GLOBALS['swi_test_hooks'][ $hook ] ) ? 10 : false; }
function has_filter( $hook, $callback = false ) {
    // Only File20's *exact native* callback can authorize contract readiness.
    if ( 'sabri_shell_welcome_frequency_contract' !== $hook ) { return false; }
    $native = array( \Sabri\UnifiedShell\WelcomeFrequency::class, 'contract' );
    return $native === $callback && ! empty( $GLOBALS['swi_native_welcome_callback_registered'] ) ? 10 : false;
}
function get_bloginfo( $field ) { return 'version' === $field ? '6.8' : ''; }
function __( $text, $domain = '' ) { return $text; }
function esc_html__( $text, $domain = '' ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $text ) { return (string) $text; }
function home_url( $path = '/' ) { return 'https://sabri.example' . $path; }
function admin_url( $path = '' ) { return 'https://sabri.example/wp-admin/' . $path; }
function wp_nonce_field( $action, $name ) { echo '<input type="hidden" name="nonce" value="test">'; }
function submit_button( $label, $type = '', $name = '', $wrap = '' ) { echo '<button>' . esc_html( $label ) . '</button>'; }
function get_option( $key, $default = false ) {
    if ( 'swi_intro_schema_version' === $key ) { return $GLOBALS['swi_schema_version']; }
    if ( 'swi_intro_config' === $key ) { return array_key_exists( 'swi_stored_override', $GLOBALS ) ? $GLOBALS['swi_stored_override'] : array( 'enabled' => false, 'status' => 'disabled', 'analytics_enabled' => false ); }
    if ( 'swi_intro_audit_gap' === $key ) { return $GLOBALS['swi_test_audit_gap'] ?? $default; }
    return $default;
}
function absint( $value ) { return abs( (int) $value ); }
}
namespace Sabri\UnifiedShell {
    // Intentionally only legacy-compatible plugin class/version: this does
    // NOT provide File20's newer WelcomeFrequency bridge/filter.
    final class FourPlanHarmonization {}
    // Class availability alone is not a registered WordPress callback.
    final class WelcomeFrequency { public static function contract( $prior = array() ) { return $prior; } }
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
        public static function preview_route_status() {
            $registered = $GLOBALS['swi_preview_registered'] ?? true;
            $persisted = $GLOBALS['swi_preview_persisted'] ?? true;
            $precedence = $GLOBALS['swi_preview_precedence'] ?? true;
            return array( 'registered' => $registered, 'persisted' => $persisted, 'precedence' => $precedence, 'available' => $registered && $persisted && $precedence );
        }
        public static function visual_contract_status() { return array( 'valid' => $GLOBALS['swi_visual_valid'] ?? true, 'provider_registered' => $GLOBALS['swi_visual_native_registered'] ?? true, 'owner' => 'file-25', 'version' => '1.9.0' ); }
    }
    final class Analytics { public static function ajax_event() {} }
    final class Authorization { public static function require_manage( $capability ) { if ( 'manage_intro' !== $capability ) { throw new \\LogicException( 'Wrong admin capability' ); } } }
    final class Eligibility { public static function safe_mode_active() { return false; } }
    final class Foundation { public static function status() { return array( 'available' => true, 'state' => $GLOBALS['swi_test_registry_state'] ?? 'synced' ); } }
    require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-health.php';
    require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-admin.php';
    $GLOBALS['swi_test_hooks'] = array();
    // Project RELEASE versions are distinct from supported plugin minimums.
    // Pure version fixtures are not an installed WordPress/staging acceptance test.
    $project_versions = array(
        array( '7.0.1', '8.3.0', true ),
        array( '7.0.2', '8.3.24', true ),
        array( '7.1.0', '8.4.0', true ),
        array( '7.0.0', '8.3.0', false ),
        array( '6.8', '8.3.0', false ),
        array( '7.0.1', '8.2.99', false ),
        array( '7.0.1', '8.1.0', false ),
        array( '6.8', '8.1.0', false ),
        array( '', '8.3.0', false ),
        array( '7.0.1-dev', '8.3.0', false ),
        array( '7.0.1', '', false ),
        array( '7.0.1', '8.3.0-dev', false ),
        array( array( '7.0.1' ), '8.3.0', false ),
        array( '7.0.1', array( '8.3.0' ), false ),
    );
    foreach ( $project_versions as $case ) {
        $runtime = Health::project_baseline_status( $case[0], $case[1] );
        if ( $runtime['meets_version_targets'] !== $case[2]
            || $runtime['acceptance_verification'] !== 'not_assessed'
            || $runtime['wordpress_required'] !== '7.0.1'
            || $runtime['php_required'] !== '8.3' ) {
            fwrite( STDERR, "Project WP/PHP target baseline incorrectly classified or falsely certified staging\\n" ); exit( 1 );
        }
    }
    $clean = Health::status();
    // File25 provider provenance is NOT interchangeable with a syntactically
    // valid contract, and is separate from historical File13 compatibility.
    if ( ! $clean['file25_visual_contract']['provider_registered']
        || ! $clean['file25_visual_contract']['valid'] ) {
        fwrite( STDERR, "Known canonical File25 provider was lost from health diagnostics\n" ); exit( 1 );
    }
    $GLOBALS['swi_visual_native_registered'] = false;
    $missing_visual_provider = Health::status();
    if ( $missing_visual_provider['file25_visual_contract']['provider_registered']
        || ! $missing_visual_provider['file25_visual_contract']['valid']
        || 'healthy' !== $missing_visual_provider['status'] ) {
        fwrite( STDERR, "Missing native File25 callback provenance was falsely reported\n" ); exit( 1 );
    }
    $GLOBALS['swi_visual_native_registered'] = true;
    $GLOBALS['swi_visual_valid'] = false;
    $incompatible_visual = Health::status();
    if ( ! $incompatible_visual['file25_visual_contract']['provider_registered']
        || $incompatible_visual['file25_visual_contract']['valid']
        || 'healthy' !== $incompatible_visual['status'] ) {
        fwrite( STDERR, "Registered but incompatible File25 callback provenance was not distinguished\n" ); exit( 1 );
    }
    unset( $GLOBALS['swi_visual_valid'], $GLOBALS['swi_visual_native_registered'] );
    if ( ! isset( $clean['project_baseline'] )
        || $clean['project_baseline']['wordpress_version'] !== '6.8'
        || $clean['project_baseline']['wordpress_meets_target']
        || $clean['project_baseline']['meets_version_targets']
        || 'not_assessed' !== $clean['project_baseline']['acceptance_verification'] ) {
        fwrite( STDERR, "Health incorrectly certified below-target WordPress or staging acceptance\\n" ); exit( 1 );
    }
    // Old File20 main and a newer unmerged draft both advertise 1.4.17.
    // Class/version presence must never be advertised as a verified
    // 30-day welcome-frequency contract.
    if ( ! $clean['file20_available'] || ! isset( $clean['file20_welcome_contract'] )
        || $clean['file20_welcome_contract']['valid'] ) {
        fwrite( STDERR, "Legacy File20 plugin presence falsely certified WelcomeFrequency contract\n" ); exit( 1 );
    }
    $approved_welcome = array(
        'owner' => 'file-20', 'version' => '1.0.0', 'days' => 30,
        'presentation_owner' => 'file-25',
        'guest_preference' => 'first-party-cookie-and-local-storage',
        'member_preference' => 'authenticated-user-meta',
        'no_file13_public_hook' => true,
        'live_verified' => false,
    );
    $GLOBALS['swi_file20_welcome_contract'] = $approved_welcome;
    $forged_status = Health::status();
    if ( $forged_status['file20_welcome_contract']['valid']
        || $forged_status['file20_welcome_contract']['provider_registered']
        || 'healthy' !== $forged_status['status'] ) {
        fwrite( STDERR, "Forged welcome filter without File20 native provider hook was accepted\n" ); exit( 1 );
    }
    $GLOBALS['swi_native_welcome_callback_registered'] = true;
    $approved_status = Health::status();
    if ( ! $approved_status['file20_welcome_contract']['valid']
        || ! $approved_status['file20_welcome_contract']['provider_registered']
        || 'healthy' !== $approved_status['status'] ) {
        fwrite( STDERR, "Canonical File20 WelcomeFrequency contract rejected or legacy health changed\n" ); exit( 1 );
    }
    $GLOBALS['swi_native_welcome_callback_registered'] = false;
    if ( Health::status()['file20_welcome_contract']['valid'] ) {
        fwrite( STDERR, "Native File20 callback removed but readiness remained true\n" ); exit( 1 );
    }
    $GLOBALS['swi_native_welcome_callback_registered'] = true;
    foreach ( array(
        array(),
        'not-an-array',
        array_replace( $approved_welcome, array( 'owner' => 'file-13' ) ),
        array_replace( $approved_welcome, array( 'version' => '0.9.0' ) ),
        array_replace( $approved_welcome, array( 'version' => '2.0.0' ) ),
        array_replace( $approved_welcome, array( 'version' => array( '1.0.0' ) ) ),
        array_replace( $approved_welcome, array( 'days' => '30' ) ),
        array_replace( $approved_welcome, array( 'days' => 29 ) ),
        array_replace( $approved_welcome, array( 'presentation_owner' => 'file-13' ) ),
        array_replace( $approved_welcome, array( 'guest_preference' => 'tracking-cookie' ) ),
        array_replace( $approved_welcome, array( 'member_preference' => 'shared-cookie' ) ),
        array_replace( $approved_welcome, array( 'no_file13_public_hook' => false ) ),
    ) as $invalid_contract ) {
        $GLOBALS['swi_file20_welcome_contract'] = $invalid_contract;
        $invalid_status = Health::status();
        if ( $invalid_status['file20_welcome_contract']['valid']
            || 'healthy' !== $invalid_status['status'] ) {
            fwrite( STDERR, "Malformed File20 welcome contract falsely certified or File13 compatibility incorrectly degraded\n" ); exit( 1 );
        }
    }
    unset( $GLOBALS['swi_file20_welcome_contract'] );
    if ( Health::status()['file20_welcome_contract']['valid'] ) {
        fwrite( STDERR, "Missing File20 welcome filter falsely certified after reset\n" ); exit( 1 );
    }
    if ( true !== $clean['legacy_public_disabled'] || 'healthy' !== $clean['status'] ) { fwrite( STDERR, "disabled baseline invalid\n" ); exit( 1 ); }
    $GLOBALS['swi_preview_registered'] = false;
    $missing = Health::status();
    if ( 'degraded' !== $missing['status'] || ! in_array( 'preview_rewrite_unregistered', $missing['issues'], true )
        || 'blocked' !== Health::file24_contract_state( 'unassessed' ) ) {
        fwrite( STDERR, "Unregistered preview was falsely certified healthy\n" ); exit( 1 );
    }
    unset( $GLOBALS['swi_preview_registered'] );
    $GLOBALS['swi_preview_persisted'] = false;
    $missing = Health::status();
    if ( 'degraded' !== $missing['status'] || ! in_array( 'preview_rewrite_not_persisted', $missing['issues'], true )
        || 'blocked' !== Health::file24_contract_state( 'unassessed' ) ) {
        fwrite( STDERR, "Missing persisted preview rewrite was falsely certified healthy\n" ); exit( 1 );
    }
    unset( $GLOBALS['swi_preview_persisted'] );
    $GLOBALS['swi_preview_precedence'] = false;
    $shadowed = Health::status();
    if ( 'degraded' !== $shadowed['status'] || ! in_array( 'preview_rewrite_shadowed', $shadowed['issues'], true )
        || 'blocked' !== Health::file24_contract_state( 'unassessed' ) ) {
        fwrite( STDERR, "Shadowed persisted preview was falsely certified healthy\n" ); exit( 1 );
    }
    unset( $GLOBALS['swi_preview_precedence'] );
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
    // Exercise the actual privileged Admin::render() output in all three
    // File25 readiness scenarios; source-only PHP fixtures, NOT staging.
    unset( $GLOBALS['swi_stored_override'] );
    foreach ( array(
        array( false, true, 'Native File 25 provider callback missing;' ),
        array( true, false, 'Native File 25 provider registered but visual contract incompatible;' ),
        array( true, true, 'Native File 25 visual contract detected;' ),
    ) as $case ) {
        $GLOBALS['swi_visual_native_registered'] = $case[0];
        $GLOBALS['swi_visual_valid'] = $case[1];
        ob_start();
        Admin::render();
        $output = ob_get_clean();
        if ( false === strpos( $output, 'File 25 visual contract:' )
            || false === strpos( $output, $case[2] )
            || false === strpos( $output, 'operational verification remains separate' )
                && false === strpos( $output, 'readiness not verified' ) ) {
            fwrite( STDERR, "Admin incorrectly reports File25 provenance/compatibility\n" ); exit( 1 );
        }
    }
    unset( $GLOBALS['swi_visual_native_registered'], $GLOBALS['swi_visual_valid'] );
    echo "File 13 legacy effective-public-disable health regression: PASS\n";
}
