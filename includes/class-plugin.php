<?php
/**
 * Main class for the plugin.
 *
 * @copyright SmallTownDev
 * @package 21Press/Flareo
 */

namespace P21\Flareo;

use P21\Flareo\Core\Core_Registrar;
use P21\Flareo\Core\Database_Upgrader;

/**
 * Class Plugin.
 */
final class Plugin {

	/**
	 * Main instance of the plugin.
	 *
	 * @var Plugin|null
	 */
	private static $instance;

	/**
	 * Database Upgrader.
	 *
	 * @var Database_Upgrader
	 */
	public $database_upgrader;

	/**
	 * Sets the plugin main file.
	 *
	 * @param string $main_file Absolute path to the plugin main file.
	 */
	public function __construct( $main_file ) {
		$this->includes();
	}

	/**
	 * Registers the plugin with WordPress.
	 */
	public function register() {
		add_action( 'init', array( self::$instance, 'load_textdomain' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( P21_FLAREO_FILE ), array( self::$instance, 'plugin_action_links' ) );

		// Core.
		Core_Registrar::get_instance();

		// Migrations.
		$this->database_upgrader = new Database_Upgrader();
		add_action( 'admin_init', array( $this->database_upgrader, 'init' ) );
	}

	/**
	 * Filter plugin name.
	 *
	 * @param string $plugin Plugin name.
	 * @return string
	 */
	public function filter_plugins( $plugin ) {
		$plugin = explode( '/', $plugin );
		return $plugin[0];
	}

	/**
	 * Plugin action links.
	 *
	 * Adds action links to the plugin list table
	 *
	 * Fired by `plugin_action_links` filter.
	 *
	 * @access public
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @return array An array of plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'edit.php?post_type=p21-flareo-flare&page=p21-flareo-settings' ), __( 'Settings', 'flareo' ) );

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @return void
	 */
	private function includes() {
		// Core.
		require_once P21_FLAREO_DIR . 'includes/utils/trait-instance.php';
		require_once P21_FLAREO_DIR . 'includes/core/class-options.php';
		require_once P21_FLAREO_DIR . 'includes/core/class-database-upgrader.php';
		require_once P21_FLAREO_DIR . 'includes/core/post-type/class-flare-post-fields.php';
		require_once P21_FLAREO_DIR . 'includes/core/post-type/class-flare-post-type.php';
		require_once P21_FLAREO_DIR . 'includes/core/post-type/class-flare-post-utilities.php';
		require_once P21_FLAREO_DIR . 'includes/core/post-type/class-flare-post-metabox.php';
		require_once P21_FLAREO_DIR . 'includes/core/class-core-registrar.php';

		// Settings.
		require_once P21_FLAREO_DIR . 'includes/settings/class-register-settings.php';
		require_once P21_FLAREO_DIR . 'includes/settings/settings-helpers.php';
	}

	/**
	 * Load plugin language files.
	 *
	 * @access public
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'flareo', false, dirname( P21_FLAREO_BASE ) . '/languages/' );
	}

	/**
	 * Retrieves the main instance of the plugin.
	 *
	 * @return Plugin Plugin main instance.
	 */
	public static function instance() {
		return self::$instance;
	}

	/**
	 * Loads the plugin main instance and initializes it.
	 *
	 * @param string $main_file Absolute path to the plugin main file.
	 * @return bool True if the plugin main instance could be loaded, false otherwise.
	 */
	public static function load( $main_file ) {
		if ( null !== self::$instance ) {
			return false;
		}

		self::$instance = new self( $main_file );
		self::$instance->register();

		do_action( 'p21_flareo_loaded' );

		return true;
	}
}
