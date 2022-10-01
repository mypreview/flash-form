<?php
/**
 * Helper functions.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.1.0
 *
 * @package       flash-form
 * @subpackage    flash-form/includes
 */

namespace Flash_Form\Includes;

use const Flash_Form\PLUGIN as PLUGIN;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'Utils' ) ) :

	/**
	 * The main helper-specific class.
	 */
	class Utils {

		/**
		 * Get the nonce key for the form.
		 *
		 * @since     1.0.0
		 * @param     null|string $form_id    The form’s client id.
		 * @return    string
		 */
		public static function get_nonce_key( ?string $form_id ): string {
			return '_wpnonce-' . $form_id ?? '';
		}

		/**
		 * Retrieve referer from ‘_wp_http_referer’ or HTTP referer.
		 *
		 * @since     1.0.0
		 * @param     null|string $referer    HTTP referer.
		 * @return    string
		 */
		public static function get_referer( ?string $referer ): string {
			return isset( $referer ) && ! empty( $referer ) ? \set_url_scheme( \wp_guess_url() . $referer ) : '';
		}

		/**
		 * Retrieve the current site (network) name.
		 *
		 * @since     1.0.0
		 * @return    string
		 */
		public static function get_sitename(): string {
			return function_exists( 'get_current_site' ) && isset( \get_current_site()->site_name ) && \get_current_site()->site_name ? \get_current_site()->site_name : '"' . \get_option( 'blogname' ) . '"';
		}

		/**
		 * Retrieves the current site ISO language code.
		 *
		 * @since     1.1.0
		 * @return    string
		 */
		public static function get_language_code(): string {
			$get_locale = explode( '_', get_locale() );

			return $get_locale[0] ?? 'en';
		}

		/**
		 * Retrieves post id of given post-object or currently queried object id.
		 *
		 * @since     1.1.0
		 * @param     int|WP_Post|null $post    Post ID or post object.
		 * @return    int
		 */
		public static function get_post_id( $post = null ): ?int {
			$post_id  = null;
			$get_post = \get_post( $post, 'OBJECT' );

			if ( is_null( $get_post ) ) {
				$post_id = (int) \get_queried_object_id();
			} elseif ( property_exists( $get_post, 'ID' ) ) {
				$post_id = (int) $get_post->ID;
			}

			return $post_id;
		}

		/**
		 * Post id of the translation if exists, null otherwise.
		 *
		 * @since     1.1.0
		 * @param     null|int $post_id    Post ID.
		 * @return    null|int
		 */
		public static function get_localized_post_id( ?int $post_id = null ): ?string {
			$return = null;

			if ( self::is_post_exists( $post_id ) ) {
				$return = $post_id;
				if ( self::is_polylang_activated() ) {
					$pll_post_id = \pll_get_post( $post_id );
					if ( $pll_post_id && ! is_null( $pll_post_id ) ) {
						$return = (int) $pll_post_id;
					}
				}
			}

			return $return;
		}

		/**
		 * Query a third-party plugin activation.
		 * This statement prevents from producing fatal errors,
		 * in case the the plugin is not activated on the site.
		 *
		 * @since     1.1.0
		 * @param     string $slug        Plugin slug to check for the activation state.
		 * @param     string $filename    Optional. Plugin’s main file name.
		 * @return    bool
		 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		 */
		public static function is_plugin_activated( string $slug, string $filename = '' ): bool {
			$filename               = empty( $filename ) ? $slug : $filename;
			$plugin_path            = \apply_filters( 'mypreview_flash_form_third_party_plugin_path', sprintf( '%s/%s.php', \esc_html( $slug ), \esc_html( $filename ) ) );
			$subsite_active_plugins = \apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
			$network_active_plugins = \apply_filters( 'active_plugins', get_site_option( 'active_sitewide_plugins' ) );

			// Bail early in case the plugin is not activated on the website.
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( ( empty( $subsite_active_plugins ) || ! in_array( $plugin_path, $subsite_active_plugins ) ) && ( empty( $network_active_plugins ) || ! array_key_exists( $plugin_path, $network_active_plugins ) ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Query the "Polylang" plugin activation.
		 *
		 * @since     1.1.0
		 * @return    bool
		 */
		public static function is_polylang_activated(): bool {
			return self::is_plugin_activated( 'polylang' ) || self::is_plugin_activated( 'polylang-pro', 'polylang' );
		}

		/**
		 * Determines if a post, identified by the specified ID,
		 * exist within the WordPress database.
		 *
		 * @since     1.1.0
		 * @param     null|int $post_id    Post ID.
		 * @return    bool
		 */
		public static function is_post_exists( ?int $post_id = null ): bool {
			return ! is_null( $post_id ) && is_string( \get_post_type( $post_id ) );
		}

		/**
		 * Filters value of a field or content and strips out disallowed HTML.
		 *
		 * @since     1.1.0
		 * @param     string $value    Content or value of the field.
		 * @return    string
		 */
		public static function clean( string $value ): string {
			$value = str_replace( array( '[', ']' ), array( '&#91;', '&#93;' ), $value );
			return nl2br( \wp_kses( $value, array() ) );
		}

		/**
		 * Recursive sanitation for an array.
		 * Returns the sanitized values of an array.
		 *
		 * @since    1.1.0
		 * @param    array $input    Array of values.
		 * @return   array
		 */
		public static function clean_array( array $input ): array {
			// Bail early, in case the input value is missing or not an array.
			if ( empty( $input ) || ! is_array( $input ) ) {
				return array();
			}

			// Loop through the array to sanitize each key/values recursively.
			foreach ( $input as $key => &$value ) {
				if ( is_array( $value ) ) {
					$value = self::clean_array( $value );
				} else {
					$value = sanitize_text_field( $value );
				}
			}

			return $input;
		}

		/**
		 * Determines whether the variable is a valid array and has at least one item within it.
		 *
		 * @since     1.1.0
		 * @param     array $input    The array to check.
		 * @return    bool
		 */
		public static function if_array( array $input ): bool {
			if ( is_array( $input ) && ! empty( $input ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Implode and escape HTML attributes for output.
		 *
		 * @since     1.1.0
		 * @param     array $raw_attributes    Attribute name value pairs.
		 * @return    string
		 */
		public static function implode_html_attributes( array $raw_attributes ): string {
			$attributes = array();
			foreach ( $raw_attributes as $name => $value ) {
				$attributes[] = \esc_attr( $name ) . '="' . \esc_attr( $value ) . '"';
			}
			return implode( ' ', $attributes );
		}

		/**
		 * Implodes given array elements using the "glue" of defined "tag" closed/open,
		 * and wraps it so the first and last items have their beginning/ending tags.
		 *
		 * @since     1.1.0
		 * @param     array  $elements    Array of elements to render.
		 * @param     string $glue        HTML tag specified as glue.
		 * @return    string
		 */
		public static function implode_wrap_html_tag( array $elements, string $glue = 'span' ): string {
			$elements = array_map( 'wp_kses_post', $elements );

			return "<$glue>" . implode( "</$glue><$glue>", $elements ) . "</$glue>";
		}

		/**
		 * Sanitizes content for allowed HTML tags for post content.
		 *
		 * @since     1.1.0
		 * @param     string $input      The context for which to retrieve tags.
		 * @param     string $context    The context for which to retrieve tags. Allowed values are "post", "strip", "data", "entities".
		 * @param     bool   $echo       Optional. Echo the string or return it.
		 * @return    string
		 */
		public static function safe_html( string $input, string $context = 'post', bool $echo = true ): string {
			$return = \wp_kses( $input, \wp_kses_allowed_html( $context ) );

			if ( $echo ) {
				echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			return $return;
		}

		/**
		 * Returns whether the current user has the specified capability.
		 *
		 * @since     1.0.0
		 * @return    bool
		 */
		public static function rest_editor_permission_callback(): bool {
			return \current_user_can( 'edit_posts' );
		}

		/**
		 * Returns the template file name without extension being added to it.
		 *
		 * @since     1.1.0
		 * @param     string $file    Template file name (filename).
		 * @return    string
		 */
		public static function get_template_filename( string $file ): string {
			return preg_replace( '/\\.[^.\\s]{3,4}$/', '', $file );
		}

		/**
		 * Returns the template file directory and relative file path.
		 *
		 * @since     1.1.0
		 * @param     string $file    File path.
		 * @return    string
		 */
		public static function get_template_path( string $file ): string {
			return PLUGIN['dir_path'] . '/templates/' . self::get_template_filename( $file ) . '.php';
		}

		/**
		 * Returns the HTML template instead of outputting.
		 *
		 * @since     1.1.0
		 * @param     string $template_name    Template name.
		 * @param     array  $args             Arguments. (default: array).
		 * @return    string
		 */
		public static function get_template_html( string $template_name, array $args = array() ): string {
			// Start remembering everything that would normally be outputted,
			// but don't quite do anything with it yet.
			ob_start();

			\load_template( self::get_template_path( $template_name ), false, $args );
			return ob_get_clean();
		}

	}
endif;
