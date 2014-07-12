<?php

/**
 * Simple BG Slides
 *
 * This is a class handling the admin-facing portions of the plugin.
 *
 * @package   Simple BG Slides
 * @author    Slushman <chris@slushman.com>
 * @license   GPL-2.0+
 * @link      http://slushman.com/plugins/simple-bg-slides
 * @copyright Copyright (c) 2014, Slushman
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) { die; }

require_once( plugin_dir_path( __FILE__ ) . '../toolkit/make_admin.php' );

if ( !class_exists( 'Simple_BG_Slides_Admin' ) ) {

	class Simple_BG_Slides_Admin extends Slushman_Make_Admin {

/**
 * Instance of this class.
 *
 * @access 	protected
 * @since 	0.1
 * @var 	object
 */
		protected static $instance = null;

/**
 * Initialize the plugin by loading admin scripts & styles and adding a
 * settings page and menu.
 *
 * @access 	public
 * @since 	0.1
 * 
 * @uses 	check_required()
 * @uses 	set_columns()
 * @uses 	set_fields()
 * @uses 	set_groups()
 * @uses 	set_menu()
 * @uses 	set_sections()
 * @uses 	set_tabs()
 * @uses 	setup()
 *
 * @return 	void
 */
		public function __construct() {

			$plugin 		= Simple_BG_Slides::get_instance();
			$this->i18n 	= $plugin->get_i18n();
			$this->name 	= $plugin->get_name();
			$this->version 	= $plugin->get_version();

			//$tools = array( 'cpt', 'taxonomy', 'metabox' );

			$tools = array( 'field', 'sanitized' );

			foreach ( $tools as $tool ) {

				require_once( plugin_dir_path( __FILE__ ) . "../toolkit/make_{$tool}.php" );

			} // End of foreach loop



			// MaxImage option fields

			$i								= 0;

			// backgroundSize
			$j								= 0;
			$fields[$i]['class']			= 'sbgs_field';
			$fields[$i]['desc']				= 'Used for older browsers that do not support background-size:cover/contain;';
			$fields[$i]['id']				= 'sbgs_backgroundsize_field';
			$fields[$i]['label']			= 'Background Size';
			$fields[$i]['name']				= 'slushman_sbgs_options[sbgs_backgroundsize_field]';
			$fields[$i]['section']			= 'sbgs_options';
			$fields[$i]['selections'][$j]	= array( 'label' => 'Contain', 'value' => 'contain' );
			$j++;
			$fields[$i]['selections'][$j]	= array( 'label' => 'Cover', 'value' => 'cover' );
			$j++;
			$fields[$i]['setting']			= 'slushman_sbgs_options';
			$fields[$i]['type']				= 'select';
			$fields[$i]['value']			= 'cover';
			$i++;
			
			// fx
			$j								= 0;
			$fields[$i]['class']			= 'sbgs_field';
			$fields[$i]['desc']				= 'Transition effect type';
			$fields[$i]['id']				= 'sbgs_fx_field';
			$fields[$i]['label']			= 'Transition Effect';
			$fields[$i]['name']				= 'slushman_sbgs_options[sbgs_fx_field]';
			$fields[$i]['section']			= 'sbgs_options';
			$fields[$i]['selections'][$j]	= array( 'label' => 'fade', 'value' => 'fade' );
			$j++;
			$fields[$i]['selections'][$j]	= array( 'label' => 'wipe', 'value' => 'wipe' );
			$j++;
			$fields[$i]['selections'][$j]	= array( 'label' => 'scrollUp', 'value' => 'scrollUp' );
			$j++;
			$fields[$i]['selections'][$j]	= array( 'label' => 'scrollDown', 'value' => 'scrollDown' );
			$j++;
			$fields[$i]['selections'][$j]	= array( 'label' => 'scrollLeft', 'value' => 'scrollLeft' );
			$j++;
			$fields[$i]['selections'][$j]	= array( 'label' => 'scrollRight', 'value' => 'scrollRight' );
			$j++;
			$fields[$i]['selections'][$j]	= array( 'label' => 'cover', 'value' => 'cover' );
			$j++;
			$fields[$i]['selections'][$j]	= array( 'label' => 'shuffle', 'value' => 'shuffle' );
			$j++;
			$fields[$i]['setting']			= 'slushman_sbgs_options';
			$fields[$i]['type']				= 'select';
			$fields[$i]['value']			= 'fade';
			$i++;
			
			// speed
			$fields[$i]['class']			= 'sbgs_field';
			$fields[$i]['desc']				= 'Speed of the transitions (in milliseconds)';
			$fields[$i]['id']				= 'sbgs_speed_field';
			$fields[$i]['label']			= 'Transition Speed';
			$fields[$i]['name']				= 'slushman_sbgs_options[sbgs_speed_field]';
			$fields[$i]['placeholder']		= '1000';
			$fields[$i]['section']			= 'sbgs_options';
			$fields[$i]['setting']			= 'slushman_sbgs_options';
			$fields[$i]['step']				= 250;
			$fields[$i]['type']				= 'number';
			$fields[$i]['value']			= '1000';
			$i++;

			// timeout
			$fields[$i]['class']			= 'sbgs_field';
			$fields[$i]['desc']				= 'How long (in milliseconds) each slide is displayed';
			$fields[$i]['id']				= 'sbgs_timeout_field';
			$fields[$i]['label']			= 'Slide Display Time';
			$fields[$i]['name']				= 'slushman_sbgs_options[sbgs_timeout_field]';
			$fields[$i]['placeholder']		= '4000';
			$fields[$i]['section']			= 'sbgs_options';
			$fields[$i]['setting']			= 'slushman_sbgs_options';
			$fields[$i]['step']				= 250;
			$fields[$i]['type']				= 'number';
			$fields[$i]['value']			= '4000';
			$i++;



			// Menu

			$menu['cap']                = 'manage_options';
			$menu['page']				= 'Simple BG Slides Settings';
			$menu['slug']				= $this->i18n;
			$menu['title']				= 'Simple BG Slides';
			$menu['top_slug']			= 'settings';

			

			// Settings sections

			$i							= 0;
			$sections[$i]['desc']		= '';
			$sections[$i]['id']			= 'sbgs_options';
			$sections[$i]['name']		= 'Cycle settings';
			$sections[$i]['setting']	= 'slushman_sbgs_options';
			$i++;



			// Settings

			$settings					= array( 'slushman_sbgs_options' );



			// Tabs
			$tabs 						= '';
						


			// Admin functions

			$this->initialize_settings( $fields, $settings );
			$this->setup( $fields, $menu, $sections, $settings, $tabs );
			$this->add_actions();
			
		} // End of __construct()

/**
 * Return an instance of this class.
 *
 * @access 	public
 * @since 	0.1
 *
 * @return 	object 		A single instance of this class.
 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {

				self::$instance = new self;
			
			}

			return self::$instance;
		
		} // End of get_instance()

/**
 * Display text above the tabs on the settings page
 * 
 * @return [type] [description]
 */
		public function above_tabs() {


		} // End of above_tabs()

/**
 * Display text below the tabs on the settings page
 * 
 * @return [type] [description]
 */
		public function below_tabs() {


		} // End of below_tabs()


	} // End of class

} // End of class check

?>