<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Authorization {
	const DEFAULT_CAPABILITY = 'manage_options';

	public static function can_manage( $action = 'manage' ) {
		$capability = apply_filters( 'swi_intro_manage_capability', self::DEFAULT_CAPABILITY, $action );
		$allowed = is_string( $capability ) && '' !== $capability && current_user_can( $capability );
		return (bool) apply_filters( 'swi_intro_authorization_decision', $allowed, $action, get_current_user_id() );
	}

	public static function require_manage( $action = 'manage' ) {
		if ( ! is_user_logged_in() || ! self::can_manage( $action ) ) {
			wp_die( esc_html__( 'You are not authorized to manage the welcome intro.', SWI_TEXT_DOMAIN ), esc_html__( 'Forbidden', SWI_TEXT_DOMAIN ), array( 'response' => 403 ) );
		}
	}

	public static function verify_post( $action, $nonce_name = '_swi_nonce' ) {
		self::require_manage( $action );
		if ( 'POST' !== strtoupper( isset( $_SERVER['REQUEST_METHOD'] ) ? (string) $_SERVER['REQUEST_METHOD'] : '' ) ) {
			wp_die( esc_html__( 'This action requires POST.', SWI_TEXT_DOMAIN ), '', array( 'response' => 405 ) );
		}
		$nonce = isset( $_POST[ $nonce_name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ) : '';
		if ( '' === $nonce || ! wp_verify_nonce( $nonce, $action ) ) {
			wp_die( esc_html__( 'Security verification failed.', SWI_TEXT_DOMAIN ), '', array( 'response' => 403 ) );
		}
	}
}
