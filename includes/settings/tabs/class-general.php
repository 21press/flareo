<?php
/**
 * General Settings
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Settings\Tabs;

use P21\Flareo\Settings\Settings_Page;

defined( 'ABSPATH' ) || exit;

/**
 * General.
 */
class General extends Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'general';
		$this->label = __( 'General', 'flareo' );

		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @param string $p21_flareo_settings_current_section Current section ID.
	 *
	 * @return array
	 */
	public function get_settings( $p21_flareo_settings_current_section = '' ) {
		global $p21_flareo_settings_current_section;

		$settings = array();

		if ( '' === $p21_flareo_settings_current_section ) {

			$settings = array(
				array(
					'title' => esc_html__( 'Default Flare Color', 'flareo' ),
					'type'  => 'title',
					'id'    => 'p21_flareo_general_settings',
				),
				array(
					'id'      => 'default_flare_color',
					'title'   => __( 'Set the default flare color for all the flares.', 'flareo' ),
					'type'    => 'color',
					'default' => '#6B4DEC',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'p21_flareo_general_settings',
				),
			);

			$settings = apply_filters( 'p21_flareo_' . $this->id . '_settings', $settings );
		}

		return apply_filters( 'p21_flareo_get_settings_' . $this->id, $settings );
	}
}

return new General();
