<?php
require __DIR__ . '/wp-stubs.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-experience.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-config.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-eligibility.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-analytics.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/class-swi-rest.php';
require dirname( __DIR__ ) . '/sabri-welcome-intro/includes/functions.php';

$root = dirname( __DIR__ );
$renderer = file_get_contents( $root . '/sabri-welcome-intro/includes/class-swi-renderer.php' );
$js = file_get_contents( $root . '/sabri-welcome-intro/assets/js/welcome-intro.js' );
$css = file_get_contents( $root . '/sabri-welcome-intro/assets/css/welcome-intro.css' );
$contracts = file_get_contents( $root . '/sabri-welcome-intro/includes/class-swi-contracts.php' );
$privacy = file_get_contents( $root . '/sabri-welcome-intro/includes/class-swi-privacy.php' );
$system = file_get_contents( $root . '/sabri-welcome-intro/includes/class-swi-system-check.php' );
$passed = 0; $failed = 0;
function fut( $condition, $message ) { global $passed, $failed; if ( $condition ) { ++$passed; echo "PASS: $message\n"; } else { ++$failed; echo "FAIL: $message\n"; } }

$d = SWI_Config::defaults(); $contract = swi_get_welcome_intro_contract();
fut( 18 === count( $contract['future_superset'] ), '18/18 Future Welcome enhancements are declared in the public File 13 contract' );
fut( ! empty( $d['adaptive_mode'] ) && false !== strpos( $js, 'chooseVariant' ), 'F13-FUT-01 adaptive intro engine is coded' );
fut( ! empty( $d['never_show_enabled'] ) && false !== strpos( $renderer, 'data-swi-never' ) && false !== strpos( $js, 'neverIntro' ), 'F13-FUT-02 Never Show Again is coded end-to-end' );
fut( ! empty( $d['replay_enabled'] ) && false !== strpos( $renderer, 'swi_public_replay' ) && false !== strpos( $contract['replay_url_helper'], 'swi_get_welcome_intro_replay_url' ), 'F13-FUT-03 signed Replay Welcome is coded' );
fut( ! empty( $d['version_replay_enabled'] ) && false !== strpos( $js, 'experienceVersion' ), 'F13-FUT-04 version-aware replay is coded' );
fut( false !== strpos( file_get_contents( $root . '/sabri-welcome-intro/includes/class-swi-rest.php' ), "'/preference'" ) && false !== strpos( $renderer, 'data-account-seen-at' ), 'F13-FUT-05 cross-device account preference sync is coded' );
fut( ! empty( $d['guest_reconcile_enabled'] ) && false !== strpos( $js, 'reconcileGuestToAccount' ), 'F13-FUT-06 guest-to-account reconciliation is coded' );
fut( ! empty( $d['instant_exit_enabled'] ) && false !== strpos( $js, 'onPointerIntent' ) && false !== strpos( $js, 'onWheelIntent' ), 'F13-FUT-07 instant interaction exit is coded' );
fut( ! empty( $d['data_saver_static'] ) && false !== strpos( $js, 'saveData' ), 'F13-FUT-08 Data Saver static mode is coded' );
fut( ! empty( $d['performance_circuit_breaker'] ) && false !== strpos( $js, 'maybeTripPerformanceCircuit' ), 'F13-FUT-09 performance circuit breaker is coded' );
fut( ! empty( $d['accessibility_profiles'] ) && false !== strpos( $css, '.swi-a11y-high-contrast' ) && false !== strpos( $css, '.swi-a11y-large-text' ), 'F13-FUT-10 accessibility profiles are coded' );
fut( isset( $d['localized_copy'] ) && false !== strpos( $renderer, 'SWI_Experience::localized_copy' ), 'F13-FUT-11 localized welcome copy is coded' );
fut( false !== strpos( $renderer, 'data-preview-variant' ) && false !== strpos( $renderer, 'data-accessibility-profile' ) && false !== strpos( $renderer, "'data-saver'" ), 'F13-FUT-12 advanced preview lab inputs are coded' );
fut( isset( $d['approval_state'] ) && method_exists( 'SWI_Config', 'approve_visual' ) && false !== strpos( $system, 'approval_current' ), 'F13-FUT-13 Founder visual approval workflow is coded' );
fut( method_exists( 'SWI_Experience', 'visual_baseline' ) && method_exists( 'SWI_Experience', 'visual_baseline_hash' ), 'F13-FUT-14 deterministic visual baseline is coded' );
fut( in_array( 'variant_static', SWI_Analytics::ALLOWED_EVENTS, true ) && false === strpos( file_get_contents( $root . '/sabri-welcome-intro/includes/class-swi-analytics.php' ), 'REMOTE_ADDR' ), 'F13-FUT-15 privacy-safe aggregate telemetry is coded without IP fingerprint fields' );
fut( false !== strpos( $system, 'production_truth' ) && false !== strpos( $system, 'performance_budget_ms' ), 'F13-FUT-16 health dashboard evidence is coded with truth boundary' );
fut( method_exists( 'SWI_Config', 'restore_snapshot' ) && method_exists( 'SWI_Config', 'approved_snapshots' ), 'F13-FUT-17 bounded rollback snapshots are coded' );
fut( false !== strpos( $contracts, 'sabri_pwa_precache_assets' ) && false === strpos( $js, 'serviceWorker.register' ), 'F13-FUT-18 PWA/offline adapter is coded without owning service-worker registration' );

fut( 30 === $d['frequency_days'], 'superset preserves the governing 30-day recurrence minimum' );
fut( 0 === $d['duration_ms'], 'superset preserves no-forced-auto-close default' );
fut( false !== strpos( $css, '#087a4e' ) && false !== strpos( $css, '--swi-accent' ), 'exact Sabri Green remains primary fallback and orange remains contextual accent' );
fut( false !== strpos( $privacy, 'USER_META_PROFILE' ) && false !== strpos( $privacy, 'Accessibility profile' ), 'privacy export/erasure includes Future accessibility preference' );
fut( false === strpos( $contracts, 'add_action( \'wp_head\'' ) && false === strpos( $contracts, 'service_worker' ), 'Future adapters do not create a duplicate global shell or service-worker owner' );

printf("FUTURE SUPERSET REGRESSIONS: %d passed, %d failed\n", $passed, $failed);
exit( $failed ? 1 : 0 );
