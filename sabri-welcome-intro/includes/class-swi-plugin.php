<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Plugin {
	/**
	 * Register the independent front-end and settings modules.
	 */
	public function run() {
		$renderer = new SWI_Renderer();
		$renderer->hooks();

		if ( is_admin() ) {
			( new SWI_Admin() )->hooks();
		}
	}
}
