<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://marshallchikari.co.zw
 * @since      1.0.0
 *
 * @package    Chikari_Album_Gallery
 * @subpackage Chikari_Album_Gallery/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Chikari_Album_Gallery
 * @subpackage Chikari_Album_Gallery/includes
 * @author     Marshall Chikari <hello@marshallchikari.co.zw>
 */
class Chikari_Album_Gallery_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'chikari-album-gallery',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
