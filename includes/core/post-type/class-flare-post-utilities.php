<?php
/**
 * Utilities for Flare CPT.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Core\PostType;

defined( 'ABSPATH' ) || exit;

use DateTime;
use P21\Flareo\Utils\Has_Instance;
use P21\Flareo\Core\PostType\Flare_Post_Type;
use P21\Flareo\Core\Options;

/**
 * Class Flare_Post_Utilities
 */
class Flare_Post_Utilities {
	use Has_Instance;

	/**
	 * Converts date into desired format.
	 *
	 * @param  string $date Date string.
	 * @param  string $from_format Date format of the input date.
	 * @param  string $to_format Desired date format for the output.
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

	/**
	 * Get all Active Flares.
	 *
	 * @param string $fields Fields to retrieve.
	 *
	 * @return array
	 */
	public static function get_all_active_flares( $fields = 'ids' ) {
		$flares = get_posts(
			array(
				'post_type'      => Flare_Post_Type::CPT_KEY,
				'post_status'    => 'any',
				'fields'         => $fields,
				'posts_per_page' => -1,
			)
		);

		$active_flares = array();

		foreach ( $flares as $flare ) {
			$is_active = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_active' );
			if ( '1' === $is_active ) {
				$active_flares[] = $flare;
			}
		}

		return $active_flares;
	}

	/**
	 * Get all Flare which are active in Admin area.
	 *
	 * @param string $fields Fields to retrieve.
	 *
	 * @return array
	 */
	public static function get_admin_active_flares( $fields = 'ids' ) {
		$active_flares = self::get_all_active_flares( $fields );

		$admin_flares = array();

		foreach ( $active_flares as $flare ) {
			$insert_method        = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_insert_method' );
			$auto_insert_location = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_method_auto_insert_locations' );

			if ( 'auto-insert' === $insert_method && in_array( $auto_insert_location, array( 'admin', 'everywhere' ), true ) ) {
				$admin_flares[] = $flare;
			}
		}

		return $admin_flares;
	}

	/**
	 * Get all Flare which are active in Front area.
	 *
	 * @param string $fields Fields to retrieve.
	 *
	 * @return array
	 */
	public static function get_front_active_flares( $fields = 'ids' ) {
		$active_flares = self::get_all_active_flares( $fields );

		$front_flares = array();

		foreach ( $active_flares as $flare ) {
			$insert_method        = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_insert_method' );
			$auto_insert_location = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_method_auto_insert_locations' );

			if ( 'auto-insert' === $insert_method && in_array( $auto_insert_location, array( 'front', 'everywhere' ), true ) ) {
				$front_flares[] = $flare;
			}
		}

		return $front_flares;
	}

	/**
	 * Get global settings.
	 *
	 * @return array
	 */
	public static function get_global_settings() {
		$options = Options::get_instance();

		return array(
			'default_flare_color' => $options->get( 'default_flare_color', '#6B4DEC' ),
		);
	}
}
