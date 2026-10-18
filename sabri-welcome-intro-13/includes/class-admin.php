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
		$foundation = Foundation::status();
		$preview = home_url( '/welcome-intro-preview/' );
		echo '<div class="wrap"><h1>' . esc_html__( 'File 13 Legacy Compatibility', SWI_TEXT_DOMAIN ) . '</h1>';
		echo '<div class="notice notice-info"><p>' . esc_html__( 'The historical File 13 public intro is disabled in this source candidate. Permanent ownership transfer requires dated Founder-approved change control. Current source contracts assign invocation and frequency to File 20 and presentation to File 25.', SWI_TEXT_DOMAIN ) . '</p></div>';
		if ( isset( $_GET['swi_registry_synced'] ) && 'synced' === (string) ( $foundation['state'] ?? '' ) ) { echo '<div class="notice notice-success"><p>' . esc_html__( 'File 01 registry synchronized.', SWI_TEXT_DOMAIN ) . '</p></div>'; }
		if ( isset( $_GET['swi_registry_error'] ) && 'synced' !== (string) ( $foundation['state'] ?? '' ) ) { echo '<div class="notice notice-error"><p>' . esc_html__( 'File 01 registry synchronization failed. Review authorization and registry health.', SWI_TEXT_DOMAIN ) . '</p></div>'; }
		echo '<p><strong>' . esc_html__( 'Health:', SWI_TEXT_DOMAIN ) . '</strong> ' . esc_html( $health['status'] ) . ' — ' . esc_html__( 'File 24 contract state:', SWI_TEXT_DOMAIN ) . ' ' . esc_html( $health['file24_contract_state'] ) . '</p>';
		echo '<p><strong>' . esc_html__( 'File 01 registry:', SWI_TEXT_DOMAIN ) . '</strong> ' . esc_html( $foundation['state'] ) . '</p>';
		// Compatibility minimums and project release targets are distinct.
		// Never imply that a version-only check is an accepted staging/live test.
		$baseline = isset( $health['project_baseline'] ) && is_array( $health['project_baseline'] )
			? $health['project_baseline'] : array();
		echo '<p><strong>' . esc_html__( 'Project version baseline (WordPress 7.0.1 / PHP 8.3):', SWI_TEXT_DOMAIN ) . '</strong> '
			. ( ! empty( $baseline['meets_version_targets'] )
				? esc_html__( 'Runtime versions meet the targets; acceptance is not verified.', SWI_TEXT_DOMAIN )
				: esc_html__( 'Runtime versions do not meet the targets; acceptance is not verified.', SWI_TEXT_DOMAIN ) )
			. '</p>';
		// File20's installed plugin class/version is NOT proof of the distinct
		// 30-day WelcomeFrequency contract (old main and draft share 1.4.17).
		$welcome_contract_valid = ! empty( $health['file20_welcome_contract']['valid'] );
		echo '<p><strong>' . esc_html__( 'File 20 welcome frequency contract:', SWI_TEXT_DOMAIN ) . '</strong> '
			. ( $welcome_contract_valid
				? esc_html__( 'Detected; operational verification remains separate.', SWI_TEXT_DOMAIN )
				: esc_html__( 'Missing or incompatible; plugin presence is insufficient.', SWI_TEXT_DOMAIN ) )
			. '</p>';
		// Keep File25 visual-owner provenance separate from File13's historical
		// compatibility and File20's canonical frequency diagnostics.
		$visual = isset( $health['file25_visual_contract'] ) && is_array( $health['file25_visual_contract'] )
			? $health['file25_visual_contract'] : array();
		if ( empty( $visual['provider_registered'] ) ) {
			$visual_message = __( 'Native File 25 provider callback missing; visual readiness not verified.', SWI_TEXT_DOMAIN );
		} elseif ( empty( $visual['valid'] ) ) {
			$visual_message = __( 'Native File 25 provider registered but visual contract incompatible; readiness not verified.', SWI_TEXT_DOMAIN );
		} else {
			$visual_message = __( 'Native File 25 visual contract detected; operational verification remains separate.', SWI_TEXT_DOMAIN );
		}
		echo '<p><strong>' . esc_html__( 'File 25 visual contract:', SWI_TEXT_DOMAIN ) . '</strong> ' . esc_html( $visual_message ) . '</p>';
		echo '<p><a class="button" href="' . esc_url( $preview ) . '" target="_blank" rel="noopener">' . esc_html__( 'Inspect legacy preview', SWI_TEXT_DOMAIN ) . '</a></p>';
		echo '<form style="display:inline-block;margin-bottom:12px" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="' . esc_attr( Foundation::SYNC_ACTION ) . '">';
		wp_nonce_field( Foundation::SYNC_ACTION, '_swi_nonce' );
		submit_button( __( 'Sync File 01 Registry', SWI_TEXT_DOMAIN ), 'secondary', 'submit', false );
		echo '</form></div>';
	}
}
