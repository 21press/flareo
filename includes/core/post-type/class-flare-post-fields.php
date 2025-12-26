<?php
/**
 * Manages Flare Post Type fields.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Core\PostType;

/**
 * Class Flare_Post_Fields
 */
class Flare_Post_Fields {
	/**
	 * Get all meta field keys for Flare post type.
	 *
	 * @return array
	 */
	public static function get_all() {
		$current_year = gmdate( 'Y' );

		return array(
			array(
				'key'    => 'source_flare',
				'title'  => __( 'Source & Flare', 'flareo' ),
				'fields' => array(
					array(
						'id'      => 'p21_flareo_flare_by_type',
						'label'   => __( 'Flare Type', 'flareo' ),
						'type'    => 'radio',
						'default' => 'confetti',
						'options' => array(
							'presets' => __( 'Presets', 'flareo' ),
							'emojis'  => __( 'Emojis', 'flareo' ),
						),
					),
				),
			),
			array(
				'key'   => 'layout_style',
				'title' => __( 'Layout & Style', 'flareo' ),
				'tabs'  => array(
					array(
						'id'     => 'layout',
						'label'  => __( 'Layout', 'flareo' ),
						'fields' => array(
							array(
								'id'      => 'p21_flareo_flare_layout_type',
								'label'   => __( 'Select a Layout Type', 'flareo' ),
								'type'    => 'radio',
								'default' => 'type-one',
								'options' => array(
									'type-one' => __( 'Type One', 'flareo' ),
									'type-two' => __( 'Type Two', 'flareo' ),
								),
							),
						),
					),
					array(
						'id'     => 'icons',
						'label'  => __( 'Icons', 'flareo' ),
						'fields' => array(),
					),
				),
			),
		);
	}

	/**
	 * Get SVG icon for a given section key.
	 *
	 * @param string $section_key Section key.
	 * @return string
	 */
	public static function get_section_icon( $section_key ) {
		$icons = array(
			'source_flare' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
								<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
								<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
							</svg>',
			'layout_style' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
								<path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
							</svg>',
		);

		return isset( $icons[ $section_key ] ) ? $icons[ $section_key ] : '';
	}

	/**
	 * Get field value for a given flare and field ID.
	 *
	 * @param int    $flare_id Flare post ID.
	 * @param string $field_id    Field meta key.
	 * @param mixed  $default    Default value if not set.
	 * @return mixed
	 */
	public static function get_field_value( $flare_id, $field_id, $default = null ) {
		// Get saved value.
		$value = get_post_meta( $flare_id, $field_id, true );

		$default = is_null( $default ) ? '' : $default;

		// Get default value from all fields above.
		if ( empty( $value ) ) {
			$all_fields = self::get_all();

			foreach ( $all_fields as $section ) {
				if ( isset( $section['fields'] ) && is_array( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						if ( $field['id'] === $field_id ) {
							$default = isset( $field['default'] ) ? $field['default'] : $default;
							break 2;
						}
					}
				} elseif ( isset( $section['tabs'] ) && is_array( $section['tabs'] ) ) {
					foreach ( $section['tabs'] as $tab ) {
						if ( isset( $tab['fields'] ) && is_array( $tab['fields'] ) ) {
							foreach ( $tab['fields'] as $field ) {
								if ( $field['id'] === $field_id ) {
									$default = isset( $field['default'] ) ? $field['default'] : $default;
									break 3;
								}
							}
						}
					}
				}
			}
		}

			// Return default if no saved value.
			return $value ? $value : $default;
	}

	/**
	 * Update field value for a given flare and field ID.
	 *
	 * @param int    $flare_id Flare post ID.
	 * @param string $field_id    Field meta key.
	 * @param mixed  $value       Value to set.
	 * @return int|bool
	 */
	public static function update_field_value( $flare_id, $field_id, $value ) {
		return update_post_meta( $flare_id, $field_id, $value );
	}
}
