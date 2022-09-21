<?php
/**
 * This class registers a custom post type where a copy of all
 * form submissions will be saved to be browsed and managed later.
 *
 * @link          https://mypreview.github.io/flash-form
 * @author        MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @since         1.1.0
 *
 * @package       flash-form
 * @subpackage    flash-form/dashboard
 */

namespace Flash_Form\Dashboard;

use const Flash_Form\PLUGIN as PLUGIN;
use Flash_Form\Includes\Utils as Utils;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Entries::class ) ) :

	/**
	 * The submission entries class.
	 */
	class Entries {

		/**
		 * Post type key.
		 *
		 * @since    1.1.0
		 * @var      string    $post_type
		 */
		public static $post_type = 'flash-form';

		/**
		 * Name of the option for storing number of unread entries.
		 *
		 * @since    1.1.0
		 * @var      string    $unread_option
		 */
		public static $unread_option = 'mypreview_flash_form_unread_entries';

		/**
		 * Name of the post-meta key for storing submission details.
		 *
		 * @since    1.1.0
		 * @var      string    $meta_key
		 */
		public static $meta_key = 'mypreview_flash_form_meta_entries';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.0.0
		 * @param     array $args    Array or arguments for registering the entries post type.
		 * @return    void
		 */
		public function __construct( $args = array() ) {
			$this->register( $args );
			\add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
			\add_action( 'mypreview_flash_form_before_prepare_email', array( $this, 'save' ) );
			\add_action( 'mypreview_flash_form_response_inserted', array( $this, 'save_meta' ), 10, 2 );
			\add_action( 'current_screen', array( $this, 'update_unread_edit' ) );
			\add_action( 'after_delete_post', array( $this, 'update_unread_delete' ), 10, 2 );
			\add_action( 'manage_' . self::$post_type . '_posts_columns', array( $this, 'custom_columns' ) );
			\add_action( 'manage_' . self::$post_type . '_posts_custom_column', array( $this, 'custom_column' ), 10, 2 );
			\add_filter( 'use_block_editor_for_post_type', array( $this, 'disable_block_editor' ), 10, 2 );
			\add_filter( 'rest_api_allowed_post_types', array( $this, 'allow_for_rest_api' ) );
		}

		/**
		 * Register a custom post type we’ll use to keep copies of the form block submissions.
		 *
		 * @since     1.1.0
		 * @param     array $args    Array or arguments for registering the entries post type.
		 * @return    void
		 */
		public function register( $args = array() ): void {
			\register_post_type(
				self::$post_type,
				\wp_parse_args(
					$args,
					array(
						'labels'                => array(
							'name'               => __( 'Form Responses', 'flash-form' ),
							'singular_name'      => __( 'Form Responses', 'flash-form' ),
							'search_items'       => __( 'Search Responses', 'flash-form' ),
							'not_found'          => __( 'No responses found', 'flash-form' ),
							'not_found_in_trash' => __( 'No responses found', 'flash-form' ),
						),
						'menu_icon'             => 'dashicons-feedback',
						'show_ui'               => true,
						'show_in_menu'          => false,
						'show_in_admin_bar'     => false,
						'public'                => false,
						'rewrite'               => false,
						'query_var'             => false,
						'capability_type'       => 'page',
						'show_in_rest'          => true,
						'rest_controller_class' => 'Flash_Form\Dashboard\Entries_Endpoint',
						'capabilities'          => array(
							'create_posts'        => 'do_not_allow',
							'publish_posts'       => 'publish_pages',
							'edit_posts'          => 'edit_pages',
							'edit_others_posts'   => 'edit_others_pages',
							'delete_posts'        => 'delete_pages',
							'delete_others_posts' => 'delete_others_pages',
							'read_private_posts'  => 'read_private_pages',
							'edit_post'           => 'edit_page',
							'delete_post'         => 'delete_page',
							'read_post'           => 'read_page',
						),
						'map_meta_cap'          => true,
					)
				)
			);
		}

		/**
		 * Regisers a top-level menu page.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function add_admin_page(): void {
			\add_menu_page(
				// Page title.
				__( 'Flash Form', 'flash-form' ),
				// Menu title.
				__( 'Flash Form', 'flash-form' ) . self::get_unread_count(),
				// Capability.
				'edit_pages',
				// Menu slug.
				PLUGIN['slug'],
				// Callback.
				null,
				// Icon.
				'dashicons-feedback',
				// Position.
				45
			);

			add_submenu_page(
				// Parent slug.
				PLUGIN['slug'],
				// Page title.
				__( 'Responses', 'flash-form' ),
				// Menu title.
				__( 'Responses', 'flash-form' ),
				// Capability.
				'edit_pages',
				// Menu slug.
				'edit.php?post_type=' . self::$post_type,
				// Callback.
				null,
				// Position.
				0
			);

			remove_submenu_page(
				// Menu slug.
				PLUGIN['slug'],
				// Submenu slug.
				PLUGIN['slug']
			);
		}

		/**
		 * Inserts a new post to store information submitted via the form block.
		 *
		 * @since     1.1.0
		 * @param     array $args    Array or arguments prepared for sending the email.
		 * @return    void
		 */
		public function save( array $args ): void {
			global $post;

			$author       = (string) $args['headers']['raw']['author'] ?? '';
			$author_email = (string) $args['headers']['raw']['author_email'] ?? '';
			$subject      = (string) $args['headers']['raw']['subject'] ?? '';
			$message      = (string) $args['message']['rendered'] ?? '';
			$ip_address   = (string) $args['extras']['ip_address'] ?? '';
			$timestamp    = (string) $args['extras']['timestamp'] ?? '';
			$post_title   = "{$author} - {$timestamp}";
			$post_status  = 'publish';

			\add_filter( 'wp_insert_post_data', array( $this, 'nullify_post_author' ), 11, 2 );

			$post_id = \wp_insert_post(
				array(
					'post_date'    => \sanitize_text_field( \current_time( 'mysql' ) ),
					'post_type'    => self::$post_type,
					'post_status'  => $post_status,
					'post_parent'  => $post ? (int) $post->ID : 0,
					'post_title'   => \sanitize_text_field( \wp_kses( $subject, \wp_kses_allowed_html( 'post' ) ) ),
					'post_content' => \wp_kses( "$message\n<!--more-->", \wp_kses_allowed_html( 'post' ) ), // Search is going to pick the data captured here.
					'post_name'    => \md5( "{$author} - {$timestamp}" ),
				)
			);

			// At this point insert has finished, we can remove the filter accordingly.
			\remove_filter( 'wp_insert_post_data', array( $this, 'nullify_post_author' ), 11 );

			if ( 'publish' === $post_status ) {
				// Increase count of unread responses.
				$unread = (array) \get_option( self::$unread_option, array() );
				\update_option( self::$unread_option, array( ...$unread, (int) $post_id ) );
			}

			/**
			 * Allow third-party resources to modify response post that is being inserted.
			 */
			\do_action( 'mypreview_flash_form_response_inserted', $post_id, $args );
		}

		/**
		 * Save form submission details within the post-meta storage to access later.
		 *
		 * @since     1.1.0
		 * @param     int   $post_id    The current post ID.
		 * @param     array $args       Array or arguments prepared for sending the email.
		 * @return    void
		 */
		public function save_meta( int $post_id, array $args ): void {
			\add_post_meta( $post_id, self::$meta_key, $args, true ); // The same key should not be added.
		}

		/**
		 * Detects whether the current submission entity viewed.
		 * Once the edit screen of the submission is openend, we can safely remove the post-id from the unread array.
		 *
		 * @since     1.1.0
		 * @param     \WP_Screen $screen    Current "WP_Screen" object.
		 * @return    void
		 */
		public function update_unread_edit( \WP_Screen $screen ): void {
			if ( isset( $screen->base, $screen->id ) && 'post' === $screen->base && self::$post_type === $screen->id ) {
				$post_id = \filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
				self::update_unread( $post_id );
			}
		}

		/**
		 * Update the number of unread entries when any submission post is deleted.
		 *
		 * @since     1.1.0
		 * @param     int      $post_id    The current post ID.
		 * @param     \WP_Post $post       Post object.
		 * @return    void
		 */
		public function update_unread_delete( int $post_id, \WP_Post $post ): void {
			if ( \get_post_type( $post ) === self::$post_type ) {
				self::update_unread( $post_id );
			}
		}

		/**
		 * Re-order and organize entries post-type admin page columns.
		 *
		 * @since     1.1.0
		 * @param     array $columns    An associative array of column headings.
		 * @return    array
		 */
		public function custom_columns( array $columns ): array {
			$custom_columns = array(
				'cb'         => $columns['cb'],
				'title'      => __( 'Subject', 'flash-form' ),
				'given-name' => __( 'Name', 'flash-form' ),
				'referer'    => __( 'Referer URL', 'flash-form' ),
				'ip_address' => __( 'IP Address', 'flash-form' ),
				'date'       => $columns['date'],
			);

			return $custom_columns;
		}

		/**
		 * Parse submission details and display in appropriate columns.
		 *
		 * @since     1.1.0
		 * @param     string $column_name    The name of the column to display.
		 * @param     int    $post_id        The current post ID.
		 * @return    void
		 */
		public function custom_column( string $column_name, int $post_id ): void {
			$post_meta         = \get_post_meta( $post_id, self::$meta_key, true );
			$author            = $post_meta['headers']['raw']['author'] ?? '';
			$author_email      = $post_meta['headers']['raw']['author_email'] ?? '';
			$author_email_line = '';
			$avatar            = \get_avatar( $author_email, 32, '', '', array( 'extra_attr' => 'style="float:left;margin-right:10px;margin-top:3px"' ) );
			$ip_address        = $post_meta['extras']['ip_address'] ?? '';
			$is_logged_in      = $post_meta['extras']['is_logged_in'] ?? false;
			$ip_address_line   = '';
			$is_logged_in_line = '';
			$referer           = $post_meta['extras']['referer'] ?? '';
			$referer_post_id   = \wp_get_post_parent_id( $post_id );
			$referer_line      = '';

			if ( ! empty( $author_email ) ) {
				$author_email_line = sprintf( '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a><br/>', \esc_url( 'mailto:' . $author_email ), \esc_html( $author_email ) );
			}

			if ( ! empty( $ip_address ) ) {
				/* translators: 1: Dashicon. */
				$is_logged_in_line = $is_logged_in ? sprintf( \esc_html__( '%1$s Sent by a verified user', 'flash-form' ), '<span class="dashicons dashicons-yes"></span>' ) : sprintf( \esc_html__( '%1$s Sent by an unverified visitor', 'flash-form' ), '<span class="button-link-delete dashicons dashicons-no"></span>' );
				$ip_address_line   = sprintf( '<a href="https://ipinfo.io/%1$s" target="_blank" target="_blank" rel="noopener noreferrer nofollow">%2$s<br/>%1$s</a><br/>', \esc_html( $ip_address ), \wp_kses_post( $is_logged_in_line ) );
			}

			if ( ! empty( $referer ) ) {
				$referer_line = sprintf( '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s<br/>%1$s</a><br/>', \esc_url( $referer ), \get_the_title( $referer_post_id ) . ' #' . strval( $referer_post_id ) );
			}

			switch ( $column_name ) {
				case 'given-name':
					Utils::safe_html( '<div>' . $avatar . '<strong>' . $author . '</strong><br/>' . $author_email_line . '</div>' );
					break;
				case 'ip_address':
					Utils::safe_html( $ip_address_line );
					break;
				case 'referer':
					Utils::safe_html( $referer_line );
					break;

			}
		}

		/**
		 * Ensure the post author is always zero for each and any form submissions.
		 * This will prevent "export/import" from trying to create new users based on form submissions from people who were logged in at the time.
		 *
		 * @since     1.1.0
		 * @param     array $data       An array of slashed, sanitized, and processed post data.
		 * @param     array $postarr    An array of sanitized (and slashed) but otherwise unmodified post data.
		 * @return    array
		 */
		public function nullify_post_author( array $data, array $postarr ): array {
			if ( self::$post_type === $data['post_type'] && self::$post_type === $postarr['post_type'] ?? '' ) {
				$data['post_author'] = 0;
			}

			return $data;
		}

		/**
		 * Disables "Block Editor" for form entries.
		 *
		 * @since     1.1.0
		 * @param     bool   $can_edit     Whether the post type can be edited or not. Default true.
		 * @param     string $post_type    The post type being checked.
		 * @return    bool
		 */
		public function disable_block_editor( bool $can_edit, string $post_type ): bool {
			return self::$post_type === $post_type ? false : $can_edit;
		}

		/**
		 * Whitelist the entries post type to allow access it from the WordPress.com REST API.
		 *
		 * @since     1.1.0
		 * @param     array $post_types    REST-API enabled post-types.
		 * @return    array
		 */
		public function allow_for_rest_api( array $post_types ): array {
			$post_types[] = self::$post_type;
			return $post_types;
		}

		/**
		 * Get number of unread messages (submissions).
		 *
		 * @since     1.1.0
		 * @return    string
		 */
		public static function get_unread_count():? string {
			$return = '';
			$unread = (array) \get_option( self::$unread_option, array() );

			if ( Utils::if_array( $unread ) ) {
				$count  = count( $unread );
				$return = '&nbsp;<span class="awaiting-mod">' . \number_format_i18n( $count ) . '</span>';
			}

			return $return;
		}

		/**
		 * Iterate over unread post-ids and update the count if appropriate.
		 *
		 * @since     1.1.0
		 * @param     int $post_id    The current post ID.
		 * @return    void
		 */
		private static function update_unread( int $post_id ): void {
			$unread = (array) \get_option( self::$unread_option, array() );
			$needle = array_search( $post_id, $unread, true );

			if ( Utils::if_array( $unread ) && $post_id && false !== $needle ) {
				unset( $unread[ $needle ] );
				\update_option( self::$unread_option, $unread );
			}
		}

	}
endif;
