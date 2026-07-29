<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Activator {
	public static function activate() {
		if ( false === get_option( 'swi_enabled', false ) ) {
			add_option( 'swi_enabled', 1, '', false );
		}
	}
}
