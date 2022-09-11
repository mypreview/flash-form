<?php
/**
 * Register all actions and filters for the plugin.
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.0.0
 *
 * @package       flash-form
 * @subpackage    flash-form/includes
 */

namespace Flash_Form\Includes;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Loader::class ) ) :

	/**
	 * The actions and filters loader class.
	 */
	class Loader {

		/**
		 * The array of actions registered with WordPress.
		 *
		 * @access    protected
		 * @var       array $actions    The actions registered with WordPress to fire when the plugin loads.
		 */
		protected $actions;

		/**
		 * The array of filters registered with WordPress.
		 *
		 * @access   protected
		 * @var      array $filters    The filters registered with WordPress to fire when the plugin loads.
		 */
		protected $filters;

		/**
		 * The array of shortcode registered with WordPress.
		 *
		 * @access    protected
		 * @var       array $shortcodes    The shortcode registered with WordPress to fire when the plugin loads.
		 */
		protected $shortcodes;

		/**
		 * Initialize the collections used to maintain the actions and filters.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function __construct() {
			$this->actions    = array();
			$this->filters    = array();
			$this->shortcodes = array();
		}

		/**
		 * Add a new action to the collection to be registered with WordPress.
		 *
		 * @since     1.0.0
		 * @param     string        $hook             The name of the WordPress action that is being registered.
		 * @param     string|object $component        A reference to the instance of the object on which the action is defined.
		 * @param     string        $callback         The name of the function definition on the $component.
		 * @param     int           $priority         Optional. The priority at which the function should be fired. Default is 10.
		 * @param     int           $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
		 * @return    void
		 */
		public function add_action( string $hook, $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
			$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
		}

		/**
		 * Add a new filter to the collection to be registered with WordPress.
		 *
		 * @since     1.0.0
		 * @param     string        $hook             The name of the WordPress filter that is being registered.
		 * @param     string|object $component        A reference to the instance of the object on which the filter is defined.
		 * @param     string        $callback         The name of the function definition on the $component.
		 * @param     int           $priority         Optional. The priority at which the function should be fired. Default is 10.
		 * @param     int           $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
		 * @return    void
		 */
		public function add_filter( string $hook, $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
			$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
		}

		/**
		 * Add a new shortcode to the collection to be registered with WordPress.
		 *
		 * @since     1.0.0
		 * @param     string        $tag              The name of the new shortcode.
		 * @param     string|object $component        A reference to the instance of the object on which the shortcode is defined.
		 * @param     string        $callback         The name of the function that defines the shortcode.
		 * @param     int           $priority         Optional. The priority at which the function should be fired. Default is 10.
		 * @param     int           $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
		 * @return    void
		 */
		public function add_shortcode( string $tag, $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
			$this->shortcodes = $this->add( $this->shortcodes, $tag, $component, $callback, $priority, $accepted_args );
		}

		/**
		 * A utility function that is used to register the actions and hooks into a single collection.
		 *
		 * @since     1.0.0
		 * @param     array         $hooks            The collection of hooks that is being registered (that is, actions or filters).
		 * @param     string        $hook             The name of the WordPress filter that is being registered.
		 * @param     string|object $component        A reference to the instance of the object on which the filter is defined.
		 * @param     string        $callback         The name of the function definition on the $component.
		 * @param     int           $priority         The priority at which the function should be fired.
		 * @param     int           $accepted_args    The number of arguments that should be passed to the $callback.
		 * @return    array
		 */
		private function add( array $hooks, string $hook, $component, string $callback, int $priority, int $accepted_args ): array {
			$hooks[] = array(
				'hook'          => $hook,
				'component'     => $component,
				'callback'      => $callback,
				'priority'      => $priority,
				'accepted_args' => $accepted_args,
			);

			return $hooks;
		}

		/**
		 * Register the filters and actions with WordPress.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function run(): void {
			foreach ( $this->filters as $hook ) {
				\add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
			}

			foreach ( $this->actions as $hook ) {
				\add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
			}

			foreach ( $this->shortcodes as $hook ) {
				\add_shortcode( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
			}
		}

	}
endif;
