<?php
/**
 * The public-facing functionality of the plugin.
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
use Flash_Form\Frontend\Honeypot as Honeypot;
use Flash_Form\Frontend\Render as Render;
use Flash_Form\Dashboard\Cache_Data as Cache_Data;
use Flash_Form\Includes\Utils as Utils;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Core::class ) ) :

	/**
	 * The main public-specific class.
	 */
	class Core {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function __construct() {
			new Honeypot();
		}

		/**
		 * Register the static resources for the public-facing side of the site.
		 * This function localizes the front-end registered script with a globally accessible variable.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function enqueue(): void {
			\wp_localize_script( \generate_block_asset_handle( PLUGIN['block_name'], 'script' ), 'mypreviewFlashFormLocalizedData', array( 'ajaxurl' => \admin_url( 'admin-ajax.php', 'relative' ) ) );

			/**
			 * Allow additional static resources to be enqueued in here.
			 */
			\do_action( 'mypreview_flash_form_enqueue_scripts' );
		}

		/**
		 * Request form the be submitted with information filled in via Ajax.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function ajax_submit(): void {
			$form_id = (string) \wp_unslash( $_REQUEST['form_id'] ?? '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$nonce   = Utils::get_nonce_key( $form_id );

			// Verifies the Ajax request to prevent processing requests external of this form.
			\check_ajax_referer( PLUGIN['nonce'], $nonce );

			$raw_data  = \stripslashes_from_strings_only( \wp_unslash( $_POST ) );
			$form_data = Cache_Data::get_data( $form_id );
			$response  = Render::process( $form_data['attributes'] ?? array(), $form_data['content'] ?? '', $raw_data );

			// Send a JSON response back to an Ajax request, indicating success.
			\wp_send_json_success( apply_filters( 'mypreview_flash_form_ajax_submit_response', $response ), 200 );
			\wp_die();
		}

		/**
		 * Appends a nonce field right before the closing form tag.
		 *
		 * The nonce field is used to validate that the contents of the form
		 * came from the location on the current site and not somewhere else.
		 * The nonce does not offer absolute protection, but should protect against most cases.
		 *
		 * @since     1.0.0
		 * @param     string $content       The block content.
		 * @param     array  $attributes    The block attributes.
		 * @return    string
		 */
		public function nonce_layer( string $content, array $attributes ): string {
			// Only print-out additional fields when the submission method set to be "POST".
			if ( 'post' === $attributes['method'] ?? 'post' && \apply_filters( 'mypreview_flash_form_post_method_nonce_layer', true ) ) {
				$form_id  = $attributes['formId'] ?? '';
				$nonce    = Utils::get_nonce_key( $form_id );
				$content .= \wp_nonce_field( PLUGIN['nonce'], $nonce, true, false );
				$content .= '<input type="hidden" name="form_id" value="' . \esc_html( $form_id ) . '" />';
				$content .= '<input type="hidden" name="timestamp" value="' . strtotime( 'now' ) . '" />';
			}

			return \apply_filters( 'mypreview_flash_form_nonce_layer', $content, $attributes );
		}

	}
endif;
