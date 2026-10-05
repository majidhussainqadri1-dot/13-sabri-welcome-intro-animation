<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

wp_clear_scheduled_hook( 'swi_intro_cleanup_aggregates' );

/**
 * Non-destructive by default. Destructive purge is allowed only when an operator
 * deliberately defines SWI_PURGE_ON_UNINSTALL=true before uninstalling.
 */
if ( defined( 'SWI_PURGE_ON_UNINSTALL' ) && SWI_PURGE_ON_UNINSTALL ) {
	delete_option( 'swi_intro_config' );
	delete_option( 'swi_intro_audit' );
	delete_option( 'swi_intro_schema_version' );
	global $wpdb;
	$like = $wpdb->esc_like( 'swi_intro_agg_' ) . '%';
	$names = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT 1000", $like ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	foreach ( (array) $names as $name ) { delete_option( $name ); }
}
