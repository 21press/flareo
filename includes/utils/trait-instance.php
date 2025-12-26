<?php
/**
 * Util has instance.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Utils;

defined( 'ABSPATH' ) || exit;

/**
 * Trait Has_Instance
 */
trait Has_Instance {
	/**
	 * Class instance.
	 *
	 * @var $instance
	 */
	protected static $instance;

	/**
	 * Get instance.
	 *
	 * @return mixed
	 */
	final public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}
}
