<?php
require __DIR__ . '/wp-stubs.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-experience.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-config.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-eligibility.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-analytics.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-rest.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-activator.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-system-check.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/functions.php';

$passed = 0; $failed = 0;
function check( $condition, $message ) { global $passed, $failed; if ( $condition ) { ++$passed; echo "PASS: $message\n"; return; } ++$failed; echo "FAIL: $message\n"; }

swi_test_reset();
$d = SWI_Config::defaults();
check( 1 === $d['enabled'], 'safe defaults enable only eligible routes' );
check( '1.1.0' === $d['experience_version'], 'Future Welcome experience version is explicit' );
check( 30 === $d['frequency_days'], 'default suppression is 30 days' );
check( 0 === $d['duration_ms'], 'default has no forced historical eight-second auto-close' );
check( 'The Tridimensional Healing System of Soul, Vital Force, and Matter' === $d['brand_claim'], 'default claim matches the governing welcome identity' );
check( array( '/' ) === $d['eligible_routes'], 'default eligible route is home only' );
check( in_array( '/clinical', $d['suppressed_prefixes'], true ), 'clinical routes are suppressed' );
check( in_array( '/emergency', $d['suppressed_prefixes'], true ), 'emergency routes are suppressed' );
check( 1 === $d['adaptive_mode'] && 1 === $d['never_show_enabled'] && 1 === $d['replay_enabled'], 'Future adaptive, never-show and replay defaults are enabled' );
check( 1 === $d['guest_reconcile_enabled'] && 1 === $d['data_saver_static'] && 1 === $d['performance_circuit_breaker'], 'Future reconciliation and resilience defaults are enabled' );

$raw = array_merge( $d, array(
	'frequency_days' => 1, 'duration_ms' => 99999, 'reduced_duration_ms' => 1, 'brand_name' => '<b>Safe Brand</b>', 'brand_language' => 'ur<script>',
	'eligible_routes' => "/\nhttps://example.test/learn/\n/../secret\n/learn//course/", 'suppressed_prefixes' => "/login\n/login\n/clinical/",
	'starts_at' => '2030-01-02 00:00:00 UTC', 'ends_at' => '2029-01-02 00:00:00 UTC', 'performance_budget_ms' => 1,
	'default_accessibility_profile' => 'INVALID',
	'localized_copy' => array( 'ur-PK' => array( 'name' => '<b>صابری</b>', 'claim' => 'خوش آمدید', 'language' => 'ur-PK' ) ),
) );
$s = SWI_Config::sanitize( $raw, false );
check( 30 === $s['frequency_days'], 'frequency cannot be configured below 30 days' );
check( 30000 === $s['duration_ms'], 'optional automatic close remains safety-bounded without restoring the obsolete eight-second cap' );
check( 250 === $s['reduced_duration_ms'], 'reduced-motion lower bound is enforced' );
check( 40 === $s['performance_budget_ms'], 'performance circuit minimum budget is bounded' );
check( 'auto' === $s['default_accessibility_profile'], 'invalid accessibility profile fails to auto' );
check( isset( $s['localized_copy']['ur-PK'] ) && 'صابری' === $s['localized_copy']['ur-PK']['name'], 'localized copy is sanitized without markup' );
check( 'Safe Brand' === $s['brand_name'], 'brand copy is stripped of markup' );
check( 'urscript' === $s['brand_language'], 'language tag is allowlisted' );
check( in_array( '/learn', $s['eligible_routes'], true ), 'absolute same-site path is normalized' );
check( in_array( '/learn/course', $s['eligible_routes'], true ), 'duplicate slashes are normalized' );
check( ! in_array( '/../secret', $s['eligible_routes'], true ), 'path traversal is rejected' );
$wild = SWI_Config::sanitize_routes( '*', array( '/' ) ); check( array( '*' ) === $wild, 'explicit wildcard remains a governed wildcard' );
check( array( '/login', '/clinical' ) === $s['suppressed_prefixes'], 'suppressed paths are deduplicated' );
check( '' === $s['starts_at'] && '' === $s['ends_at'], 'inverted schedule fails to an unrestricted safe state' );

