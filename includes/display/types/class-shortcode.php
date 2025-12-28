<?php
/**
 * Registers and manages Shortcode Flareo Flare.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Display\Types;

defined( 'ABSPATH' ) || exit;

use P21\Flareo\Utils\Has_Instance;
use P21\Flareo\Core\PostType\Flare_Post_Type;
use P21\Flareo\Core\PostType\Flare_Post_Fields;
use P21\Flareo\Core\PostType\Flare_Post_Utilities as Flare_Utils;

/**
 * Class Shortcode
 */
class Shortcode {
	use Has_Instance;

	/**
	 * Class constructor.
	 */
	public function __construct() {
			add_shortcode( 'p21_flareo_flare', array( $this, 'render' ) );
	}

	/**
	 * Render Flareo Flare via Shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function render( $atts ) {
		// Avoid rendering in REST API edit context.
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return '';
		}

		$atts = shortcode_atts(
			array(
				'id' => '',
			),
			$atts,
			'p21_flareo_flare'
		);

		if ( '' === $atts['id'] ) {
			$user_can_manage_options = user_can( get_current_user_id(), 'manage_options' );
			if ( $user_can_manage_options ) {
				return '<div class="p21-flareo-flare-error" style="background: #000; color: #fff; padding: 10px; border-radius: 5px;">' . esc_html__( 'Flareo: Please provide the Flare id attribute!', 'flareo' ) . '</div>';
			} else {
				return '';
			}
		}

		$flare_id = intval( $atts['id'] );

		// Check if valid flare post type.
		$post_type = get_post_type( $flare_id );

		if ( ! $post_type || Flare_Post_Type::CPT_KEY !== $post_type ) {
			return '<div class="p21-flareo-flare-error" style="background: #000; color: #fff; padding: 10px; border-radius: 5px;">' . esc_html__( 'Invalid flare ID provided.', 'flareo' ) . '</div>';
		}

			$insert_method = Flare_Post_Fields::get_field_value( $flare_id, 'p21_flareo_flare_insert_method' );

		if ( 'shortcode' !== $insert_method ) {
			$user_can_manage_options = user_can( get_current_user_id(), 'manage_options' );
			if ( $user_can_manage_options ) {
				return '<div class="p21-flareo-flare-error" style="background: #000; color: #fff; padding: 10px; border-radius: 5px;">' . esc_html__( 'Flareo: The specified flare is not set to be inserted via Shortcode method.', 'flareo' ) . '</div>';
			} else {
				return '';
			}
		}

		// Enqueue scripts.
		wp_enqueue_script( 'p21-flareo-confetti-js' );
		wp_enqueue_script( 'p21-flareo-flare-js' );

		$global_settings = Flare_Utils::get_global_settings();

		$trigger_method         = Flare_Post_Fields::get_field_value( $flare_id, 'p21_flareo_flare_trigger_method' );
		$preset_type            = Flare_Post_Fields::get_field_value( $flare_id, 'p21_flareo_flare_by_preset_type' );
		$preset_style           = Flare_Post_Fields::get_field_value( $flare_id, 'p21_flareo_flare_by_preset_style' );
		$css_trigger_selector   = Flare_Post_Fields::get_field_value( $flare_id, 'p21_flareo_flare_css_target_selectors' );
		$trigger_at_target_area = Flare_Post_Fields::get_field_value( $flare_id, 'p21_flareo_flare_trigger_at_target_selector' );

		if ( 'page-load' === $trigger_method ) {
			$safe_js = 'jQuery(document).ready(function($){
						p21FlareoFlareOnPageLoad({
							flareId: ' . esc_js( $flare_id ) . ',
							flarePresetType: "' . esc_js( $preset_type ) . '",
							flarePresetStyle: "' . esc_js( $preset_style ) . '",
							flareTriggerTargetSelector: "' . esc_js( $css_trigger_selector ) . '",
							flareTriggerTargetArea: ' . esc_js( $trigger_at_target_area ) . ',
							flareGlobalColor: "' . esc_js( $global_settings['default_flare_color'] ) . '",
						});
					});';
		} elseif ( 'on-click' === $trigger_method ) {
			// Skip if no trigger selector is set.
			if ( empty( $css_trigger_selector ) ) {
				return;
			}

			$safe_js = 'jQuery(document).ready(function($){
						p21FlareoFlareOnClick( "' . esc_js( $css_trigger_selector ) . '", {
							flareId: ' . esc_js( $flare_id ) . ',
							flarePresetType: "' . esc_js( $preset_type ) . '",
							flarePresetStyle: "' . esc_js( $preset_style ) . '",
							flareTriggerTargetArea: ' . esc_js( $trigger_at_target_area ) . ',
							flareGlobalColor: "' . esc_js( $global_settings['default_flare_color'] ) . '",
						});
					});';
		}

		return '<script type="text/javascript">' . $safe_js . '</script>';
	}
}
