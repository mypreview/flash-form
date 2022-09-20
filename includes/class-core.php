<?php
/**
 * The core plugin class.
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
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
use Flash_Form\Dashboard\Core as Dashboard;
use Flash_Form\Frontend\Core as Frontend;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'Core' ) ) :

	/**
	 * The file that defines the core plugin class.
	 */
	final class Core {

		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the plugin.
		 *
		 * @access    protected
		 * @var       object $loader    Maintains and registers all hooks for the plugin.
		 */
		protected $loader;

		/**
		 * Define the core functionality of the plugin.
		 *
		 * Set the plugin name and the plugin version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function __construct() {
			$this->load_loader();
			$this->set_locale();
			$this->dashboard_hooks();
			$this->frontend_hooks();
		}

		/**
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		private function load_loader(): void {
			$this->loader = new Loader();
		}

		/**
		 * Define the locale for this plugin for internationalization.
		 * Uses the `Locale` class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		private function set_locale(): void {
			$this->loader->add_action( 'plugins_loaded', __NAMESPACE__ . '\I18n', 'load_textdomain' );
		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the plugin.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		private function dashboard_hooks(): void {
			$plugin_dashboard = new Dashboard();

			$this->loader->add_action( 'init', $plugin_dashboard, 'register_block' );
			$this->loader->add_action( 'rest_api_init', $plugin_dashboard, 'rest_api' );
			$this->loader->add_filter( 'plugin_row_meta', $plugin_dashboard, 'add_meta_links', 10, 2 );
		}

		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the plugin.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		private function frontend_hooks(): void {
			$plugin_frontend = new Frontend();

			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_frontend, 'enqueue' );
			$this->loader->add_action( 'wp_ajax_mypreview_flash_form_submit', $plugin_frontend, 'ajax_submit' );
			$this->loader->add_action( 'wp_ajax_nopriv_mypreview_flash_form_submit', $plugin_frontend, 'ajax_submit' );
			$this->loader->add_filter( 'mypreview_flash_form_render_callback_after_fieldset', $plugin_frontend, 'nonce_layer', 10, 2 );
		}

		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function run(): void {
			$this->loader->run();
		}

		/**
		 * The reference to the class that orchestrates the hooks with the plugin.
		 *
		 * @since     1.0.0
		 * @return    object
		 */
		public function get_loader(): object {
			return $this->loader;
		}

	}
endif;