$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = array_merge( $d, array( 'updated_at' => '2026-08-06T10:00:00+00:00', 'updated_by' => 7 ) );
$before = SWI_Config::get(); sleep( 1 ); $after = SWI_Config::get();
check( $before['updated_at'] === $after['updated_at'], 'reads do not falsify updated_at metadata' ); check( 7 === $after['updated_by'], 'reads preserve the recorded updater' );
add_filter( 'swi_runtime_config', static function ( $config ) { $config['frequency_days'] = 45; return $config; } );
check( 45 === SWI_Config::get()['frequency_days'], 'File 20/runtime config contract can impose stricter recurrence' );

swi_test_reset(); $GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = $d;
$conflict = SWI_Config::save( $d, 99 ); check( is_wp_error( $conflict ) && 'swi_config_conflict' === $conflict->get_error_code(), 'optimistic configuration conflict fails closed' );
$saved = SWI_Config::save( array_merge( $d, array( 'frequency_days' => 60 ) ), 1 );
check( is_array( $saved ) && 2 === $saved['config_version'], 'configuration save increments version' ); check( 60 === $saved['frequency_days'], 'configuration save persists approved value' );
check( count( SWI_Config::approved_snapshots() ) >= 1, 'configuration write preserves rollback snapshot evidence' );
$audit = SWI_Config::audit_log(); check( ! empty( $audit ) && 'config_updated' === $audit[0]['event'], 'configuration change creates audit evidence' ); check( in_array( 'frequency_days', $audit[0]['changed_keys'], true ), 'audit identifies changed field without storing copy values' );

swi_test_reset(); $GLOBALS['swi_test_options'][ 'swi_enabled' ] = 0; SWI_Activator::activate();
check( 0 === SWI_Config::get()['enabled'], 'legacy enable option migrates idempotently' ); check( '1.1.0' === get_option( SWI_Config::OPTION_SCHEMA, '' ), 'schema version is recorded' );
$version = SWI_Config::get()['config_version']; SWI_Activator::maybe_upgrade(); check( $version === SWI_Config::get()['config_version'], 'current activation is idempotent' );

