<?php
$root = dirname( __DIR__ );
$plugin = $root . '/sabri-welcome-intro-13';

$required = array(
	$plugin . '/sabri-welcome-intro.php',
	$plugin . '/includes/class-plugin.php',
	$plugin . '/includes/class-settings.php',
	$plugin . '/includes/class-authorization.php',
	$plugin . '/includes/class-eligibility.php',
	$plugin . '/includes/class-renderer.php',
	$plugin . '/includes/class-analytics.php',
	$plugin . '/includes/class-rest.php',
	$plugin . '/includes/class-health.php',
	$plugin . '/includes/class-foundation.php',
	$plugin . '/includes/class-admin.php',
	$plugin . '/assets/js/welcome-intro.js',
	$plugin . '/uninstall.php',
	$root . '/docs/TRACEABILITY.md',
	$root . '/docs/TWENTY-PASS-AUDIT.md',
	$root . '/tools/build-package.sh',
	$root . '/tests/js-behavior.test.js',
);

$failures = array();
foreach ( $required as $file ) {
	if ( ! is_file( $file ) || 0 === filesize( $file ) ) { $failures[] = 'missing:' . $file; }
}

$checks = array(
	$root . '/docs/TRACEABILITY.md' => array( 'Founder-approved File 13 plan v1.0', 'schema migration is current', 'audit gap each changes' ),
	$root . '/docs/MIGRATION.md' => array( 'schema 1.0.1 is persisted', 'dated Founder change-control record', '13-sabri-welcome-intro' ),
	$root . '/docs/ROLLBACK.md' => array( 'schema migration is current', 'backup/restore proof', 'Founder authorization' ),
	$root . '/README.md' => array( 'fail-closed in this source candidate', 'dated Founder-approved change-control evidence', 'package-identity change-control approval remains unverified' ),
	$plugin . '/includes/class-plugin.php' => array( 'Settings::register()', 'Renderer::register()', 'Rest::register()', 'Health::register()' ),
	$plugin . '/includes/class-renderer.php' => array( 'swi_intro_legacy_invocation_blocked', 'sabri_shell_file25_visual_contract', 'visual_contract_status', 'file-25', 'surface_strong', '#087a4e' ),
	$plugin . '/assets/js/welcome-intro.js' => array( 'cfg.preview', 'swi-welcome-intro-preview', 'Escape', 'prefers-reduced-motion' ),
	$plugin . '/includes/class-settings.php' => array( '$out[\'enabled\'] = false', '$out[\'status\'] = \'disabled\'', '$out[\'analytics_enabled\'] = false', 'config_version', 'option_value=%s', 'swi_intro_audit_contention' ),
	$plugin . '/includes/class-foundation.php' => array( 'SPF_Registry', 'Welcome Intro Historical Compatibility', 'file13-welcome-intro-preview', 'file-20', 'file-25', 'legacy-suppression', 'map_route', 'register_manifest', 'swi_foundation_partial_sync', 'swi_foundation_module_protected' ),
	$plugin . '/includes/class-health.php' => array( 'spcrc/file13_contract_state', 'legacy_public_disabled', 'invocation_frequency', 'legacy_public_renderer_registered', "? 'compatible'", 'Settings::stored()', 'legacy_stored_activation_detected' ),
	$plugin . '/includes/class-authorization.php' => array( 'current_user_can( self::DEFAULT_CAPABILITY )', 'return $allowed && $institutional', 'swi_intro_authorization_decision' ),
	$plugin . '/includes/class-analytics.php' => array( 'register_retention', 'option_id > %d', 'ORDER BY option_id ASC' ),
	$plugin . '/uninstall.php' => array( 'swi_intro_audit_gap', 'option_id > %d', 'SWI_PURGE_ON_UNINSTALL' ),
	$root . '/tools/build-package.sh' => array( 'python3', 'os.replace', 'NamedTemporaryFile' ),
);
foreach ( $checks as $file => $needles ) {
	$body = is_file( $file ) ? file_get_contents( $file ) : '';
	foreach ( $needles as $needle ) {
		if ( false === strpos( $body, $needle ) ) { $failures[] = basename( $file ) . ':missing-contract:' . $needle; }
	}
}

$all = '';
foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $plugin ) ) as $entry ) {
	if ( $entry->isFile() ) { $all .= file_get_contents( $entry->getPathname() ); }
}
foreach ( array( 'http://fonts.', 'https://fonts.', '#ff8a1f' ) as $forbidden ) {
	if ( false !== stripos( $all, $forbidden ) ) { $failures[] = 'forbidden-source-token:' . $forbidden; }
}

$plugin_bootstrap = file_get_contents( $plugin . '/includes/class-plugin.php' );
$renderer = file_get_contents( $plugin . '/includes/class-renderer.php' );
$rest = file_get_contents( $plugin . '/includes/class-rest.php' );
$admin = file_get_contents( $plugin . '/includes/class-admin.php' );
$javascript = file_get_contents( $plugin . '/assets/js/welcome-intro.js' );

if ( false !== strpos( $plugin_bootstrap, 'Analytics::register()' ) ) { $failures[] = 'legacy-public-analytics-must-not-register'; }
if ( false === strpos( $plugin_bootstrap, 'Analytics::register_retention()' ) ) { $failures[] = 'legacy-retention-cron-must-register'; }
if ( false !== strpos( $plugin_bootstrap, 'Eligibility::register()' ) ) { $failures[] = 'legacy-public-eligibility-must-not-register'; }
if ( false !== strpos( $renderer, "add_action( 'sabri_shell_welcome_intro_invoke'" ) ) { $failures[] = 'legacy-public-renderer-must-not-register'; }
if ( false !== strpos( $rest, "'/config'" ) ) { $failures[] = 'legacy-config-rest-write-must-not-exist'; }
if ( false !== strpos( $admin, 'swi_save_intro_config' ) ) { $failures[] = 'legacy-config-admin-write-must-not-exist'; }
foreach ( array( 'localStorage', 'sessionStorage', 'fetch(', 'admin-ajax.php' ) as $forbidden ) {
	if ( false !== strpos( $javascript, $forbidden ) ) { $failures[] = 'compatibility-preview-forbidden-token:' . $forbidden; }
}

if ( $failures ) {
	fwrite( STDERR, implode( PHP_EOL, $failures ) . PHP_EOL );
	exit( 1 );
}
echo "File 13 governed static contract suite: PASS\n";
