<?php
/**
 * Plugin Name: Sabri Welcome Intro Animation
 * Plugin URI: https://github.com/majidhussainqadri1-dot/13-sabri-welcome-intro-animation
 * Description: Fail-closed compatibility and migration guard for the historical File 13 welcome intro.
 * Version: 1.0.1
 * Author: Dr. Allamah Majid Hussain Sabri Muhaddith Mursheed
 * Text Domain: sabri-welcome-intro
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.1
 *
 * @package SabriWelcomeIntro
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'SWI_VERSION', '1.0.1' );
define( 'SWI_SCHEMA_VERSION', '1.0.1' );
define( 'SWI_FILE', __FILE__ );
define( 'SWI_PATH', plugin_dir_path( __FILE__ ) );
define( 'SWI_URL', plugin_dir_url( __FILE__ ) );
define( 'SWI_TEXT_DOMAIN', 'sabri-welcome-intro' );

require_once SWI_PATH . 'includes/class-authorization.php';
require_once SWI_PATH . 'includes/class-settings.php';
require_once SWI_PATH . 'includes/class-eligibility.php';
require_once SWI_PATH . 'includes/class-analytics.php';
require_once SWI_PATH . 'includes/class-renderer.php';
require_once SWI_PATH . 'includes/class-rest.php';
require_once SWI_PATH . 'includes/class-health.php';
require_once SWI_PATH . 'includes/class-foundation.php';
require_once SWI_PATH . 'includes/class-admin.php';
require_once SWI_PATH . 'includes/class-plugin.php';

register_activation_hook( __FILE__, array( 'Sabri\\WelcomeIntro\\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Sabri\\WelcomeIntro\\Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', static function () {
	load_plugin_textdomain( SWI_TEXT_DOMAIN, false, dirname( plugin_basename( SWI_FILE ) ) . '/languages' );
	Sabri\WelcomeIntro\Plugin::instance()->register();
} );
