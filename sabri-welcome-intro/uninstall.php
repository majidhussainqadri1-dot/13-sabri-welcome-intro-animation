<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Non-destructive by default. Purge requires an explicit, audited operator decision.
if ( ! defined( 'SWI_PURGE_ON_UNINSTALL' ) || true !== SWI_PURGE_ON_UNINSTALL ) {
	return;
}

foreach ( array( 'swi_config', 'swi_schema_version', 'swi_audit_log', 'swi_aggregate_metrics', 'swi_config_write_lock', 'swi_approved_snapshots', 'swi_enabled' ) as $option ) {
	delete_option( $option );
}

global $wpdb;
foreach ( array( 'swi_last_dismissed_at', 'swi_last_config_version', 'swi_never_show', 'swi_last_experience_version' ) as $meta_key ) {
	$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => $meta_key ) );
}
