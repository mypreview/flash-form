<?php
/**
 * Basic integration with Google reCaptcha V2.
 *
 * The "I'm not a robot" Checkbox requires the user to click
 * a checkbox indicating the user is not a robot. This will
 * either pass the user immediately (with No CAPTCHA) or
 * challenge them to validate whether or not they are human.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.1.0
 *
 * @package       flash-form
 * @subpackage    flash-form/frontend
 */

namespace Flash_Form\Frontend;

use const Flash_Form\PLUGIN as PLUGIN;
use Flash_Form\Dashboard\ReCaptcha as ReCaptcha_Dashboard;
use Flash_Form\Dashboard\Send_Email as Send_Email;
use Flash_Form\Includes\Utils as Utils;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( ReCaptcha::class ) ) :

	/**
	 * The Google reCAPTCHA V2 integration class.
	 */
	class ReCaptcha {

		/**
		 * API endpoint for verifying the user's response.
		 *
		 * @since    1.1.0
		 * @var      string    $verify_uri
		 */
		private static $verify_uri = 'https://www.google.com/recaptcha/api/siteverify';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.1.0
		 * @return    void
		 */
		public function __construct() {
			\add_action( 'mypreview_flash_form_enqueue_scripts', array( $this, 'enqueue' ) );
			\add_filter( 'mypreview_flash_form_render_callback_content', array( $this, 'widget' ), 99, 2 );
			\add_filter( 'mypreview_flash_form_submit_unset_fields', array( $this, 'unset' ), 20, 2 );
			\add_action( 'mypreview_flash_form_submit_before_response', array( $this, 'validate' ), 20, 3 );
		}

		/**
		 * Enqueue reCAPTCHA’s static resources.
		 *
		 * @since     1.1.0
		 * @return    void
		 */
		public static function enqueue(): void {
			if ( ! self::has_keys() ) {
				return;
			}

			$uri = \apply_filters( 'mypreview_flash_form_google_recaptcha_uri', \add_query_arg( array( 'hl' => Utils::get_language_code() ), 'https://www.google.com/recaptcha/api.js' ) );

			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			\wp_enqueue_script( 'g-recaptcha', $uri, array(), null, false );
		}

		/**
		 * Appends a reCAPTCHA widget to the form.
		 *
		 * The `g-recaptcha` tag is a `DIV` element with
		 * class name `g-recaptcha` and stored site key
		 * in the `data-sitekey` HTML attribute.
		 *
		 * @since     1.1.0
		 * @param     string $content       The block content.
		 * @param     array  $attributes    The block attributes.
		 * @return    string
		 */
		public static function widget( string $content, array $attributes = array() ): string {
			$is_captcha = $attributes['isCaptcha'] ?? false;

			// Bail early, in case the reCAPTCHA keys are missing.
			if ( ! self::is_enabled( $is_captcha ) ) {
				return $content;
			}

			\libxml_use_internal_errors( true );
			$dom = new \DOMDocument();
			$dom->loadHTML( $content, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED );
			$xpath  = new \DomXPath( $dom );
			$target = PLUGIN['class_name'] . '-field-button';
			$node   = $xpath->query( "//div[contains(@class, '$target')]" );

			if ( $node && $node->length ) {
				$keys                   = (array) \get_option( ReCaptcha_Dashboard::$option, array() );
				$widget                 = \sprintf( '<div class="g-recaptcha" data-sitekey="%1$s"></div>', \esc_html( $keys['siteKey'] ?? '' ) );
				$before_button_fragment = $dom->createDocumentFragment();
				$before_button_fragment->appendXML( $widget );
				$node->item( 0 )->insertBefore( $before_button_fragment, $node->item( 0 )->firstChild );
			}

			\libxml_clear_errors();
			$content = $dom->saveHTML();

			return apply_filters( 'mypreview_flash_form_g_recaptcha_widget', $content );
		}

		/**
		 * Unset reCAPTCHA widget field id in the process of form submission.
		 *
		 * @since     1.1.0
		 * @param     array $fields        Form field ids.
		 * @param     array $attributes    The block attributes.
		 * @return    array
		 */
		public function unset( array $fields, array $attributes ): array {
			$is_captcha = $attributes['isCaptcha'] ?? false;

			// Ensure captcha is enabled.
			if ( self::is_enabled( $is_captcha ) ) {
				$fields[] = 'g-recaptcha-response';
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
			$is_captcha = $attributes['isCaptcha'] ?? false;

			// Ensure captcha is enabled.
			if ( self::is_enabled( $is_captcha ) ) {
				$token    = $data['g-recaptcha-response'] ?? '';
				$keys     = (array) \get_option( ReCaptcha_Dashboard::$option, array() );
				$request  = \wp_safe_remote_get(
					\add_query_arg(
						\apply_filters(
							'mypreview_flash_form_g_captcha_validate_args',
							array(
								'secret'   => $keys['secretKey'] ?? '',
								'response' => $token,
								'remoteip' => Send_Email::get_ip_address(),
							)
						),
						self::$verify_uri
					)
				);
				$response = \wp_remote_retrieve_body( $request );
				$json     = \json_decode( $response );

				if ( \is_wp_error( $request ) || ! $json->success ) {
					\add_filter( 'mypreview_flash_form_submit_raw_response', array( $this, 'get_failed_summary' ), 99, 2 );
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
			$heading  = \apply_filters( 'mypreview_flash_form_email_submit_heading', \sprintf( \esc_html__( '%1$sAre you a human being? %2$s(go back)%3$s', 'flash-form' ), '<h3>', \sprintf( '<a href="%s">', \esc_url( $referer ) ), '</a></h3>' ) );
			$message  = \__( 'We apologize for the confusion, but we can’t quite tell if you’re a person or a script. Please don’t take this personally. Next time, click the captcha box, and we’ll get out of your hair. Contact the site administrator if you feel this was an error.', 'flash-form' );
			$response = \sprintf( '%s<blockquote><p>%s</p></blockquote>', $heading, $message );

			return \apply_filters( 'mypreview_flash_form_email_submit_summary', $response );
		}

		/**
		 * Whether the captcha keys are already stored.
		 *
		 * @since     1.1.0
		 * @return    bool
		 */
		public static function has_keys(): bool {
			$keys = (array) \get_option( ReCaptcha_Dashboard::$option, array() );

			return is_array( $keys ) && isset( $keys['siteKey'], $keys['secretKey'] ) && ! empty( $keys['siteKey'] ) && ! empty( $keys['secretKey'] );
		}

		/**
		 * Whether the captcha is enabled.
		 *
		 * @since     1.1.0
		 * @param     bool $is_captcha    The current state of the reCAPTCHA integration.
		 * @return    bool
		 */
		public static function is_enabled( bool $is_captcha ): bool {
			return true === $is_captcha && self::has_keys();
		}

	}
endif;
