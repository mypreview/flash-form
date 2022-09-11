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

use Flash_Form\Includes\Core as Core;

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
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since     1.0.0
 * @return    void
 */
function run(): void {
	$plugin = new Core();
	$plugin->run();
}
run();
