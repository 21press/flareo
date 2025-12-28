<?php
/**
 * Settings handler.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Settings;

defined( 'ABSPATH' ) || exit;

use P21\Flareo\Utils\Has_Instance;

/**
 * Register_Settings.
 */
class Register_Settings {
	use Has_Instance;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );

		// Handle saving settings earlier than load-{page} hook to avoid race conditions in conditional menus.
		add_action( 'wp_loaded', array( $this, 'save_settings' ) );

		add_action( 'init', array( $this, 'create_options' ) );

		add_action( 'load-settings_page_p21-flareo-settings', array( $this, 'cleanup_plugin_settings_page' ) );
	}

	/**
	 * Register plugin menu.
	 *
	 * @return void
	 */
	public function register_menu() {
		$primary_slug = 'edit.php?post_type=p21-flareo-flare';

		add_submenu_page(
			$primary_slug,
			__( 'Flareo Settings', 'flareo' ),
			__( 'Settings', 'flareo' ),
			'manage_options',
			'p21-flareo-settings',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Add settings page.
	 *
	 * @return void
	 */
	public function settings_page() {
		Admin_Settings::output();
	}

	/**
	 * Default options.
	 *
	 * Sets up the default options used on the settings page.
	 */
	public function create_options() {
		if ( ! is_admin() ) {
			return false;
		}

		// Include settings so that we can run through defaults.
		include P21_FLAREO_DIR . 'includes/settings/class-admin-settings.php';

		$settings = Admin_Settings::get_settings_pages();

		foreach ( $settings as $section ) {
			if ( 'object' !== gettype( $section ) || ! method_exists( $section, 'get_settings' ) ) {
				continue;
			}
			$subsections = array_unique( array_merge( array( '' ), array_keys( $section->get_sections() ) ) );

			foreach ( $subsections as $subsection ) {
				foreach ( $section->get_settings( $subsection ) as $value ) {
					if ( isset( $value['default'], $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}
	}

	/**
	 * Handle saving of settings.
	 *
	 * @return void
	 */
	public function save_settings() {
		global $p21_flareo_settings_current_tab, $p21_flareo_settings_current_section;

		// We should only save on the settings page.
		if ( ! is_admin() || ! isset( $_GET['page'] ) || 'p21-flareo-settings' !== $_GET['page'] ) {
			return;
		}

		// Include settings pages.
		Admin_Settings::get_settings_pages();

		// Get current tab/section.
		$p21_flareo_settings_current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) );
		$p21_flareo_settings_current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) );
		$nonce                               = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';

		// Save settings if data has been posted.
		if ( wp_verify_nonce( $nonce, 'p21-flareo-settings' ) ) {
			if ( '' !== $p21_flareo_settings_current_section && apply_filters( "p21_flareo_save_settings_{$p21_flareo_settings_current_tab}_{$p21_flareo_settings_current_section}", ! empty( $_POST['save'] ) ) ) {
				Admin_Settings::save();
			} elseif ( '' === $p21_flareo_settings_current_section && apply_filters( "p21_flareo_save_settings_{$p21_flareo_settings_current_tab}", ! empty( $_POST['save'] ) ) ) {
				Admin_Settings::save();
			}
		}
	}

	/**
	 * Remove all notices from settings page for a clean and minimal look.
	 *
	 * @return void
	 */
	public function cleanup_plugin_settings_page() {
		remove_all_actions( 'admin_notices' );
	}
}

// Instantiate settings.
Register_Settings::get_instance();
