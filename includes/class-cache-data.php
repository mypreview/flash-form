<?php
/**
 * Caches form data for a short period of time.
 *
 * This class introduces a simple caching layer to handle
 * Ajax form submissions as WordPress offers no way to
 * retrieve block associated attributes using an identifier.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.0.0
 *
 * @package       flash-form
 * @subpackage    flash-form/includes
 */

namespace Flash_Form\Includes;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Cache_Data::class ) ) :

	/**
	 * The cache form data class.
	 */
	class Cache_Data {

		/**
		 * Call this method on each newly-created object.
		 *
		 * Every time this class is instantiated, new cache storage will be dedicated
		 * to the corresponding form to store and request data associated with it.
		 *
		 * @since     1.0.0
		 * @param     int|string|array ...$args    Acceptable arguments to be passed to the `set_data` method directly.
		 * @return    void
		 */
		public function __construct( ...$args ) {
			$this->set_data( ...$args );
		}

		/**
		 * Transient name associated with this caching layer.
		 *
		 * @since     1.0.0
		 * @param     string $form_id    The form’s client id.
		 * @return    string
		 */
		public static function get_key( string $form_id ): string {
			return sprintf( 'mypreview_flash_form_cache_storage_%s', esc_html( $form_id ) );
		}

		/**
		 * Retrieves the cached data of the form using the transient API.
		 *
		 * @since     1.0.0
		 * @param     string $form_id    The form’s client id.
		 * @return    array
		 */
		public static function get_data( string $form_id ): array {
			$key = self::get_key( $form_id );
			return (array) get_transient( $key );
		}

		/**
		 * Sets/updates the form cache of a transient. the transient API.
		 *
		 * @since     1.0.0
		 * @param     array  $attributes    The block attributes.
		 * @param     string $content       The block content.
		 * @param     string $form_id       The form’s client id.
		 * @param     int    $expire        Time until expiration in seconds.
		 * @return    array
		 */
		public function set_data( array $attributes, string $content, ?string $form_id, int $expire = HOUR_IN_SECONDS ): ?array {
			// Bail early, in case the form-id is missing.
			if ( empty( $form_id ) ) {
				return null;
			}

			$key  = self::get_key( $form_id );
			$data = array(
				'attributes' => $attributes,
				'content'    => $content,
			);
			set_transient( $key, $data, $expire );

			return $data;
		}
	}
endif;
