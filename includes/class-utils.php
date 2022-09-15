<?php
/**
 * Helper functions.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.1.0
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
		 * Retrieves the current site ISO language code.
		 *
		 * @since     1.1.0
		 * @return    string
		 */
		public static function get_language_code(): string {
			$get_locale = explode( '_', get_locale() );

			return $get_locale[0] ?? 'en';
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

		/**
		 * Recursive sanitation for an array.
		 * Returns the sanitized values of an array.
		 *
		 * @since    1.1.0
		 * @param    array $input    Array of values.
		 * @return   array
		 */
		public static function clean_array( array $input ): array {
			// Bail early, in case the input value is missing or not an array.
			if ( empty( $input ) || ! is_array( $input ) ) {
				return array();
			}

			// Loop through the array to sanitize each key/values recursively.
			foreach ( $input as $key => &$value ) {
				if ( is_array( $value ) ) {
					$value = self::clean_array( $value );
				} else {
					$value = sanitize_text_field( $value );
				}
			}

			return $input;
		}

		/**
		 * Returns whether the current user has the specified capability.
		 *
		 * @since     1.0.0
		 * @return    bool
		 */
		public static function rest_editor_permission_callback(): bool {
			return \current_user_can( 'edit_posts' );
		}

	}
endif;
