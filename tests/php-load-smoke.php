<?php

declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['swi_actions'] = array();
$GLOBALS['swi_options'] = array();
$GLOBALS['swi_activation_callback'] = null;

function plugin_dir_path( $file ) {
	return dirname( $file ) . '/';
}

function plugin_dir_url( $file ) {
	return 'https://example.test/wp-content/plugins/' . basename( dirname( $file ) ) . '/';
}

function register_activation_hook( $file, $callback ) {
	$GLOBALS['swi_activation_callback'] = $callback;
}

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	$GLOBALS['swi_actions'][] = array( $hook, $callback, $priority, $accepted_args );
}


function is_admin() {
	return false;
}

function get_option( $name, $default = false ) {
	return array_key_exists( $name, $GLOBALS['swi_options'] ) ? $GLOBALS['swi_options'][ $name ] : $default;
}

function add_option( $name, $value ) {
	$GLOBALS['swi_options'][ $name ] = $value;
	return true;
}

require dirname( __DIR__ ) . '/sabri-welcome-intro/sabri-welcome-intro.php';

assert( defined( 'SWI_VERSION' ) );
assert( '0.2.0' === SWI_VERSION );
assert( class_exists( 'SWI_Activator' ) );
assert( class_exists( 'SWI_Admin' ) );
assert( class_exists( 'SWI_Renderer' ) );
assert( class_exists( 'SWI_Plugin' ) );
assert( is_callable( $GLOBALS['swi_activation_callback'] ) );

call_user_func( $GLOBALS['swi_activation_callback'] );
assert( 1 === $GLOBALS['swi_options']['swi_enabled'] );

$plugin_loaded = null;
foreach ( $GLOBALS['swi_actions'] as $action ) {
	if ( 'plugins_loaded' === $action[0] ) {
		$plugin_loaded = $action[1];
		break;
	}
}
assert( is_callable( $plugin_loaded ) );
call_user_func( $plugin_loaded );

$registered_hooks = array_map(
	static function ( $action ) {
		return $action[0];
	},
	$GLOBALS['swi_actions']
);

assert( in_array( 'wp_head', $registered_hooks, true ) );
assert( in_array( 'wp_enqueue_scripts', $registered_hooks, true ) );
assert( in_array( 'wp_body_open', $registered_hooks, true ) );
assert( in_array( 'wp_footer', $registered_hooks, true ) );

fwrite( STDOUT, "Plugin load and activation smoke test passed.\n" );
