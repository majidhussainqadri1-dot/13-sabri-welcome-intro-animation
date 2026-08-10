<?php

defined( 'ABSPATH' ) || exit;
final class SWI_Plugin {
	/** @var SWI_Renderer|null */ private static $renderer = null;
	public function run() { load_plugin_textdomain( 'sabri-welcome-intro', false, dirname( SWI_BASENAME ) . '/languages' ); $eligibility = new SWI_Eligibility(); $analytics = new SWI_Analytics(); self::$renderer = new SWI_Renderer( $eligibility ); self::$renderer->hooks(); ( new SWI_REST( $analytics ) )->hooks(); ( new SWI_Privacy() )->hooks(); ( new SWI_Contracts() )->hooks(); if ( is_admin() ) { ( new SWI_Admin( $analytics ) )->hooks(); } }
	/** @return SWI_Renderer|null */ public static function renderer() { return self::$renderer; }
}
