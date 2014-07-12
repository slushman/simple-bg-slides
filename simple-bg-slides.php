<?php
/**
 * Simple BG Slides: Simply add a slides of images in the background of any page on your site.
 *
 * Loosely based on the WordPress Plugin Boilerplate by Tom McFarlin
 * 
 * @package   Simple BG Slides
 * @author    Slushman <chris@slushman.com>
 * @copyright Copyright (c) 2014, Slushman
 * @license   GPL-2.0+
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link      http://slushman.com/plugins/simple-bg-slides
 * @version   0.1
 * 
 * @wordpress-plugin
 * Plugin Name: 		Simple BG Slides
 * Plugin URI: 			http://slushman.com/plugins/simple-bg-slides
 * Description: 		Simply add a slides of images in the background of any page on your site.
 * Version: 			0.1
 * Author: 				Slushman
 * Author URI: 			http://www.slushman.com
 * Text Domain:			simple-bg-slides
 * Domain Path:			/languages
 * Github Plugin URI: 	https://github.com/slushman/simple-bg-slides
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/**
 * Includes the plugin class file
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-simple-bg-slides.php' );




/**
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Simple_BG_Slides', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Simple_BG_Slides', 'deactivate' ) );

/**
 * Loads the plugin instance when plugins are loaded
 */
add_action( 'plugins_loaded', array( 'Simple_BG_Slides', 'get_instance' ) );


/**
 * Create a global variable for accessing the plugin options
 */
$slushman_sbgs_settings = get_option( 'slushman_sbgs_options' );



/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/**
 * Includes the admin file and loads the instance of it when the plugins are loaded.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	//require_once( plugin_dir_path( __FILE__ ) . 'admin/class-admin.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/slides_metabox.php' );

	//add_action( 'plugins_loaded', array( 'Simple_BG_Slides_Admin', 'get_instance' ) );
	add_action( 'plugins_loaded', array( 'Simple_BG_Slides_Metabox', 'get_instance' ) );

} // End of admin check

?>