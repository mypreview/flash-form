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

namespace Flash_Form\Includes\Utils;

use const Flash_Form\PLUGIN as PLUGIN;
use Flash_Form\Includes\Send_Email as Send_Email;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'form_submit' ) ) :
	/**
	 * Determine the form submission result and act accordingly.
	 *
	 * @since     1.0.0
	 * @param     array      $attributes    The block attributes.
	 * @param     string     $content       Block’s HTML content/output.
	 * @param     null|array $form_data     Set of key/value pairs representing form fields and their values.
	 * @return    string
	 */
	function form_submit( array $attributes, string $content, ?array $form_data = null ): ?string {
		global $post;
		$return  = null;
		$form_id = $attributes['formId'] ?? '';

		// Reterive nonce value according to the form id.
		$nonce = get_nonce_key( $form_id );
		// Verifies the request to prevent processing unauthorized external requests.
		if ( wp_verify_nonce( filter_input( INPUT_POST, $nonce, FILTER_SANITIZE_SPECIAL_CHARS ), PLUGIN['nonce'] ) ) {
			$response                 = null;
			$raw_data                 = is_null( $form_data ) ? wp_unslash( $_POST ) : wp_unslash( $form_data );
			$data                     = stripslashes_from_strings_only( $raw_data );
			$referer                  = isset( $data['_wp_http_referer'] ) ? set_url_scheme( wp_guess_url() . $data['_wp_http_referer'] ) : '';
			$to                       = isset( $attributes['to'] ) ? explode( ',', $attributes['to'] ) : array( sanitize_email( get_option( 'admin_email' ) ) );
			$subject                  = isset( $attributes['subject'] ) ? $attributes['subject'] : sprintf( '[%s] %s', esc_html( get_bloginfo( 'name' ) ), wp_kses_post( get_the_title( $post ) ) );
			$custom_thankyou          = $attributes['customThankyou'] ?? '';
			$custom_thankyou_message  = $attributes['customThankyouMessage'] ?? '';
			$custom_thankyou_redirect = $attributes['customThankyouRedirect'] ?? '';

			/**
			 * Allow third-party resources to extend the block submission response.
			 */
			do_action( 'mypreview_flash_form_submit_response', $response, $attributes );

			// Make sure that the response is null and nothing before this
			// conditional statement is not meant to be displayed on the page.
			if ( is_null( $response ) ) {
				// Remove security specific key/values.
				$unset_fields = apply_filters( 'mypreview_flash_form_submit_unset_fields', array( '_wp_http_referer', 'form_id', 'timestamp', $nonce ), $attributes, $form_id );
				foreach ( $unset_fields as $unset_field ) {
					unset( $data[ $unset_field ] );
				}

				$dom = new \DOMDocument();
				$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED );
				$xpath         = new \DomXPath( $dom );
				$send_email    = new Send_Email();
				$prepare_email = $send_email->prepare( $to, $subject, $data, $xpath, $referer );
				$has_fired     = $send_email->fire( ...$prepare_email );

				/**
				 * Allow third-party resources to extend the block content.
				 */
				do_action( 'mypreview_flash_form_submit_after_send_email', $has_fired, $prepare_email, $attributes );

				// Determine the action when the form is being submitted.
				if ( 'redirect' === $custom_thankyou ) {
					wp_safe_redirect( $custom_thankyou_redirect, 302, 'WordPress' );
					exit();
				} elseif ( 'message' === $custom_thankyou ) {
					$response = wpautop( $custom_thankyou_message );
				} else {
					$response = $send_email->get_submit_summary( $data, $xpath, $referer );
				}
			}

			$return = sprintf( '<div class="contact-form-submission">%s</div>', $response );
		}

		return apply_filters( 'mypreview_flash_form_submit_response', $return );
	}
endif;

if ( ! function_exists( 'get_nonce_key' ) ) :
	/**
	 * Get the nonce key for the form.
	 *
	 * @since     1.0.0
	 * @param     string $form_id    The form’s client id.
	 * @return    string
	 */
	function get_nonce_key( ?string $form_id ): string {
		return '_wpnonce-' . $form_id;
	}
endif;

if ( ! function_exists( 'clean' ) ) :
	/**
	 * Filters value of a field or content and strips out disallowed HTML.
	 *
	 * @since     1.0.0
	 * @param     string $value    Content or value of the field.
	 * @return    string
	 */
	function clean( string $value ): string {
		$value = str_replace( array( '[', ']' ), array( '&#91;', '&#93;' ), $value );
		return nl2br( wp_kses( $value, array() ) );
	}
endif;