function resolve_with( $path, array $config, $user = 0, $replay = false ) { $_SERVER['REQUEST_URI'] = $path; $GLOBALS['swi_test_current_user'] = $user; return ( new SWI_Eligibility() )->resolve( $config, false, $replay ); }
swi_test_reset(); $config = SWI_Config::defaults();
$r = ( new SWI_Eligibility() )->resolve( $config, true ); check( $r['eligible'] && 'authorized_preview' === $r['reason'], 'authorized preview overrides public frequency' );
$config['enabled'] = 0; $r = resolve_with( '/', $config ); check( ! $r['eligible'] && 'disabled' === $r['reason'], 'admin flag is an immediate kill switch' );
$config = SWI_Config::defaults(); add_filter( 'swi_force_disabled', '__return_true_for_test' ); function __return_true_for_test() { return true; }
$r = resolve_with( '/', $config ); check( ! $r['eligible'] && 'safe_mode' === $r['reason'], 'File 20/24 safe mode fails closed' );
swi_test_reset(); $config = SWI_Config::defaults(); $GLOBALS['swi_test_admin'] = true; $r = resolve_with( '/', $config ); check( ! $r['eligible'] && 'non_visual_request' === $r['reason'], 'admin requests never receive the intro' );
swi_test_reset(); $GLOBALS['swi_test_feed'] = true; $r = resolve_with( '/', $config ); check( ! $r['eligible'] && 'non_page_request' === $r['reason'], 'feeds never receive visual overlay' );
swi_test_reset(); $r = resolve_with( '/login/reset', $config ); check( ! $r['eligible'] && 'suppressed_route' === $r['reason'], 'login and recovery paths are suppressed by prefix' );
$r = resolve_with( '/learn', $config ); check( ! $r['eligible'] && 'route_not_eligible' === $r['reason'], 'non-allowlisted deep links are not interrupted' );
$r = resolve_with( '/', $config ); check( $r['eligible'] && 'eligible' === $r['reason'], 'first eligible home visit is allowed' );
$GLOBALS['swi_test_user_meta'][5][SWI_Config::USER_META_LAST] = time() - DAY_IN_SECONDS; $GLOBALS['swi_test_user_meta'][5][SWI_Config::USER_META_EXPERIENCE] = '1.1.0';
$r = resolve_with( '/', $config, 5 ); check( ! $r['eligible'] && 'account_frequency_suppressed' === $r['reason'], 'logged-in account timestamp is preferred for suppression' );
$GLOBALS['swi_test_user_meta'][5][SWI_Config::USER_META_NEVER] = 1; $r = resolve_with( '/', $config, 5 ); check( ! $r['eligible'] && 'account_never_show' === $r['reason'], 'Never Show Again suppresses ordinary account visits' );
$r = resolve_with( '/', $config, 5, true ); check( $r['eligible'] && 'authorized_replay' === $r['reason'], 'signed replay bypasses recurrence/never-show only after other server gates' );
$GLOBALS['swi_test_user_meta'][5][SWI_Config::USER_META_NEVER] = 0; $GLOBALS['swi_test_user_meta'][5][SWI_Config::USER_META_EXPERIENCE] = '1.0.0';
$r = resolve_with( '/', $config, 5 ); check( $r['eligible'], 'new experience version may replay despite recent prior-version dismissal' );
$GLOBALS['swi_test_user_meta'][5][SWI_Config::USER_META_LAST] = time() - 31 * DAY_IN_SECONDS; $GLOBALS['swi_test_user_meta'][5][SWI_Config::USER_META_EXPERIENCE] = '1.1.0'; $r = resolve_with( '/', $config, 5 ); check( $r['eligible'], 'eligible visit resumes after the 30-day minimum' );
add_filter( 'swi_eligibility_decision', static function () { return 'invalid'; } ); $r = resolve_with( '/', $config, 0 ); check( ! $r['eligible'] && 'invalid_filter_result' === $r['reason'], 'malformed integration filter fails closed' );

swi_test_reset(); $analytics = new SWI_Analytics(); check( false === $analytics->record( 'shown', 1 ), 'analytics is opt-in and disabled by default' );
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = array_merge( SWI_Config::defaults(), array( 'analytics_enabled' => 1 ) );
check( true === $analytics->record( 'shown', 1 ), 'allowed aggregate event is counted' ); check( true === $analytics->record( 'variant_static', 1 ), 'privacy-safe adaptive variant aggregate is counted' ); check( false === $analytics->record( 'fingerprint', 1 ), 'unknown or fingerprint-like event is rejected' );
$metrics = $analytics->all(); $day = gmdate( 'Y-m-d' ); check( 2 === $metrics[$day]['_total'], 'aggregate stores totals only' ); check( ! isset( $metrics[$day]['ip'] ) && ! isset( $metrics[$day]['user_agent'] ), 'aggregate contains no network or device identifiers' );

