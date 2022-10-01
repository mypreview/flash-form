<?php
/**
 * The template part for displaying submission date time in user friendly format.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.1.0
 *
 * @package       flash-form
 * @subpackage    flash-form/templates/edit
 */

namespace Flash_Form\Templates\Edit;

use Flash_Form\Includes\Utils as Utils;

$mypreview_flash_form_post_id        = (int) $args['post_id'] ?? 0;
$mypreview_flash_form_post_timestamp = \get_post_timestamp( $mypreview_flash_form_post_id );

Utils::safe_html(
	sprintf(
						/* translators: %s: message submission date */
		esc_html( __( 'Submitted on: %s', 'flash-form' ) ),
		'<b>' . sprintf(
							/* translators: Publish box date string. 1: Date, 2: Time. See https://www.php.net/manual/datetime.format.php */
			__( '%1$s at %2$s', 'flash-form' ),
			date_i18n(
								/* translators: Publish box date format, see https://www.php.net/manual/datetime.format.php */
				_x( 'M j, Y', 'publish box date format', 'flash-form' ),
				$mypreview_flash_form_post_timestamp
			),
			date_i18n(
								/* translators: Publish box time format, see https://www.php.net/manual/datetime.format.php */
				_x( 'H:i', 'publish box time format', 'flash-form' ),
				$mypreview_flash_form_post_timestamp
			)
		) . '</b>'
	)
);
