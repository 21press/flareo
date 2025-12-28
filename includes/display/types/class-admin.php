<?php
/**
 * Registers and manages Admin Flareo Flare.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Display\Types;

defined( 'ABSPATH' ) || exit;

use P21\Flareo\Utils\Has_Instance;
use P21\Flareo\Core\Options;
use P21\Flareo\Core\PostType\Flare_Post_Fields;
use P21\Flareo\Core\PostType\Flare_Post_Utilities as Flare_Utils;

/**
 * Class Admin
 */
class Admin {
	use Has_Instance;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Enqueue Flareo Flare on Flare Edit Screen Scripts.
		add_action( 'p21_flareo_enqueue_flare_edit_screen_scripts', array( $this, 'attach_activate_flareo_edit_screen_scripts' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'attach_activate_flareo_scripts' ), 10, 2 );
	}

	/**
	 * Attach and activate Flareo Flare scripts.
	 */
	public function attach_activate_flareo_edit_screen_scripts() {
		wp_enqueue_script( 'p21-flareo-confetti-js' );
		wp_enqueue_script( 'p21-flareo-flare-js' );

		$global_settings = Flare_Utils::get_global_settings();

		wp_add_inline_script(
			'p21-flareo-flare-js',
			'jQuery(document).ready(function($){
					$( "#p21-flareo-flare-preview-button" ).on( "click", async function(event) {
						const flareType = $("input[name=\'p21_flareo_flare_by_type\']:checked").val();
						const flarePresetType = $("input[name=\'p21_flareo_flare_by_preset_type\']:checked").val();
						const flarePresetStyle = $("input[name=\'p21_flareo_flare_by_preset_style\']:checked").val();

					 	p21FlareoFlareOnPageLoad({
							flareType,
							flarePresetType,
							flarePresetStyle,
							flareGlobalColor: "' . esc_js( $global_settings['default_flare_color'] ) . '",
						});
					});
			});'
		);
	}

	/**
	 * Attach and activate Flareo Flare scripts.
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function attach_activate_flareo_scripts( $hook_suffix ) {
		$flares = Flare_Utils::get_admin_active_flares();

		if ( empty( $flares ) ) {
			return;
		}

		wp_enqueue_script( 'p21-flareo-confetti-js' );
		wp_enqueue_script( 'p21-flareo-flare-js' );

		$global_settings = Flare_Utils::get_global_settings();

		foreach ( $flares as $flare ) {
			$trigger_method = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_trigger_method' );
			$preset_type    = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_by_preset_type' );
			$preset_style   = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_by_preset_style' );

			if ( 'page-load' === $trigger_method ) {
				wp_add_inline_script(
					'p21-flareo-flare-js',
					'jQuery(document).ready(function($){
						p21FlareoFlareOnPageLoad({
							flareId: ' . esc_js( $flare ) . ',
							flarePresetType: "' . esc_js( $preset_type ) . '",
							flarePresetStyle: "' . esc_js( $preset_style ) . '",
							flareGlobalColor: "' . esc_js( $global_settings['default_flare_color'] ) . '",
						});
					});'
				);
			} elseif ( 'on-click' === $trigger_method ) {
				$trigger_selector = Flare_Post_Fields::get_field_value( $flare, 'p21_flareo_flare_trigger_selector' );

				// Skip if no trigger selector is set.
				if ( empty( $trigger_selector ) ) {
					continue;
				}

				wp_add_inline_script(
					'p21-flareo-flare-js',
					'jQuery(document).ready(function($){
						p21FlareoFlareOnClick( "' . esc_js( $trigger_selector ) . '", {
							flareId: ' . esc_js( $flare ) . ',
							flarePresetType: "' . esc_js( $preset_type ) . '",
							flarePresetStyle: "' . esc_js( $preset_style ) . '",
							flareGlobalColor: "' . esc_js( $global_settings['default_flare_color'] ) . '",
						});
					});'
				);
			}
		}
	}
}
