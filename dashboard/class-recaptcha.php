<?php
/**
 * Basic integration for retriving and storing Google reCaptcha V2 keys.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.1.0
 *
 * @package       flash-form
 * @subpackage    flash-form/dashboard
 */

namespace Flash_Form\Dashboard;

use const Flash_Form\PLUGIN as PLUGIN;
use Flash_Form\Includes\Utils as Utils;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( ReCaptcha::class ) ) :

	/**
	 * The Google reCAPTCHA V2 integration API class.
	 */
	class ReCaptcha {

		/**
		 * Name of the option.
		 *
		 * @since    1.1.0
		 * @var      string    $option
		 */
		public static $option = 'mypreview_flash_form_recaptcha';

		/**
		 * The "permission_callback" argument.
		 *
		 * @since    1.1.0
		 * @var      string    $permission
		 */
		private static $permission = 'Flash_Form\Includes\Utils::rest_editor_permission_callback';

		/**
		 * Initialize the class.
		 *
		 * @since     1.1.0
		 * @return    void
		 */
		public function __construct() {
			$this->register_routes();
		}

		/**
		 * Registers custom REST API routes
		 * to store and retrieve site keys.
		 *
		 * @since     1.1.0
		 * @return    void
		 */
		public function register_routes(): void {
			\register_rest_route(
				PLUGIN['rest_namespace'],
				'/recaptcha',
				array(
					array(
						'methods'             => \WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get' ),
						'permission_callback' => self::$permission,
					),
					array(
						'methods'             => \WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'set' ),
						'permission_callback' => self::$permission,
					),
				)
			);
		}

		/**
		 * Implement a REST response object with site keys.
		 *
		 * @since     1.1.0
		 * @return    \WP_REST_Response
		 */
		public function get(): \WP_REST_Response {
			$keys     = (array) get_option( self::$option, array() );
			$response = new \WP_REST_Response( Utils::clean_array( $keys ) );
			$response->set_status( 200 );

			return \rest_ensure_response( $response );
		}

		/**
		 * Updates the value of the reCaptcha keys that was already added.
		 *
		 * @since     1.1.0
		 * @param     \WP_REST_Request $request    REST request object.
		 * @return    \WP_REST_Response
		 */
		public function set( \WP_REST_Request $request ): \WP_REST_Response {
			$is_updated = (bool) update_option( self::$option, Utils::clean_array( $request->get_json_params() ), false );
			$response   = new \WP_REST_Response( $is_updated );
			$response->set_status( 200 );

			return \rest_ensure_response( $response );
		}
	}
endif;
