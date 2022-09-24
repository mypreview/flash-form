<?php
/**
 * This class extends custom controller to use instead of "WP_REST_Posts_Controller".
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.1.0
 *
 * @package       flash-form
 * @subpackage    flash-form/dashboard
 */

namespace Flash_Form\Dashboard;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Entries_Endpoint::class ) && class_exists( 'WP_REST_Posts_Controller' ) ) :

	/**
	 * The submission entries API class.
	 */
	class Entries_Endpoint extends \WP_REST_Posts_Controller {

		/**
		 * Check whether a given request has proper authorization to view form submission entries.
		 *
		 * @since     1.0.0
		 * @param     \WP_REST_Request $request    Full details about the request.
		 * @return    \WP_Error|boolean
		 */
		public function get_items_permissions_check( \WP_REST_Request $request ) {
			if ( ! \is_user_member_of_blog( \get_current_user_id(), \get_current_blog_id() ) ) {
				return new \WP_Error(
					'rest_cannot_view',
					\esc_html__( 'Sorry, you cannot view this resource.', 'flash-form' ),
					array( 'status' => 401 )
				);
			}

			return true;
		}

		/**
		 * Check whether a given request has proper authorization to view form submission entry.
		 *
		 * @since     1.0.0
		 * @param     \WP_REST_Request $request    Full details about the request.
		 * @return    \WP_Error|boolean
		 */
		public function get_item_permissions_check( \WP_REST_Request $request ) {
			if ( ! \is_user_member_of_blog( \get_current_user_id(), \get_current_blog_id() ) ) {
				return new \WP_Error(
					'rest_cannot_view',
					\esc_html__( 'Sorry, you cannot view this resource.', 'flash-form' ),
					array( 'status' => 401 )
				);
			}

			return true;
		}

	}
endif;
