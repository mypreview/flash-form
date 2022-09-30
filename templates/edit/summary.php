<?php
/**
 * The template part for displaying submission summary.
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
$mypreview_flash_form_post_timestamp = Utils::get_template_html( 'edit/timestamp', array( 'post_id' => $mypreview_flash_form_post_id ) );

?>
<div class="submitbox" id="submitlink" style="margin:0 -12px -12px;">
	<div id="minor-publishing">
		<div id="misc-publishing-actions">
			<div class="misc-pub-section curtime misc-pub-curtime">
				<span id="timestamp">
					<?php Utils::safe_html( $mypreview_flash_form_post_timestamp ); ?>
				</span>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div id="major-publishing-actions">
		<div id="delete-action">
			<?php if ( current_user_can( 'delete_post', $mypreview_flash_form_post_id ) ) : ?>
				<a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $mypreview_flash_form_post_id ) ); ?>">
					<?php echo ! EMPTY_TRASH_DAYS ? esc_html_e( 'Delete permanently', 'flash-form' ) : esc_html_e( 'Move to Trash', 'flash-form' ); ?>
				</a>
			<?php endif; ?>
		</div>
		<div id="publishing-action">
			<span class="spinner"></span>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
