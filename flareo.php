<?php
/**
 * Plugin main file.
 *
 * @package     21Press/Flareo
 * @copyright   SmallTownDev
 * @link        https://21press.com/plugins/flareo/
 *
 * Plugin Name: Flareo
 * Plugin URI:  https://github.com/21press/flareo/
 * Description: Add beautiful effects to your WordPress Site — just plug and play.
 * Version:     0.2.0
 * Author:      21Press
 * Author URI:  https://21Press.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: flareo
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'P21_FLAREO_PHP_MINIMUM', '8.0' );
define( 'P21_FLAREO_WP_MINIMUM', '6.0' );
define( 'P21_FLAREO_VERSION', '0.2.0' );
define( 'P21_FLAREO_FILE', __FILE__ );
define( 'P21_FLAREO_URL', plugin_dir_url( P21_FLAREO_FILE ) );
define( 'P21_FLAREO_DIR', plugin_dir_path( P21_FLAREO_FILE ) );
define( 'P21_FLAREO_BASE', plugin_basename( P21_FLAREO_FILE ) );
define( 'P21_FLAREO_WEBSITE_URL', 'https://21press.com/plugins/flareo' );

add_action(
	'plugins_loaded',
	function () {

		if ( version_compare( PHP_VERSION, P21_FLAREO_PHP_MINIMUM, '<' ) ) {
			wp_die(
			/* translators: %s: version number */
				esc_html( sprintf( __( 'Flareo requires PHP version %s', 'flareo' ), P21_FLAREO_PHP_MINIMUM ) ),
				esc_html__( 'Error Activating', 'flareo' )
			);
		}

		// Load main plugin class.
		require_once P21_FLAREO_DIR . 'includes/class-plugin.php';

		// Initialize the plugin.
		\P21\Flareo\Plugin::load( P21_FLAREO_FILE );
	}
);
