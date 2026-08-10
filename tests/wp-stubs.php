<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'DAY_IN_SECONDS', 86400 );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'SWI_VERSION', '1.1.0' );
define( 'SWI_SCHEMA_VERSION', '1.1.0' );
define( 'SWI_DIR', dirname( __DIR__ ) . '/sabri-welcome-intro/' );
define( 'SWI_URL', 'https://example.test/wp-content/plugins/sabri-welcome-intro/' );

$GLOBALS['swi_test_options'] = array();
$GLOBALS['swi_test_user_meta'] = array();
$GLOBALS['swi_test_transients'] = array();
$GLOBALS['swi_test_filters'] = array();
$GLOBALS['swi_test_actions'] = array();
$GLOBALS['swi_test_current_user'] = 0;
$GLOBALS['swi_test_admin'] = false;
$GLOBALS['swi_test_ajax'] = false;
$GLOBALS['swi_test_json'] = false;
$GLOBALS['swi_test_feed'] = false;
$GLOBALS['swi_test_robots'] = false;
$GLOBALS['swi_test_trackback'] = false;
$GLOBALS['swi_test_embed'] = false;

class WP_Error {
	private $code; private $message; private $data;
	public function __construct( $code = '', $message = '', $data = null ) { $this->code = $code; $this->message = $message; $this->data = $data; }
	public function get_error_code() { return $this->code; }
	public function get_error_message() { return $this->message; }
	public function get_error_data() { return $this->data; }
}
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function __( $value ) { return $value; }
function esc_html__( $value ) { return $value; }
function absint( $value ) { return abs( (int) $value ); }
function sanitize_key( $value ) { return strtolower( preg_replace( '/[^a-zA-Z0-9_\-]/', '', (string) $value ) ); }
function wp_strip_all_tags( $value ) { return trim( strip_tags( (string) $value ) ); }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function untrailingslashit( $value ) { return rtrim( (string) $value, '/\\' ); }
function trailingslashit( $value ) { return untrailingslashit( $value ) . '/'; }
function wp_unslash( $value ) { return $value; }
function wp_json_encode( $value ) { return json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); }
function wp_timezone() { return new DateTimeZone( 'Asia/Karachi' ); }
function wp_generate_uuid4() { return '11111111-2222-4333-8444-555555555555'; }
function home_url( $path = '/' ) { return 'https://example.test' . ( '/' === substr( $path, 0, 1 ) ? $path : '/' . $path ); }
function esc_url_raw( $url ) { return filter_var( $url, FILTER_SANITIZE_URL ); }
function wp_validate_redirect( $url, $fallback = '' ) { $host = parse_url( $url, PHP_URL_HOST ); return $host && 'example.test' !== strtolower( $host ) ? $fallback : $url; }
function add_query_arg( $args, $url ) { $separator = false === strpos( $url, '?' ) ? '?' : '&'; return $url . $separator . http_build_query( $args ); }
function wp_create_nonce( $action ) { return 'valid-' . $action; }
function get_current_user_id() { return (int) $GLOBALS['swi_test_current_user']; }
function is_user_logged_in() { return get_current_user_id() > 0; }
function is_admin() { return (bool) $GLOBALS['swi_test_admin']; }
function wp_doing_ajax() { return (bool) $GLOBALS['swi_test_ajax']; }
function wp_is_json_request() { return (bool) $GLOBALS['swi_test_json']; }
function is_feed() { return (bool) $GLOBALS['swi_test_feed']; }
function is_robots() { return (bool) $GLOBALS['swi_test_robots']; }
function is_trackback() { return (bool) $GLOBALS['swi_test_trackback']; }
function is_embed() { return (bool) $GLOBALS['swi_test_embed']; }
function get_option( $name, $default = false ) { return array_key_exists( $name, $GLOBALS['swi_test_options'] ) ? $GLOBALS['swi_test_options'][ $name ] : $default; }
function add_option( $name, $value ) { if ( array_key_exists( $name, $GLOBALS['swi_test_options'] ) ) return false; $GLOBALS['swi_test_options'][ $name ] = $value; return true; }
function update_option( $name, $value ) { $changed = ! array_key_exists( $name, $GLOBALS['swi_test_options'] ) || $GLOBALS['swi_test_options'][ $name ] !== $value; $GLOBALS['swi_test_options'][ $name ] = $value; return $changed; }
function delete_option( $name ) { $had = array_key_exists( $name, $GLOBALS['swi_test_options'] ); unset( $GLOBALS['swi_test_options'][ $name ] ); return $had; }
function get_user_meta( $user_id, $key, $single = false ) { return $GLOBALS['swi_test_user_meta'][ $user_id ][ $key ] ?? ''; }
function update_user_meta( $user_id, $key, $value ) { $GLOBALS['swi_test_user_meta'][ $user_id ][ $key ] = $value; return true; }
function delete_user_meta( $user_id, $key ) { $had = isset( $GLOBALS['swi_test_user_meta'][ $user_id ][ $key ] ); unset( $GLOBALS['swi_test_user_meta'][ $user_id ][ $key ] ); return $had; }
function get_transient( $key ) { return $GLOBALS['swi_test_transients'][ $key ] ?? false; }
function set_transient( $key, $value ) { $GLOBALS['swi_test_transients'][ $key ] = $value; return true; }
function add_filter( $tag, $callback, $priority = 10 ) { $GLOBALS['swi_test_filters'][ $tag ][ $priority ][] = $callback; return true; }
function apply_filters( $tag, $value ) { $args = func_get_args(); array_shift( $args ); if ( empty( $GLOBALS['swi_test_filters'][ $tag ] ) ) return $value; ksort( $GLOBALS['swi_test_filters'][ $tag ] ); foreach ( $GLOBALS['swi_test_filters'][ $tag ] as $callbacks ) foreach ( $callbacks as $callback ) { $args[0] = call_user_func_array( $callback, $args ); } return $args[0]; }
function add_action( $tag, $callback, $priority = 10 ) { $GLOBALS['swi_test_actions'][ $tag ][ $priority ][] = $callback; return true; }
function do_action( $tag ) { return null; }
function has_filter( $tag ) { return ! empty( $GLOBALS['swi_test_filters'][ $tag ] ); }
function has_action( $tag ) { return ! empty( $GLOBALS['swi_test_actions'][ $tag ] ); }
function swi_test_reset() {
	$GLOBALS['swi_test_options'] = array(); $GLOBALS['swi_test_user_meta'] = array(); $GLOBALS['swi_test_transients'] = array(); $GLOBALS['swi_test_filters'] = array(); $GLOBALS['swi_test_actions'] = array(); $GLOBALS['swi_test_current_user'] = 0;
	$GLOBALS['swi_test_admin'] = $GLOBALS['swi_test_ajax'] = $GLOBALS['swi_test_json'] = false; $GLOBALS['swi_test_feed'] = $GLOBALS['swi_test_robots'] = $GLOBALS['swi_test_trackback'] = $GLOBALS['swi_test_embed'] = false; $_SERVER['REQUEST_URI'] = '/';
}
function current_user_can( $capability ) { return 'manage_options' === $capability || 'edit_user' === $capability; }
function add_rewrite_rule() { return true; }
function flush_rewrite_rules() { return true; }
function get_query_var() { return ''; }
function wp_localize_script() { return true; }
class WP_REST_Server { const CREATABLE = 'POST'; const READABLE = 'GET'; }
class WP_REST_Request {
	private $params; private $headers;
	public function __construct( array $params = array(), array $headers = array() ) { $this->params = $params; $this->headers = array_change_key_case( $headers, CASE_LOWER ); }
	public function get_param( $key ) { return $this->params[ $key ] ?? null; }
	public function get_header( $key ) { return $this->headers[ strtolower( $key ) ] ?? ''; }
}
function rest_ensure_response( $value ) { return $value; }
function wp_verify_nonce( $nonce, $action ) { return 'valid-' . $action === $nonce; }
