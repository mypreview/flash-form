<?php
/**
 * The template part for displaying table body (rows and data).
 * The "<tbody>" HTML element encapsulates a set of table rows ("<tr>" elements), indicating that they comprise the body of the table ("<table>").
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

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$mypreview_flash_form_entries = (array) $args['entries'] ?? array();

if ( ! Utils::if_array( $mypreview_flash_form_entries ) || empty( $mypreview_flash_form_entries ) ) {
	return;
}

?>
<tbody>
	<?php
	foreach ( $mypreview_flash_form_entries as $mypreview_flash_form_entry ) :
		?>
		<tr>
			<?php Utils::safe_html( Utils::implode_wrap_html_tag( array_values( $mypreview_flash_form_entry ), 'td' ) ); ?>
		</tr>
	<?php endforeach; ?>
</tbody>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
