<?php
/**
 * Core functions.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.0.0
 *
 * @package       flash-form
 * @subpackage    flash-form/includes
 */

namespace Flash_Form\Includes\Core;

use const Flash_Form\PLUGIN as PLUGIN;
use function Flash_Form\Includes\Utils\form_submit as form_submit;
use function Flash_Form\Includes\Utils\get_nonce_key as get_nonce_key;
use Flash_Form\Includes\Cache_Data as Cache_Data;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * This function localizes the front-end registered script with a globally accessible variable.
 *
 * @since     1.0.0
 * @return    void
 */
function localized_data(): void {
	wp_localize_script( generate_block_asset_handle( PLUGIN['block_name'], 'script' ), 'mypreviewFlashFormLocalizedData', array( 'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ) ) );

	/**
	 * Allow additional static resources to be enqueued in here.
	 */
	do_action( 'mypreview_flash_form_enqueue_scripts' );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\localized_data' );

/**
 * Request form the be submitted with information filled in via Ajax.
 *
 * @since     1.0.0
 * @return    void
 */
function form_submit_ajax(): void {
	$form_id = (string) wp_unslash( $_REQUEST['form_id'] ?? '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$nonce   = get_nonce_key( $form_id );
	// Verifies the Ajax request to prevent processing requests external of this form.
	check_ajax_referer( PLUGIN['nonce'], $nonce );

	$raw_data  = stripslashes_from_strings_only( wp_unslash( $_POST ) );
	$form_data = Cache_Data::get_data( $form_id );
	$response  = form_submit( $form_data['attributes'] ?? array(), $form_data['content'] ?? '', $raw_data );

	// Send a JSON response back to an Ajax request, indicating success.
	wp_send_json_success( apply_filters( 'mypreview_flash_form_ajax_submit_response', $response ), 200 );
	wp_die();
}
add_action( 'wp_ajax_mypreview_flash_form_submit', __NAMESPACE__ . '\form_submit_ajax' );
add_action( 'wp_ajax_nopriv_mypreview_flash_form_submit', __NAMESPACE__ . '\form_submit_ajax' );

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
function nonce_layer( string $content, array $attributes ): string {
	$form_id  = $attributes['formId'] ?? '';
	$nonce    = get_nonce_key( $form_id );
	$content .= wp_nonce_field( PLUGIN['nonce'], $nonce, true, false );
	$content .= '<input type="hidden" name="action" value="mypreview_flash_form_submit" />';
	$content .= '<input type="hidden" name="form_id" value="' . esc_html( $form_id ) . '" />';
	$content .= '<input type="hidden" name="timestamp" value="' . strtotime( 'now' ) . '" />';

	return apply_filters( 'mypreview_flash_form_nonce_layer', $content, $attributes );
}
add_filter( 'mypreview_flash_form_render_callback_after_fieldset', __NAMESPACE__ . '\nonce_layer', 10, 2 );
