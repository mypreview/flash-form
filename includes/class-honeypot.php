<?php
/**
 * Basic implementation of a honeypot trap for the Form block.
 *
 * A honeypot trap is a kind of spam prevention technology designed
 * to trick spambots into revealing themselves.
 *
 * Once a bot falls into your trap, the form prevents any information
 * populated by the bot to be submitted.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.0.0
 *
 * @package       flash-form
 * @subpackage    flash-form/includes
 */

namespace Flash_Form\Includes;

use function Flash_Form\Includes\Utils\get_referer as get_referer;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'Honeypot' ) ) :

	/**
	 * The cache form data class.
	 */
	class Honeypot {

		/**
		 * Define the core functionality of the plugin.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function __construct() {
			add_filter( 'mypreview_flash_form_nonce_layer', array( $this, 'time_check' ), 10, 2 );
			add_filter( 'mypreview_flash_form_nonce_layer', array( $this, 'inline_css' ), 20, 2 );
			add_filter( 'mypreview_flash_form_submit_unset_fields', array( $this, 'unset' ), 10, 3 );
			add_action( 'mypreview_flash_form_submit_before_response', array( $this, 'validate' ), 10, 3 );
		}

		/**
		 * Time check hidden input field.
		 *
		 * @since     1.0.0
		 * @param     string $content       The block content.
		 * @param     array  $attributes    The block attributes.
		 * @return    string
		 */
		public function time_check( string $content, array $attributes ) {
			$return     = $content;
			$honeypot   = $attributes['honeypot'] ?? array();
			$time_check = $honeypot['timeCheck'] ?? false;

			if ( self::is_enabled( $honeypot ) && $time_check && is_numeric( $time_check ) ) {
				$time_check_field = '<input type="hidden" name="time_check" value="' . $time_check . '" />';
				$return          .= $time_check_field;
			}

			return $return;
		}

		/**
		 * Hides the honeypot trap field and label from the view.
		 *
		 * @since     1.0.0
		 * @param     string $content       The block content.
		 * @param     array  $attributes    The block attributes.
		 * @return    string
		 */
		public function inline_css( string $content, array $attributes ): string {
			$return        = $content;
			$honeypot      = $attributes['honeypot'] ?? array();
			$is_inline_css = $honeypot['moveInlineCSS'] ?? false;

			if ( self::is_enabled( $honeypot ) && $is_inline_css ) {
				$form_id = $attributes['formId'] ?? '';
				$style   = '<style>[id="' . $form_id . '"] .form-field--hp{display:none;visibility:hidden;}</style>';
				$return .= $style;
			}

			return $return;
		}

		/**
		 * Unset time-check field id in the process of form submission.
		 *
		 * @since     1.0.0
		 * @param     array  $fields        Form field ids.
		 * @param     array  $attributes    The block attributes.
		 * @param     string $form_id       Unique form block id.
		 * @return    array
		 */
		public function unset( array $fields, array $attributes, string $form_id ): array {
			$honeypot   = $attributes['honeypot'] ?? array();

			if ( self::is_enabled( $honeypot ) ) {
				$fields[]   = 'hp-' . $form_id;
				$time_check = $honeypot['timeCheck'] ?? false;

				if ( $time_check && is_numeric( $time_check ) ) {
					$fields[] = 'time_check';
				}
			}

			return $fields;
		}

		/**
		 * Validate honeypot trap being empty during the form submission.
		 *
		 * @since     1.0.0
		 * @param     null|string $response      Form submission response.
		 * @param     array       $attributes    The block attributes.
		 * @param     array       $data          Set of key/value pairs representing form fields and their values.
		 * @return    void
		 */
		public function validate( ?string $response, $attributes, $data ): void {
			$honeypot = $attributes['honeypot'] ?? '';

			// Ensure honeypot is enabled.
			if ( self::is_enabled( $honeypot ) ) {
				$form_id    = $attributes['formId'] ?? '';
				$timestamp  = $data['timestamp'] ?? '';
				$value      = $data[ 'hp-' . $form_id ] ?? '';
				$time_check = $data['time_check'] ?? '';

				if ( ! empty( $value ) || ( ! empty( $time_check ) && strtotime( '+' . strval( $time_check ) . ' seconds', $timestamp ) > strtotime( 'now' ) ) ) {
					add_filter( 'mypreview_flash_form_submit_raw_response', array( $this, 'get_failed_summary' ), 99, 2 );
				}
			}
		}

		/**
		 * Outputs friendly message outlining that honeypot trap prevented form submission.
		 *
		 * @since     1.0.0
		 * @param     null|string $response   Form submission response.
		 * @param     string      $referer    Referer from the "_wp_http_referer".
		 * @return    string
		 */
		public function get_failed_summary( ?string $response, $referer ): string {
			// Form failed submission heading along with a link to (go back).
			/* translators: 1: Open heading tag, 2: Open anchor tag, 3: Close anchor and heading tags. */
			$heading  = apply_filters( 'mypreview_flash_form_email_submit_heading', sprintf( esc_html__( '%1$sSomething is stuck in the honey! %2$s(go back)%3$s', 'flash-form' ), '<h3>', sprintf( '<a href="%s">', esc_url( $referer ) ), '</a></h3>' ) );
			$message  = __( 'You filled out a form field that was created to stop spammers. Please go back and try again or contact the site administrator if you feel this was an error.', 'flash-form' );
			$response = sprintf( '%s<blockquote><p>%s</p></blockquote>', $heading, $message );

			return apply_filters( 'mypreview_flash_form_email_submit_summary', $response );
		}

		/**
		 * Whether the honeypot trap is enabled.
		 *
		 * @since     1.0.0
		 * @param     array $honeypot    Attributes associated with "Honeypot".
		 * @return    bool
		 */
		public static function is_enabled( array $honeypot ): bool {
			return is_array( $honeypot ) && isset( $honeypot['enable'] ) && true === $honeypot['enable'];
		}
	}
endif;

return new Honeypot();
