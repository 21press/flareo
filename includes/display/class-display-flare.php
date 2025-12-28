<?php
/**
 * Displays Flareo Flare where needed.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Display;

defined( 'ABSPATH' ) || exit;

use P21\Flareo\Utils\Has_Instance;
use P21\Flareo\Display\Types\Shortcode;
use P21\Flareo\Display\Types\Frontend;
use P21\Flareo\Display\Types\Admin;

/**
 * Class Display_Flare
 */
class Display_Flare {
	use Has_Instance;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->includes();

		// Initialize Display Types.
		Shortcode::get_instance();
		Frontend::get_instance();
		Admin::get_instance();
	}

	/**
	 * Includes required files.
	 */
	private function includes() {
		require_once __DIR__ . '/types/class-shortcode.php';
		require_once __DIR__ . '/types/class-frontend.php';
		require_once __DIR__ . '/types/class-admin.php';
	}
}
