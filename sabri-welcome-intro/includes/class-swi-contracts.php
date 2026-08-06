<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Contracts {
	public function hooks() {
		add_filter( 'sabri_shell_module_registry', array( $this, 'register_with_shell' ) );
		add_action( 'sabri_shell_welcome_intro', 'swi_render_welcome_intro', 10 );
	}

	/**
	 * Register a versioned, read-only File 13 contract with File 20 when that registry exists.
	 *
	 * @param mixed $registry Existing registry.
	 * @return array<string,mixed>
	 */
	public function register_with_shell( $registry ) {
		$registry = is_array( $registry ) ? $registry : array();
		$registry['file-13-welcome-intro'] = swi_get_welcome_intro_contract();
		return $registry;
	}
}
