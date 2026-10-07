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
	}

	public static function status() {
		return rest_ensure_response( Health::status() );
	}

}
