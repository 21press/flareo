<?php
/**
 * Manages Flareo Flare Post Type.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Core\PostType;

defined( 'ABSPATH' ) || exit;

use P21\Flareo\Utils\Has_Instance;
use P21\Flareo\Core\PostType\Flare_Post_Fields;

/**
 * Class Flare_Post_Type
 */
class Flare_Post_Type {
	use Has_Instance;

	const CPT_KEY = 'p21-flareo-flare';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Create and register custom post type.
		add_action( 'init', array( $this, 'flare_post_type' ), 0 );

		// Add custom columns to the Flare CPT.
		add_filter( 'manage_p21-flareo-flare_posts_columns', array( $this, 'add_custom_columns' ) );
		// Display data to custom columns at the Flare CPT.
		add_action( 'manage_p21-flareo-flare_posts_custom_column', array( $this, 'display_custom_column_data' ), 10, 2 );
		// Action to update flare status on toggles.
		add_action( 'wp_ajax_update_flare_status', array( $this, 'update_flare_status' ) );

		// Change Default new Flare post from auto-draft to draft.
		add_action( 'wp_insert_post', array( $this, 'change_default_post_status' ), 10, 3 );

		// Add custom scripts to Flare CPT edit screen.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_custom_scripts' ), 999 );

		// Add custom messages for the Flare CPT.
		add_filter( 'post_updated_messages', array( $this, 'admin_publish_update_notice' ) );
	}

	/**
	 * Registers post type if it doesn't exist already.
	 */
	public function flare_post_type() {

		if ( ! post_type_exists( self::CPT_KEY ) ) {
			$labels = array(
				'name'                  => esc_attr__( 'Flareo Flares', 'flareo' ),
				'singular_name'         => esc_attr__( 'Flare', 'flareo' ),
				'menu_name'             => esc_attr__( 'Flareo', 'flareo' ),
				/* translators: 1. Trademarked term */
				'name_admin_bar'        => sprintf( esc_attr__( '%1$s flare', 'flareo' ), 'Automator' ),
				'archives'              => esc_attr__( 'Flare Archives', 'flareo' ),
				'attributes'            => esc_attr__( 'Flare Attributes', 'flareo' ),
				'parent_item_colon'     => esc_attr__( 'Parent Flare:', 'flareo' ),
				'all_items'             => esc_attr__( 'All Flares', 'flareo' ),
				/* translators: Non-personal infinitive verb */
				'add_new_item'          => esc_attr__( 'New Flare', 'flareo' ),
				/* translators: Non-personal infinitive verb */
				'add_new'               => esc_attr_x( 'New Flare', 'Flare', 'flareo' ),
				'new_item'              => esc_attr__( 'New Flare', 'flareo' ),
				/* translators: Non-personal infinitive verb */
				'edit_item'             => esc_attr__( 'Edit Flare', 'flareo' ),
				/* translators: Non-personal infinitive verb */
				'update_item'           => esc_attr__( 'Update Flare', 'flareo' ),
				/* translators: Non-personal infinitive verb */
				'view_item'             => esc_attr__( 'View Flare', 'flareo' ),
				/* translators: Non-personal infinitive verb */
				'view_items'            => esc_attr__( 'View Flares', 'flareo' ),
				/* translators: Non-personal infinitive verb */
				'search_items'          => esc_attr__( 'Search Flares', 'flareo' ),
				'not_found'             => esc_attr_x( 'Not found', 'Flare', 'flareo' ),
				'not_found_in_trash'    => esc_attr_x( 'Not found in trash', 'Flare', 'flareo' ),
				'featured_image'        => 'Featured Image',
				'set_featured_image'    => 'Set Featured Image',
				'remove_featured_image' => 'Remove Featured Image',
				'use_featured_image'    => 'Use as Featured Image',
				'insert_into_item'      => 'Insert Into the Flare',
				'uploaded_to_this_item' => 'Uploaded to This Flare',
				'items_list'            => 'Flares List',
				'items_list_navigation' => 'Flares List Navigation',
				'filter_items_list'     => 'Filter Flares List',
			);
			$args   = array(
				'label'               => esc_attr__( 'Flareo Flares', 'flareo' ),
				'description'         => 'Flareo for WordPress',
				'labels'              => $labels,
				'supports'            => array( 'title', 'author' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 40,
				'menu_icon'           => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iOTAiIGhlaWdodD0iOTAiIHZpZXdCb3g9IjAgMCA5MCA5MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTYwLjg0IDQ3LjA3TDQ4Ljc4IDUzLjA5OThDNDUuMTggNTQuODk5OCA0Mi4yMDk4IDU3Ljg3IDQwLjQxIDYxLjQ2OThMMzQuMzgwMSA3My41Mjk4QzMzLjQ4MDEgNzUuMjM5OSAzMS4wNTAzIDc1LjIzOTkgMzAuMTUwMyA3My41Mjk4TDI0LjEyMDQgNjEuNDY5OEMyMi4zMjA0IDU3Ljg2OTggMTkuMzUwMyA1NC44OTk3IDE1Ljc1MDQgNTMuMDk5OEwzLjUxMDQ1IDQ3LjA3QzEuODAwMzcgNDYuMTcgMS44MDAzNyA0My43NDAxIDMuNTEwNDUgNDIuODQwMUwxNS42NjA0IDM2Ljg5OTlDMTkuMjYwNCAzNS4wOTk5IDIyLjIzMDYgMzIuMTI5OCAyNC4wMzA0IDI4LjUyOTlMMzAuMDYwMyAxNi40Njk5QzMwLjk2MDMgMTQuNzU5OCAzMy4zOTAxIDE0Ljc1OTggMzQuMjkwMSAxNi40Njk5TDQwLjMyIDI4LjUyOTlDNDIuMTIgMzIuMTI5OSA0NS4wOTAxIDM1LjEwMDEgNDguNjkgMzYuODk5OUw2MC43NSA0Mi45Mjk4QzYyLjU1IDQzLjczOTggNjIuNTUwMSA0Ni4yNTk5IDYwLjg0IDQ3LjA3Wk04Ny4yMSA3NC4wN0w4MS45ODk5IDcxLjQ1OTlDODAuNDYgNzAuNjQ5OCA3OS4xOTk5IDY5LjM4OTggNzguMzg5OSA2Ny44NTk5TDc1Ljc3OTggNjIuNjM5OEM3NS40MTk3IDYxLjkxOTcgNzQuMzM5OCA2MS45MTk3IDczLjk3OTggNjIuNjM5OEw3MS4zNjk3IDY3Ljg1OTlDNzAuNTU5NiA2OS4zODk4IDY5LjI5OTYgNzAuNjQ5OCA2Ny43Njk3IDcxLjQ1OTlMNjIuNTQ5NiA3NC4wN0M2MS44Mjk0IDc0LjQzMDEgNjEuODI5NCA3NS41MDk5IDYyLjU0OTYgNzUuODdMNjcuNzY5NyA3OC40ODAxQzY5LjI5OTYgNzkuMjkwMSA3MC41NTk2IDgwLjU1MDIgNzEuMzY5NyA4Mi4wODAxTDczLjk3OTggODcuMzAwMkM3NC4zMzk4IDg4LjAyMDMgNzUuNDE5NyA4OC4wMjAzIDc1Ljc3OTggODcuMzAwMkw3OC4zODk5IDgyLjA4MDFDNzkuMTk5OSA4MC41NTAyIDgwLjQ2IDc5LjI5MDEgODEuOTg5OSA3OC40ODAxTDg3LjIxIDc1Ljg3Qzg3LjkyOTggNzUuNDIgODcuOTI5OCA3NC40Mjk4IDg3LjIxIDc0LjA3Wk04Ny4yMSAxNC4yMkw4MS45ODk5IDExLjYwOTlDODAuNDYgMTAuNzk5OCA3OS4xOTk5IDkuNTM5NzcgNzguMzg5OSA4LjAwOTkyTDc1Ljc3OTggMi43ODk3N0M3NS40MTk3IDIuMDY5NjUgNzQuMzM5OCAyLjA2OTY1IDczLjk3OTggMi43ODk3N0w3MS4zNjk3IDguMDA5OTJDNzAuNTU5NiA5LjUzOTc3IDY5LjI5OTYgMTAuNzk5OCA2Ny43Njk3IDExLjYwOTlMNjIuNTQ5NiAxNC4yMkM2MS44Mjk0IDE0LjU4MDEgNjEuODI5NCAxNS42NTk5IDYyLjU0OTYgMTYuMDJMNjcuNzY5NyAxOC42MzAxQzY5LjI5OTYgMTkuNDQwMSA3MC41NTk2IDIwLjcwMDIgNzEuMzY5NyAyMi4yMzAxTDczLjk3OTggMjcuNDUwMkM3NC4zMzk4IDI4LjE3MDMgNzUuNDE5NyAyOC4xNzAzIDc1Ljc3OTggMjcuNDUwMkw3OC4zODk5IDIyLjIzMDFDNzkuMTk5OSAyMC43MDAyIDgwLjQ2IDE5LjQ0MDEgODEuOTg5OSAxOC42MzAxTDg3LjIxIDE2LjAyQzg3LjkyOTggMTUuNTcgODcuOTI5OCAxNC41Nzk4IDg3LjIxIDE0LjIyWiIgZmlsbD0id2hpdGUiLz4KPC9zdmc+Cg==',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'taxonomies'          => array(),
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capabilities'        => array(
					'publish_posts'       => 'manage_options',
					'edit_posts'          => 'manage_options',
					'edit_others_posts'   => 'manage_options',
					'delete_posts'        => 'manage_options',
					'delete_others_posts' => 'manage_options',
					'read_private_posts'  => 'manage_options',
					'edit_post'           => 'manage_options',
					'delete_post'         => 'manage_options',
				),
			);

			register_post_type( self::CPT_KEY, apply_filters( 'p21_flareo_post_type_flare_args', $args ) );
		}
	}

	/**
	 * Highlights the menu item when adding a new Flare post type.
	 *
	 * @param string $parent_file Parent file source.
	 * @return mixed
	 */
	public function menu_highlight( $parent_file ) {
		global $plugin_page, $post_type;

		if ( self::CPT_KEY === $post_type ) {
			$plugin_page = 'edit.php?post_type=' . self::CPT_KEY; // phpcs:ignore.
		}

		return $parent_file;
	}

	/**
	 * Change the default post status.
	 *
	 * @param int      $post_ID Post ID.
	 * @param \WP_Post $post Post object.
	 * @param mixed    $update Unaccounted param. (Kept for later use if needed).
	 *
	 * @return void
	 */
	public function change_default_post_status( $post_ID, $post, $update ) {
		if ( self::CPT_KEY !== (string) $post->post_type ) {
			return;
		}

		if ( 'auto-draft' !== (string) $post->post_status ) {
			return;
		}

		/**
		 * Save plugin version for future use in case
		 * something has to be changed for older flares.
		 */
		update_post_meta( $post_ID, 'p21_flareo_flare_version', P21_FLAREO_VERSION );
	}

	/**
	 * Remove the WP standard Post publish metabox
	 *
	 * @return void
	 */
	public function remove_publish_box() {
		remove_meta_box( 'submitdiv', self::CPT_KEY, 'side' );
	}

	/**
	 * Enqueue scripts only at Flare CPT pages.
	 *
	 * @param string $hook Page hook.
	 *
	 * @return void
	 */
	public function enqueue_custom_scripts( $hook ) {

		// Scripts for Flare CPT screen.
		$this->scripts_for_cpt_list_screen( $hook );

		// Scripts for Flare CPT Edit screen.
		$this->scripts_for_cpt_edit_screen( $hook );
	}

	/**
	 * Scripts for Flare CPT list screen.
	 *
	 * @param string $hook Page template.
	 *
	 * @return void
	 */
	public function scripts_for_cpt_list_screen( $hook ) {
		if ( ! function_exists( 'get_current_screen' ) ) {
			require_once ABSPATH . 'wp-admin/includes/screen.php';
		}

		$screen = get_current_screen();

		if ( 'edit.php' !== $hook && 'edit-p21-flareo-flare' === $screen->id ) {
			return;
		}

		// Attach scripts here.
		wp_enqueue_style( 'p21-flareo-flare-list-screen' );
		wp_enqueue_script( 'p21-flareo-flare-list-screen' );
		wp_localize_script(
			'p21-flareo-flare-list-screen',
			'p21_flareo_flare_data',
			array(
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				'nonce'               => wp_create_nonce( 'update_flare_status_nonce' ),
				'translation_strings' => array(
					'status_updated'  => __( 'Status updated:', 'flareo' ),
					'status_error'    => __( 'Error updating status:', 'flareo' ),
					'request_support' => __( 'Please report it to 21Press support!', 'flareo' ),
				),
			)
		);

		// Custom action hook for further extension.
		do_action( 'p21_flareo_enqueue_flare_list_screen_scripts', $hook, $screen );
	}

	/**
	 * Scripts for Flares CPT edit screen.
	 *
	 * @param string $hook Page template.
	 *
	 * @return void
	 */
	public function scripts_for_cpt_edit_screen( $hook ) {
		// Add scripts ONLY to Flare custom post type.
		if ( 'post-new.php' !== $hook && 'post.php' !== $hook ) {
			return;
		}
		if ( self::CPT_KEY !== (string) get_post_type() ) {
			return;
		}

		// Attach scripts here.
		wp_enqueue_style( 'p21-flareo-flare-edit-screen' );
		wp_enqueue_script( 'p21-flareo-flare-edit-screen' );

		// Custom action hook for further extension.
		do_action( 'p21_flareo_enqueue_flare_edit_screen_scripts', $hook );
	}

	/**
	 * Create custom columns in the recipe list
	 *
	 * @param array $columns Existing columns.
	 *
	 * @return mixed
	 */
	public function add_custom_columns( $columns ) {
		$updated_columns = array();

		foreach ( $columns as $key => $column ) {
			if ( 'title' === $key ) {
				$updated_columns[ $key ]          = $column;
				$updated_columns['status']        = esc_attr__( 'Status', 'flareo' );
				$updated_columns['insert-method'] = esc_attr__( 'Insert Method', 'flareo' );
				$updated_columns['priority']      = esc_attr__( 'Priority', 'flareo' );
			} elseif ( in_array( $key, array( 'cb' ), true ) ) {
				$updated_columns[ $key ] = $column;
			}
		}

		return $updated_columns;
	}

	/**
	 * Add data to custom columns at the Flare CPT.
	 *
	 * @param string $column Column key.
	 * @param int    $post_id Flare CPT post id.
	 *
	 * @return void
	 */
	public function display_custom_column_data( $column, $post_id ) {
		switch ( $column ) {
			case 'status':
				$is_active = Flare_Post_Fields::get_field_value( $post_id, 'p21_flareo_flare_active' );
				?>
				<div class="p21-flareo-flare_status">
					<div>
						<label class="switch" for="p21_flareo_flare_active[<?php echo esc_attr( $post_id ); ?>]">
							<input
								type="checkbox"
								name="p21_flareo_flare_active[<?php echo esc_attr( $post_id ); ?>]"
								id="p21_flareo_flare_active[<?php echo esc_attr( $post_id ); ?>]"
								value="1"
								data-flare_id="<?php echo esc_attr( $post_id ); ?>"
								<?php checked( $is_active, 1 ); ?>
							>
							<span class="slider round"></span>
							<span class="input-label"><?php esc_html_e( 'Active', 'flareo' ); ?></span>
						</label>
					</div>
				</div>
				<?php

				break;
			case 'insert-method':
				$insert_method = Flare_Post_Fields::get_field_value( $post_id, 'p21_flareo_flare_insert_method' );

				if ( 'auto-insert' === $insert_method ) {
					$insert_method_label = __( 'Auto Insert', 'flareo' );

					$auto_insert_locations = Flare_Post_Fields::get_field_value( $post_id, 'p21_flareo_flare_method_auto_insert_locations' );

					$insert_method_label .= ' - ' . ucfirst( str_replace( '-', ' ', $auto_insert_locations ) );
				} elseif ( 'shortcode' === $insert_method ) {
					$insert_method_label = __( 'Shortcode', 'flareo' );
				} else {
					$insert_method_label = __( 'N/A', 'flareo' );
				}

				echo '<p>' . esc_html( $insert_method_label ) . '</p>';

				if ( 'shortcode' === $insert_method ) :
					?>
				<div class="p21-flareo-flare_shortcode">
					<input type="text" readonly value="[p21_flareo_flare id=&quot;<?php echo esc_attr( $post_id ); ?>&quot;]" id="flare-shortcode-<?php echo esc_attr( $post_id ); ?>" />
					<button type="button" class="p21-flareo-button p21-flareo-button-secondary p21-flareo-copy-target button button-secondary" data-target="#flare-shortcode-<?php echo esc_attr( $post_id ); ?>"><span class="p21-flareo-default-icon"><svg class="p21-flareo-icon p21-flareo-icon-copy" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" id=""><path d="M10.8125 1.125H3.3125C2.625 1.125 2.0625 1.6875 2.0625 2.375V11.125H3.3125V2.375H10.8125V1.125ZM12.6875 3.625H5.8125C5.125 3.625 4.5625 4.1875 4.5625 4.875V13.625C4.5625 14.3125 5.125 14.875 5.8125 14.875H12.6875C13.375 14.875 13.9375 14.3125 13.9375 13.625V4.875C13.9375 4.1875 13.375 3.625 12.6875 3.625ZM12.6875 13.625H5.8125V4.875H12.6875V13.625Z" fill="currentColor"></path></svg></span><span class="p21-flareo-success-icon"><svg class="p21-flareo-icon p21-flareo-icon-check" width="14" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg" id=""><path d="M5.8002 10.9L1.6002 6.70005L0.200195 8.10005L5.8002 13.7L17.8002 1.70005L16.4002 0.300049L5.8002 10.9Z" fill="currentColor"></path></svg></span><?php esc_html_e( 'Copy', 'flareo' ); ?></button>
				</div>
					<?php
				endif;
				break;
			case 'priority':
				?>
				<div class="p21-flareo-flare_priority">
					<p><?php echo esc_html( Flare_Post_Fields::get_field_value( $post_id, 'p21_flareo_flare_method_auto_insert_priority' ) ); ?></p>
				</div>
				<?php
				break;
		}
	}

	/**
	 * Update Flare Status toggle action.
	 *
	 * @return void
	 */
	public function update_flare_status() {

		if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'update_flare_status_nonce' ) ) {
			return;
		}

		if ( isset( $_POST['flare_id'] ) && isset( $_POST['is_active'] ) ) {
			$flare_id  = intval( $_POST['flare_id'] );
			$is_active   = Flare_Post_Utilities::sanitize_bool( wp_unslash( $_POST['is_active'] ) ); // phpcs:ignore.

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( __( 'User does not have permission to edit this flare', 'flareo' ) );
				return;
			}

			// Update the post meta.
			update_post_meta( $flare_id, 'p21_flareo_flare_active', $is_active );

			wp_send_json_success(
				array(
					'flare_id'  => $flare_id,
					'is_active' => $is_active,
				)
			);
		} else {
			wp_send_json_error( __( 'Invalid data', 'flareo' ) );
		}
	}

	/**
	 * Default button hide from the plugin.
	 *
	 * @param string $messages give custom notice of publish and update.
	 */
	public function admin_publish_update_notice( $messages ) {
		$messages[ self::CPT_KEY ][6] = __( 'Flareo flare has been published.', 'flareo' );
		$messages[ self::CPT_KEY ][1] = __( 'Flareo flare updated.', 'flareo' );
		return $messages;
	}
}