swi_test_reset(); $GLOBALS['swi_test_current_user'] = 9; $GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = SWI_Config::defaults(); $rest = new SWI_REST( new SWI_Analytics() );
$invalid_dismiss = $rest->dismiss( new WP_REST_Request( array( 'event' => 'shown', 'config_version' => 1, 'idempotency_key' => 'a' ) ) ); check( is_wp_error( $invalid_dismiss ) && 'swi_invalid_dismissal_event' === $invalid_dismiss->get_error_code(), 'shown/error events cannot mutate account dismissal state' );
$valid_dismiss = $rest->dismiss( new WP_REST_Request( array( 'event' => 'skipped', 'config_version' => 1, 'experience_version' => '1.1.0', 'idempotency_key' => 'b' ) ) ); check( is_array( $valid_dismiss ) && true === $valid_dismiss['ok'], 'allowlisted authenticated dismissal succeeds' );
check( get_user_meta( 9, SWI_Config::USER_META_LAST, true ) > 0 && '1.1.0' === get_user_meta( 9, SWI_Config::USER_META_EXPERIENCE, true ), 'valid dismissal writes account timestamp and experience version' );
$duplicate_dismiss = $rest->dismiss( new WP_REST_Request( array( 'event' => 'skipped', 'config_version' => 1, 'idempotency_key' => 'b' ) ) ); check( is_array( $duplicate_dismiss ) && true === $duplicate_dismiss['duplicate'], 'dismissal idempotency prevents duplicate side effects' );
$pref = $rest->preference( new WP_REST_Request( array( 'event' => 'preference', 'config_version' => 1, 'last_seen' => time(), 'never_show' => 1, 'accessibility_profile' => 'large-text', 'experience_version' => '1.1.0', 'idempotency_key' => 'pref-1' ) ) );
check( is_array( $pref ) && true === $pref['ok'], 'cross-device preference reconciliation succeeds for authenticated user' ); check( 1 == get_user_meta( 9, SWI_Config::USER_META_NEVER, true ), 'cross-device reconciliation persists Never Show Again' ); check( 'large-text' === get_user_meta( 9, SWI_Config::USER_META_PROFILE, true ), 'cross-device reconciliation persists accessibility profile' );
$disabled_event = $rest->event( new WP_REST_Request( array( 'event' => 'shown', 'config_version' => 1, 'idempotency_key' => 'c' ), array( 'X-SWI-Nonce' => 'valid-swi_public_event' ) ) ); check( is_array( $disabled_event ) && true === $disabled_event['disabled'], 'public analytics endpoint fails closed while analytics is disabled' );
$GLOBALS['swi_test_options'][ SWI_Config::OPTION_CONFIG ] = array_merge( SWI_Config::defaults(), array( 'analytics_enabled' => 1 ) );
$bad_nonce = $rest->event( new WP_REST_Request( array( 'event' => 'shown', 'config_version' => 1, 'idempotency_key' => 'd' ), array( 'X-SWI-Nonce' => 'bad' ) ) ); check( is_wp_error( $bad_nonce ) && 'swi_invalid_nonce' === $bad_nonce->get_error_code(), 'public aggregate event requires its separate nonce' );
$good_event = $rest->event( new WP_REST_Request( array( 'event' => 'shown', 'config_version' => 1, 'idempotency_key' => 'e' ), array( 'X-SWI-Nonce' => 'valid-swi_public_event' ) ) ); check( is_array( $good_event ) && true === $good_event['ok'], 'valid aggregate event succeeds without identity data' );

$check = SWI_System_Check::snapshot(); check( '1.1.0' === $check['plugin_version'], 'system check exposes exact plugin version' ); check( 'healthy' === $check['status'], 'system check verifies packaged assets' ); check( 30 === $check['frequency_days'], 'system check exposes governed frequency' ); check( false === $check['approval_current'], 'unapproved visual baseline is truthfully not marked Founder-approved' );
$contract = swi_get_welcome_intro_contract(); check( 'sabri.file13.welcome-intro' === $contract['contract'], 'versioned File 20 contract is exposed' ); check( '1.1.0' === $contract['version'], 'Future Superset contract version is explicit' ); check( true === $contract['fail_open'], 'contract declares fail-open behavior' ); check( 30 === $contract['frequency_min_days'], 'contract declares 30-day minimum' ); check( 18 === count( $contract['future_superset'] ), 'contract advertises exactly the approved 18 Future enhancements' );

$external = swi_get_welcome_intro_replay_url( 'https://evil.example/path' ); check( 0 === strpos( $external, 'https://example.test/' ) && false === strpos( $external, 'evil.example' ), 'replay helper never leaks nonce to an external host' );

printf("PHP CONTRACT TESTS: %d passed, %d failed\n", $passed, $failed);
exit( $failed > 0 ? 1 : 0 );
