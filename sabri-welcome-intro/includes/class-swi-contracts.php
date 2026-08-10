<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Contracts {
	public function hooks() { add_filter( 'sabri_shell_module_registry', array( $this, 'register_with_shell' ) ); add_action( 'sabri_shell_welcome_intro', 'swi_render_welcome_intro', 10 ); add_filter( 'sabri_pwa_precache_assets', array( $this, 'register_pwa_assets' ) ); }
	/** @param mixed $registry @return array<string,mixed> */ public function register_with_shell( $registry ) { $registry = is_array( $registry ) ? $registry : array(); $registry['file-13-welcome-intro'] = swi_get_welcome_intro_contract(); return $registry; }
	/** @param mixed $assets @return string[] */ public function register_pwa_assets( $assets ) { $assets = is_array( $assets ) ? $assets : array(); $config = SWI_Config::get(); if ( empty( $config['pwa_precache_enabled'] ) ) { return $assets; } return array_values( array_unique( array_merge( $assets, SWI_Experience::pwa_precache_assets() ) ) ); }
}
