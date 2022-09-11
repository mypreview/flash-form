<?php
/**
 * Helper functions.
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

if ( ! class_exists( 'Utils' ) ) :

	/**
	 * The main helper-specific class.
	 */
	class Utils {

		/**
		 * Get the nonce key for the form.
		 *
		 * @since     1.0.0
		 * @param     null|string $form_id    The form’s client id.
		 * @return    string
		 */
		public static function get_nonce_key( ?string $form_id ): string {
			return '_wpnonce-' . $form_id ?? '';
		}

		/**
		 * Retrieve referer from ‘_wp_http_referer’ or HTTP referer.
		 *
		 * @since     1.0.0
		 * @param     null|string $referer    HTTP referer.
		 * @return    string
		 */
		public static function get_referer( ?string $referer ): string {
			return isset( $referer ) && ! empty( $referer ) ? \set_url_scheme( \wp_guess_url() . $referer ) : '';
		}

		/**
		 * Filters value of a field or content and strips out disallowed HTML.
		 *
		 * @since     1.0.0
		 * @param     string $value    Content or value of the field.
		 * @return    string
		 */
		public static function clean( string $value ): string {
			$value = str_replace( array( '[', ']' ), array( '&#91;', '&#93;' ), $value );
			return nl2br( \wp_kses( $value, array() ) );
		}

	}
endif;
