<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Privacy {
	public function hooks() { add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) ); add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) ); add_action( 'show_user_profile', array( $this, 'profile_field' ) ); add_action( 'edit_user_profile', array( $this, 'profile_field' ) ); add_action( 'personal_options_update', array( $this, 'save_profile_field' ) ); add_action( 'edit_user_profile_update', array( $this, 'save_profile_field' ) ); }
	public function register_exporter( $exporters ) { $exporters['sabri-welcome-intro'] = array( 'exporter_friendly_name' => __( 'Sabri Welcome Intro', 'sabri-welcome-intro' ), 'callback' => array( $this, 'export' ) ); return $exporters; }
	public function register_eraser( $erasers ) { $erasers['sabri-welcome-intro'] = array( 'eraser_friendly_name' => __( 'Sabri Welcome Intro', 'sabri-welcome-intro' ), 'callback' => array( $this, 'erase' ) ); return $erasers; }
	public function export( $email_address, $page = 1 ) {
		$user = get_user_by( 'email', $email_address ); $data = array();
		if ( $user ) { $last = get_user_meta( $user->ID, SWI_Config::USER_META_LAST, true ); $ver = get_user_meta( $user->ID, SWI_Config::USER_META_VER, true ); $never = get_user_meta( $user->ID, SWI_Config::USER_META_NEVER, true ); $experience = get_user_meta( $user->ID, SWI_Config::USER_META_EXPERIENCE, true );
			if ( $last || $never || $experience ) { $data[] = array( 'group_id' => 'sabri-welcome-intro', 'group_label' => __( 'Sabri Welcome Intro', 'sabri-welcome-intro' ), 'item_id' => 'swi-preference-' . $user->ID, 'data' => array( array( 'name' => __( 'Last dismissed at', 'sabri-welcome-intro' ), 'value' => $last ? gmdate( 'c', (int) $last ) : '' ), array( 'name' => __( 'Configuration version', 'sabri-welcome-intro' ), 'value' => (string) absint( $ver ) ), array( 'name' => __( 'Never show again', 'sabri-welcome-intro' ), 'value' => $never ? 'yes' : 'no' ), array( 'name' => __( 'Experience version', 'sabri-welcome-intro' ), 'value' => (string) $experience ) ) ); }
		}
		return array( 'data' => $data, 'done' => true );
	}
	public function erase( $email_address, $page = 1 ) { $user = get_user_by( 'email', $email_address ); $removed = false; if ( $user ) { foreach ( array( SWI_Config::USER_META_LAST, SWI_Config::USER_META_VER, SWI_Config::USER_META_NEVER, SWI_Config::USER_META_EXPERIENCE ) as $key ) { $removed = delete_user_meta( $user->ID, $key ) || $removed; } } return array( 'items_removed' => $removed, 'items_retained' => false, 'messages' => array(), 'done' => true ); }
	public function profile_field( $user ) {
		if ( ! current_user_can( 'edit_user', $user->ID ) ) { return; } $last = get_user_meta( $user->ID, SWI_Config::USER_META_LAST, true ); $never = get_user_meta( $user->ID, SWI_Config::USER_META_NEVER, true ); ?>
		<h2><?php esc_html_e( 'Sabri Welcome Intro', 'sabri-welcome-intro' ); ?></h2><table class="form-table" role="presentation"><tr><th><?php esc_html_e( 'Welcome intro preference', 'sabri-welcome-intro' ); ?></th><td><label><input type="checkbox" name="swi_reset_intro" value="1"> <?php esc_html_e( 'Enable the intro again and clear account suppression', 'sabri-welcome-intro' ); ?></label><?php if ( $never ) : ?><p class="description"><?php esc_html_e( 'This account currently has “Never show again” enabled.', 'sabri-welcome-intro' ); ?></p><?php endif; ?><?php if ( $last ) : ?><p class="description"><?php echo esc_html( sprintf( __( 'Last dismissed: %s UTC', 'sabri-welcome-intro' ), gmdate( 'Y-m-d H:i:s', (int) $last ) ) ); ?></p><?php endif; ?><?php wp_nonce_field( 'swi_profile_preference_' . $user->ID, 'swi_profile_nonce' ); ?></td></tr></table><?php
	}
	public function save_profile_field( $user_id ) { if ( ! current_user_can( 'edit_user', $user_id ) ) { return; } $nonce = isset( $_POST['swi_profile_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['swi_profile_nonce'] ) ) : ''; if ( ! wp_verify_nonce( $nonce, 'swi_profile_preference_' . $user_id ) ) { return; } if ( ! empty( $_POST['swi_reset_intro'] ) ) { foreach ( array( SWI_Config::USER_META_LAST, SWI_Config::USER_META_VER, SWI_Config::USER_META_NEVER, SWI_Config::USER_META_EXPERIENCE ) as $key ) { delete_user_meta( $user_id, $key ); } do_action( 'swi_user_preference_changed', $user_id, 'show_again', '' ); } }
}
