<?php
/**
 * Plugin Name: Sabri Welcome Intro Animation
 * Plugin URI: https://sabrihomeopathy.com/
 * Description: Accessible, non-blocking and governance-aware welcome intro for the Sabri Social Homeopathy Platform.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: Dr. Allamah Majid Hussain Sabri Muhaddith Mursheed
 * License: GPL-2.0-or-later
 * Text Domain: sabri-welcome-intro
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'SWI_VERSION', '1.0.0' );
define( 'SWI_SCHEMA_VERSION', '1.0.0' );
define( 'SWI_FILE', __FILE__ );
define( 'SWI_DIR', plugin_dir_path( __FILE__ ) );
define( 'SWI_URL', plugin_dir_url( __FILE__ ) );
define( 'SWI_BASENAME', plugin_basename( __FILE__ ) );

require_once SWI_DIR . 'includes/class-swi-config.php';
require_once SWI_DIR . 'includes/class-swi-activator.php';
require_once SWI_DIR . 'includes/class-swi-eligibility.php';
require_once SWI_DIR . 'includes/class-swi-analytics.php';
require_once SWI_DIR . 'includes/class-swi-rest.php';
require_once SWI_DIR . 'includes/class-swi-privacy.php';
require_once SWI_DIR . 'includes/class-swi-contracts.php';
require_once SWI_DIR . 'includes/class-swi-renderer.php';
require_once SWI_DIR . 'includes/class-swi-system-check.php';
require_once SWI_DIR . 'includes/class-swi-admin.php';
require_once SWI_DIR . 'includes/class-swi-plugin.php';
require_once SWI_DIR . 'includes/functions.php';

register_activation_hook( SWI_FILE, array( 'SWI_Activator', 'activate' ) );
register_deactivation_hook( SWI_FILE, array( 'SWI_Activator', 'deactivate' ) );

add_action(
	'plugins_loaded',
	static function () {
		SWI_Activator::maybe_upgrade();
		( new SWI_Plugin() )->run();
	},
	30
);
