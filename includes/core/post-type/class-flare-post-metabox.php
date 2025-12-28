<?php
/**
 * Manages Flare Post Type metaboxes.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Core\PostType;

defined( 'ABSPATH' ) || exit;

use P21\Flareo\Utils\Has_Instance;
use P21\Flareo\Core\PostType\Flare_Post_Type;

/**
 * Class Flare_Post_Metabox
 */
class Flare_Post_Metabox {
	use Has_Instance;

	/**
	 * Metabox default field options.
	 *
	 * @var array<string>
	 * @return array<string>
	 */
	public $default_fields = array(
		array(
			'id'   => 'p21_flareo_flare_active',
			'type' => 'checkbox',
		),
	);

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Adding entry point for JS based triggers and actions UI into Meta Boxes.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box_ui' ), 11 );

		// Save metabox fields into their respective meta.
		add_action( 'save_post', array( $this, 'save_meta_boxes' ) );

		// Update Flare post status based on active option.
		add_action( 'wp_after_insert_post', array( $this, 'update_status' ), 10, 2 );
	}

	/**
	 * Adds conditional data attributes for fields.
	 *
	 * @param string $key   The attribute key.
	 * @param string $value The attribute value.
	 *
	 * @return string
	 */
	public function add_conditional_field_attr( $key, $value ) {
		// Return empty if value is empty.
		if ( empty( $value ) ) {
			return '';
		}

		return 'data-conditional-' . esc_attr( $key ) . '=' . esc_attr( $value );
	}

	/**
	 * Adds fields for the flare post type.
	 *
	 * @param int   $flare_id The flare post ID.
	 * @param array $fields      The fields to add.
	 * @return void
	 */
	public function add_fields( $flare_id, $fields ) {
		// Return if fields is not valid.
		if ( ! isset( $fields ) || ! is_array( $fields ) || empty( $fields ) ) {
			return;
		}
		?>
		<div class="options-group">
			<?php
			foreach ( $fields as $field ) {
				$type     = isset( $field['type'] ) ? $field['type'] : '';
				$field_id = isset( $field['id'] ) ? $field['id'] : '';

				// Jump to next if id is empty or type is invalid.
				if ( empty( $field_id ) || ! in_array( $type, array( 'hidden', 'text', 'number', 'select', 'checkbox', 'radio', 'ext-radio', 'shortcode' ), true ) ) {
					continue;
				}

				$label         = isset( $field['label'] ) ? $field['label'] : '';
				$desc          = isset( $field['desc'] ) ? $field['desc'] : '';
				$type          = isset( $field['type'] ) ? $field['type'] : 'text';
				$placeholder   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
				$options       = isset( $field['options'] ) ? $field['options'] : array();
				$default_value = isset( $field['default'] ) ? $field['default'] : '';
				$saved_value   = get_post_meta( $flare_id, $field_id, true );
				$saved_value   = ! empty( $saved_value ) ? $saved_value : $default_value;

				$conditional_field = isset( $field['conditional'] ) ? $field['conditional']['field'] : false;
				$conditional_value = isset( $field['conditional'] ) ? $field['conditional']['value'] : false;
				?>
					<div class="option-group <?php echo esc_attr( $field_id ); ?>" <?php echo esc_attr( $this->add_conditional_field_attr( 'field', $conditional_field ) ); ?> <?php echo esc_attr( $this->add_conditional_field_attr( 'value', $conditional_value ) ); ?>>
						<h3 class="option-group__title"><?php echo esc_html( $label ); ?></h3>
						<div class="option-group__inputs <?php echo esc_attr( $type ); ?>-input">
							<?php
							switch ( $type ) {
								case 'text':
								case 'number':
								case 'email':
									?>
									<div>
										<input
												type="<?php echo esc_attr( $type ); ?>"
												name="<?php echo esc_attr( $field_id ); ?>"
												id="<?php echo esc_attr( $field_id ); ?>"
												value="<?php echo esc_attr( $saved_value ); ?>"
												placeholder="<?php echo esc_attr( $placeholder ); ?>"
										/>
									</div>
									<?php
									break;
								case 'select':
									?>
									<select name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>">
										<?php
										foreach ( $options as $key => $label ) {
											?>
											<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $saved_value ); ?>>
												<?php echo esc_html( $label ); ?>
											</option>
											<?php
										}
										?>
									</select>
									<?php
									break;
								case 'checkbox':
									$toggle_label = isset( $field['toggle_label'] ) ? $field['toggle_label'] : '';
									$saved_value  = get_post_meta( $flare_id, $field_id, true );
									if ( '' === $saved_value ) {
										$saved_value = $default_value;
									}
									?>
									<label class="switch" for="<?php echo esc_attr( $field_id ); ?>">
										<input
												type="checkbox"
												name="<?php echo esc_attr( $field_id ); ?>"
												id="<?php echo esc_attr( $field_id ); ?>"
												value="1"
												<?php checked( $saved_value, 1 ); ?>
										>
										<span class="slider round"></span>
										<span class="input-label"><?php echo ! empty( $toggle_label ) ? esc_html( $toggle_label ) : esc_html__( 'Show', 'flareo' ); ?></span>
									</label>
									<?php
									break;
								case 'radio':
									?>
									<ul class="<?php echo esc_attr( $type ); ?>">
										<?php
										foreach ( $options as $key => $label ) {
											?>
											<li class="option-customized-radio">
												<label for="<?php echo esc_attr( $key ); ?>">
													<input
															name="<?php echo esc_attr( $field_id ); ?>"
															value="<?php echo esc_attr( $key ); ?>"
															type="radio"
															id="<?php echo esc_attr( $key ); ?>"
														<?php checked( $key, $saved_value ); ?>
													/>
													<h4><?php echo esc_html( $label ); ?></h4>
													</label>
											</li>
											<?php
										}
										?>
									</ul>
									<?php
									break;
								case 'ext-radio':
									?>
									<ul class="<?php echo esc_attr( $type ); ?>">
										<?php
										foreach ( $options as $key => $data ) {
											?>
											<li class="option-customized-radio">
												<label for="<?php echo esc_attr( $key ); ?>">
													<input
															name="<?php echo esc_attr( $field_id ); ?>"
															value="<?php echo esc_attr( $key ); ?>"
															type="radio"
															id="<?php echo esc_attr( $key ); ?>"
														<?php checked( $key, $saved_value ); ?>
													/>
													<img src="<?php echo esc_url( $data['img_url'] ); ?>" alt="<?php echo esc_attr( $data['name'] ); ?>" class="image" />
													<h4><?php echo esc_html( $data['name'] ); ?></h4>
													</label>
													<?php if ( isset( $data['needs_config'] ) && $data['needs_config'] ) : ?>
														<a href="<?php echo esc_url( $data['config_url'] ); ?>" >Configure</a>
													<?php endif; ?>
											</li>
											<?php
										}
										?>
									</ul>
									<?php
									break;
								case 'shortcode':
									?>
									<div>
											<input
													type="text"
													name="<?php echo esc_attr( $field_id ); ?>"
													id="<?php echo esc_attr( $field_id ); ?>"
													value="<?php echo esc_attr( $saved_value ); ?>"
													placeholder="<?php echo esc_attr( $placeholder ); ?>"
													readonly
											/>
											<button type="button" class="p21-flareo-button p21-flareo-button-secondary p21-flareo-copy-target button button-secondary" data-target="#<?php echo esc_attr( $field_id ); ?>"><span class="p21-flareo-default-icon"><svg class="p21-flareo-icon p21-flareo-icon-copy" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" id=""><path d="M10.8125 1.125H3.3125C2.625 1.125 2.0625 1.6875 2.0625 2.375V11.125H3.3125V2.375H10.8125V1.125ZM12.6875 3.625H5.8125C5.125 3.625 4.5625 4.1875 4.5625 4.875V13.625C4.5625 14.3125 5.125 14.875 5.8125 14.875H12.6875C13.375 14.875 13.9375 14.3125 13.9375 13.625V4.875C13.9375 4.1875 13.375 3.625 12.6875 3.625ZM12.6875 13.625H5.8125V4.875H12.6875V13.625Z" fill="currentColor"></path></svg></span><span class="p21-flareo-success-icon"><svg class="p21-flareo-icon p21-flareo-icon-check" width="14" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg" id=""><path d="M5.8002 10.9L1.6002 6.70005L0.200195 8.10005L5.8002 13.7L17.8002 1.70005L16.4002 0.300049L5.8002 10.9Z" fill="currentColor"></path></svg></span><?php esc_html_e( 'Copy', 'flareo' ); ?></button>
									</div>
									<?php
									break;
							}
							?>
							<p><?php echo wp_kses_post( $desc ); ?></p>
						</div>
					</div>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Adds the meta box UI for the flare post type.
	 *
	 * @return void
	 */
	public function add_meta_box_ui() {
		// Get global $post.
		global $post;
		if ( ! $post instanceof \WP_Post ) {
			return;
		}

		// Preview section.
		add_meta_box(
			'p21-flareo-flare-preview-meta-box-ui',
			esc_attr__( 'Flare Preview', 'flareo' ),
			function ( $flare ) {
				ob_start();
				?>
				<div id="p21-flareo-flare-preview-ui" class="p21-flareo-flare p21-flareo-flare-preview">
					<p><?php esc_html_e( 'Press the button to see a preview of the flare based on the selected options and styles.', 'flareo' ); ?></p>
					<button type="button" class="button button-primary" id="p21-flareo-flare-preview-button"><?php esc_html_e( 'Preview Flare', 'flareo' ); ?></button>
				</div>
				<?php
				// HTML is included. Ignoring!
				echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			},
			Flare_Post_Type::CPT_KEY,
			'p21_flareo_flare',
			'high'
		);

		add_meta_box(
			'p21-flareo-flare-legendary-meta-box-ui',
			esc_attr__( 'Flare Options', 'flareo' ),
			function ( $flare ) {
				$all_fields = Flare_Post_Fields::get_all();

				ob_start();
				wp_nonce_field( 'p21-flareo-flare', 'p21_flareo_flare_nonce' );
				?>
				<div id="p21-flareo-flare-legendary-ui" class="p21-flareo-flare p21-flareo-flare-legendary">
					<?php
					foreach ( $all_fields as $section ) {
						?>
						<div class="card">
							<div class="card-header">
								<h2 class="card-header__title">
									<span class="card-header__icon">
										<?php echo Flare_Post_Fields::get_section_icon( $section['key'] ); // phpcs:ignore ?>
									</span>
									<?php echo esc_html( $section['title'] ); ?></h2>
									<span class="card-header__toggle-indicator">
										<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
											<path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
										</svg>
									</span>
							</div>
							<div class="card-body">
								<?php
								if ( isset( $section['tabs'] ) && is_array( $section['tabs'] ) ) {
									$default_tab = $section['default_tab'] ?? false;
									?>
									<div class="p21-flareo-tabs" role="tablist-wrapper">
										<div class="p21-flareo-tabs-nav" role="tablist" aria-label="Data options tabs">
											<?php foreach ( $section['tabs'] as $tab ) : ?>
												<button type="button" class="p21-flareo-tab" role="tab" aria-selected="<?php echo $default_tab === $tab['id'] ? 'true' : 'false'; ?>" aria-controls="p21-flareo-tab-panel-<?php echo esc_attr( $tab['id'] ); ?>" id="p21-flareo-tab-<?php echo esc_attr( $tab['id'] ); ?>">
													<?php echo esc_html( $tab['label'] ); ?>
												</button>
											<?php endforeach; ?>
										</div>
										<?php foreach ( $section['tabs'] as $tab ) : ?>
											<div id="p21-flareo-tab-panel-<?php echo esc_attr( $tab['id'] ); ?>" class="p21-flareo-tabs-panel" role="tabpanel" aria-labelledby="p21-flareo-tab-<?php echo esc_attr( $tab['id'] ); ?>">
												<?php
												if ( isset( $tab['fields'] ) && is_array( $tab['fields'] ) ) {
													$this->add_fields( $flare->ID, $tab['fields'] );
												}
												?>
											</div>
										<?php endforeach; ?>
									</div>
									<?php
								}

								if ( ! empty( $section['fields'] ) && is_array( $section['fields'] ) ) {
									$this->add_fields( $flare->ID, $section['fields'] );
								}
								?>
							</div>
						</div>
						<?php
					}
					?>
				</div>
				<?php
				// HTML is included. Ignoring!
				echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			},
			Flare_Post_Type::CPT_KEY,
			'p21_flareo_flare',
			'high'
		);

		add_action(
			'edit_form_after_title',
			function () {
				global $post, $wp_meta_boxes;
				do_meta_boxes( get_current_screen(), 'p21_flareo_flare', $post );
				unset( $wp_meta_boxes[ get_post_type( $post ) ]['p21_flareo_flare'] );
			}
		);

		add_meta_box(
			'p21-flareo-flare-publish',
			esc_attr__( 'Status', 'flareo' ),
			function ( $flare ) {
				$is_active = get_post_meta( $flare->ID, 'p21_flareo_flare_active', true );
				ob_start();
				?>
				<div id="p21-flareo-flare-publish-metabox" class="p21-flareo-flare p21-flareo-flare-publish-metabox">
					<div class="metabox-footer">
						<div class="metabox-footer__left">
							<label class="switch" for="p21_flareo_flare_active">
								<input
										type="checkbox"
										name="p21_flareo_flare_active"
										id="p21_flareo_flare_active"
										value="1"
										<?php checked( $is_active, 1 ); ?>
								>
								<span class="slider round"></span>
								<span class="input-label"><?php esc_html_e( 'Active', 'flareo' ); ?></span>
							</label>
						</div>
					</div>

				</div>
				<?php
				// HTML is included ignoring.
				echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			},
			Flare_Post_Type::CPT_KEY,
			'side',
			'default'
		);

		add_meta_box(
			'p21-flareo-flare-page-builders',
			esc_attr__( 'Page Builders', 'flareo' ),
			function () {
				ob_start();
				?>
				<div id="p21-flareo-flare-page-builders-metabox" class="p21-flareo-flare p21-flareo-flare-page-builders-metabox">
					<p>We support all popular page builders and you will find our Flareo flare block or relevant widget available in all of them, namely:</p>
					<ul>
						<li><a href="#">Block Editor/Gutenberg</a></li>
						<li><a href="#">Elementor</a></li>
						<li><a href="#">Bricks</a></li>
						<li><a href="#">Beaver Builder</a></li>
						<li><a href="#">Divi Page Builder</a></li>
						<li><a href="#">WPBakery Page Builder aka Visual Composer</a></li>
						<li><a href="#">Oxygen Builder</a></li>
					</ul>
				</div>
				<?php
				// HTML is included ignoring.
				echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			},
			Flare_Post_Type::CPT_KEY,
			'side',
			'default'
		);
	}

	/**
	 * Save flare meta fields in post meta.
	 *
	 * @param int $flare_id Flare Post ID.
	 *
	 * @return void
	 */
	public function save_meta_boxes( $flare_id = 0 ) {
		if ( ! isset( $_POST['p21_flareo_flare_nonce'] ) ) {
			return;
		}

		check_admin_referer( 'p21-flareo-flare', 'p21_flareo_flare_nonce' );

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $flare_id ) ) {
			return;
		}

		if ( ! empty( $_POST ) && isset( $_POST['post_type'] ) && Flare_Post_Type::CPT_KEY === $_POST['post_type'] ) {

			// Get all default fields.
			$default_fields = $this->default_fields;

			$flare_meta_fields = array();

			foreach ( $default_fields as $default_field ) {
				$flare_meta_fields[] = $default_field;
			}

			// Get all registered flare post fields.
			$flare_post_fields = Flare_Post_Fields::get_all();

			// Grab all field IDs from the fields definition.
			foreach ( $flare_post_fields as $section ) {
				if ( isset( $section['fields'] ) && is_array( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						$flare_meta_fields[] = array(
							'id'                => $field['id'],
							'type'              => isset( $field['type'] ) ? $field['type'] : '',
							'sanitize_callback' => isset( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : null,
						);
					}
				} elseif ( isset( $section['tabs'] ) && is_array( $section['tabs'] ) ) {
					foreach ( $section['tabs'] as $tab ) {
						if ( isset( $tab['fields'] ) && is_array( $tab['fields'] ) ) {
							foreach ( $tab['fields'] as $field ) {
								$flare_meta_fields[] = array(
									'id'                => $field['id'],
									'type'              => isset( $field['type'] ) ? $field['type'] : '',
									'sanitize_callback' => isset( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : null,
								);
							}
						}
					}
				}
			}

			foreach ( $flare_meta_fields as $field ) {
				// For processing and sanitizing multiple data types.
				switch ( $field['type'] ) {
					case 'shortcode':
						continue 2;
					case 'checkbox':
						$field_value = isset( $_POST[ $field['id'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field['id'] ] ) ) : false;
						$field_value = Flare_Post_Utilities::sanitize_bool( $field_value );
						break;
					default:
						$field_value = isset( $_POST[ $field['id'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field['id'] ] ) ) : '';
						break;
				}

				if ( '' !== $field_value ) {
					update_post_meta( $flare_id, $field['id'], $field_value );
				}
			}
		}
	}

	/**
	 * Update Flare post status based on active state.
	 *
	 * @param int      $flare_id Flare ID.
	 * @param \WP_Post $post        Flare post object.
	 *
	 * @return void
	 */
	public function update_status( $flare_id, $post ) {
		if ( Flare_Post_Type::CPT_KEY !== (string) $post->post_type ) {
			return;
		}
		if ( 'publish' === (string) $post->post_status ) {
			return;
		}

		$is_active = get_post_meta( $flare_id, 'p21_flareo_flare_active', true );

		if ( intval( $is_active ) ) {
			$args = array(
				'ID'          => $flare_id,
				'post_status' => 'publish',
			);

			// Update the flare post.
			wp_update_post( $args );
		}
	}
}
