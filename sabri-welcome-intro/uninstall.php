<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Non-destructive by default. Purge requires an explicit, audited operator decision.
if ( ! defined( 'SWI_PURGE_ON_UNINSTALL' ) || true !== SWI_PURGE_ON_UNINSTALL ) {
	return;
}

delete_option( 'swi_config' );
delete_option( 'swi_schema_version' );
delete_option( 'swi_audit_log' );
delete_option( 'swi_aggregate_metrics' );
delete_option( 'swi_enabled' );

global $wpdb;
$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'swi_last_dismissed_at' ) );
$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'swi_last_config_version' ) );
