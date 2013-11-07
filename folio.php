<?php
/*
	Plugin Name: Folio
	Demo: http://folio.ahansson.com
	Description: Folio is a Custom Post Type section that is helping you to show your work in a nice way.
	Version: 1.7
	Author: Aleksander Hansson
	Author URI: http://ahansson.com
	v3: true
*/

class ah_Folio_Plugin {

	function __construct() {
		add_action( 'init', array( &$this, 'ah_updater_init' ) );
	}

	/**
	 * Load and Activate Plugin Updater Class.
	 * @since 0.1.0
	 */
	function ah_updater_init() {

		/* Load Plugin Updater */
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/plugin-updater.php' );

		/* Updater Config */
		$config = array(
			'base'      => plugin_basename( __FILE__ ), //required
			'repo_uri'  => 'http://shop.ahansson.com',  //required
			'repo_slug' => 'folio',  //required
		);

		/* Load Updater Class */
		new AH_Folio_Plugin_Updater( $config );
	}

}

new ah_Folio_Plugin;