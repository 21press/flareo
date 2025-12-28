<?php
/**
 * Displays Flareo Flare at Front where needed.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Display\Types;

defined( 'ABSPATH' ) || exit;

use P21\Flareo\Utils\Has_Instance;
use P21\Flareo\Core\PostType\Flare_Post_Fields;
use P21\Flareo\Core\PostType\Flare_Post_Utilities as Flare_Utils;

/**
 * Class Frontend
 */
class Frontend {
	use Has_Instance;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'attach_activate_flareo_scripts' ), 10, 2 );
	}

	/**
	 * Attach and activate Flareo Flare scripts.
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function attach_activate_flareo_scripts( $hook_suffix ) {
		$flares = Flare_Utils::get_front_active_flares();

		if ( empty( $flares ) ) {
			return;
		}

		wp_enqueue_script( 'p21-flareo-confetti-js' );
		wp_enqueue_script( 'p21-flareo-flare-js' );

		$global_settings = Flare_Utils::get_global_settings();

		foreach ( $flares as $flare ) {
			$trigger_method         = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_trigger_method' );
			$preset_type            = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_by_preset_type' );
			$preset_style           = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_by_preset_style' );
			$css_trigger_selector   = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_css_target_selectors' );
			$trigger_at_target_area = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_trigger_at_target_selector' );

			if ( 'page-load' === $trigger_method ) {
				wp_add_inline_script(
					'p21-flareo-flare-js',
					'jQuery(document).ready(function($){
						p21FlareoFlareOnPageLoad({
							flareId: ' . esc_js( $flare ) . ',
							flarePresetType: "' . esc_js( $preset_type ) . '",
							flarePresetStyle: "' . esc_js( $preset_style ) . '",
							flareTriggerTargetSelector: "' . esc_js( $css_trigger_selector ) . '",
							flareTriggerTargetArea: ' . esc_js( $trigger_at_target_area ) . ',
							flareGlobalColor: "' . esc_js( $global_settings['default_flare_color'] ) . '",
						});
					});'
				);
			} elseif ( 'on-click' === $trigger_method ) {
				// Skip if no trigger selector is set.
				if ( empty( $css_trigger_selector ) ) {
					continue;
				}

				wp_add_inline_script(
					'p21-flareo-flare-js',
					'jQuery(document).ready(function($){
						p21FlareoFlareOnClick( "' . esc_js( $css_trigger_selector ) . '", {
							flareId: ' . esc_js( $flare ) . ',
							flarePresetType: "' . esc_js( $preset_type ) . '",
							flarePresetStyle: "' . esc_js( $preset_style ) . '",
							flareTriggerTargetArea: ' . esc_js( $trigger_at_target_area ) . ',
							flareGlobalColor: "' . esc_js( $global_settings['default_flare_color'] ) . '",
						});
					});'
				);
			}
		}
	}
}
