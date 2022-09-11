<?php
/**
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.0.0
 *
 * @package       flash-form
 * @subpackage    flash-form/includes
 */

namespace Flash_Form\Includes;

use const Flash_Form\PLUGIN as PLUGIN;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( I18n::class ) ) :

	/**
	 * Define the internationalization functionality.
	 */
	class I18n {

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public static function load_textdomain(): void {
			\load_plugin_textdomain( PLUGIN['slug'], false, dirname( PLUGIN['basename'] ) . '/languages/' );
		}
	}
endif;
