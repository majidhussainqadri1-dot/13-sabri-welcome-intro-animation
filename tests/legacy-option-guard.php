<?php
/**
 * File 13 historical option guard regression.
 * Source-only WordPress function stubs; not an installed-database or live test.
 */
define( 'ABSPATH', __DIR__ );
define( 'SWI_TEXT_DOMAIN', 'sabri-welcome-intro' );
function absint( $value ) { return abs( (int) $value ); }
function sanitize_key( $value ) { return strtolower( (string) $value ); }
function sanitize_text_field( $value ) { return trim( (string) $value ); }
function sanitize_textarea_field( $value ) { return trim( (string) $value ); }
function wp_parse_url( $value, $component = -1 ) { return parse_url( $value, $component ); }
function untrailingslashit( $value ) { return rtrim( $value, '/' ); }
require dirname( __DIR__ ) . '/sabri-welcome-intro-13/includes/class-settings.php';
use Sabri\WelcomeIntro\Settings;
function verify_legacy_guard( $condition, $message ) {
    if ( ! $condition ) { fwrite( STDERR, $message . PHP_EOL ); exit( 1 ); }
}
function verify_suppressed( $value, $reason ) {
    verify_legacy_guard( is_array( $value )
        && false === $value['enabled']
        && 'disabled' === $value['status']
        && false === $value['analytics_enabled'], $reason );
}

$historical = array_replace( Settings::defaults(), array(
    'enabled' => true, 'status' => 'active', 'analytics_enabled' => true,
    'config_version' => 27, 'heading' => 'Historical brand',
) );

// A scalar payload was previously returned as the old active array, bypassing
// the suppressive sanitizer. It must always repair the legacy persisted flags.
foreach ( array( 'invalid', false, null, 123, new stdClass() ) as $invalid ) {
    $guarded = Settings::guard_option_write( $invalid, $historical, Settings::OPTION );
    verify_suppressed( $guarded, 'Non-array option update preserved legacy activation.' );
    verify_legacy_guard( 27 === $guarded['config_version'], 'Non-array option update rewound the revision.' );
    verify_legacy_guard( 'Historical brand' === $guarded['heading'], 'Malformed input erased historical copy.' );
}

// A direct legacy update_option() caller must never impersonate an authorized,
// audited CAS revision: neither backward, forward, nor malformed revisions.
foreach ( array( 1, 999999, '0', '32', array( 20 ), null ) as $forged ) {
    $new = array(
        'enabled' => true, 'status' => 'active', 'analytics_enabled' => true,
        'config_version' => $forged, 'heading' => 'Accepted preview copy',
    );
    $guarded = Settings::guard_option_write( $new, $historical, Settings::OPTION );
    verify_suppressed( $guarded, 'Direct option write re-enabled the public runtime.' );
    verify_legacy_guard( 27 === $guarded['config_version'], 'Direct option write forged a revision.' );
    verify_legacy_guard( 'Accepted preview copy' === $guarded['heading'], 'Legitimate historical copy lost.' );
}

$corrupt = array_replace( $historical, array( 'config_version' => new stdClass() ) );
$guarded = Settings::guard_option_write( array( 'enabled' => true ), $corrupt, Settings::OPTION );
verify_suppressed( $guarded, 'Corrupt revision revived the legacy intro.' );
verify_legacy_guard( 1 === $guarded['config_version'], 'Corrupt stored revision not normalized.' );

$clean = Settings::guard_option_write( array( 'heading' => 'Welcome' ), Settings::defaults(), Settings::OPTION );
verify_suppressed( $clean, 'Fresh safe config became active.' );
verify_legacy_guard( 1 === $clean['config_version'], 'Fresh config revision not preserved.' );
echo "File 13 scalar/forged legacy option suppression and revision guard: PASS" . PHP_EOL;
