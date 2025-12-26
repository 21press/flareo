<?php
/**
 * Utilities for Flare CPT.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Core\PostType;

use DateTime;
use P21\Flareo\Utils\Has_Instance;

/**
 * Class Flare_Post_Utilities
 */
class Flare_Post_Utilities {
	use Has_Instance;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Only if something needs to run once or when class is instantiated.
	}

	/**
	 * Converts date into desired format.
	 *
	 * @param $date
	 * @param $from_format
	 * @param $to_format
	 *
	 * @return string
	 */
	public function convert_date_format( $date, $from_format = 'd-m-Y', $to_format = 'Y-m-d' ) {
		$date_obj = DateTime::createFromFormat( $from_format, $date );
		return $date_obj->format( $to_format );
	}

	/**
	 * Sanitizes a value into a boolean.
	 *
	 * @param mixed $input Could be any value.
	 *
	 * @return int
	 */
	public static function sanitize_bool( $input ) {
		if ( is_bool( $input ) ) {
			return $input ? 1 : 0;
		}

		if ( is_int( $input ) ) {
			return 1 === $input ? 1 : 0;
		}

		if ( is_string( $input ) ) {
			$input = strtolower( $input );
			if ( in_array( $input, array( '1', 'true', 'yes', 'on' ), true ) ) {
				return 1;
			}
			if ( in_array( $input, array( '0', 'false', 'no', 'off' ), true ) ) {
				return 0;
			}
		}

		return 0;
	}
}
