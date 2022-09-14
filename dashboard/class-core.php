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

		/**
		 * Display additional links in plugins table page.
		 * Filters the list of action links displayed for a specific plugin in the Plugins list table.
		 *
		 * @since     1.0.0
		 * @param     array $links    Plugin table/item action links.
		 * @return    array
		 */
		public function add_action_links( array $links ): array {
			$plugin_links = array();
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			$plugin_links[] = sprintf( _x( '%1$sHire Me!%2$s', 'plugin link', 'flash-form' ), sprintf( '<a href="%s" class="button-link-delete" target="_blank" rel="noopener noreferrer nofollow" title="%s">', esc_url( PLUGIN['author_uri'] ), esc_attr_x( 'Looking for help? Hire Me!', 'flash-form' ) ), '</a>' );

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Add additional helpful links to the plugin’s metadata.
		 *
		 * @since     1.0.0
		 * @param     array  $links    An array of the plugin’s metadata.
		 * @param     string $file     Path to the plugin file relative to the plugins directory.
		 * @return    array
		 */
		public function add_meta_links( array $links, string $file ): array {
			if ( PLUGIN['basename'] !== $file ) {
				return $links;
			}

			$plugin_links = array();
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			$plugin_links[] = \sprintf( _x( '%1$sCommunity support%2$s', 'plugin link', 'flash-form' ), \sprintf( '<a href="https://wordpress.org/support/plugin/%s" target="_blank" rel="noopener noreferrer nofollow">', PLUGIN['slug'] ), '</a>' );
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			$plugin_links[] = \sprintf( _x( '%1$sDonate%2$s', 'plugin link', 'flash-form' ), \sprintf( '<a href="https://www.buymeacoffee.com/mahdiyazdani" class="button-link-delete" target="_blank" rel="noopener noreferrer nofollow" title="%s">☕ ', \esc_attr__( 'Donate to support this plugin', 'flash-form' ) ), '</a>' );

			return array_merge( $links, $plugin_links );
		}

	}
endif;
