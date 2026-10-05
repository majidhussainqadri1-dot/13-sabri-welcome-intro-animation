<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Eligibility {
	const PREVIEW_QUERY_VAR = 'swi_intro_preview';

	public static function register() {
		add_action( 'init', array( __CLASS__, 'register_rewrite' ) );
		add_filter( 'query_vars', array( __CLASS__, 'query_vars' ) );
	}

	public static function register_rewrite() {
		add_rewrite_rule( '^welcome-intro-preview/?$', 'index.php?' . self::PREVIEW_QUERY_VAR . '=1', 'top' );
	}

	public static function query_vars( $vars ) {
		$vars[] = self::PREVIEW_QUERY_VAR;
		return $vars;
	}

	public static function public_request_eligible( array $context ) {
		$config = Settings::get();
		if ( ! Settings::active_now( $config ) || self::safe_mode_active() ) { return false; }
		if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) { return false; }
		if ( get_query_var( self::PREVIEW_QUERY_VAR ) ) { return false; }
		if ( empty( $context['route_eligible'] ) || 'file-20-shell-placement' !== (string) ( $context['owner'] ?? '' ) ) { return false; }
		if ( empty( $context['contract_version'] ) || ! preg_match( '/^\d+\.\d+\.\d+$/', (string) $context['contract_version'] ) ) { return false; }
		if ( function_exists( 'is_feed' ) && is_feed() ) { return false; }
		if ( function_exists( 'is_404' ) && is_404() ) { return false; }

		$path = self::current_path();
		if ( ! self::path_allowed( $path, $config['eligible_paths'] ) ) { return false; }
		return (bool) apply_filters( 'swi_intro_request_eligible', true, $context, $config, $path );
	}

	public static function safe_mode_active() {
		if ( defined( 'SWI_DISABLE_INTRO' ) && SWI_DISABLE_INTRO ) { return true; }
		$disabled = false;
		$class = 'Sabri\\UnifiedShell\\SafeMode';
		if ( class_exists( $class ) && is_callable( array( $class, 'disabled' ) ) ) {
			try { $disabled = (bool) call_user_func( array( $class, 'disabled' ) ); }
			catch ( \Throwable $error ) { unset( $error ); $disabled = true; }
		}
		return (bool) apply_filters( 'swi_intro_safe_mode_active', $disabled );
	}

	public static function current_path() {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
		$path = wp_parse_url( $request_uri, PHP_URL_PATH );
		return Settings::normalize_path( is_string( $path ) ? $path : '/' );
	}

	private static function path_allowed( $path, $allowed_paths ) {
		$allowed_paths = is_array( $allowed_paths ) ? $allowed_paths : array( '/' );
		foreach ( $allowed_paths as $allowed ) {
			$allowed = Settings::normalize_path( $allowed );
			if ( '/' === $allowed && ( '/' === $path || ( function_exists( 'is_front_page' ) && ( is_front_page() || is_home() ) ) ) ) { return true; }
			if ( $allowed === $path ) { return true; }
			if ( str_ends_with( $allowed, '/*' ) ) {
				$prefix = rtrim( substr( $allowed, 0, -1 ), '/' );
				if ( '' !== $prefix && str_starts_with( $path, $prefix . '/' ) ) { return true; }
			}
		}
		return false;
	}
}
