<?php
/**
 * Manages Flareo Flare Post Type.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Core\PostType;

use P21\Flareo\Utils\Has_Instance;

/**
 * Class Flare_Post_Type
 */
class Flare_Post_Type {
	use Has_Instance;

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

		if ( ! post_type_exists( 'p21-flareo-flare' ) ) {
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

			register_post_type( 'p21-flareo-flare', apply_filters( 'p21_flareo_post_type_flare_args', $args ) );
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

		if ( 'p21-flareo-flare' === $post_type ) {
			$plugin_page = 'edit.php?post_type=p21-flareo-flare'; // phpcs:ignore.
		}

		return $parent_file;
	}

	/**
	 * Change the default post status.
	 *
	 * @param int      $post_ID Post ID.
	 * @param \WP_Post $post Post object.
	 * @param mixed    $update Unaccounted param.
	 *
	 * @return void
	 */
	public function change_default_post_status( $post_ID, $post, $update ) {
		if ( 'p21-flareo-flare' !== (string) $post->post_type ) {
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
		remove_meta_box( 'submitdiv', 'p21-flareo-flare', 'side' );
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

		// Register scripts here.
		wp_register_style( 'p21-flareo-flare-list-screen', P21_FLAREO_URL . '/assets/css/admin/flare-list-screen.css', array(), filemtime( P21_FLAREO_DIR . '/assets/css/admin/flare-list-screen.css' ) );
		wp_register_script( 'p21-flareo-flare-list-screen', P21_FLAREO_URL . '/assets/js/admin/flare-list-screen.js', array(), filemtime( P21_FLAREO_DIR . '/assets/js/admin/flare-list-screen.js' ), true );

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
		if ( 'p21-flareo-flare' !== (string) get_post_type() ) {
			return;
		}

		// Register scripts here.
		wp_register_style( 'p21-flareo-flare-edit-screen', P21_FLAREO_URL . '/assets/css/admin/edit-flare-screen.css', array( 'wp-color-picker' ), filemtime( P21_FLAREO_DIR . '/assets/css/admin/edit-flare-screen.css' ) );
		wp_register_script( 'p21-flareo-flare-edit-screen', P21_FLAREO_URL . '/assets/js/admin/edit-flare-screen.js', array( 'jquery', 'wp-color-picker' ), filemtime( P21_FLAREO_DIR . '/assets/js/admin/edit-flare-screen.js' ), true );

		// Attach scripts here.
		wp_enqueue_style( 'p21-flareo-flare-edit-screen' );
		wp_enqueue_script( 'p21-flareo-flare-edit-screen' );
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
				$updated_columns[ $key ]      = $column;
				$updated_columns['status']    = esc_attr__( 'Status', 'flareo' );
				$updated_columns['shortcode'] = esc_attr__( 'Shortcode', 'flareo' );
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
				$is_active = get_post_meta( $post_id, 'p21_flareo_flare_active', true );
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
			case 'shortcode':
				?>
				<div class="p21-flareo-flare_shortcode">
					<p>
						<code>[p21_flareo_flare id="<?php echo esc_attr( $post_id ); ?>"]</code>
					</p>
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
		$messages['p21-flareo-flare'][6] = __( 'Flareo flare has been published.', 'flareo' );
		$messages['p21-flareo-flare'][1] = __( 'Flareo flare updated.', 'flareo' );
		return $messages;
	}
}
