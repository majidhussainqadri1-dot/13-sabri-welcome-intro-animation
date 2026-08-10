<?php

defined( 'ABSPATH' ) || exit;

/** File 13 welcome-experience policy helpers; no shell/service-worker ownership. */
final class SWI_Experience {
	const EXPERIENCE_CONTRACT = '1.1.0';
	const ACCESSIBILITY_PROFILES = array( 'auto', 'high-contrast', 'large-text', 'simple', 'screen-reader' );
	const VARIANTS = array( 'full', 'light', 'static' );

	/** @param array<string,mixed> $config @return array{name:string,claim:string,language:string} */
	public static function localized_copy( array $config ) {
		$language = function_exists( 'determine_locale' ) ? (string) determine_locale() : ( function_exists( 'get_locale' ) ? (string) get_locale() : (string) $config['brand_language'] );
		$normalized = str_replace( '_', '-', $language );
		$variants = isset( $config['localized_copy'] ) && is_array( $config['localized_copy'] ) ? $config['localized_copy'] : array();
		$candidates = array( $normalized, strtolower( $normalized ) ); $parts = explode( '-', $normalized );
		if ( ! empty( $parts[0] ) ) { $candidates[] = $parts[0]; $candidates[] = strtolower( $parts[0] ); }
		foreach ( array_unique( $candidates ) as $candidate ) {
			if ( ! isset( $variants[ $candidate ] ) || ! is_array( $variants[ $candidate ] ) ) { continue; }
			$copy = $variants[ $candidate ];
			if ( empty( $copy['name'] ) || empty( $copy['claim'] ) ) { continue; }
			return array( 'name' => (string) $copy['name'], 'claim' => (string) $copy['claim'], 'language' => ! empty( $copy['language'] ) ? (string) $copy['language'] : $candidate );
		}
		return array( 'name' => (string) $config['brand_name'], 'claim' => (string) $config['brand_claim'], 'language' => (string) $config['brand_language'] );
	}

	/** @return string[] */
	public static function pwa_precache_assets() { return array( SWI_URL . 'assets/css/welcome-intro.css', SWI_URL . 'assets/js/welcome-intro.js', SWI_URL . 'assets/images/sabri-sh-logo.svg' ); }

	/**
	 * Deterministic Founder visual baseline. It binds both source visuals and the
	 * canonical stored visual/copy policy. Runtime projections are intentionally
	 * excluded so File 20/24 cannot silently change what a Founder approval means.
	 *
	 * @return array<string,mixed>
	 */
	public static function visual_baseline() {
		$files = array( 'css' => SWI_DIR . 'assets/css/welcome-intro.css', 'js' => SWI_DIR . 'assets/js/welcome-intro.js', 'logo' => SWI_DIR . 'assets/images/sabri-sh-logo.svg', 'renderer' => SWI_DIR . 'includes/class-swi-renderer.php' );
		$out = array();
		foreach ( $files as $key => $path ) { $out[ $key ] = is_readable( $path ) ? hash_file( 'sha256', $path ) : ''; }
		$stored = class_exists( 'SWI_Config' ) ? get_option( SWI_Config::OPTION_CONFIG, array() ) : array();
		$stored = is_array( $stored ) ? $stored : array();
		$defaults = class_exists( 'SWI_Config' ) ? SWI_Config::defaults() : array();
		$config = array_merge( $defaults, $stored );
		$visual_keys = array(
			'experience_version', 'duration_ms', 'reduced_duration_ms', 'brand_name', 'brand_claim', 'brand_language', 'localized_copy',
			'adaptive_mode', 'instant_exit_enabled', 'data_saver_static', 'performance_circuit_breaker', 'performance_budget_ms',
			'accessibility_profiles', 'default_accessibility_profile', 'never_show_enabled', 'replay_enabled', 'version_replay_enabled',
		);
		$spec = array();
		foreach ( $visual_keys as $key ) { $spec[ $key ] = array_key_exists( $key, $config ) ? $config[ $key ] : null; }
		ksort( $spec );
		$out['governed_visual_spec'] = $spec;
		ksort( $out );
		return $out;
	}

	/** @return string */
	public static function visual_baseline_hash() { return hash( 'sha256', wp_json_encode( self::visual_baseline() ) ); }

	/** @return string */
	public static function sanitize_profile( $profile ) { $profile = str_replace( '_', '-', sanitize_key( (string) $profile ) ); return in_array( $profile, self::ACCESSIBILITY_PROFILES, true ) ? $profile : 'auto'; }

	/** @return string */
	public static function sanitize_variant( $variant ) { $variant = sanitize_key( (string) $variant ); return in_array( $variant, self::VARIANTS, true ) ? $variant : 'full'; }

	/** @param array<string,mixed> $config @return array<string,mixed> */
	public static function account_preferences( $user_id, array $config ) {
		if ( ! $user_id ) { return array( 'last_seen' => 0, 'never_show' => false, 'profile' => self::sanitize_profile( $config['default_accessibility_profile'] ), 'experience_version' => '' ); }
		$last = get_user_meta( $user_id, SWI_Config::USER_META_LAST, true );
		$never = get_user_meta( $user_id, SWI_Config::USER_META_NEVER, true );
		$profile = get_user_meta( $user_id, SWI_Config::USER_META_PROFILE, true );
		$experience = get_user_meta( $user_id, SWI_Config::USER_META_EXPERIENCE, true );
		return array( 'last_seen' => is_numeric( $last ) ? max( 0, (int) $last ) : 0, 'never_show' => ! empty( $never ), 'profile' => self::sanitize_profile( '' === (string) $profile ? $config['default_accessibility_profile'] : $profile ), 'experience_version' => (string) $experience );
	}
}
