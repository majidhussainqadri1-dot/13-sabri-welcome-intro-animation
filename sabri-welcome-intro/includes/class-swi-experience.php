<?php

defined( 'ABSPATH' ) || exit;

/**
 * File 13 future-experience policy helpers.
 *
 * This class deliberately owns only welcome-experience decisions. It does not
 * own the global shell, profile data, authentication, service workers or the
 * File 25 visual token registry.
 */
final class SWI_Experience {
	const EXPERIENCE_CONTRACT = '1.1.0';
	const ACCESSIBILITY_PROFILES = array( 'auto', 'high-contrast', 'large-text', 'simple', 'screen-reader' );
	const VARIANTS = array( 'full', 'light', 'static' );

	/**
	 * Choose Founder-approved localized copy without creating a translation backend.
	 *
	 * @param array<string,mixed> $config Normalized configuration.
	 * @return array{name:string,claim:string,language:string}
	 */
	public static function localized_copy( array $config ) {
		$language = function_exists( 'determine_locale' ) ? (string) determine_locale() : ( function_exists( 'get_locale' ) ? (string) get_locale() : (string) $config['brand_language'] );
		$normalized = str_replace( '_', '-', $language );
		$variants = isset( $config['localized_copy'] ) && is_array( $config['localized_copy'] ) ? $config['localized_copy'] : array();
		$candidates = array( $normalized, strtolower( $normalized ) );
		$parts = explode( '-', $normalized );
		if ( ! empty( $parts[0] ) ) {
			$candidates[] = $parts[0];
			$candidates[] = strtolower( $parts[0] );
		}

		foreach ( $candidates as $candidate ) {
			if ( isset( $variants[ $candidate ] ) && is_array( $variants[ $candidate ] ) ) {
				return array(
					'name'     => (string) $variants[ $candidate ]['name'],
					'claim'    => (string) $variants[ $candidate ]['claim'],
					'language' => (string) $variants[ $candidate ]['language'],
				);
			}
		}

		return array(
			'name'     => (string) $config['brand_name'],
			'claim'    => (string) $config['brand_claim'],
			'language' => (string) $config['brand_language'],
		);
	}

	/** @return string[] */
	public static function pwa_precache_assets() {
		return array(
			SWI_URL . 'assets/css/welcome-intro.css',
			SWI_URL . 'assets/js/welcome-intro.js',
			SWI_URL . 'assets/images/sabri-sh-logo.svg',
		);
	}

	/**
	 * Source-level visual baseline. Browser screenshot acceptance remains a staging gate.
	 *
	 * @return array<string,string>
	 */
	public static function visual_baseline() {
		$files = array(
			'css'      => SWI_DIR . 'assets/css/welcome-intro.css',
			'js'       => SWI_DIR . 'assets/js/welcome-intro.js',
			'logo'     => SWI_DIR . 'assets/images/sabri-sh-logo.svg',
			'renderer' => SWI_DIR . 'includes/class-swi-renderer.php',
		);
		$out = array();
		foreach ( $files as $key => $path ) {
			$out[ $key ] = is_readable( $path ) ? hash_file( 'sha256', $path ) : '';
		}
		return $out;
	}

	public static function sanitize_profile( $profile ) {
		$profile = str_replace( '_', '-', sanitize_key( (string) $profile ) );
		return in_array( $profile, self::ACCESSIBILITY_PROFILES, true ) ? $profile : 'auto';
	}
}
