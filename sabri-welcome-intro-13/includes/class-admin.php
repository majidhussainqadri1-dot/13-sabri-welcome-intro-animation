<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Admin {
	const PAGE = 'sabri-welcome-intro';

	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
	}

	public static function menu() {
		// A companion filter can only tighten render authorization, not the native menu gate.
		add_options_page( __( 'Sabri Welcome Intro', SWI_TEXT_DOMAIN ), __( 'Sabri Welcome Intro', SWI_TEXT_DOMAIN ), Authorization::DEFAULT_CAPABILITY, self::PAGE, array( __CLASS__, 'render' ) );
	}

	public static function render() {
		Authorization::require_manage( 'manage_intro' );
		$health = Health::status();
		$preview = home_url( '/welcome-intro-preview/' );
		echo '<div class="wrap"><h1>' . esc_html__( 'File 13 Legacy Compatibility', SWI_TEXT_DOMAIN ) . '</h1>';
		echo '<div class="notice notice-info"><p>' . esc_html__( 'The historical File 13 public intro is permanently disabled. File 20 owns invocation and frequency; File 25 owns presentation.', SWI_TEXT_DOMAIN ) . '</p></div>';
		if ( isset( $_GET['swi_registry_synced'] ) ) { echo '<div class="notice notice-success"><p>' . esc_html__( 'File 01 registry synchronized.', SWI_TEXT_DOMAIN ) . '</p></div>'; }
		if ( isset( $_GET['swi_registry_error'] ) ) { echo '<div class="notice notice-error"><p>' . esc_html__( 'File 01 registry synchronization failed. Review authorization and registry health.', SWI_TEXT_DOMAIN ) . '</p></div>'; }
		echo '<p><strong>' . esc_html__( 'Health:', SWI_TEXT_DOMAIN ) . '</strong> ' . esc_html( $health['status'] ) . ' — ' . esc_html__( 'File 24 contract state:', SWI_TEXT_DOMAIN ) . ' ' . esc_html( $health['file24_contract_state'] ) . '</p>';
		$foundation = Foundation::status();
		echo '<p><strong>' . esc_html__( 'File 01 registry:', SWI_TEXT_DOMAIN ) . '</strong> ' . esc_html( $foundation['state'] ) . '</p>';
		echo '<p><a class="button" href="' . esc_url( $preview ) . '" target="_blank" rel="noopener">' . esc_html__( 'Inspect legacy preview', SWI_TEXT_DOMAIN ) . '</a></p>';
		echo '<form style="display:inline-block;margin-bottom:12px" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="' . esc_attr( Foundation::SYNC_ACTION ) . '">';
		wp_nonce_field( Foundation::SYNC_ACTION, '_swi_nonce' );
		submit_button( __( 'Sync File 01 Registry', SWI_TEXT_DOMAIN ), 'secondary', 'submit', false );
		echo '</form></div>';
	}
}
