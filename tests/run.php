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
	$plugin . '/includes/class-renderer.php' => array( 'sabri_shell_welcome_intro_invoke', 'sabri_shell_file25_visual_contract', 'visual_contract_status', 'file-25', 'surface_strong', '#087a4e' ),
	$plugin . '/includes/class-eligibility.php' => array( 'file-20-shell-placement', 'SafeMode', 'SWI_DISABLE_INTRO' ),
	$plugin . '/assets/js/welcome-intro.js' => array( 'swi.dismissed.until', 'Math.max(30', 'Escape', 'prefers-reduced-motion' ),
	$plugin . '/includes/class-settings.php' => array( "'enabled' => false", "'status' => 'disabled'", 'max( 30', 'config_version', 'eligible_paths', 'option_value=%s' ),
	$plugin . '/includes/class-foundation.php' => array( 'SPF_Registry', 'file13-welcome-intro-preview', 'file-20', 'file-25', 'map_route', 'register_manifest' ),
	$plugin . '/includes/class-analytics.php' => array( 'option_value=%s', 'analytics_contention', 'config_version' ),
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

if ( $failures ) {
	fwrite( STDERR, implode( PHP_EOL, $failures ) . PHP_EOL );
	exit( 1 );
}
echo "File 13 governed static contract suite: PASS\n";
