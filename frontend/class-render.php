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
		 * @param     array     $attributes    The block attributes.
		 * @param     string    $content       The block content.
		 * @param     \WP_Block $block         Block object.
		 * @return    string
		 */
		public static function callback( array $attributes, string $content, \WP_Block $block ): ?string {
			// Add private (internal) attributes.
			$post_id               = $block->context['postId'] ?? Utils::get_post_id();
			$attributes['_hash']   = \sha1( \wp_json_encode( $attributes ) . $content );
			$attributes['_postId'] = Utils::get_localized_post_id( $post_id );

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
			\do_action( 'mypreview_flash_form_render_callback_before_content', $attributes );

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
			$return  = null;
			$form_id = $attributes['formId'] ?? '';

			// Reterive nonce value according to the form id.
			$nonce = Utils::get_nonce_key( $form_id );
			// Verifies the request to prevent processing unauthorized external requests.
			if ( \wp_verify_nonce( filter_input( INPUT_POST, $nonce, FILTER_SANITIZE_SPECIAL_CHARS ), PLUGIN['nonce'] ) ) {
				$response = null;
				$raw_data = is_null( $form_data ) ? \wp_unslash( $_POST ) : \wp_unslash( $form_data );
				$data     = \stripslashes_from_strings_only( $raw_data );
				$referer  = Utils::get_referer( $data['_wp_http_referer'] ?? '' );

				/**
				 * Allow third-party resources to extend the block submission response.
				 */
				\do_action( 'mypreview_flash_form_submit_before_response', $response, $attributes, $data );
				$response = \apply_filters( 'mypreview_flash_form_submit_raw_response', $response, $referer );

				// Make sure that the response is null and nothing before this
				// conditional statement is not meant to be displayed on the page.
				// // // // // // // // // // // // // // // // // // // // // // /
				// Avoid timing attack with ensuring safe string (hash) comparison.
				if ( \is_null( $response ) && \hash_equals( $attributes['_hash'] ?? '', \wp_unslash( $data['hash'] ?? '' ) ) ) {
					$dom = new \DOMDocument();
					$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED );
					$xpath         = new \DomXPath( $dom );
					$post_id       = isset( $data['post_id'] ) && Utils::is_post_exists( (int) $data['post_id'] ) ? (int) $data['post_id'] : 0;
					$send_email    = new Send_Email();
					$prepare_email = array();
					$has_fired     = false;

					// Remove security specific key/values.
					$unset_fields = \apply_filters( 'mypreview_flash_form_submit_unset_fields', array( '_wp_http_referer', 'action', 'form_id', 'post_id', 'hash', 'timestamp', $nonce ), $attributes, $form_id );
					foreach ( $unset_fields as $unset_field ) {
						if ( isset( $data[ $unset_field ] ) ) {
							unset( $data[ $unset_field ] );
						}
					}

					// Filter to choose whether an email should be sent after each successful form submission.
					if ( \apply_filters( 'mypreview_flash_form_should_send_email', true, $form_id, $post_id ) ) {
						$to            = isset( $attributes['to'] ) ? explode( ',', $attributes['to'] ) : array( \sanitize_email( \get_option( 'admin_email' ) ) );
						$subject       = isset( $attributes['subject'] ) ? $attributes['subject'] : \sprintf( '[%s] %s', \esc_html( \get_bloginfo( 'name' ) ), \wp_kses_post( \get_the_title( $post ) ) );
						$prepare_email = $send_email->prepare( $to, $subject, $data, $xpath, $referer, $post_id );
						$has_fired     = $send_email->fire( ...$prepare_email );
					}

					/**
					 * Allow third-party resources to extend the block content.
					 */
					\do_action( 'mypreview_flash_form_submit_after_send_email', $has_fired, $prepare_email, $attributes );

					// Determine what should happen when form submitted.
					$custom_thankyou          = $attributes['customThankyou'] ?? '';
					$custom_thankyou_message  = $attributes['customThankyouMessage'] ?? '';
					$custom_thankyou_redirect = $attributes['customThankyouRedirect'] ?? '';

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

				$return = \sprintf( '<div class="%s__submission">%s</div>', sanitize_html_class( PLUGIN['class_name'] ), $response );
			}

			return \apply_filters( 'mypreview_flash_form_submit_response', $return );
		}

	}
endif;
