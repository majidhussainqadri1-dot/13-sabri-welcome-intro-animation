<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Plugin {
	private static $instance;

	public static function instance() {
		if ( ! self::$instance ) { self::$instance = new self(); }
		return self::$instance;
	}

	public function register() {
		Settings::register();
		Eligibility::register();
		Analytics::register();
		Renderer::register();
		Rest::register();
		Health::register();
		Foundation::register();
		Admin::register();
		add_filter( 'plugin_action_links_' . plugin_basename( SWI_FILE ), array( $this, 'action_links' ) );
	}

	public function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=sabri-welcome-intro' ) ) . '">' . esc_html__( 'Settings', SWI_TEXT_DOMAIN ) . '</a>' );
		return $links;
	}

	public static function activate() {
		Settings::activate();
		Eligibility::register_rewrite();
		flush_rewrite_rules( false );
		Analytics::schedule_cleanup();
	}

	public static function deactivate() {
		wp_clear_scheduled_hook( Analytics::CLEANUP_HOOK );
		flush_rewrite_rules( false );
	}
}
