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
		Analytics::register_retention(); // Cron only, no public analytics endpoints.
		Renderer::register();
		Rest::register();
		Health::register();
		Foundation::register();
		Admin::register();
		add_filter( 'plugin_action_links_' . plugin_basename( SWI_FILE ), array( $this, 'action_links' ) );
	}

	public function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=sabri-welcome-intro' ) ) . '">' . esc_html__( 'Status', SWI_TEXT_DOMAIN ) . '</a>' );
		return $links;
	}

	public static function activate() {
		Settings::activate();
		// Never flush a persisted foreign route during activation.
		if ( Renderer::register_rewrite() ) {
			flush_rewrite_rules( false );
		}
		Analytics::schedule_cleanup();
	}

	public static function deactivate() {
		wp_clear_scheduled_hook( Analytics::CLEANUP_HOOK );
		// WordPress fires deactivation after init: the rule may already be in
		// extra_rules_top. Remove only our exact rule before flushing, or it
		// would survive deactivation in the persisted rewrite_rules option.
		global $wp_rewrite;
		if ( is_object( $wp_rewrite )
			&& isset( $wp_rewrite->extra_rules_top )
			&& is_array( $wp_rewrite->extra_rules_top )
			&& isset( $wp_rewrite->extra_rules_top[ Renderer::PREVIEW_REWRITE_PATTERN ] )
			&& Renderer::PREVIEW_REWRITE_TARGET === $wp_rewrite->extra_rules_top[ Renderer::PREVIEW_REWRITE_PATTERN ] ) {
			unset( $wp_rewrite->extra_rules_top[ Renderer::PREVIEW_REWRITE_PATTERN ] );
		}
		flush_rewrite_rules( false );
	}
}
