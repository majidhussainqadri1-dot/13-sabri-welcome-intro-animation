<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Rest {
	const NAMESPACE = 'sabri-welcome-intro/v1';

	public static function register() {
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	public static function routes() {
		register_rest_route( self::NAMESPACE, '/status', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'status' ),
			'permission_callback' => static fn() => Authorization::can_manage( 'get_intro_status' ),
		) );
		register_rest_route( self::NAMESPACE, '/config', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'set_config' ),
			'permission_callback' => static fn() => Authorization::can_manage( 'set_intro_config' ),
		) );
	}

	public static function status() {
		return rest_ensure_response( Health::status() );
	}

	public static function set_config( \WP_REST_Request $request ) {
		$params = $request->get_json_params();
		if ( ! is_array( $params ) ) { $params = $request->get_params(); }
		$expected = isset( $params['expected_revision'] ) ? absint( $params['expected_revision'] ) : 0;
		unset( $params['expected_revision'] );
		$result = Settings::update( $params, $expected, get_current_user_id() );
		return is_wp_error( $result ) ? $result : rest_ensure_response( array( 'config' => $result ) );
	}
}
