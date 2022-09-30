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
		public static $post_type = 'flash-form-entries';

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
			\add_action( 'mypreview_flash_form_entry_inserted', array( $this, 'save_meta' ), 10, 2 );
			\add_action( 'current_screen', array( $this, 'update_unread_edit' ) );
			\add_action( 'after_delete_post', array( $this, 'update_unread_delete' ), 10, 2 );
			\add_action( 'manage_' . self::$post_type . '_posts_columns', array( $this, 'custom_columns' ) );
			\add_action( 'manage_' . self::$post_type . '_posts_custom_column', array( $this, 'custom_column' ), 10, 2 );
			\add_action( 'add_meta_boxes_' . self::$post_type, array( $this, 'meta_boxes' ) );
			\add_filter( 'wp_editor_settings', array( $this, 'disable_tinymce_editor' ), 10, 2 );
			\add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );
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
							'name'               => _x( 'Form Entries', 'post type', 'flash-form' ),
							'singular_name'      => _x( 'Form Entries', 'post type', 'flash-form' ),
							'edit_item'          => _x( 'View Submission', 'post type', 'flash-form' ),
							'search_items'       => _x( 'Search Entries', 'post type', 'flash-form' ),
							'not_found'          => _x( 'No entries found', 'post type', 'flash-form' ),
							'not_found_in_trash' => _x( 'No entries found', 'post type', 'flash-form' ),
						),
						'menu_icon'             => self::svg_icon_url(),
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
			// Bail early, in case the post-type is unregistered.
			if ( ! self::is_post_type_exists() ) {
				return;
			}

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
				self::svg_icon_url(),
				// Position.
				45
			);

			add_submenu_page(
				// Parent slug.
				PLUGIN['slug'],
				// Page title.
				__( 'Entries', 'flash-form' ),
				// Menu title.
				__( 'Entries', 'flash-form' ),
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
			// Bail early, in case the post-type is unregistered.
			if ( ! self::is_post_type_exists() ) {
				return;
			}

			$author          = (string) $args['headers']['raw']['author'] ?? '';
			$author_email    = (string) $args['headers']['raw']['author_email'] ?? '';
			$subject         = (string) $args['headers']['raw']['subject'] ?? '';
			$message         = (string) $args['message']['rendered'] ?? '';
			$ip_address      = (string) $args['extras']['ip_address'] ?? '';
			$referer_post_id = (int) $args['extras']['post_id'] ?? 0;
			$timestamp       = (string) $args['extras']['timestamp'] ?? '';
			$post_name       = "{$author} - {$timestamp}";
			$post_title      = \sanitize_text_field( \wp_kses( $subject, \wp_kses_allowed_html( 'post' ) ) );
			$post_status     = 'publish';

			\add_filter( 'wp_insert_post_data', array( $this, 'nullify_post_author' ), 11, 2 );

			$post_id = \wp_insert_post(
				array(
					'post_date'    => \sanitize_text_field( \current_time( 'mysql' ) ),
					'post_type'    => self::$post_type,
					'post_status'  => $post_status,
					'post_parent'  => $referer_post_id,
					'post_title'   => $subject ? $post_title : $post_name,
					'post_content' => \wp_kses( "$message\n<!--more-->", \wp_kses_allowed_html( 'post' ) ), // Search is going to pick the data captured here.
					'post_name'    => \md5( $post_name ),
				)
			);

			// At this point insert has finished, we can remove the filter accordingly.
			\remove_filter( 'wp_insert_post_data', array( $this, 'nullify_post_author' ), 11 );

			if ( 'publish' === $post_status ) {
				// Increase count of unread entries.
				$unread = (array) \get_option( self::$unread_option, array() );
				\update_option( self::$unread_option, array( ...$unread, (int) $post_id ) );
			}

			/**
			 * Allow third-party resources to modify form entry post that is being inserted.
			 */
			\do_action( 'mypreview_flash_form_entry_inserted', $post_id, $args );
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
			$referer           = $post_meta['extras']['referer'] ?? '';
			$referer_post_id   = \wp_get_post_parent_id( $post_id );
			$referer_line      = '';

			if ( ! empty( $author_email ) ) {
				$author_email_line = sprintf( '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a><br/>', \esc_url( 'mailto:' . $author_email ), \esc_html( $author_email ) );
			}

			if ( ! empty( $referer ) ) {
				$referer_line = sprintf( '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s<br/>%1$s</a><br/>', \esc_url( $referer ), \get_the_title( $referer_post_id ) . ' #' . strval( $referer_post_id ) );
			}

			switch ( $column_name ) {
				case 'given-name':
					Utils::safe_html( '<div>' . $avatar . '<strong>' . $author . '</strong><br/>' . $author_email_line . '</div>' );
					break;
				case 'referer':
					Utils::safe_html( $referer_line );
					break;

			}
		}

		/**
		 * Adds a few meta box to post edit screen contextually for the entries post type.
		 *
		 * @since     1.1.0
		 * @return    void
		 */
		public function meta_boxes(): void {
			remove_meta_box( 'submitdiv', null, 'side', 'core' );
			add_meta_box( PLUGIN['slug'] . '-summarydiv', __( 'Summary', 'flash-form' ), array( $this, 'summarydiv_callback' ), null, 'side', 'core' );
			add_meta_box( PLUGIN['slug'] . '-entriesdiv', __( 'Entries', 'flash-form' ), array( $this, 'entriesdiv_callback' ), null, 'normal', 'core' );
			add_meta_box( PLUGIN['slug'] . '-extrasdiv', __( 'Extras', 'flash-form' ), array( $this, 'extrasdiv_callback' ), null, 'normal', 'core' );
		}

		/**
		 * Function that fills the "Summary" meta-box with the desired content.
		 * This function echos its output.
		 *
		 * @since     1.1.0
		 * @param     \WP_Post $post    The post object.
		 * @return    void
		 */
		public function summarydiv_callback( \WP_Post $post ): void {
			$post_id = Utils::get_localized_post_id( $post->ID );

			Utils::safe_html( Utils::get_template_html( 'edit/summary', array( 'post_id' => $post_id ) ) );
		}

		/**
		 * Function that fills the "Entries" meta-box with the desired content.
		 * This function echos its output.
		 *
		 * @since     1.1.0
		 * @param     \WP_Post $post    The post object.
		 * @return    void
		 */
		public function entriesdiv_callback( \WP_Post $post ): void {
			$post_id   = Utils::get_localized_post_id( $post->ID );
			$post_meta = \get_post_meta( $post_id, self::$meta_key, true );
			$entries   = (array) $post_meta['message']['raw'] ?? array();

			Utils::safe_html( Utils::get_template_html( 'edit/wrapper-start' ) );
			Utils::safe_html( Utils::get_template_html( 'edit/entries', array( 'entries' => $entries ) ) );
			Utils::safe_html( Utils::get_template_html( 'edit/wrapper-end' ) );
		}

		/**
		 * Function that fills the "Entries" meta-box with the desired content.
		 * This function echos its output.
		 *
		 * @since     1.1.0
		 * @param     \WP_Post $post    The post object.
		 * @return    void
		 */
		public function extrasdiv_callback( \WP_Post $post ): void {
			$entries         = array();
			$post_id         = Utils::get_localized_post_id( $post->ID );
			$post_meta       = \get_post_meta( $post_id, self::$meta_key, true );
			$referer_post_id = \wp_get_post_parent_id( $post_id );
			$entries[]       = array(
				'label' => __( 'IP address', 'flash-form' ),
				'value' => $post_meta['extras']['ip_address'] ?? '',
			);
			$entries[]       = array(
				'label' => __( 'User status', 'flash-form' ),
				'value' => Send_Email::get_user_status( $post_meta['extras']['is_logged_in'] ?? false ),
			);
			$entries[]       = array(
				'label' => __( 'Referrer', 'flash-form' ),
				'value' => sprintf( '<a href="%1$s" target="_blank" rel="noopener noreferrer">%1$s</a>', \esc_url( $post_meta['extras']['referer'] ?? '' ) ),
			);

			Utils::safe_html( Utils::get_template_html( 'edit/wrapper-start' ) );
			Utils::safe_html( Utils::get_template_html( 'edit/entries', array( 'entries' => $entries ) ) );
			Utils::safe_html( Utils::get_template_html( 'edit/wrapper-end' ) );
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
		 * Disable "TinyMCE" HTML Editor for entries post edit screen.
		 *
		 * @since     1.1.0
		 * @param     array  $settings     Array of editor arguments.
		 * @param     string $editor_id    Unique editor identifier. e.g. "content". Accepts "classic-block" when called from block editor's Classic block.
		 * @return    array
		 */
		public function disable_tinymce_editor( array $settings, string $editor_id ): array {
			global $current_screen;

			// Ensure entries post-type is the only match.
			if ( 'content' !== $editor_id || ! function_exists( 'get_current_screen' ) || ! isset( get_current_screen()->post_type ) || get_current_screen()->post_type !== self::$post_type ) {
				return $settings;
			}

			$settings['tinymce']       = false;
			$settings['quicktags']     = false;
			$settings['media_buttons'] = false;
			$settings['editor_css']    = '<style>#postdivrich{pointer-events:none;display:none;}</style>';

			return $settings;
		}

		/**
		 * Alter the default row action links on the "Posts" list table.
		 *
		 * @since     1.1.0
		 * @param     array    $actions    An array of row action links.
		 * @param     \WP_Post $post       The post object.
		 * @return    array
		 */
		public function post_row_actions( array $actions, \WP_Post $post ): array {
			// Ensure entries post-type is the only match.
			if ( self::$post_type !== $post->post_type ) {
				return $actions;
			}

			$post_id        = Utils::get_localized_post_id( $post->ID );
			$custom_actions = array(
				'edit'  => sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					esc_url( get_edit_post_link( $post_id ) ),
					/* translators: %s: Post title. */
					esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'flash-form' ), wp_kses_post( get_the_title( $post_id ) ) ) ),
					__( 'View', 'flash-form' )
				),
				'trash' => $actions['trash'],
			);

			return $custom_actions;
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
		 * Returns a base64-encoded SVG using a data URI, which will be colored to match the color scheme.
		 * "base64_encode()" function is used to obfuscate the SVG icon to generate data-URI, as recommended by the core.
		 *
		 * @see       http://developer.wordpress.org/reference/functions/add_menu_page
		 * @since     1.1.0
		 * @return    string
		 * @phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		 */
		private static function svg_icon_url(): string {
			return 'data:image/svg+xml;base64,' . base64_encode( '<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="black" d="m.11 11.905c0-6.567 5.324-11.891 11.89-11.891.04 0 .096-.002.165-.005.51-.018 1.725-.063 1.639.512l-1.101 7.337a.1.1 0 00.088.114l7.387.821a.353.353 0 01.261.539l-1.816 2.905a.353.353 0 01-.35.163l-11.727-1.675a.353.353 0 01-.24-.551l6-8.67c-5.782-.166-10.71 4.61-10.71 10.4 0 1.181.197 2.316.56 3.374.187.55-.117.962-.421 1.375l-.096.13a.292.292 0 01-.506-.046 11.85 11.85 0 01-1.023-4.832zm8.347-2.25a.1.1 0 01-.069-.156l3.006-4.342c.12-.172.39-.067.363.14l-.368 2.764c-.12.802.053 1.142.858 1.232l6.084.633a.1.1 0 01.074.152l-.538.861a.1.1 0 01-.099.046l-9.311-1.33zm15.435 2.448c0 6.679-5.48 11.736-11.995 11.893-.441.01-1.767.042-1.683-.515l1.1-7.337a.1.1 0 00-.087-.114l-7.388-.821a.353.353 0 01-.26-.539l1.816-2.906a.353.353 0 01.35-.162l11.727 1.675c.26.037.39.335.24.551l-6.006 8.675c5.78.16 10.7-4.615 10.7-10.4 0-1.18-.197-2.315-.56-3.373-.188-.55.117-.962.421-1.376l.096-.13a.292.292 0 01.506.047 11.849 11.849 0 011.023 4.832zm-8.33 2.244a.1.1 0 01.067.155l-3.006 4.342c-.12.173-.39.068-.362-.14l.368-2.764c.12-.801-.053-1.142-.859-1.231l-6.083-.633a.1.1 0 01-.074-.152l.538-.862a.1.1 0 01.099-.046l9.311 1.33zm1.208-9.527a.267.267 0 00-.465-.144l-1.805 2.028a.267.267 0 00.175.444l2.088.19c.17.015.311-.13.29-.3l-.283-2.218zm-9.54 14.37a.267.267 0 00.465.144l1.805-2.026a.267.267 0 00-.176-.444l-2.088-.19a.267.267 0 00-.29.3l.285 2.218z"/></svg>' );
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

		/**
		 * Determines whether the entries post type is registered.
		 *
		 * @since     1.1.0
		 * @return    bool
		 */
		private static function is_post_type_exists() {
			return \post_type_exists( self::$post_type );
		}

	}
endif;
