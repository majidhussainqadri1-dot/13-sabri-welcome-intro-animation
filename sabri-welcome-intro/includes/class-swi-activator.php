<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Activator {
	public static function activate() {
		self::migrate();
		SWI_Config::record_audit( 'plugin_activated', array(), (int) SWI_Config::get()['config_version'] );
		if ( function_exists( 'add_rewrite_rule' ) ) {
			add_rewrite_rule( '^welcome-intro-preview/?$', 'index.php?swi_preview_route=1', 'top' );
		}
		if ( function_exists( 'flush_rewrite_rules' ) ) {
			flush_rewrite_rules( false );
		}
	}

	public static function deactivate() {
		SWI_Config::record_audit( 'plugin_deactivated', array(), (int) SWI_Config::get()['config_version'] );
		global $wp_rewrite;
		if ( is_object( $wp_rewrite ) && isset( $wp_rewrite->extra_rules_top['^welcome-intro-preview/?$'] ) ) {
			unset( $wp_rewrite->extra_rules_top['^welcome-intro-preview/?$'] );
		}
		if ( function_exists( 'flush_rewrite_rules' ) ) {
			flush_rewrite_rules( false );
		}
	}

	public static function maybe_upgrade() {
		$schema = (string) get_option( SWI_Config::OPTION_SCHEMA, '' );
		if ( SWI_SCHEMA_VERSION !== $schema ) {
			self::migrate();
		}
	}

	private static function migrate() {
		$current = get_option( SWI_Config::OPTION_CONFIG, null );
		if ( ! is_array( $current ) ) {
			$defaults = SWI_Config::defaults();
			$legacy   = get_option( 'swi_enabled', null );
			if ( null !== $legacy && false !== $legacy ) {
				$defaults['enabled'] = empty( $legacy ) ? 0 : 1;
			}
			$defaults['updated_at'] = gmdate( 'c' );
			add_option( SWI_Config::OPTION_CONFIG, $defaults, '', false );
			SWI_Config::record_audit( 'legacy_migrated', array( 'swi_enabled' ), 1 );
		} else {
			$normalized = SWI_Config::sanitize( array_merge( SWI_Config::defaults(), $current ), false );
			update_option( SWI_Config::OPTION_CONFIG, $normalized, false );
		}

		update_option( SWI_Config::OPTION_SCHEMA, SWI_SCHEMA_VERSION, false );
	}
}
