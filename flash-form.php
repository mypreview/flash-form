<?php
/**
 * The "Flash Form" bootstrap file.
 *
 * The plugin bootstrap file.
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * Flash Form is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * @link                https://www.mypreview.one
 * @since               1.0.0
 * @package             flash-form
 * @author              MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @copyright           © 2015 - 2022 MyPreview. All Rights Reserved.
 *
 * @wordpress-plugin
 * Plugin Name:         Flash Form
 * Plugin URI:          https://mypreview.github.io/flash-form
 * Description:         Create a form as easily as editing a block.
 * Version:             1.0.0
 * Requires at least:   5.5
 * Requires PHP:        7.4
 * Author:              Mahdi Yazdani
 * Author URI:          https://www.mahdiyazdani.com
 * License:             GPL-3.0+
 * License URI:         http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:         flash-form
 * Domain Path:         /languages
 */

namespace Flash_Form;

use Flash_Form\Includes\Cache_Data as Cache_Data;
use function Flash_Form\Includes\Utils\form_submit as form_submit;

define(
	__NAMESPACE__ . '\PLUGIN',
	array(
		'basename'   => plugin_basename( __FILE__ ),
		'block_name' => 'mypreview/flash-form',
		'class_name' => 'wp-block-mypreview-flash-form',
		'dir_path'   => untrailingslashit( plugin_dir_path( __FILE__ ) ),
		'dir_url'    => untrailingslashit( plugin_dir_url( __FILE__ ) ),
		'nonce'      => 'mypreview_flash_form_nonce',
		'slug'       => 'flash-form',
	)
);

/**
 * Loads the PSR-4 autoloader implementation.
 *
 * @since     1.0.0
 * @return    void
 */
require_once PLUGIN['dir_path'] . '/vendor/autoload.php';

/**
 * Load the plugin text domain for translation.
 *
 * @since     1.0.0
 * @return    void
 */
function textdomain(): void {
	load_plugin_textdomain( 'flash-form', false, dirname( PLUGIN['basename'] ) . '/languages' );
}
add_action( 'init', __NAMESPACE__ . '\textdomain' );

/**
 * Registers the block type from the metadata stored in the "block.json" file.
 *
 * @since     1.0.0
 * @return    void
 */
function register_block(): void {
	// Clean (erase) and start the output buffer.
	ob_clean();
	ob_start();

	register_block_type_from_metadata(
		PLUGIN['dir_path'],
		array(
			'render_callback' => __NAMESPACE__ . '\render_callback',
		)
	);
}
add_action( 'init', __NAMESPACE__ . '\register_block' );

/**
 * Renders the block on server.
 *
 * @since     1.0.0
 * @param     array  $attributes    The block attributes.
 * @param     string $content       The block content.
 * @return    string
 */
function render_callback( array $attributes = array(), string $content ): ?string {
	// Look for the form response, if there is any!
	$return = form_submit( $attributes, $content );

	if ( ! empty( $return ) ) {
		return $return;
	}

	libxml_use_internal_errors( true );
	$dom = new \DOMDocument();
	$dom->loadHTML( $content, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED );
	$xpath = new \DomXPath( $dom );
	$node  = $xpath->query( "//form[contains(@class, '" . PLUGIN['class_name'] . "__fieldset')]" );

	if ( $node && $node->length ) {
		$before_fieldset = apply_filters( 'mypreview_flash_form_render_callback_before_fieldset', __return_empty_string(), $attributes );
		$after_fieldset  = apply_filters( 'mypreview_flash_form_render_callback_after_fieldset', __return_empty_string(), $attributes );

		if ( ! empty( $before_fieldset ) ) {
			$before_fieldset_fragment = $dom->createDocumentFragment();
			$before_fieldset_fragment->appendXML( $before_fieldset );
			$node->item( 0 )->insertBefore( $before_fieldset_fragment, $node->item( 0 )->firstChild );
		}

		if ( ! empty( $after_fieldset ) ) {
			$after_fieldset_fragment = $dom->createDocumentFragment();
			$after_fieldset_fragment->appendXML( $after_fieldset );
			$node->item( 0 )->insertBefore( $after_fieldset_fragment );
		}

		libxml_clear_errors();
		$content = utf8_decode( $dom->saveHTML( $dom->documentElement ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	}

	if ( $attributes['isAjax'] ?? false ) {
		new Cache_Data( $attributes, $content, $attributes['formId'] ?? '' );
	}

	/**
	 * Allow third-party resources to extend the block content.
	 */
	do_action( 'mypreview_flash_form_render_callback_content', $attributes );

	return apply_filters( 'mypreview_flash_form_render_callback_content', $content, $attributes );
}

/**
 * Add additional helpful links to the plugin’s metadata.
 *
 * @since     1.0.0
 * @param     array  $links    An array of the plugin’s metadata.
 * @param     string $file     Path to the plugin file relative to the plugins directory.
 * @return    array
 */
function add_meta_links( array $links, string $file ): array {
	if ( PLUGIN['basename'] !== $file ) {
		return $links;
	}

	$plugin_links = array();
	/* translators: 1: Open anchor tag, 2: Close anchor tag. */
	$plugin_links[] = sprintf( _x( '%1$sCommunity support%2$s', 'plugin link', 'flash-form' ), sprintf( '<a href="https://wordpress.org/support/plugin/%s" target="_blank" rel="noopener noreferrer nofollow">', PLUGIN['slug'] ), '</a>' );
	/* translators: 1: Open anchor tag, 2: Close anchor tag. */
	$plugin_links[] = sprintf( _x( '%1$sDonate%2$s', 'plugin link', 'flash-form' ), sprintf( '<a href="https://www.buymeacoffee.com/mahdiyazdani" class="button-link-delete" target="_blank" rel="noopener noreferrer nofollow" title="%s">☕ ', esc_attr__( 'Donate to support this plugin', 'flash-form' ) ), '</a>' );

	return array_merge( $links, $plugin_links );
}
add_filter( 'plugin_row_meta', __NAMESPACE__ . '\add_meta_links', 10, 2 );
