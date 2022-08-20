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
 * Description:         .....
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

define(
	__NAMESPACE__ . '\PLUGIN',
	array(
		'basename' => plugin_basename( __FILE__ ),
		'dir_path' => untrailingslashit( plugin_dir_path( __FILE__ ) ),
		'dir_url'  => untrailingslashit( plugin_dir_url( __FILE__ ) ),
		'slug'     => 'flash-form',
	)
);

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
	register_block_type_from_metadata( PLUGIN['dir_path'] );
}
add_action( 'init', __NAMESPACE__ . '\register_block' );
