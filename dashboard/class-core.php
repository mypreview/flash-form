<?php
/**
 * The admin-facing functionality of the plugin.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.0.0
 *
 * @package       flash-form
 * @subpackage    flash-form/dashboard
 */

namespace Flash_Form\Dashboard;

use const Flash_Form\PLUGIN as PLUGIN;
use Flash_Form\Includes\Utils as Utils;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Core::class ) ) :

	/**
	 * The main admin-facing class.
	 */
	class Core {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function __construct() {}

		/**
		 * Registers the block type from the metadata stored in the "block.json" file.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function register_block(): void {
			// Clean (erase) and start the output buffer.
			ob_clean();
			ob_start();

			register_block_type_from_metadata(
				PLUGIN['dir_path'],
				array(
					'render_callback' => 'Flash_Form\Frontend\Render::callback',
				)
			);
		}

	}
endif;
