<?php
/**
 * Sending email class.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.0.0
 *
 * @package       flash-form
 * @subpackage    flash-form/dashboard
 */

namespace Flash_Form\Dashboard;

use Flash_Form\Includes\Utils as Utils;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Send_Email::class ) ) :

	/**
	 * The send email class.
	 */
	class Send_Email {

		/**
		 * Filters the message sent via email after a successful form submission.
		 *
		 * @since     1.0.0
		 * @param     array  $to         Array of valid email addresses.
		 * @param     string $subject    Feedback email subject.
		 * @param     array  $data       Submitted form data.
		 * @param     object $xpath      The DOMDocument associated with the DOMXPath.
		 * @param     string $referer    Referer from the "_wp_http_referer".
		 * @return    array
		 */
		public function prepare( array $to, string $subject, array $data, object $xpath, string $referer ): array {
			$headers              = array();
			$message              = array();
			$comment_author       = $this->input_node( $xpath, 'input', 'autocomplete', 'given-name', $data );
			$comment_author_email = $this->input_node( $xpath, 'input', 'autocomplete', 'username', $data );

			if ( \is_email( $comment_author_email ) ) {
				$headers[] = 'From: "' . $comment_author . '" <' . $comment_author_email . ">\r\n";
				$headers[] = 'Reply-To: "' . $comment_author . '" <' . $comment_author_email . ">\r\n";
			}

			foreach ( $data as $id => $value ) {
				$label_node = $this->input_node( $xpath, 'label', 'for', $id );
				$label      = isset( $label_node->item( 0 )->nodeValue ) ? $label_node->item( 0 )->nodeValue : __( 'Unlabeled', 'flash-form' );
				$value      = is_array( $value ) ? implode( ', ', $value ) : $value;
				$message[]  = \sprintf( '<b>%1$s</b>: %2$s%3$s', Utils::clean( $label ), Utils::clean( $value ), '<br />' );
			}

			array_push(
				$message,
				'<br />',
				'<hr />',
				__( 'Time:', 'flash-form' ) . ' ' . self::get_timestamp() . '<br />',
				__( 'IP Address:', 'flash-form' ) . ' ' . self::get_ip_address() . '<br />',
				__( 'Contact Form URL:', 'flash-form' ) . ' ' . $referer . '<br />'
			);

			if ( \is_user_logged_in() ) {
				array_push(
					$message,
					sprintf(
						/* translators: %s: Current website’s name. */
						'<p>' . __( 'Sent by a verified %s user.', 'flash-form' ) . '</p>',
						isset( $GLOBALS['current_site']->site_name ) && $GLOBALS['current_site']->site_name ?
						$GLOBALS['current_site']->site_name : '"' . \get_option( 'blogname' ) . '"'
					)
				);
			} else {
				array_push( $message, '<p>' . __( 'Sent by an unverified visitor to your site.', 'flash-form' ) . '</p>' );
			}

			/**
			 * Filters the message sent via email after a successful form submission.
			 *
			 * @param    string $message    Feedback email message.
			 */
			$message = \apply_filters( 'mypreview_flash_form_prepare_email_message', $message );
			$message = $this->wrap_in_html( join( '', $message ) );
			/**
			 * Filters the headers sent via email after a successful form submission.
			 *
			 * @param    array $headers    Additional headers.
			 */
			$headers = \apply_filters( 'mypreview_flash_form_prepare_email_headers', $headers );

			return \apply_filters( 'mypreview_flash_form_prepare_email_args', array( $to, $subject, $message, $headers ) );
		}

		/**
		 * Wrapper for "wp_mail()" that enables HTML messages with text alternatives.
		 *
		 * @since     1.0.0
		 * @param     array  $to             List of email addresses to send message.
		 * @param     string $subject        Email subject.
		 * @param     string $message        Message contents.
		 * @param     array  $headers        Optional. Additional headers.
		 * @param     array  $attachments    Optional. Files to attach.
		 * @return    bool
		 */
		public function fire( array $to, string $subject, string $message, array $headers = array(), array $attachments = array() ): bool {
			\add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
			\add_action( 'phpmailer_init', array( $this, 'add_plain_text_alternative' ) );

			// Whether the email contents were sent successfully.
			$result = \wp_mail( $to, $subject, $message, $headers, $attachments );

			\remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
			\remove_action( 'phpmailer_init', array( $this, 'add_plain_text_alternative' ) );

			return $result;
		}

		/**
		 * Get email content type.
		 *
		 * @since     1.0.0
		 * @return    string
		 */
		public function get_content_type(): string {
			return \apply_filters( 'mypreview_flash_form_email_content_type', 'text/html' );
		}

		/**
		 * Fires after PHPMailer is initialized and adds the alternative body content.
		 *
		 * @since     1.0.0
		 * @param     object $phpmailer    The PHPMailer instance (passed by reference).
		 * @return    void
		 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		 */
		public function add_plain_text_alternative( object $phpmailer ): void {
			$alt_body           = str_replace( '<p>', '<p><br />', $phpmailer->Body );              // Add an extra break so that the extra space above the <p> is preserved after the <p> is stripped out.
			$alt_body           = str_replace( array( '<br>', '<br />' ), "\n", $alt_body );        // Convert <br> to \n breaks, to preserve the space between lines that we want to keep.
			$alt_body           = str_replace( array( '<hr>', '<hr />' ), "----\n", $alt_body );    // Convert <hr> to an plain-text equivalent, to preserve the integrity of the message.
			$phpmailer->AltBody = trim( \wp_strip_all_tags( $alt_body ) );                           // Trim the plain text message to remove the \n breaks that were after <doctype>, <html>, and <body>.
		}

		/**
		 * Retrieves an input value from which the user has submitted the form on the page.
		 *
		 * @since     1.0.0
		 * @param     object $xpath              The DOMDocument associated with the DOMXPath.
		 * @param     string $tag_name           Target field HTML tag name.
		 * @param     string $attribute          Target field attribute name.
		 * @param     string $attribute_value    Target field attribute value.
		 * @param     array  $data               Optional. Submitted form data.
		 * @return    string|object
		 */
		public function input_node( object $xpath, string $tag_name, string $attribute, string $attribute_value, array $data = array() ) {
			$return   = '';
			$the_node = $xpath->query( "//{$tag_name}[contains(@{$attribute}, '${attribute_value}')]" );

			if ( $the_node ) {
				if ( empty( $data ) ) {
					$return = $the_node;
				} else {
					$field    = $the_node->item( 0 );
					$field_id = ! is_null( $field ) ? $field->getAttribute( 'id' ) : null;
					$return   = isset( $field_id, $data[ $field_id ] ) ? $data[ $field_id ] : '';
				}
			}

			return $return;
		}

		/**
		 * Wrap a message body with the appropriate in HTML tags.
		 * This helps to ensure correct parsing by clients, and also helps avoid triggering spam filtering rules.
		 *
		 * @since     1.0.0
		 * @param     string $body    Message contents.
		 * @return    string
		 */
		protected function wrap_in_html( string $body ): string {
			// Don't do anything if the message was already wrapped in HTML tags.
			// That could have be done by a plugin via filters.
			if ( false !== strpos( $body, '<html' ) ) {
				return $body;
			}

			$template = \sprintf(
				// The tabs are just here so that the raw code is correctly formatted for developers.
				// They're removed so that they don't affect the final message sent to users.
				str_replace(
					"\t",
					'',
					'<!doctype html>
                    <html xmlns="http://www.w3.org/1999/xhtml">
                    <body>
                    %s
                    </body>
                    </html>'
				),
				$body
			);

			return \apply_filters( 'mypreview_flash_form_email_html_wrapper', $template );
		}

		/**
		 * Retrieves the current time from which the user has submitted the form on the page.
		 *
		 * @since     1.0.0
		 * @return    string
		 */
		public static function get_timestamp(): string {
			/* translators: 1: "date_format" placeholder, 2: "time_format" placeholder. */
			$date_time_format = \_x( '%1$s \a\t %2$s', '{$date_format} \a\t {$time_format}', 'flash-form' );
			$date_time_format = \sprintf( $date_time_format, \get_option( 'date_format' ), \get_option( 'time_format' ) );
			$time             = \sanitize_text_field( \date_i18n( $date_time_format, \current_datetime() ) );

			return \apply_filters( 'mypreview_flash_form_email_timestamp', $time );
		}

		/**
		 * The IP address from which the user is viewing the current page.
		 *
		 * @since     1.0.0
		 * @return    string
		 */
		public static function get_ip_address(): string {
			$ip = '';

			if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) {
				$ip = \sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ) );
			} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				// Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
				// Make sure we always only send through the first IP in the list which should always be the client IP.
				$ip = (string) \rest_is_ip_address( trim( current( preg_split( '/,/', \sanitize_text_field( \wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) ) ) );
			} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
				$ip = \sanitize_text_field( \wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
			}

			return \apply_filters( 'mypreview_flash_form_email_ip_address', $ip );
		}

		/**
		 * Outputs list of form submission summary including each field’s label and value.
		 *
		 * @since     1.0.0
		 * @param     array  $data       Submitted form data.
		 * @param     object $xpath      The DOMDocument associated with the DOMXPath.
		 * @param     string $referer    Referer from the "_wp_http_referer".
		 * @return    string
		 */
		public function get_submit_summary( array $data, object $xpath, string $referer ): string {
			// Form submission heading along with a link to (go back).
			/* translators: 1: Open heading tag, 2: Open anchor tag, 3: Close anchor and heading tags. */
			$heading = \apply_filters( 'mypreview_flash_form_email_submit_heading', \sprintf( \esc_html__( '%1$sMessage sent! %2$s(go back)%3$s', 'flash-form' ), '<h3>', \sprintf( '<a href="%s">', \esc_url( $referer ) ), '</a></h3>' ) );
			$return  = \sprintf( '%s<blockquote><ul>', $heading );

			foreach ( $data as $id => $value ) {
				$label_node = $this->input_node( $xpath, 'label', 'for', $id );
				$label      = isset( $label_node->item( 0 )->nodeValue ) ? $label_node->item( 0 )->nodeValue : \__( 'Unlabeled', 'flash-form' );
				$value      = is_array( $value ) ? implode( ',', $value ) : $value;
				$return    .= \sprintf( '<li><label>%s:&nbsp;</label><span>%s</span></li>', Utils::clean( $label ), Utils::clean( $value ) );
			}

			$return .= '</ul></blockquote>';

			return \apply_filters( 'mypreview_flash_form_email_submit_summary', $return, $referer );
		}
	}
endif;
