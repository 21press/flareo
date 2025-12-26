<?php
/**
 * Handles core registration.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Core;

use P21\Flareo\Core\PostType\Flare_Post_Type;
use P21\Flareo\Core\PostType\Flare_Post_Metabox;
use P21\Flareo\Core\PostType\Flare_Post_Utilities;
use P21\Flareo\Utils\Has_Instance;

/**
 * Class Core_Registrar
 */
class Core_Registrar {
	use Has_Instance;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Registers and manages Flare post type instance.
		Flare_Post_Type::get_instance();

		// Registers and manages Flare CPT metaboxes.
		Flare_Post_Metabox::get_instance();

		// Registers and manages Flare CPT utilities.
		Flare_Post_Utilities::get_instance();
	}
}
