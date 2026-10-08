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
	delete_option( 'swi_intro_audit_gap' );
	global $wpdb;
	$like = $wpdb->esc_like( 'swi_intro_agg_' ) . '%';
	$cursor = 0;
	do {
		$rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT option_id, option_name FROM {$wpdb->options} WHERE option_name LIKE %s AND option_id > %d ORDER BY option_id ASC LIMIT 500", $like, $cursor ),
			ARRAY_A
		);
		if ( ! is_array( $rows ) || ! $rows ) { break; }
		foreach ( $rows as $row ) {
			$cursor = max( $cursor, absint( $row['option_id'] ) );
			$name = (string) $row['option_name'];
			if ( preg_match( '/^swi_intro_agg_\\d{8}_v\\d+$/', $name ) ) { delete_option( $name ); }
		}
	} while ( count( $rows ) === 500 );
}
