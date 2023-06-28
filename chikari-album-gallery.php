<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://marshallchikari.co.zw
 * @since             1.0.0
 * @package           Chikari_Album_Gallery
 *
 * @wordpress-plugin
 * Plugin Name:       Chikari Album Gallery
 * Plugin URI:        https://marshallchikari.co.zw
 * Description:       The "Chikari Gallery Plugin" is a custom WordPress plugin that allows users to create and display image galleries on their websites.
 * Version:           1.0.0
 * Author:            Marshall Chikari
 * Author URI:        https://marshallchikari.co.zw
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       chikari-album-gallery
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('CHIKARI_ALBUM_GALLERY_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-chikari-album-gallery-activator.php
 */
function activate_chikari_album_gallery()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-chikari-album-gallery-activator.php';
    Chikari_Album_Gallery_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-chikari-album-gallery-deactivator.php
 */
function deactivate_chikari_album_gallery()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-chikari-album-gallery-deactivator.php';
    Chikari_Album_Gallery_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_chikari_album_gallery');
register_deactivation_hook(__FILE__, 'deactivate_chikari_album_gallery');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-chikari-album-gallery.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_chikari_album_gallery()
{

    $plugin = new Chikari_Album_Gallery();
    $plugin->run();

}
run_chikari_album_gallery();
