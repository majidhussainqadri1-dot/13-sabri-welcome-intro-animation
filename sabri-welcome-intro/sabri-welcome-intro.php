<?php
/**
 * Plugin Name: Sabri Welcome Intro Animation
 * Plugin URI: https://www.sabrihomeopathy.com/
 * Description: An accessible, fail-safe eight-second welcome animation for Sabri Homeopathy, shown once per browser session.
 * Version: 0.2.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Dr. Allamah Majid Hussain Sabri Muhaddith Mursheed
 * License: GPL-2.0-or-later
 * Text Domain: sabri-welcome-intro
 */

defined( 'ABSPATH' ) || exit;

define( 'SWI_VERSION', '0.2.0' );
define( 'SWI_FILE', __FILE__ );
define( 'SWI_DIR', plugin_dir_path( __FILE__ ) );
define( 'SWI_URL', plugin_dir_url( __FILE__ ) );

require_once SWI_DIR . 'includes/class-swi-activator.php';
require_once SWI_DIR . 'includes/class-swi-renderer.php';
require_once SWI_DIR . 'includes/class-swi-admin.php';
require_once SWI_DIR . 'includes/class-swi-plugin.php';

register_activation_hook( SWI_FILE, array( 'SWI_Activator', 'activate' ) );

add_action(
	'plugins_loaded',
	static function () {
		( new SWI_Plugin() )->run();
	},
	40
);
