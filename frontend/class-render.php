<?php
/**
 * Render block on the server.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.0.0
 *
 * @package       flash-form
 * @subpackage    flash-form/frontend
 */

namespace Flash_Form\Frontend;

use const Flash_Form\PLUGIN as PLUGIN;
use Flash_Form\Dashboard\Cache_Data as Cache_Data;
use Flash_Form\Dashboard\Send_Email as Send_Email;
use Flash_Form\Includes\Utils as Utils;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Render::class ) ) :

	/**
	 * The public-specific render class.
	 */
	class Render {

		/**
		 * Renders the block on server.
		 *
		 * @since     1.0.0
		 * @param     array  $attributes    The block attributes.
		 * @param     string $content       The block content.
		 * @return    string
		 */
		public static function callback( array $attributes = array(), string $content ): ?string {
			// Look for the form response, if there is any!
			$return = self::process( $attributes, $content );

			if ( ! empty( $return ) ) {
				return $return;
			}

			\libxml_use_internal_errors( true );
			$dom = new \DOMDocument();
			$dom->loadHTML( $content, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED );
			$xpath = new \DomXPath( $dom );
			$node  = $xpath->query( "//form[contains(@class, '" . PLUGIN['class_name'] . "__fieldset')]" );

			if ( $node && $node->length ) {
				$before_fieldset = \apply_filters( 'mypreview_flash_form_render_callback_before_fieldset', \__return_empty_string(), $attributes );
				$after_fieldset  = \apply_filters( 'mypreview_flash_form_render_callback_after_fieldset', \__return_empty_string(), $attributes );

				if ( ! empty( $before_fieldset ) ) {
					$before_fieldset_fragment = $dom->createDocumentFragment();
					$before_fieldset_fragment->appendXML( $before_fieldset );
					$node->item( 0 )->insertBefore( $before_fieldset_fragment, $node->item( 0 )->firstChild );
				}

				if ( ! empty( $after_fieldset ) ) {
					$after_fieldset_fragment = $dom->createDocumentFragment();
					$after_fieldset_fragment->appendXML( $after_fieldset );
					$node->item( 0 )->insertBefore( $after_fieldset_fragment );
				}

				\libxml_clear_errors();
				$content = \utf8_decode( $dom->saveHTML( $dom->documentElement ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}

			if ( $attributes['isAjax'] ?? false ) {
				new Cache_Data( $attributes, $content, $attributes['formId'] ?? '' );
			}

			/**
			 * Allow third-party resources to extend the block content.
			 */
			\do_action( 'mypreview_flash_form_render_callback_content', $attributes );

			return \apply_filters( 'mypreview_flash_form_render_callback_content', $content, $attributes );
		}

		/**
		 * Determine the form submission result and act accordingly.
		 *
		 * @since     1.0.0
		 * @param     array      $attributes    The block attributes.
		 * @param     string     $content       Block’s HTML content/output.
		 * @param     null|array $form_data     Set of key/value pairs representing form fields and their values.
		 * @return    string
		 */
		public static function process( array $attributes, string $content, ?array $form_data = null ): ?string {
			global $post;
			$return  = null;
			$form_id = $attributes['formId'] ?? '';

			// Reterive nonce value according to the form id.
			$nonce = Utils::get_nonce_key( $form_id );
			// Verifies the request to prevent processing unauthorized external requests.
			if ( \wp_verify_nonce( filter_input( INPUT_POST, $nonce, FILTER_SANITIZE_SPECIAL_CHARS ), PLUGIN['nonce'] ) ) {
				$response                 = null;
				$raw_data                 = is_null( $form_data ) ? \wp_unslash( $_POST ) : \wp_unslash( $form_data );
				$data                     = \stripslashes_from_strings_only( $raw_data );
				$referer                  = Utils::get_referer( $data['_wp_http_referer'] ?? '' );
				$to                       = isset( $attributes['to'] ) ? explode( ',', $attributes['to'] ) : array( \sanitize_email( \get_option( 'admin_email' ) ) );
				$subject                  = isset( $attributes['subject'] ) ? $attributes['subject'] : \sprintf( '[%s] %s', \esc_html( \get_bloginfo( 'name' ) ), \wp_kses_post( \get_the_title( $post ) ) );
				$custom_thankyou          = $attributes['customThankyou'] ?? '';
				$custom_thankyou_message  = $attributes['customThankyouMessage'] ?? '';
				$custom_thankyou_redirect = $attributes['customThankyouRedirect'] ?? '';

				/**
				 * Allow third-party resources to extend the block submission response.
				 */
				\do_action( 'mypreview_flash_form_submit_before_response', $response, $attributes, $data );
				$response = \apply_filters( 'mypreview_flash_form_submit_raw_response', $response, $referer );

				// Make sure that the response is null and nothing before this
				// conditional statement is not meant to be displayed on the page.
				if ( is_null( $response ) ) {
					// Remove security specific key/values.
					$unset_fields = \apply_filters( 'mypreview_flash_form_submit_unset_fields', array( '_wp_http_referer', 'action', 'form_id', 'timestamp', $nonce ), $attributes, $form_id );
					foreach ( $unset_fields as $unset_field ) {
						if ( isset( $data[ $unset_field ] ) ) {
							unset( $data[ $unset_field ] );
						}
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
					\do_action( 'mypreview_flash_form_submit_after_send_email', $has_fired, $prepare_email, $attributes );

					// Determine the action when the form is being submitted.
					if ( 'redirect' === $custom_thankyou ) {
						\wp_safe_redirect( $custom_thankyou_redirect, 302, 'WordPress' );
						exit();
					} elseif ( 'message' === $custom_thankyou ) {
						$response = \wpautop( $custom_thankyou_message );
					} else {
						$response = $send_email->get_submit_summary( $data, $xpath, $referer );
					}
				}

				$return = \sprintf( '<div class="contact-form-submission">%s</div>', $response );
			}

			return \apply_filters( 'mypreview_flash_form_submit_response', $return );
		}

	}
endif;
