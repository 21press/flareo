<?php
/**
 * Manages Flareo Flare Assets.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Core;

defined( 'ABSPATH' ) || exit;

use P21\Flareo\Utils\Has_Instance;

/**
 * Class Assets
 */
class Assets {
	use Has_Instance;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_global_assets' ), 0 );
	}

	/**
	 * Register plugin assets.
	 *
	 * @return void
	 */
	public function register_global_assets() {
		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ( defined( 'P21_DEBUG' ) && P21_DEBUG );
		$script_debug = $script_debug ? '' : '.min';

		wp_register_script(
			'p21-flareo-confetti-js',
			P21_FLAREO_URL . 'assets/js/canvas-confetti/confetti.browser' . $script_debug . '.js',
			array( 'jquery' ),
			filemtime( P21_FLAREO_DIR . 'assets/js/canvas-confetti/confetti.browser' . $script_debug . '.js' ),
			true
		);

		wp_register_script(
			'p21-flareo-flare-js',
			P21_FLAREO_URL . 'assets/js/flareo.js',
			array( 'jquery', 'p21-flareo-confetti-js' ),
			filemtime( P21_FLAREO_DIR . 'assets/js/flareo.js' ),
			true
		);
	}

	/**
	 * Register admin scripts.
	 *
	 * @return void
	 */
	public function register_admin_scripts() {
		// Register Flareo Flare List Screen assets here.
		wp_register_style( 'p21-flareo-flare-list-screen', P21_FLAREO_URL . '/assets/css/admin/flare-list-screen.css', array(), filemtime( P21_FLAREO_DIR . '/assets/css/admin/flare-list-screen.css' ) );
		wp_register_script( 'p21-flareo-flare-list-screen', P21_FLAREO_URL . '/assets/js/admin/flare-list-screen.js', array(), filemtime( P21_FLAREO_DIR . '/assets/js/admin/flare-list-screen.js' ), true );

		// Register Flareo Flare Edit Screen assets here.
		wp_register_style( 'p21-flareo-flare-edit-screen', P21_FLAREO_URL . '/assets/css/admin/edit-flare-screen.css', array( 'wp-color-picker' ), filemtime( P21_FLAREO_DIR . '/assets/css/admin/edit-flare-screen.css' ) );
		wp_register_script( 'p21-flareo-flare-edit-screen', P21_FLAREO_URL . '/assets/js/admin/edit-flare-screen.js', array( 'jquery', 'wp-color-picker' ), filemtime( P21_FLAREO_DIR . '/assets/js/admin/edit-flare-screen.js' ), true );

		// Attach scripts here.
		$this->register_global_assets();
	}
}
